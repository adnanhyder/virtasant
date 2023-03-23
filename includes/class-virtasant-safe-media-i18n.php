<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://profiles.wordpress.org/adnanhyder/
 * @since      1.0.0
 *
 * @package    Virtasant_Safe_Media
 * @subpackage Virtasant_Safe_Media/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Virtasant_Safe_Media
 * @subpackage Virtasant_Safe_Media/includes
 * @author     Adnan <12345adnan@gmail.com>
 */
class Virtasant_Safe_Media_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'virtasant-safe-media',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
