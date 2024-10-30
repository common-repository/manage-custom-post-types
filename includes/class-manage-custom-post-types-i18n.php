<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.elsner.com/
 * @since      1.0.0
 *
 * @package    Manage_Custom_Post_Types
 * @subpackage Manage_Custom_Post_Types/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Manage_Custom_Post_Types
 * @subpackage Manage_Custom_Post_Types/includes
 * @author     Aakif Kadiwala <aakifkadiwala1995@gmail.com>
 */
class Manage_Custom_Post_Types_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'manage-custom-post-types',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
