<?php

/**
 * The rest-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 * @version    1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/rest
 */

/**
 * The rest-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and an example hook for how to
 * register a rest route.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/rest
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Rest
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
	 * Register rest routes
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 * @return   void
	 */
	public function register_routes()
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

		register_rest_route($this->plugin_name . '/v1', '/route', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => "wp_send_json_success",
			"permission_callback" => "__return_true",
			'args' => array(),
		));
	}
}