<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://sample.com
 * @since      1.0.0
 *
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/public
 * @author     Okido Nguyễn <okidonguyen88@gmail.com>
 */
class Neon_Sign_Customize_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/neon-sign-customize-public.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/neon-sign-customize-public.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_name, 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

	}

	public function load_neon_sign_template($template)
	{
		if (is_singular('product')) {
			$product_id = get_queried_object_id();
			$product = wc_get_product($product_id);

			if ($product && ($product->product_type === 'neon_sign')) {
				$custom_template = plugin_dir_path(__FILE__) . 'partials/neon-sign-customize-public-display.php';

				// Check if the custom template file exists
				if (file_exists($custom_template)) {
					return $custom_template;
				}
			}
		}
		return $template;
	}

	// Add this code to your theme's functions.php or a custom plugin
	public function update_custom_data_in_cart_ajax()
	{
		if (isset($_POST['product_id'])) {
			$product_id = intval($_POST['product_id']);

			// Get and sanitize each custom data field
			$custom_text = sanitize_text_field($_POST['custom_text']);
			$custom_price = sanitize_text_field($_POST['custom_price']);
			$custom_font = sanitize_text_field($_POST['custom_font']);
			$custom_color = sanitize_text_field($_POST['custom_color']);
			$custom_size = sanitize_text_field($_POST['custom_size']);
			$custom_backboard = sanitize_text_field($_POST['custom_backboard']);
			$custom_backboard_color = sanitize_text_field($_POST['custom_backboard_color']);
			$custom_material = sanitize_text_field($_POST['custom_material']);
			$custom_jacket = sanitize_text_field($_POST['custom_jacket']);
			$custom_mounting = sanitize_text_field($_POST['custom_mounting']);
			$custom_remote_control = sanitize_text_field($_POST['custom_remote_control']);
			$custom_plug_type = sanitize_text_field($_POST['custom_plug_type']);
			$custom_cable_color = sanitize_text_field($_POST['custom_cable_color']);
			$custom_special = sanitize_text_field($_POST['custom_special']);
			// Sanitize other custom data fields here			

			$cart_items = WC()->cart->get_cart();
			foreach ($cart_items as $cart_item_key => $cart_item) {
				$product = $cart_item['data'];
				if ($product->get_type() === 'neon_sign') {
					WC()->cart->remove_cart_item($cart_item_key);
				}
			}

			// Add the new product to the cart
			WC()->cart->add_to_cart(
				$product_id,
				1,
				0,
				array(),
				array(
					'_custom_text' => $custom_text,
					'_custom_price' => $custom_price,
					'_custom_font' => $custom_font,
					'_custom_color' => $custom_color,
					'_custom_size' => $custom_size,
					'_custom_backboard' => $custom_backboard,
					'_custom_backboard_color' => $custom_backboard_color,
					'_custom_material' => $custom_material,
					'_custom_jacket' => $custom_jacket,
					'_custom_mounting' => $custom_mounting,
					'_custom_remote_control' => $custom_remote_control,
					'_custom_plug_type' => $custom_plug_type,
					'_custom_cable_color' => $custom_cable_color,
					'_custom_special' => $custom_special,
					// Add other custom data fields here
				)
			);

			WC()->cart->calculate_totals();

			echo 'success';
		}

		wp_die();

	}

	public function session_save_ajax()
	{
		session_start();
		if (isset($_POST['custom_text'])) {
			$_SESSION['neon-size']['text'] = $_POST['custom_text'];
		}
		if (isset($_POST['custom_font'])) {
			$_SESSION['neon-size']['font'] = $_POST['custom_font'];
		}
		if (isset($_POST['custom_color'])) {
			$_SESSION['neon-size']['color'] = $_POST['custom_color'];
		}
		if (isset($_POST['custom_size'])) {
			$_SESSION['neon-size']['size'] = $_POST['custom_size'];
		}
		if (isset($_POST['custom_backboard'])) {
			$_SESSION['neon-size']['backboard'] = $_POST['custom_backboard'];
		}
		if (isset($_POST['custom_backboard_color'])) {
			$_SESSION['neon-size']['backboard_color'] = $_POST['custom_backboard_color'];
		}
		if (isset($_POST['custom_material'])) {
			$_SESSION['neon-size']['material'] = $_POST['custom_material'];
		}
		if (isset($_POST['custom_jacket'])) {
			$_SESSION['neon-size']['jacket'] = $_POST['custom_jacket'];
		}
		if (isset($_POST['custom_mounting'])) {
			$_SESSION['neon-size']['mounting'] = $_POST['custom_mounting'];
		}
		if (isset($_POST['custom_remote_control'])) {
			$_SESSION['neon-size']['remote_control'] = $_POST['custom_remote_control'];
		}
		if (isset($_POST['custom_plug_type'])) {
			$_SESSION['neon-size']['plug_type'] = $_POST['custom_plug_type'];
		}
		if (isset($_POST['custom_cable_color'])) {
			$_SESSION['neon-size']['cable_color'] = $_POST['custom_cable_color'];
		}
		if (isset($_POST['custom_special'])) {
			$_SESSION['neon-size']['special'] = $_POST['custom_special'];
		}
		if (isset($_POST['custom_price'])) {
			$_SESSION['neon-size']['price'] = $_POST['custom_price'];
		}
		echo json_encode(array('message' => 'success'));

		wp_die();

	}

}