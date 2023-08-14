<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://sample.com
 * @since      1.0.0
 *
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/admin
 * @author     Okido Nguyễn <okidonguyen88@gmail.com>
 */
define('MY_AWESOME_PLUGIN_PATH', __DIR__);
class Neon_Sign_Customize_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Neon_Sign_Customize_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Neon_Sign_Customize_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$valid_pages = array(
			"neon-sign-customize",
			"neon-sign-fonts",
			"neon-new-fonts"
		);

		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";

		if (in_array($page, $valid_pages)) {
			wp_enqueue_style('nsc-bootstrap', plugin_dir_url(__FILE__) . 'css/css_bootstrap.min.css', array(), '4.6.2', 'all');
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/neon-sign-customize-admin.css', array(), $this->version, 'all');
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Neon_Sign_Customize_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Neon_Sign_Customize_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$valid_pages = array(
			"neon-sign-customize",
			"neon-sign-fonts",
			"neon-new-fonts"
		);

		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";

		if (in_array($page, $valid_pages)) {
			wp_enqueue_script('nsc-jquery', plugin_dir_url(__FILE__) . 'js/jquery.slim.min.js', array('jquery'), '3.5.1', true);
			wp_enqueue_script('nsc-bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.min.js', array('jquery'), '4.6.2', true);
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/neon-sign-customize-admin.js', array('jquery'), $this->version, false);
		}
	}

	public function add_neon_sign_menu()
	{
		add_menu_page(
			"Neon Sign Customize",
			"Neon Sign Customize",
			"manage_options",
			"neon-sign-customize",
			array($this, "neon_sign_default"),
			'',
			3
		);

		add_submenu_page(
			"neon-sign-customize",
			"Neon Sign Customize",
			"Hướng dẫn sử dụng",
			"manage_options",
			"neon-sign-customize",
			array($this, "neon_sign_default")
		);

		// Fonts manage
		add_submenu_page(
			"neon-sign-customize",
			"Neon Sign Customize",
			"Quản lý Fonts",
			"manage_options",
			"neon-sign-fonts",
			array($this, "neon_sign_fonts")
		);

		// Fonts Manage - add new fonts
		add_submenu_page(
			"neon-sign-customize",
			"Neon Sign Customize",
			'|_ Thêm Fonts',
			"manage_options",
			"neon-new-fonts",
			array($this, "neon_new_fonts")
		);

		add_submenu_page(
			"neon-sign-customize",
			"Neon Sign Customize",
			"Quản lý Màu",
			"manage_options",
			"neon-sign-colors",
			array($this, "neon_sign_colors")
		);

		add_submenu_page(
			"neon-sign-customize",
			"Neon Sign Customize",
			"Quản lý Kích thước",
			"manage_options",
			"neon-sign-size",
			array($this, "neon_sign_size")
		);

		add_submenu_page(
			"neon-sign-customize",
			"Neon Sign Customize",
			"Quản lý Chất liệu",
			"manage_options",
			"neon-sign-materials",
			array($this, "neon_sign_materials")
		);
	}

	// Menu callback funtion
	public function neon_sign_default()
	{
		global $wpdb;
		echo "
		<div>
			<h1>Hướng dẫn sử dụng</h1>
			<p>
			Main feature
			</p>
		</div>	

		";
	}

	public function neon_sign_fonts()
	{
		include(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-fonts-manage.php');
	}

	public function neon_new_fonts()
	{
		include(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-fonts-add-new.php');
	}

	public function add_new_font()
	{
		if (isset($_POST['task']) && isset($_POST['font_id']) && isset($_POST['font_name']) && isset($_POST['font_short']) && isset($_POST['font_url']) && isset($_POST['extra_price'])) {
			global $wpdb;

			$font_name = sanitize_text_field($_POST['font_name']);
			$font_short = sanitize_text_field($_POST['font_short']);
			$font_url = sanitize_text_field($_POST['font_url']);
			$extra_price = sanitize_text_field($_POST['extra_price']);
			$task = sanitize_text_field($_POST['task']);
			$font_id = sanitize_text_field($_POST['font_id']);

			// Insert data into the database
			$table_name = $wpdb->prefix . 'nsc_fonts'; // Replace with your table name
			if ($task == "add") {
				$wpdb->insert(
					$table_name,
					array(
						'font_name' => $font_name,
						'font_short' => $font_short,
						'font_url' => $font_url,
						'extra_price' => $extra_price,
					)
				);

			}

			if ($task == "update") {
				$wpdb->update(
					$table_name,
					array(
						'font_name' => $font_name,
						'font_short' => $font_short,
						'font_url' => $font_url,
						'extra_price' => $extra_price,
					),
					array(
						'font_id' => $font_id
					)
				);
			}

			if ($wpdb->last_error === '') {
				echo json_encode(array('message' => admin_url('admin.php?page=neon-sign-fonts')));
			} else {
				echo json_encode(array('message' => 'Some error.'));
			}

		} else {
			echo json_encode(array('message' => 'Data not inserted.'));
		}

		wp_die();
	}

	public function delete_font()
	{
		if (isset($_POST['item_id'])) {
			global $wpdb;
			$id = intval($_POST['item_id']);
			$table_name = $wpdb->prefix . 'nsc_fonts';
			$wpdb->delete($table_name, array('font_id' => $id));
			echo json_encode(array('message' => 'Item deleted successfully.'));
		} else {
			echo json_encode(array('message' => 'Item not deleted.'));
		}
		wp_die();
	}


	public function neon_sign_colors()
	{
		echo "<h1>Welcome to Customize Neon Sign Colors</h1>";
	}

	public function neon_sign_size()
	{
		echo "<h1>Welcome to Customize Neon Sign size</h1>";
	}

	public function neon_sign_materials()
	{
		echo "<h1>Welcome to Customize Neon Sign materials</h1>";
	}

	function display_custom_item_data($item_data, $cart_item)
	{
		if (isset($cart_item['_custom_price'], $cart_item['_custom_text'], $cart_item['_custom_font'])) {
			$item_data[] = array(
				'key' => __('Price', 'your-text-domain'),
				'value' => wc_price($cart_item['_custom_price']),
			);

			$item_data[] = array(
				'key' => __('Text', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_text']),
			);

			$item_data[] = array(
				'key' => __('Font', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_font']),
			);

			$item_data[] = array(
				'key' => __('Color', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_color']),
			);

			$item_data[] = array(
				'key' => __('Size', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_size']),
			);

			$item_data[] = array(
				'key' => __('Backboard', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_backboard']),
			);

			$item_data[] = array(
				'key' => __('Backboard Color', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_backboard_color']),
			);

			$item_data[] = array(
				'key' => __('Material', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_material']),
			);

			$item_data[] = array(
				'key' => __('Jacket', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_jacket']),
			);

			$item_data[] = array(
				'key' => __('Mounting', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_mounting']),
			);

			$item_data[] = array(
				'key' => __('Remote Control', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_remote_control']),
			);

			$item_data[] = array(
				'key' => __('Plug Type', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_plug_type']),
			);

			$item_data[] = array(
				'key' => __('Cable Color', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_cable_color']),
			);

			$item_data[] = array(
				'key' => __('Special Requests', 'your-text-domain'),
				'value' => sanitize_text_field($cart_item['_custom_special']),
			);

		}
		return $item_data;
	}


	function custom_display_item_data($cart_data, $cart_item)
	{
		// Get the product ID from the cart item
		$product_id = $cart_item['product_id'];

		// Get the product type
		$product = wc_get_product($product_id);
		$product_type = $product->get_type();
		$product_meta_data = get_post_meta($product_id);

		$specific_meta_values = (object) array(
			'_custom_price' => 'Custom Price',
			'_custom_text' => 'Text',
			'_custom_font' => 'Font',
			'_custom_color' => 'Color',
			'_custom_size' => 'Size',
			'_custom_backboard' => 'BackBoard',
			'_custom_backboard_color' => 'BackBoard Color',
			'_custom_material' => 'Material',
			'_custom_jacket' => 'Jacket',
			'_custom_mounting' => 'Mounting',
			'_custom_remote_control' => 'Remote Control',
			'_custom_plug_type' => 'Plug Type',
			'_custom_cable_color' => 'Cable Color',
			'_custom_special_requests' => 'Special Requests'
		);

		// Create an array of additional item data
		$extra_data = array();

		if ($product_type === 'neon_sign') {
			foreach ($product_meta_data as $meta_key => $meta_values) {
				// Check if the meta key is in the list of specific keys and has a value in the predefined object
				if (isset($specific_meta_values->$meta_key)) {
					$specific_value = $specific_meta_values->$meta_key;
					foreach ($meta_values as $meta_value) {
						$extra_data[] = array(
							'key' => $specific_value,
							'value' => $meta_value
						);
					}
				}
			}
		}
		// Merge the custom data with the existing cart data
		return array_merge($cart_data, $extra_data);

	}

	/**
	 * Add custom fields to WooCommerce product edit page.
	 */
	function add_custom_fields()
	{
		global $post;

		echo '<div id="neon_sign_params" class="panel woocommerce_options_panel">
		<div class="options_group">';

		// Custom text input field
		woocommerce_wp_text_input(
			array(
				'id' => '_price_start',
				'label' => __('Price Start', 'woocommerce'),
				'desc_tip' => true,
				'description' => __('Enter Price start', 'woocommerce'),
				'type' => 'number',
				'custom_attributes' => array(
					// Add any custom attributes here
					'step' => '0.01',
					'min' => '1'
				)
			)
		);

		woocommerce_wp_text_input(
			array(
				'id' => '_price_per_char',
				'label' => __('Price Per Character', 'woocommerce'),
				'desc_tip' => true,
				'description' => __('Enter Price Per Character.', 'woocommerce'),
				'type' => 'number',
				'custom_attributes' => array(
					// Add any custom attributes here
					'step' => '0.01',
					'min' => '1'
				)
			)
		);

		woocommerce_wp_text_input(
			array(
				'id' => '_character_min',
				'label' => __('Text Character Min', 'woocommerce'),
				'desc_tip' => true,
				'description' => __('Enter Text Character Min.', 'woocommerce'),
				'type' => 'number',
				'custom_attributes' => array(
					// Add any custom attributes here
					'step' => '1',
					'min' => '1'
				)
			)
		);

		woocommerce_wp_text_input(
			array(
				'id' => '_character_max',
				'label' => __('Text Character Max', 'woocommerce'),
				'desc_tip' => true,
				'description' => __('Enter Text Character Max.', 'woocommerce'),
				'type' => 'number',
				'custom_attributes' => array(
					// Add any custom attributes here
					'step' => '1',
					'min' => '1'
				)
			)
		);



		echo '</div></div>';
	}

	// Save the custom price
	function save_custom_fields($post_id)
	{
		$price_start = sanitize_text_field($_POST['_price_start']);
		update_post_meta($post_id, '_price_start', $price_start);

		$price_per_char = sanitize_text_field($_POST['_price_per_char']);
		update_post_meta($post_id, '_price_per_char', $price_per_char);

		$character_min = sanitize_text_field($_POST['_character_min']);
		update_post_meta($post_id, '_character_min', $character_min);

		$character_max = sanitize_text_field($_POST['_character_max']);
		update_post_meta($post_id, '_character_max', $character_max);

	}

	function calculate_custom_price_total($cart)
	{
		if (is_admin() && !defined('DOING_AJAX'))
			return;

		// Loop through each cart item
		foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
			// Check if custom price exists in cart item data
			if (isset($cart_item['_custom_price'])) {
				$product = $cart_item['data'];

				// Check if the product type is "neon_sign"
				if ($product->get_type() === 'neon_sign') {
					// Set the custom price for the product
					$product->set_price($cart_item['_custom_price']);
				}
			}
		}
	}



}