<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.elsner.com/
 * @since             1.0.0
 * @package           Manage_Custom_Post_Types
 *
 * @wordpress-plugin
 * Plugin Name:       Manage Custom Post Types
 * Plugin URI:        http://www.elsner.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.1
 * Author:            Elsner Technologies Pvt. Ltd.
 * Author URI:        http://www.elsner.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       manage-custom-post-types
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-manage-custom-post-types-activator.php
 */
function activate_manage_custom_post_types() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-manage-custom-post-types-activator.php';
	Manage_Custom_Post_Types_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-manage-custom-post-types-deactivator.php
 */
function deactivate_manage_custom_post_types() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-manage-custom-post-types-deactivator.php';
	Manage_Custom_Post_Types_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_manage_custom_post_types' );
register_deactivation_hook( __FILE__, 'deactivate_manage_custom_post_types' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-manage-custom-post-types.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_manage_custom_post_types() {

	$plugin = new Manage_Custom_Post_Types();
	$plugin->run();

}
run_manage_custom_post_types();


function mcpt_manage_post_types(){
	include(  plugin_dir_path( __FILE__ )  . 'admin/partials/manage-post-types.php' );
}

function mcpt_add_new_post_types(){
	include(  plugin_dir_path( __FILE__ )  . 'admin/partials/add-post-types.php' );
}