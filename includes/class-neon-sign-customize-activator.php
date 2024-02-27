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
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;

		// FONTS
		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_fonts() . "'") != Neon_Sign_Customize_Activator::wp_nsc_fonts()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_fonts() . "` (
				`font_id` int(11) NOT NULL AUTO_INCREMENT,
				`font_name` varchar(100) NOT NULL UNIQUE,				
				`font_url` text NOT NULL,
				`extra_price` float NOT NULL DEFAULT 0,
				`extra_price_type` int(2) NOT NULL DEFAULT 0,
				`status` tinyint(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (`font_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);
		}

		// COLORS
		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_colors() . "'") != Neon_Sign_Customize_Activator::wp_nsc_colors()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_colors() . "` (
				`color_id` int(11) NOT NULL AUTO_INCREMENT,
				`color_name` varchar(100) NOT NULL UNIQUE,
				`color_hex` varchar(100) NOT NULL,
				`is_rbg` tinyint(2) NOT NULL DEFAULT 0,
				`extra_price` float NOT NULL DEFAULT 0,
				`extra_price_type` int(2) NOT NULL DEFAULT 0,
				`status` tinyint(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (`color_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_sizes() . "'") != Neon_Sign_Customize_Activator::wp_nsc_sizes()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_sizes() . "` (
				`size_id` int(11) NOT NULL AUTO_INCREMENT,
				`size_name` varchar(100) NOT NULL UNIQUE,
				`size_des` varchar(200) NOT NULL,
				`size_vol` float NOT NULL DEFAULT 5000,
				`size_fee` float NOT NULL DEFAULT 20,
				`size_length` float NOT NULL DEFAULT 10,
				`text_width` int(10) NOT NULL DEFAULT 100,
				`text_height` int(10) NOT NULL DEFAULT 50,
				`text_line` int(10) NOT NULL,
				`size_char_min` int(10) NOT NULL,
				`size_char_max` int(10) NOT NULL,
				`status` tinyint(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (`size_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_prices() . "'") != Neon_Sign_Customize_Activator::wp_nsc_prices()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_prices() . "` (
				`price_id` int(11) NOT NULL AUTO_INCREMENT,
				`price_name` varchar(100) NOT NULL,
				`size_id` int(11) NOT NULL,
				`line_one_base` int(10) NOT NULL DEFAULT 0,
				`line_one_letter` int(10) NOT NULL DEFAULT 0,
				`line_two_base` int(10) NOT NULL DEFAULT 0,
				`line_two_letter` int(10) NOT NULL DEFAULT 0,
				`line_three_base` int(10) NOT NULL DEFAULT 0,
				`line_three_letter` int(10) NOT NULL DEFAULT 0,
				`status` tinyint(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (`price_id`),
				 UNIQUE KEY `unique_price_size` (`price_name`, `size_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);
		}

		// ADDITIONAL
		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_additional() . "'") != Neon_Sign_Customize_Activator::wp_nsc_additional()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_additional() . "` (
				`additional_id` int(11) NOT NULL AUTO_INCREMENT,
				`additional_name` varchar(100) NOT NULL,
				`additional_des` varchar(100) NOT NULL,
				`additional_img` text NOT NULL,
				`extra_price` float NOT NULL,
				`extra_price_type` tinyint(1) NOT NULL DEFAULT 0,
				`additional_type` varchar(100) NOT NULL,
				`status` tinyint(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (`additional_id`),
				UNIQUE KEY `my_uniq_id` (`additional_name`,`additional_type`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_backboard_color_link() . "'") != Neon_Sign_Customize_Activator::wp_nsc_backboard_color_link()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_backboard_color_link() . "` (
				`backboard_id` int(11) NOT NULL,
				`backboard_color_id` int(11) NOT NULL,
				PRIMARY KEY (`backboard_id`, `backboard_color_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_font_price() . "'") != Neon_Sign_Customize_Activator::wp_nsc_font_price()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_font_price() . "` (
				`font_id` int(11) NOT NULL,
				`price_id` int(11) NOT NULL,
				PRIMARY KEY (`font_id`, `price_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);
		}

		// BACKBOARD
		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_backboard() . "'") != Neon_Sign_Customize_Activator::wp_nsc_backboard()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_backboard() . "` (
				`backboard_id` int(11) NOT NULL AUTO_INCREMENT,
				`backboard_name` varchar(100) NOT NULL UNIQUE,
				`backboard_des` varchar(200) NOT NULL,
				`backboard_type` varchar(50) NOT NULL,
				`backboard_img` text,
				`extra_price` float NOT NULL DEFAULT 0,
				`extra_price_type` int(2) NOT NULL DEFAULT 0,
				`status` tinyint(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (`backboard_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);

			$records = array(
				array(
					'backboard_name' => 'Cut to shape',
					'backboard_des' => 'Backboard is cut closely around the outside of your wording',
					'backboard_type' => 'cut_to_shape',
					'status' => 1,
				),
				array(
					'backboard_name' => 'Board',
					'backboard_des' => 'Backboard cut in a rectangle around the outside of your wording',
					'backboard_type' => 'board',
					'status' => 1,
				),
				array(
					'backboard_name' => 'Box',
					'backboard_des' => 'Your sign will be inside a 6 sided perspex box',
					'backboard_type' => 'box',
					'status' => 1,
				),
				array(
					'backboard_name' => 'Raceway',
					'backboard_des' => 'Your sign will be held by a stand with 2 straight lines ',
					'backboard_type' => 'raceway',
					'status' => 1,
				),
				array(
					'backboard_name' => 'Stand',
					'backboard_des' => 'Self supporting stand, great for office desks or table tops etc ',
					'backboard_type' => 'stand',
					'status' => 1,
				),

			);

			foreach ($records as $record) {
				$wpdb->insert(Neon_Sign_Customize_Activator::wp_nsc_backboard(), $record);
			}
		}

		// BACKBOARD COLORS
		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_backboard_color() . "'") != Neon_Sign_Customize_Activator::wp_nsc_backboard_color()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_backboard_color() . "` (
				`backboard_color_id` int(11) NOT NULL AUTO_INCREMENT,
				`backboard_color_name` varchar(100) NOT NULL UNIQUE,
				`backboard_color_hex` varchar(100) NOT NULL,
				`backboard_color_img` text NOT NULL,
				`extra_price` float NOT NULL DEFAULT 0,
				`extra_price_type` int(2) NOT NULL DEFAULT 0,
				`status` tinyint(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (`backboard_color_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);
		}

		// SETTINGS
		if ($wpdb->get_var("SHOW TABLES LIKE '" . Neon_Sign_Customize_Activator::wp_nsc_settings() . "'") != Neon_Sign_Customize_Activator::wp_nsc_settings()) {
			// dynamic create table
			$table_query = "CREATE TABLE `" . Neon_Sign_Customize_Activator::wp_nsc_settings() . "` (
				`setting_id` int(11) NOT NULL AUTO_INCREMENT,
				`setting_name` varchar(100) NOT NULL UNIQUE,
				`status` tinyint(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (`setting_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($table_query);

			$records = array(
				// array(
				// 	'setting_name' => 'font',
				// 	'status' => 1,
				// ),
				// array(
				// 	'setting_name' => 'price',
				// 	'status' => 1,
				// ),
				// array(
				// 	'setting_name' => 'color',
				// 	'status' => 1,
				// ),
				// array(
				// 	'setting_name' => 'size',
				// 	'status' => 1,
				// ),
				array(
					'setting_name' => 'backboard',
					'status' => 1,
				),
				array(
					'setting_name' => 'backboard_color',
					'status' => 1,
				),
				array(
					'setting_name' => 'material',
					'status' => 1,
				),
				array(
					'setting_name' => 'jacket',
					'status' => 1,
				),
				array(
					'setting_name' => 'mounting',
					'status' => 1,
				),
				array(
					'setting_name' => 'remote_control',
					'status' => 1,
				),
				array(
					'setting_name' => 'plug_type',
					'status' => 1,
				),
				array(
					'setting_name' => 'cable_color',
					'status' => 1,
				),
				array(
					'setting_name' => 'special_requests',
					'status' => 1,
				),
				array(
					'setting_name' => 'show_shipping_fee',
					'status' => 1,
				),
			);

			foreach ($records as $record) {
				$wpdb->insert(Neon_Sign_Customize_Activator::wp_nsc_settings(), $record);
			}
		}
	}



	public static function wp_nsc_fonts()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_fonts";
	}

	public static function wp_nsc_colors()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_colors";
	}

	public static function wp_nsc_sizes()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_sizes";
	}

	public static function wp_nsc_prices()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_prices";
	}

	public static function wp_nsc_backboard()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_backboard";
	}

	public static function wp_nsc_backboard_color()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_backboard_color";
	}

	public static function wp_nsc_additional()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_additional";
	}

	public static function wp_nsc_backboard_color_link()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_backboard_color_link";
	}

	public static function wp_nsc_font_price()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_font_price";
	}

	public static function wp_nsc_settings()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_settings";
	}

	public static function wp_nsc_shipping_fee()
	{
		global $wpdb;
		return $wpdb->prefix . "nsc_shipping_fee";
	}
}