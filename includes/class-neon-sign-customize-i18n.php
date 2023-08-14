<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://sample.com
 * @since      1.0.0
 *
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/includes
 * @author     Okido Nguyễn <okidonguyen88@gmail.com>
 */
class Neon_Sign_Customize_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'neon-sign-customize',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
