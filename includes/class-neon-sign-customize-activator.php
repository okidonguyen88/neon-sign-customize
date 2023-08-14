<?php

/**
 * Fired during plugin activation
 *
 * @link       https://sample.com
 * @since      1.0.0
 *
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/includes
 * @author     Okido Nguyễn <okidonguyen88@gmail.com>
 */
class Neon_Sign_Customize_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;

		// Check if table not exist, create new
		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_fonts() . "'") != Neon_Sign_Customize_Activator::wp_nsc_fonts()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_fonts() . "` (
				`font_id` int(11) NOT NULL AUTO_INCREMENT,
				`font_name` varchar(100) NOT NULL,
				`font_short` varchar(100) NOT NULL,
				`font_url` text NOT NULL,
				`extra_price` int(10) NOT NULL DEFAULT 0,
				`status` tinyint(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (`font_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);
		}



	}

	public static function wp_nsc_fonts()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_fonts";
	}
}