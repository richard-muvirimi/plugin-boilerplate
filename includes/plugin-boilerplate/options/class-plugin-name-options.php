<?php

/**
 * The options-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 * @version    1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/options
 */

/**
 * The options-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the options-facing stylesheet and JavaScript as well as how to register options.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/options
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Options
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    The version of this plugin.
	 * @return   void
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the options-facing side of the site.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @return   void
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/plugin-name-options.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the options-facing side of the site.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @return   void
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/plugin-name-options.js', array('jquery'), $this->version, false);
	}

	/**
	 * Initialize the registration of options.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @return   void
	 */
	public function admin_init()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$this->registerSettings();
		$this->add_settings_sections();
		$this->add_settings_fields();
	}

	/**
	 * Register the options.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @return   void
	 */
	public function registerSettings()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		/* register_setting(); */
	}

	/**
	 * Register the option headers.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @return   void
	 */
	public function add_settings_sections()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		/* add_settings_section(); */
	}

	/**
	 * Register the settings fields.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @return   void
	 */
	public function add_settings_fields()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		/* add_settings_field(); */
	}
}