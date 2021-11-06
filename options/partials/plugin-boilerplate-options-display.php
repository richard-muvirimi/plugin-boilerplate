<?php

/**
 * Provide a option area view for the plugin
 *
 * This file is used to markup the option-facing aspects of the plugin.
 *
 * @link       htts://tyganeutronics.com
 * @since      1.0.0
 * @version 1.0.0
 *
 * @package    Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate/option/partials
 */

// show error/update messages
settings_errors($this->plugin_name);
?>

<div class="wrap">
    <h1><?php esc_html_e(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
        <?php
        // output security fields for the registered setting "wporg"
        settings_fields($this->plugin_name . "-options");
        settings_fields($this->plugin_name . "-features");
        settings_fields($this->plugin_name . "-vcs");
        // output setting sections and their fields
        // (sections are registered for "wporg", each field is registered to a specific section)
        do_settings_sections($this->plugin_name . "-options");
        // output save settings button

        $attr = array("style" => "margin: 0 7px");

        submit_button(__('Save', $this->plugin_name), "primary", $this->plugin_name . "-action[save]", false, $attr);
        if (current_user_can('upload_plugins')) :
            submit_button(__('Copy to Plugin Dir', $this->plugin_name), "primary", $this->plugin_name . "-action[copy]", false, $attr);
        endif;
        if (class_exists("ZipArchive", false)) :
            submit_button(__('Download Plugin Zip', $this->plugin_name), "primary", $this->plugin_name . "-action[download]", false, $attr);
        endif;
        if (current_user_can('upload_plugins')) :
            submit_button(__('Install Plugin', $this->plugin_name), "primary", $this->plugin_name . "-action[install]", false, $attr);
        endif;
        ?>
    </form>
</div>