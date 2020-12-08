<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.cmsminds.com/
 * @since             1.0.0
 * @package           Wp_Custom_Contact_Form
 *
 * @wordpress-plugin
 * Plugin Name:       WP Custom Contact Form
 * Plugin URI:        https://github.com/chandani293
 * Description:       Wordpress Custom Contact Form Form DataList, Bulk Delete, Edit, Search Using Ajax Option
 * Version:           1.0.0
 * Author:            Chandani Vadaria
 * Author URI:        https://www.cmsminds.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-custom-contact-form
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_CUSTOM_CONTACT_FORM_VERSION', '1.0.0' );

define( 'WP_CUSTOM_CONTACT_FORM_TABLE', $wpdb->prefix . 'custom_contact_form' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-custom-contact-form-activator.php
 */
function activate_wp_custom_contact_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-custom-contact-form-activator.php';
	Wp_Custom_Contact_Form_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-custom-contact-form-deactivator.php
 */
function deactivate_wp_custom_contact_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-custom-contact-form-deactivator.php';
	Wp_Custom_Contact_Form_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_custom_contact_form' );
register_deactivation_hook( __FILE__, 'deactivate_wp_custom_contact_form' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-custom-contact-form.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_custom_contact_form() {

	$plugin = new Wp_Custom_Contact_Form();
	$plugin->run();

}
run_wp_custom_contact_form();