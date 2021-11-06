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

// Get the value of the setting we've registered with register_setting()

?>

<input id="<?php esc_attr_e($args['label_for']) ?>" name="<?php esc_attr_e($args['label_for']) ?>" data-custom="<?php esc_attr_e($args['value']); ?>" type="<?php esc_attr_e($args['type']); ?>" placeholder="<?php esc_attr_e($args['placeholder'] ?? ""); ?>" value="<?php esc_attr_e($args['value']); ?>">

<div>
    <small class="description">
        <?php esc_html_e($args['description']); ?>
    </small>
</div>