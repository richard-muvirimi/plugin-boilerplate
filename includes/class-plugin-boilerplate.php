<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://tyganeutronics.com
 * @since      1.0.0
 * @version 1.0.0
 *
 * @package    Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @version 1.0.0
 * @package    Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate/includes
 * @author     Richard Muvirimi <tygalive@gmail.com>
 */
class Plugin_Boilerplate
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @access   protected
	 * @var      Plugin_Boilerplate_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function __construct()
	{

		$this->version = PLUGIN_BOILERPLATE_VERSION;
		$this->plugin_name = PLUGIN_BOILERPLATE_SLUG;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_options_hooks();
		$this->define_rest_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Boilerplate_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Boilerplate_i18n. Defines internationalization functionality.
	 * - Plugin_Boilerplate_Admin. Defines all hooks for the admin area.
	 * - Plugin_Boilerplate_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @access   private
	 * @return void
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plugin-boilerplate-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plugin-boilerplate-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plugin-boilerplate-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-plugin-boilerplate-public.php';

		/**
		 * The class responsible for defining all actions that occur in the options-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'options/class-plugin-boilerplate-options.php';

		/**
		 * The class responsible for defining all actions that occur in the rest-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'rest/class-plugin-boilerplate-rest.php';

		/**
		 * The file responsible for defining all plugin general functionality
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/plugin-boilerplate-functions.php';

		$this->loader = new Plugin_Boilerplate_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Boilerplate_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @access   private
	 * @return void
	 */
	private function set_locale()
	{

		$plugin_i18n = new Plugin_Boilerplate_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @access   private
	 * @return void
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Plugin_Boilerplate_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		if (version_compare(get_bloginfo("version"), "5.5.0", ">=")) {
			$this->loader->add_filter('allowed_options', $plugin_admin, 'on_save_options');
		} else {
			$this->loader->add_filter('whitelist_options', $plugin_admin, 'on_save_options');
		}
		$this->loader->add_action($this->get_plugin_name() . "-generate-plugin", $plugin_admin, 'generate_plugin');

		/**
		 * Plugin content
		 */
		$this->loader->add_filter($this->get_plugin_name() . "-plugin-slug", $plugin_admin, 'filter_plugin_slug');
		$this->loader->add_filter($this->get_plugin_name() . "-plugin-name", $plugin_admin, 'filter_plugin_name');
		$this->loader->add_filter($this->get_plugin_name() . "-plugin-website", $plugin_admin, 'filter_plugin_website');
		$this->loader->add_filter($this->get_plugin_name() . "-plugin-author", $plugin_admin, 'filter_plugin_author');
		$this->loader->add_filter($this->get_plugin_name() . "-plugin-author-website", $plugin_admin, 'filter_plugin_author_website');
		$this->loader->add_filter($this->get_plugin_name() . "-plugin-author-email", $plugin_admin, 'filter_plugin_author_email');

		$this->loader->add_filter($this->get_plugin_name() . "-file-file-valid", $plugin_admin, 'filter_plugin_file_valid', 10, 2);

		$this->loader->add_filter($this->get_plugin_name() . "-file-path", $plugin_admin, 'filter_plugin_file_path');

		$this->loader->add_filter($this->get_plugin_name() . "-file-content", $plugin_admin, 'filter_plugin_file_content_name');
		$this->loader->add_filter($this->get_plugin_name() . "-file-content", $plugin_admin, 'filter_plugin_file_content_slug');
		$this->loader->add_filter($this->get_plugin_name() . "-file-content", $plugin_admin, 'filter_plugin_file_content_website');
		$this->loader->add_filter($this->get_plugin_name() . "-file-content", $plugin_admin, 'filter_plugin_file_content_author');
		$this->loader->add_filter($this->get_plugin_name() . "-file-content", $plugin_admin, 'filter_plugin_file_content_author_website');
		$this->loader->add_filter($this->get_plugin_name() . "-file-content", $plugin_admin, 'filter_plugin_file_content_author_email');

		//filter included features
		$this->loader->add_filter($this->get_plugin_name() . "-file-content", $plugin_admin, 'filter_plugin_file_content_features', 10, 2);

		$this->loader->add_filter("wp_redirect", $plugin_admin, 'on_options_redirect');
		$this->loader->add_filter("update-custom_" . $this->plugin_name, $plugin_admin, 'install_plugin');
		$this->loader->add_filter("install_plugin_complete_actions", $plugin_admin, 'install_plugin_complete_actions');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @access   private
	 * @return void
	 */
	private function define_options_hooks()
	{

		$plugin_options = new Plugin_Boilerplate_Options($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_options, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_options, 'enqueue_scripts');

		$this->loader->add_action('admin_menu', $plugin_options, 'admin_menu');

		$this->loader->add_action('admin_init', $plugin_options, 'admin_init');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @access   private
	 * @return void
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Plugin_Boilerplate_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}

	/**
	 * Register all of the hooks related to the rest-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   private
	 * @return   void
	 */
	private function define_rest_hooks()
	{

		$plugin_rest = new Plugin_BoilerPlate_Rest($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('rest_api_init', $plugin_rest, 'register_rest_routes');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @version 1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @version 1.0.0
	 * @return    Plugin_Boilerplate_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @version 1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}