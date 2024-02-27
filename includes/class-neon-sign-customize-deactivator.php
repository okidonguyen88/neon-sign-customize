<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://sample.com
 * @since      1.0.0
 *
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/includes
 * @author     Okido Nguyễn <okidonguyen88@gmail.com>
 */
class Neon_Sign_Customize_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		global $wpdb;

		$nsc_fonts = $wpdb->prefix . 'nsc_fonts';
		$nsc_colors = $wpdb->prefix . 'nsc_colors';
		$nsc_sizes = $wpdb->prefix . 'nsc_sizes';
		$nsc_prices = $wpdb->prefix . 'nsc_prices';
		$nsc_backboard = $wpdb->prefix . 'nsc_backboard';
		$nsc_backboard_color = $wpdb->prefix . 'nsc_backboard_color';
		$nsc_additional = $wpdb->prefix . 'nsc_additional';
		$nsc_backboard_color_link = $wpdb->prefix . 'nsc_backboard_color_link';
		$nsc_font_price = $wpdb->prefix . 'nsc_font_price';
		$nsc_settings = $wpdb->prefix . 'nsc_settings';

		//		drop the table from the database.		
		// $wpdb->query("DROP TABLE IF EXISTS $nsc_fonts");
		// $wpdb->query("DROP TABLE IF EXISTS $nsc_colors");
		// $wpdb->query("DROP TABLE IF EXISTS $nsc_sizes");
		// $wpdb->query("DROP TABLE IF EXISTS $nsc_prices");
		// $wpdb->query("DROP TABLE IF EXISTS $nsc_backboard");
		// $wpdb->query("DROP TABLE IF EXISTS $nsc_backboard_color");
		// $wpdb->query("DROP TABLE IF EXISTS $nsc_additional");
		// $wpdb->query("DROP TABLE IF EXISTS $nsc_backboard_color_link");
		// $wpdb->query("DROP TABLE IF EXISTS $nsc_font_price");
		// $wpdb->query("DROP TABLE IF EXISTS $nsc_settings");

	}

}