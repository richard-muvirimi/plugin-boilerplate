<?php

/**
 * The options-specific functionality of the plugin.
 *
 * @link       https://tyganeutronics.com
 * @since      1.0.0
 *
 * @package    Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate/options
 */

/**
 * The options-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the options-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate/options
 * @author     Richard Muvirimi <tygalive@gmail.com>
 */
class Plugin_Boilerplate_Options
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
	 * Register the stylesheets for the options area.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function enqueue_styles()
	{

		wp_register_style($this->plugin_name . "-options", plugin_dir_url(__FILE__) . 'css/plugin-boilerplate-options.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the options area.
	 *
	 * @since    1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function enqueue_scripts()
	{

		wp_register_script($this->plugin_name . "-options", plugin_dir_url(__FILE__) . 'js/plugin-boilerplate-options.js', array('jquery'), $this->version, false);
	}

	/**
	 * Add generate plugin page to menu
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function admin_menu()
	{
		add_plugins_page(__("Plugin Boilerplate", $this->plugin_name), __("Plugin Boilerplate", $this->plugin_name), "manage_options", $this->plugin_name, array($this, "render_page"));
	}

	/**
	 * Register and initialize settings
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function admin_init()
	{
		$this->registerSettings();
		$this->add_settings_sections();
		$this->add_settings_fields();
	}

	/**
	 * Register plugin settings
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function registerSettings()
	{

		register_setting(
			$this->plugin_name . "-options",
			$this->plugin_name . "-name",
			array("sanitize_callback" => "sanitize_text_field")
		);

		register_setting(
			$this->plugin_name . "-options",
			$this->plugin_name . "-slug",
			array("sanitize_callback" => "sanitize_title")
		);

		register_setting(
			$this->plugin_name . "-options",
			$this->plugin_name . "-website",
			array("sanitize_callback" => "esc_url_raw")
		);

		register_setting(
			$this->plugin_name . "-options",
			$this->plugin_name . "-author-name",
			array("sanitize_callback" => "sanitize_text_field")
		);

		register_setting(
			$this->plugin_name . "-options",
			$this->plugin_name . "-author-website",
			array("sanitize_callback" => "esc_url_raw")
		);

		register_setting(
			$this->plugin_name . "-options",
			$this->plugin_name . "-author-email",
			array("sanitize_callback" => "sanitize_email")
		);

		register_setting(
			$this->plugin_name . "-features",
			$this->plugin_name . "-feature-admin",
			array("sanitize_callback" => "trim")
		);

		register_setting(
			$this->plugin_name . "-features",
			$this->plugin_name . "-feature-public",
			array("sanitize_callback" => "trim")
		);

		register_setting(
			$this->plugin_name . "-features",
			$this->plugin_name . "-feature-options",
			array("sanitize_callback" => "trim")
		);

		register_setting(
			$this->plugin_name . "-features",
			$this->plugin_name . "-feature-rest",
			array("sanitize_callback" => "trim")
		);

		register_setting(
			$this->plugin_name . "-features",
			$this->plugin_name . "-feature-ajax",
			array("sanitize_callback" => "trim")
		);

		register_setting(
			$this->plugin_name . "-features",
			$this->plugin_name . "-feature-composer",
			array("sanitize_callback" => "trim")
		);

		register_setting(
			$this->plugin_name . "-vcs",
			$this->plugin_name . "-vcs-git",
			array("sanitize_callback" => "trim")
		);
	}

	/**
	 * Register settings sections
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function add_settings_sections()
	{
		add_settings_section(
			$this->plugin_name,
			__("Plugin Details", $this->plugin_name),
			array($this, "render_options_header"),
			$this->plugin_name . "-options"
		);

		add_settings_section(
			$this->plugin_name . "-features",
			__("Plugin Features", $this->plugin_name),
			array($this, "render_features_header"),
			$this->plugin_name . "-options"
		);

		add_settings_section(
			$this->plugin_name . "-vcs",
			__("Plugin Version Control", $this->plugin_name),
			array($this, "render_vcs_header"),
			$this->plugin_name . "-options"
		);
	}

	/**
	 * Register settings fields
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function add_settings_fields()
	{

		add_settings_field(
			$this->plugin_name . '-name',
			__('Name', $this->plugin_name),
			array($this, 'render_input_field'),
			$this->plugin_name . "-options",
			$this->plugin_name,
			array(
				'label_for' => $this->plugin_name . '-name',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-name"),
				'description' => __("The name of your new plugin.", $this->plugin_name),
				"type" => "text",
				"placeholder" => "Plugin Name"
			)
		);

		add_settings_field(
			$this->plugin_name . '-slug',
			__('Slug', $this->plugin_name),
			array($this, 'render_input_field'),
			$this->plugin_name . "-options",
			$this->plugin_name,
			array(
				'label_for' => $this->plugin_name . '-slug',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-slug"),
				'description' => __("The unique name of your plugin.", $this->plugin_name),
				"type" => "text",
				"placeholder" => "plugin-name"
			)
		);

		add_settings_field(
			$this->plugin_name . '-website',
			__('Website', $this->plugin_name),
			array($this, 'render_input_field'),
			$this->plugin_name . "-options",
			$this->plugin_name,
			array(
				'label_for' => $this->plugin_name . '-website',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-website", site_url()),
				'description' => __("The webiste to get more information about your plugin.", $this->plugin_name),
				"type" => "url",
				"placeholder" => site_url()
			)
		);

		add_settings_field(
			$this->plugin_name . '-author-name',
			__('Author', $this->plugin_name),
			array($this, 'render_input_field'),
			$this->plugin_name . "-options",
			$this->plugin_name,
			array(
				'label_for' => $this->plugin_name . '-author-name',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-author-name", wp_get_current_user()->display_name),
				'description' => __("The name of the author of the plugin.", $this->plugin_name),
				"type" => "text",
				"placeholder" => wp_get_current_user()->display_name ?? "Your Name or Your Company"
			)
		);

		add_settings_field(
			$this->plugin_name . '-author-website',
			__('Author Website', $this->plugin_name),
			array($this, 'render_input_field'),
			$this->plugin_name . "-options",
			$this->plugin_name,
			array(
				'label_for' => $this->plugin_name . '-author-website',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-author-website", site_url()),
				'description' => __("The website of the author of the plugin.", $this->plugin_name),
				"type" => "url",
				"placeholder" =>  site_url()
			)
		);

		add_settings_field(
			$this->plugin_name . '-author-email',
			__('Author Email', $this->plugin_name),
			array($this, 'render_input_field'),
			$this->plugin_name . "-options",
			$this->plugin_name,
			array(
				'label_for' => $this->plugin_name . '-author-email',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-author-email", get_bloginfo("admin_email")),
				'description' => __("The email of the author of the plugin.", $this->plugin_name),
				"type" => "email",
				"placeholder" => get_bloginfo("admin_email") ?: "email@example.com"
			)
		);

		add_settings_field(
			$this->plugin_name . '-feature-admin',
			__('Admin', $this->plugin_name),
			array($this, 'render_checkbox_field'),
			$this->plugin_name . "-options",
			$this->plugin_name . "-features",
			array(
				'label_for' => $this->plugin_name . '-feature-admin',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-feature-admin", "yes"),
				'description' => __("Whether this plugin will include admin functionality.", $this->plugin_name),
			)
		);

		add_settings_field(
			$this->plugin_name . '-feature-public',
			__('Public', $this->plugin_name),
			array($this, 'render_checkbox_field'),
			$this->plugin_name . "-options",
			$this->plugin_name . "-features",
			array(
				'label_for' => $this->plugin_name . '-feature-public',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-feature-public", "yes"),
				'description' => __("Whether this plugin will include public functionality.", $this->plugin_name),
			)
		);

		add_settings_field(
			$this->plugin_name . '-feature-options',
			__('Options', $this->plugin_name),
			array($this, 'render_checkbox_field'),
			$this->plugin_name . "-options",
			$this->plugin_name . "-features",
			array(
				'label_for' => $this->plugin_name . '-feature-options',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-feature-options"),
				'description' => __("Whether this plugin will include options functionality.", $this->plugin_name),
			)
		);

		add_settings_field(
			$this->plugin_name . '-feature-rest',
			__('Rest', $this->plugin_name),
			array($this, 'render_checkbox_field'),
			$this->plugin_name . "-options",
			$this->plugin_name . "-features",
			array(
				'label_for' => $this->plugin_name . '-feature-rest',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-feature-rest"),
				'description' => __("Whether this plugin will include rest functionality.", $this->plugin_name),
			)
		);

		add_settings_field(
			$this->plugin_name . '-feature-ajax',
			__('Ajax', $this->plugin_name),
			array($this, 'render_checkbox_field'),
			$this->plugin_name . "-options",
			$this->plugin_name . "-features",
			array(
				'label_for' => $this->plugin_name . '-feature-ajax',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-feature-ajax"),
				'description' => __("Whether this plugin will include ajax functionality.", $this->plugin_name),
			)
		);

		add_settings_field(
			$this->plugin_name . '-feature-composer',
			__('Composer', $this->plugin_name),
			array($this, 'render_checkbox_field'),
			$this->plugin_name . "-options",
			$this->plugin_name . "-features",
			array(
				'label_for' => $this->plugin_name . '-feature-composer',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-feature-composer"),
				'description' => __("Whether this plugin will include composer packages.", $this->plugin_name),
			)
		);

		add_settings_field(
			$this->plugin_name . '-vcs-git',
			__('Git Support', $this->plugin_name),
			array($this, 'render_checkbox_field'),
			$this->plugin_name . "-options",
			$this->plugin_name . "-vcs",
			array(
				'label_for' => $this->plugin_name . '-vcs-git',
				'class' => $this->plugin_name . '-row',
				"value" => get_option($this->plugin_name . "-vcs-git"),
				'description' => __("Whether this plugin will be version controlled by git.", $this->plugin_name),
			)
		);
	}

	/**
	 * Display the plugin details header
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function render_options_header()
	{
		include plugin_dir_path(__FILE__) . "partials/plugin-boilerplate-options-header-options.php";
	}

	/**
	 * Display the plugin features header
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function render_features_header()
	{
		include plugin_dir_path(__FILE__) . "partials/plugin-boilerplate-options-header-features.php";
	}

	/**
	 * Display the plugin vcs header
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function render_vcs_header()
	{
		include plugin_dir_path(__FILE__) . "partials/plugin-boilerplate-options-header-vcs.php";
	}

	/**
	 * Display text input field
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function render_input_field($args)
	{
		include plugin_dir_path(__FILE__) . "partials/plugin-boilerplate-options-field.php";
	}

	/**
	 * Display checkbox input field
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function render_checkbox_field($args)
	{
		include plugin_dir_path(__FILE__) . "partials/plugin-boilerplate-options-field-checkbox.php";
	}

	/**
	 * Display main options page
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	public function render_page()
	{
		wp_enqueue_style($this->plugin_name . "-options");
		wp_enqueue_script($this->plugin_name . "-options");

		include plugin_dir_path(__FILE__) . "partials/plugin-boilerplate-options-display.php";
	}
}