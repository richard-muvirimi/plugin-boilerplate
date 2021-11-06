<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 * @version    1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
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
 * @version    1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class Plugin_Name
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   protected
	 * @var      Plugin_Name_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
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
	 * @version  1.0.0
	 * @return   void
	 */
	public function __construct()
	{
		$this->version = PLUGIN_NAME_VERSION;
		$this->plugin_name = PLUGIN_NAME_SLUG;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_ajax_hooks();
		$this->define_options_hooks();
		$this->define_public_hooks();
		$this->define_rest_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n. Defines internationalization functionality.
	 * - Plugin_Name_Admin. Defines all hooks for the admin area.
	 * - Plugin_Name_Ajax. Defines all hooks for the ajax functionality.
	 * - Plugin_Name_Options. Defines all hooks for the options functionality.
	 * - Plugin_Name_Public. Defines all hooks for the public side of the site.
	 * - Plugin_Name_Rest. Defines all hooks for the rest functionality.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   private
	 * @return   void
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plugin-name-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plugin-name-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plugin-name-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the ajax-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'ajax/class-plugin-name-ajax.php';

		/**
		 * The class responsible for defining all actions that occur in the options-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'options/class-plugin-name-options.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-plugin-name-public.php';

		/**
		 * The class responsible for defining all actions that occur in the rest-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'rest/class-plugin-name-rest.php';

		/**
		 * Load composer packages
		 */
		include_once plugin_dir_path(dirname(__FILE__)) . 'vendor/autoload.php';

		$this->loader = new Plugin_Name_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   private
	 * @return   void
	 */
	private function set_locale()
	{

		$plugin_i18n = new Plugin_Name_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   private
	 * @return   void
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Plugin_Name_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
	}

	/**
	 * Register all of the hooks related to the ajax-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   private
	 * @return   void
	 */
	private function define_ajax_hooks()
	{

		$plugin_ajax = new Plugin_Name_Ajax($this->get_plugin_name(), $this->get_version());

		/**
		 * This code is for demonstration only
		 * 
		 * Your ajax action parameter would have to equal plugin-name-ajax (in this instance)
		 */
		$this->loader->add_action('wp_ajax_' . $this->get_plugin_name() . '-ajax', $plugin_ajax, 'handle_ajax');
	}

	/**
	 * Register all of the hooks related to the options-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   private
	 * @return   void
	 */
	private function define_options_hooks()
	{

		$plugin_options = new Plugin_Name_Options($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_options, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_options, 'enqueue_scripts');

		$this->loader->add_action('admin_init', $plugin_options, 'admin_init');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   private
	 * @return   void
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Plugin_Name_Public($this->get_plugin_name(), $this->get_version());

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

		$plugin_rest = new Plugin_Name_Rest($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('rest_api_init', $plugin_rest, 'register_routes');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @return   void
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
	 * @version   1.0.0
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
	 * @version   1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @version   1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}