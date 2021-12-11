<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tyganeutronics.com
 * @since      1.0.0
 * @version		1.0.0
 *
 * @package    Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate/admin
 * @author     Richard Muvirimi <tygalive@gmail.com>
 */
class Plugin_Boilerplate_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function enqueue_styles()
	{

		wp_register_style($this->plugin_name . "-options", plugin_dir_url(__FILE__) . 'css/plugin-boilerplate-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function enqueue_scripts()
	{

		wp_register_script($this->plugin_name . "-options", plugin_dir_url(__FILE__) . 'js/plugin-boilerplate-admin.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_name . "-options", "plugin_boilerplate", array(
			"name" => $this->plugin_name,
			"cb" => rest_url($this->plugin_name . '/v1/sanitize_title')
		));
	}

	/**
	 * On saving options, check if our page and initiate requested action
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param array $allowed_options
	 * @return array
	 */
	public function on_save_options($allowed_options)
	{

		if (get_current_screen()->id == ("options")) {

			if (isset($_POST[$this->plugin_name . "-action"])) {

				$action = array_keys($_POST[$this->plugin_name . "-action"])[0];

				if ($action) {

					/**
					 * Initiate plugin generation
					 */
					do_action($this->plugin_name . "-generate-plugin", $action);
				}

				if (empty(get_settings_errors())) {
					add_settings_error($this->plugin_name, $this->plugin_name, __('Plugin details saved for future use.', $this->plugin_name), 'updated');
				}
			}
		}

		return  $allowed_options;
	}

	/**
	 * Try to initialize the file system
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 * @return bool
	 */
	private function init_file_system()
	{
		global $wp_filesystem;

		$url = admin_url('plugins.php?page=' . $this->plugin_name);

		ob_start();
		$credentials = request_filesystem_credentials($url);
		$data        = ob_get_clean();

		if (false === $credentials) {
			if (!empty($data)) {
				require_once ABSPATH . 'wp-admin/admin-header.php';
				echo $data;
				require_once ABSPATH . 'wp-admin/admin-footer.php';
				exit;
			}
			return false;
		}

		if (!WP_Filesystem($credentials)) {
			ob_start();
			// Failed to connect. Error and request again.
			request_filesystem_credentials($url, '', true);
			$data = ob_get_clean();

			if (!empty($data)) {
				require_once ABSPATH . 'wp-admin/admin-header.php';
				echo $data;
				require_once ABSPATH . 'wp-admin/admin-footer.php';
				exit;
			}
			return false;
		}

		if (!is_object($wp_filesystem)) {
			add_settings_error($this->plugin_name, $this->plugin_name, __('Could not access filesystem.', $this->plugin_name));
			return false;
		}

		if (is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->has_errors()) {
			add_settings_error($this->plugin_name, $this->plugin_name, __('Filesystem error.', $this->plugin_name));
			return false;
		}

		// Get the base plugin folder.
		$content_dir = $wp_filesystem->wp_content_dir();
		if (empty($content_dir)) {
			add_settings_error($this->plugin_name, $this->plugin_name, __('Unable to locate WordPress content directory.', $this->plugin_name));
			return false;
		}

		return true;
	}

	/**
	 * Generate the plugin file and action it
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $action
	 * @return void
	 */
	public function generate_plugin($action)
	{
		if ($this->init_file_system()) {

			$location =	plugin_boilerplate_files();

			switch ($action) {
				case "download":
					$location =	$this->generate_zip($location);
					wp_cache_set("plugin", array("location" => $location, "action" => "download"), $this->plugin_name);
					break;
				case "copy":
					$this->duplicate_plugin($location);
					break;
				case "install":
					$location =	$this->generate_zip($location);
					wp_cache_set("plugin", array("location" => $location, "action" => "install"), $this->plugin_name);
					break;
			}
		}
	}

	/**
	 * Confirm if we should copy subdir
	 *
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param bool $valid
	 * @param string $path
	 * @return bool
	 */
	public function filter_plugin_file_valid($valid, $path)
	{
		global $wp_filesystem;

		if ($wp_filesystem->is_dir($path)) {

			$dir = str_replace(plugin_boilerplate_files(), "", $path);

			preg_match("/^[\/\\\\]?(\w+)[\/\\\\]?/", $dir, $match);

			if (in_array($match[1], plugin_boilerplate_dirs())) {

				$valid = filter_input(INPUT_POST, $this->plugin_name . "-feature-" . $match[1], FILTER_SANITIZE_STRING) == "on";
			}

			/**
			 * Composer
			 * 
			 * was disabled above re-enable here
			 */
			if ($match[1] == "vendor" && filter_input(INPUT_POST, $this->plugin_name . "-feature-composer", FILTER_SANITIZE_STRING) == "on") {
				$valid = true;
			}
		}

		if ($wp_filesystem->is_file($path)) {
			//composer
			if (basename($path) == "composer.json" && filter_input(INPUT_POST, $this->plugin_name . "-feature-composer", FILTER_SANITIZE_STRING) != "on") {
				$valid = false;
			}

			//git
			if (basename($path) == ".gitignore" && filter_input(INPUT_POST, $this->plugin_name . "-vcs-git", FILTER_SANITIZE_STRING) != "on") {
				$valid = false;
			}
		}

		return $valid;
	}

	/**
	 * Get plugin slug as submitted
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $slug
	 * @return string
	 */
	public function filter_plugin_slug($slug)
	{
		return sanitize_title(filter_input(INPUT_POST, $this->plugin_name . "-slug", FILTER_SANITIZE_STRING)) ?: $slug;
	}

	/**
	 * Get plugin name as submitted
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $name
	 * @return string
	 */
	public function filter_plugin_name($name)
	{
		return sanitize_text_field(filter_input(INPUT_POST, $this->plugin_name . "-name", FILTER_SANITIZE_STRING)) ?: $name;
	}

	/**
	 * Get plugin website as submitted
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $website
	 * @return string
	 */
	public function filter_plugin_website($website)
	{
		return esc_url_raw(filter_input(INPUT_POST, $this->plugin_name . "-website", FILTER_SANITIZE_STRING)) ?: $website;
	}

	/**
	 * Get author name as submitted
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $author
	 * @return string
	 */
	public function filter_plugin_author($author)
	{
		return sanitize_text_field(filter_input(INPUT_POST, $this->plugin_name . "-author-name", FILTER_SANITIZE_STRING)) ?: $author;
	}

	/**
	 * Get author website as submitted
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $website
	 * @return string
	 */
	public function filter_plugin_author_website($website)
	{
		return esc_url_raw(filter_input(INPUT_POST, $this->plugin_name . "-author-website", FILTER_SANITIZE_STRING)) ?: $website;
	}

	/**
	 * Get author email as submitted
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $email
	 * @return string
	 */
	public function filter_plugin_author_email($email)
	{
		return sanitize_email(filter_input(INPUT_POST, $this->plugin_name . "-author-email", FILTER_SANITIZE_STRING)) ?: $email;
	}

	/**
	 * Download generated plugin zip file
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 * @param string $location
	 * @return void
	 */
	public function on_options_redirect($location)
	{

		if (function_exists("get_current_screen")) {

			if (get_current_screen()->id == "options") {

				parse_str(parse_url($location, PHP_URL_QUERY), $args);

				if (isset($args["settings-updated"]) && $args["settings-updated"]) {

					$action = wp_cache_get("plugin", $this->plugin_name);

					if ($action) {

						wp_cache_delete("plugin", $this->plugin_name);

						/**
						 * Resave options incase we have added our own as this functions is called late
						 */
						set_transient('settings_errors', get_settings_errors(), 30);

						switch ($action["action"]) {
							case "download":
								$this->download_plugin($action["location"]);
								break;
							case "install":

								global $wp_filesystem;

								$target = plugin_boilerplate_upload_dir() . basename($action["location"]);

								$wp_filesystem->move($action["location"], $target, true);

								$location = add_query_arg(array(
									"action" => $this->plugin_name,
									"_wpnonce" => wp_create_nonce($this->plugin_name),
									"_wp_http_referer" => admin_url("plugins.php"),
									"package" => $target
								), admin_url("update.php"));
								break;
						}
					}
				}
			}
		}

		return $location;
	}

	/**
	 * Force download of generated zip file
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 * @param string $location
	 * @return void
	 */
	private function download_plugin($location)
	{
		global $wp_filesystem;

		if ($wp_filesystem->exists($location)) {

			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . basename($location) . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . $wp_filesystem->size($location));
			header('Set-Cookie:fileLoading=true');
			readfile($location);

			$wp_filesystem->delete($location);
			exit;
		} else {
			add_settings_error($this->plugin_name, $this->plugin_name, __('Failed to locate the file to download.', $this->plugin_name));

			/**
			 * Resave options incase we have added our own as this functions is called late
			 */
			set_transient('settings_errors', get_settings_errors(), 30);
		}
	}

	/**
	 * Install the plugin
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 * @return void
	 */
	public function install_plugin()
	{

		if (!current_user_can('upload_plugins')) {
			wp_die(__('Sorry, you are not allowed to install plugins on this site.'));
		}

		if (filter_input(INPUT_GET, "overwrite")) {
			wp_verify_nonce(filter_input(INPUT_GET, "_wpnonce"));
		} else {
			check_admin_referer($this->plugin_name);
		}

		$package = filter_input(INPUT_GET, "package");

		$title        = __('Install Generated Plugin');
		$parent_file  = 'plugins.php';
		$submenu_file = 'plugin-install.php';
		require_once ABSPATH . 'wp-admin/admin-header.php';

		$title = sprintf(__('Installing generated plugin: %s'), esc_html(get_option($this->plugin_name . '-name', "Plugin Name")));
		$nonce = $this->plugin_name;
		$url   = add_query_arg(compact('package'), 'update.php?action=' . $this->plugin_name);
		$type  = 'upload'; // Install plugin type, From Web or an Upload.

		$overwrite = isset($_GET['overwrite']) ? sanitize_text_field($_GET['overwrite']) : '';
		$overwrite = in_array($overwrite, array('update-plugin', 'downgrade-plugin'), true) ? $overwrite : '';

		$upgrader = new Plugin_Upgrader(new Plugin_Installer_Skin(compact('type', 'title', 'nonce', 'url', 'overwrite')));
		$result   = $upgrader->install($package, array('overwrite_package' => $overwrite));

		if ($result || is_wp_error($result)) {
			global $wp_filesystem;

			if ($wp_filesystem->exists($package)) {
				$wp_filesystem->delete($package);
			}
		}

		require_once ABSPATH . 'wp-admin/admin-footer.php';
	}

	/**
	 * Move plugin from temp dir to plugins dir
	 * 
	 * @since 1.0.0
	 * @version 1.0.0
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 * @param string $location
	 * @return string
	 */
	private function duplicate_plugin($location)
	{
		if (current_user_can('upload_plugins')) {

			global $wp_filesystem;

			$file = $wp_filesystem->wp_plugins_dir() .  apply_filters($this->plugin_name . "-plugin-slug", "plugin-name");

			if (!get_transient($this->plugin_name . "-force-copy") && $wp_filesystem->exists($file)) {
				add_settings_error($this->plugin_name, $this->plugin_name, __('Target directory already exists, use install instead or copy again to overwrite existing contents.', $this->plugin_name));

				set_transient($this->plugin_name . "-force-copy", true, MINUTE_IN_SECONDS * 2);
			} else {

				//clean target
				if ($wp_filesystem->exists($file)) {
					$wp_filesystem->delete($file, true);
				}

				//copy files
				$done =	plugin_boilerplate_walk_dir(function ($source, $target,  $content) use ($wp_filesystem, $file) {

					$done = true;

					if (!$wp_filesystem->exists($file)) {
						$wp_filesystem->mkdir($file);
					}

					$dir = dirname($file . $target);
					if (!$wp_filesystem->exists($dir)) {
						$wp_filesystem->mkdir($dir);
					}

					if ($wp_filesystem->is_file($source)) {

						$done |= $wp_filesystem->touch($file . $target);
						$done |= $wp_filesystem->put_contents($file . $target, $content);
					}

					return $done;
				}, $location);

				delete_transient($this->plugin_name . "-force-copy");
			}

			if ($done) {
				add_settings_error($this->plugin_name, $this->plugin_name, __('Plugin copied to the plugin dir.', $this->plugin_name), 'updated');
			} else {
				add_settings_error($this->plugin_name, $this->plugin_name, __('Failed to copy plugin to the plugins dir.', $this->plugin_name));
			}
		} else {
			add_settings_error($this->plugin_name, $this->plugin_name,  __('Sorry, you are not allowed to install plugins on this site.', $this->plugin_name));
		}

		return $file;
	}

	/**
	 * Generate a zip file
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $location
	 * @return string
	 */
	private function generate_zip($location)
	{

		$done = false;
		if (class_exists("ZipArchive", false)) {

			$zip_file = plugin_boilerplate_temp_dir() .  apply_filters($this->plugin_name . "-plugin-slug", "plugin-name") . '.zip';

			$zip = new ZipArchive();
			$zipArchive = $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
			if ($zipArchive !== TRUE) {
				add_settings_error($this->plugin_name, $this->plugin_name, __('Zip extension not enabled, you may only install the plugin.', $this->plugin_name));
			} else {

				global $wp_filesystem;

				$done =	plugin_boilerplate_walk_dir(function ($source, $target,  $content) use (&$zip, $wp_filesystem) {

					$success = true;
					if ($wp_filesystem->is_file($source)) {
						$success =	$zip->addFromString($target, $content);
					}
					return $success;
				}, $location);

				$zip->close();
				$done =  $zip_file;
			}
		} else {
			add_settings_error($this->plugin_name, $this->plugin_name, __('Zip extension not enabled, you may only install the plugin.', $this->plugin_name));
		}
		return $done;
	}

	/**
	 * Replace plugin name with the one provided
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $content
	 * @return string
	 */
	public function filter_plugin_file_content_name($content)
	{
		return preg_replace("/(\s\*\sPlugin\sName:\s+)(.*)/", "$1" . apply_filters($this->plugin_name . "-plugin-name", "$2"), $content);
	}

	/**
	 * Rename contents of a file to provided slug
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $content
	 * @return string
	 */
	public function filter_plugin_file_content_slug($content)
	{
		$name = apply_filters($this->plugin_name . "-plugin-slug", "plugin-name");

		/**
		 * Try to preserve case of original text (class names, functions).
		 * 
		 * If upper case or lower follow suit else try to make the first character follow case
		 */
		return preg_replace_callback("/(?<=[^\$>](?<!get_|WordPress\-))plugin(-|_)name/i", function ($match) use ($name) {

			if (preg_match("/\b([[:upper:]_]+)\b/", $match[0])) {
				$name = strtoupper($name);
			} elseif (preg_match("/\b([[:lower:]_]+)\b/", $match[0])) {
				$name = strtolower($name);
			} else {

				$name = preg_replace_callback("/\b(?![^[:alpha:]])([[:alpha:]])\w+/", function ($_match) {
					return ucfirst($_match[0]);
				}, $name);
			}

			/**
			 * Join words with appropriate symbol
			 */
			return preg_replace("/[-|_| ]/", $match[1], $name);
		}, $content);
	}

	/**
	 * Replace plugin url with the one provided
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $content
	 * @return string
	 */
	public function filter_plugin_file_content_website($content)
	{
		return preg_replace("/((\s\*\s@link\s+)|(\s\*\sPlugin\sURI:\s+))(.*)/", "$1" . apply_filters($this->plugin_name . "-plugin-website", "$4"), $content);
	}

	/**
	 * Replace plugin author with the one provided
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $content
	 * @return string
	 */
	public function filter_plugin_file_content_author($content)
	{
		return preg_replace("/(Your.*)(?<=(Company)|(Name))/", apply_filters($this->plugin_name . "-plugin-author", "$0"), $content);
	}

	/**
	 * Replace plugin author url with the one provided
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $content
	 * @return string
	 */
	public function filter_plugin_file_content_author_website($content)
	{
		return preg_replace("/(\s\*\sAuthor\sURI:\s+)(.*)/", "$1" . apply_filters($this->plugin_name . "-plugin-author-website", "$2"), $content);
	}

	/**
	 * Replace plugin author email with the one provided
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $content
	 * @return string
	 */
	public function filter_plugin_file_content_author_email($content)
	{
		return preg_replace("/(<)(email@example\.com)(>)/", "$1" . apply_filters($this->plugin_name . "-plugin-author-email", "$2") . "$3", $content);
	}

	/**
	 * Rename file path of plugin
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $path
	 * @return string
	 */
	public function filter_plugin_file_path($path)
	{

		$match = "(-|_| )";

		$name = apply_filters($this->plugin_name . "-plugin-slug", "plugin-name");
		$name = preg_replace("/" . $match . "/", "\$1", $name);

		return preg_replace("/plugin" . $match . "name/", $name, $path);
	}

	/**
	 * Set plugin features as requested
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param string $content
	 * @param string $path
	 * @return string
	 */
	public function filter_plugin_file_content_features($content, $path)
	{

		if (basename($path) ==  "class-plugin-name.php") {

			/**
			 * Filter plugin directories
			 */
			$dirs = plugin_boilerplate_dirs();
			foreach ($dirs as $dir) {

				if (filter_input(INPUT_POST, $this->plugin_name . "-feature-" . $dir, FILTER_SANITIZE_STRING) != "on") {

					//remove calling function
					$content = preg_replace("/\n.*define.*" . $dir . ".*;/", "", $content);

					//remove doc block comments
					$content = preg_replace("/(\/\*{2}[\w\s\*.\-:]+)(?:\n.*_" . $dir . ".*)([\w\s\*.\-@]+\*\/\n\s+.*load_dependencies)/i", "$1$2", $content);

					//remove include file
					$content = preg_replace("/\s+\/\*{2}[\w\s\*.\-]+\*\/\n\s+require_once.*" . $dir . ".*" . $dir . ".*;/", "", $content);

					//remove functions
					$content = preg_replace("/\s+\/\*{2}[\w\s\*.\-@]+\*\/\n\s+.*function.*" . $dir . ".*\(\)[\s\S]+}/U", "", $content);
				}
			}

			/**
			 * Filter plugin features
			 */
			if (filter_input(INPUT_POST, $this->plugin_name . "-feature-composer", FILTER_SANITIZE_STRING) != "on") {

				//remove composer include
				$content = preg_replace("/\s+\/\*{2}[\w\s\*.]+\*\/\n\s+include_once.*vendor.*;/", "", $content);
			}
		}

		if (basename($path) ==  "composer.json") {
			$composer = json_decode($content, true);

			$name = apply_filters($this->plugin_name . "-plugin-slug", "plugin-name");
			$composer["name"] = $name . "/" . $name;

			$content = json_encode($composer, JSON_PRETTY_PRINT);
		}

		return $content;
	}

	/**
	 * After installing plugin actions
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param array $install_actions
	 * @return array
	 */
	public function install_plugin_complete_actions($install_actions)
	{

		if (filter_input(INPUT_GET, "action") == $this->plugin_name) {
			$install_actions[$this->plugin_name] = sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url('plugins.php?page=' . $this->plugin_name),
				__('Go to Plugin Boilerplate page')
			);
		}

		return $install_actions;
	}
}