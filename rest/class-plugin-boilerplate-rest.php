<?php

if (!defined('WPINC')) {
    die(); // Exit if accessed directly.
}

/**
 * The admin-specific functionality of the plugin.
 *
 *
 * @package Plugin_BoilerPlate
 * @subpackage Plugin_BoilerPlate/admin
 *
 * @link https://tyganeutronics.com/woo-custom-gateway/
 * @since 1.0.0
 */

/**
 * The admin-specific functionality of the plugin.
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 *
 * @package Plugin_BoilerPlate
 * @subpackage Plugin_BoilerPlate/admin
 *
 * @author https://tyganeutronics.com <tygalive@gmail.com>
 */
class Plugin_BoilerPlate_Rest
{

    /**
     * The ID of this plugin.
     *
     * @access private
     * @var string $plugin_name The ID of this plugin.
     * @since 1.0.0
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @access private
     * @var string $version The current version of this plugin.
     * @since 1.0.0
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     *            The name of this plugin.
     *            The version of this plugin.
     *
     * @since 1.0.0
     *
     * @param string $plugin_name
     * @param string $version
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Sanitize text field
     * 
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return void
     */
    public function register_rest_routes()
    {

        register_rest_route($this->plugin_name . '/v1', '/sanitize_title', array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => function (WP_REST_Request $request) {
                return wp_send_json_success(array("title" => $request->get_param("title")));
            },
            "permission_callback" => "__return_true",
            'args' => array(
                "title" => array(
                    'sanitize_callback' => function ($param) {
                        return sanitize_title($param);
                    }
                )
            ),
        ));
    }
}