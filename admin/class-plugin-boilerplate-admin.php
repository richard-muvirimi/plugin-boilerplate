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
					wp_cache_set("download", $location, $this->plugin_name);
					break;
				case "copy":
					$this->install_plugin($location);
					break;
			}
		}
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
		return strtolower(filter_input(INPUT_POST, $this->plugin_name . "-slug", FILTER_SANITIZE_STRING)) ?: $slug;
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
		return filter_input(INPUT_POST, $this->plugin_name . "-name", FILTER_SANITIZE_STRING) ?: $name;
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
		return filter_input(INPUT_POST, $this->plugin_name . "-website", FILTER_SANITIZE_STRING) ?: $website;
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
		return filter_input(INPUT_POST, $this->plugin_name . "-author-name", FILTER_SANITIZE_STRING) ?: $author;
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
		return filter_input(INPUT_POST, $this->plugin_name . "-author-website", FILTER_SANITIZE_STRING) ?: $website;
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
		return filter_input(INPUT_POST, $this->plugin_name . "-author-email", FILTER_SANITIZE_STRING) ?: $email;
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

					$path = wp_cache_get("download", $this->plugin_name);

					if ($path) {

						wp_cache_delete("download", $this->plugin_name);

						global $wp_filesystem;

						if ($wp_filesystem->exists($path)) {

							/**
							 * Resave options incase we have added our own as this functions is called late
							 */
							set_transient('settings_errors', get_settings_errors(), 30);

							header('Content-Description: File Transfer');
							header('Content-Type: application/octet-stream');
							header('Content-Disposition: attachment; filename="' . basename($path) . '"');
							header('Expires: 0');
							header('Cache-Control: must-revalidate');
							header('Pragma: public');
							header('Content-Length: ' . $wp_filesystem->size($path));
							header('Set-Cookie:fileLoading=true');
							readfile($path);

							$wp_filesystem->delete($path);
							exit;
						}
					}
				}
			}
		}

		return $location;
	}

	/**
	 * Move plugin from temp dir to plugins dir
	 * 
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 * @param string $location
	 * @return string
	 */
	private function install_plugin($location)
	{
		global $wp_filesystem;

		$file = $wp_filesystem->wp_plugins_dir() .  apply_filters($this->plugin_name . "-plugin-slug", "plugin-name");

		$done =	plugin_boilerplate_walk_dir(function ($source, $target,  $content) use ($wp_filesystem, $file) {

			$done = true;

			if (!$wp_filesystem->exists($file)) {
				$wp_filesystem->mkdir($file);
			}

			if ($wp_filesystem->is_file($source)) {
				$done |= $wp_filesystem->touch($file . $target);
				$done |= $wp_filesystem->put_contents($file . $target, $content);
			} elseif ($wp_filesystem->is_dir($source)) {
				$done |= $wp_filesystem->mkdir($file . $target);
			}

			return $done;
		}, $location);

		if ($done) {
			add_settings_error($this->plugin_name, $this->plugin_name, __('Plugin copied to the plugin dir.', $this->plugin_name), 'updated');
		} else {
			add_settings_error($this->plugin_name, $this->plugin_name, __('Failed to copy plugin to the plugins dir.', $this->plugin_name));
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

		if (class_exists("ZipArchive", false)) {

			$zip_file = plugin_boilerplate_temp_dir() .  apply_filters($this->plugin_name . "-plugin-slug", "plugin-name") . '.zip';

			$zip = new ZipArchive();
			$zipArchive = $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
			if ($zipArchive !== TRUE) {
				add_settings_error($this->plugin_name, $this->plugin_name, __('Zip extension not enabled, you may only install the plugin.', $this->plugin_name));
			} else {

				global $wp_filesystem;

				$done =	plugin_boilerplate_walk_dir(function ($source, $target,  $content) use (&$zip, $wp_filesystem) {

					if ($wp_filesystem->is_file($source)) {
						$zip->addFromString($target, $content);
					}
					return true;
				}, $location);

				if ($done) {
					$zip->close();
					return $zip_file;
				}
			}
		} else {
			add_settings_error($this->plugin_name, $this->plugin_name, __('Zip extension not enabled, you may only install the plugin.', $this->plugin_name));
		}
		return false;
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
		return preg_replace_callback("/(?<=[^\$>])plugin(-|_)name(?!\(\))/i", function ($match) use ($name) {

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
}