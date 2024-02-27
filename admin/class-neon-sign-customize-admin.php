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
define('MY_ROOT_PLUGIN_PATH', WP_PLUGIN_DIR . '/neon-sign-customize');
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
			"neon-sign-customize"
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

		$valid_pages = array(
			"neon-sign-customize"
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
			"NS Customize",
			"NS Customize",
			"manage_options",
			"neon-sign-customize",
			array($this, "neon_sign_customize"),
			'',
			3
		);
	}

	// Menu callback funtion
	public function neon_sign_customize()
	{
		include(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-customize.php');
	}

	public function add_new_font()
	{
		if (isset($_POST['task']) && isset($_POST['font_id']) && isset($_POST['font_name']) && isset($_FILES['font_url']) && isset($_POST['price_tier']) && isset($_POST['extra_price']) && isset($_POST['extra_price_type'])) {
			global $wpdb;

			$font_name = sanitize_text_field($_POST['font_name']);
			$font_url = $_FILES['font_url'];
			$extra_price = sanitize_text_field($_POST['extra_price']);
			$extra_price_type = sanitize_text_field($_POST['extra_price_type']);
			$task = sanitize_text_field($_POST['task']);
			$font_id = sanitize_text_field($_POST['font_id']);

			$valid = true;
			$time = time();
			$upload_font = '';
			$target_font = '';

			if ($font_url['size'] != 0) {
				$upload_font = $time . '.' . pathinfo($font_url['name'], PATHINFO_EXTENSION);
				$target_font = MY_ROOT_PLUGIN_PATH . '/public/partials/font_url/' . $upload_font;

				// Check target & upload font
				if (move_uploaded_file($font_url['tmp_name'], $target_font)) {
					$valid = true;
				} else {
					$valid = false;
				}
			}

			if ($valid) {
				// Insert data into the database
				$table_name = $wpdb->prefix . 'nsc_fonts';
				if ($task == "add") {
					$insert_result = $wpdb->insert(
						$table_name,
						array(
							'font_name' => $font_name,
							'font_url' => $upload_font,
							'extra_price' => $extra_price,
							'extra_price_type' => $extra_price_type,
						)
					);
					if ($insert_result === false) {
						echo json_encode(array('message' => 'Insert Error.'));
					} else {
						$price_tier_list = $_POST['price_tier'];
						$inserted_id = $wpdb->insert_id;
						foreach ($price_tier_list as $price_tier) {
							$wpdb->insert(
								$wpdb->prefix . 'nsc_font_price',
								array(
									'font_id' => $inserted_id,
									'price_id' => sanitize_text_field($price_tier),
								)
							);
						}
					}

				}

				if ($task == "update") {
					// Retrieve the existing font data including old file paths
					$query = $wpdb->prepare("SELECT font_url FROM $table_name WHERE font_id = %s", $font_id);
					$existing_font_data = $wpdb->get_row($query);

					// Create an associative array to store the updated font data
					$temp_data = array();
					$temp_data['font_name'] = $font_name;
					$temp_data['extra_price'] = $extra_price;
					$temp_data['extra_price_type'] = $extra_price_type;

					// Check if a new font URL file was uploaded
					if ($font_url['size'] != 0) {
						$temp_data['font_url'] = $upload_font;

						// Delete old font URL file if it exists
						if (!empty($existing_font_data->font_url)) {
							$old_font_url_path = MY_ROOT_PLUGIN_PATH . '/public/partials/font_url/' . $existing_font_data->font_url; // Adjust the path as needed
							if (file_exists($old_font_url_path)) {
								unlink($old_font_url_path);
							}
						}
					}

					// Update the database with the new font data
					$nsc_font_price = $wpdb->prefix . 'nsc_font_price';
					$wpdb->delete($nsc_font_price, array('font_id' => $font_id));
					$update_result = $wpdb->update(
						$table_name,
						$temp_data,
						array(
							'font_id' => $font_id
						)
					);
					if ($update_result === false) {
						echo json_encode(array('message' => 'update Error.'));
					} else {
						$price_tier_list = $_POST['price_tier'];
						foreach ($price_tier_list as $price_tier) {
							$wpdb->insert(
								$wpdb->prefix . 'nsc_font_price',
								array(
									'font_id' => $font_id,
									'price_id' => sanitize_text_field($price_tier),
								)
							);
						}
					}
				}

			}

			if ($wpdb->last_error === '') {
				echo json_encode(array('message' => 'success'));
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
			$nsc_fonts = $wpdb->prefix . 'nsc_fonts';
			$nsc_font_price = $wpdb->prefix . 'nsc_font_price';
			$query = $wpdb->prepare("SELECT font_url FROM $nsc_fonts WHERE font_id = %d", $id);
			$font_data = $wpdb->get_row($query);
			// Call helper
			$this->delete_nsc_helper($nsc_fonts, $font_data, array('font_url'), array('font_id' => $id));
			$wpdb->delete($nsc_font_price, array('font_id' => $id));

		} else {
			echo json_encode(array('message' => 'Item not deleted.'));
		}
		wp_die();
	}

	// COLOR
	public function add_new_color()
	{
		if (isset($_POST['task']) && isset($_POST['color_id']) && isset($_POST['color_name']) && isset($_POST['color_hex']) && isset($_POST['is_rbg']) && isset($_POST['extra_price']) && isset($_POST['extra_price_type'])) {
			global $wpdb;

			$color_name = sanitize_text_field($_POST['color_name']);
			$color_hex = sanitize_text_field($_POST['color_hex']);
			$is_rbg = sanitize_text_field($_POST['is_rbg']);
			$extra_price = sanitize_text_field($_POST['extra_price']);
			$extra_price_type = sanitize_text_field($_POST['extra_price_type']);
			$task = sanitize_text_field($_POST['task']);
			$color_id = sanitize_text_field($_POST['color_id']);

			$valid = true;
			$time = time();
			$upload_img = '';


			if ($valid) {
				// Insert data into the database
				$table_name = $wpdb->prefix . 'nsc_colors'; // Replace with your table name
				if ($task == "add") {
					$wpdb->insert(
						$table_name,
						array(
							'color_name' => $color_name,
							'color_hex' => $color_hex,
							'is_rbg' => $is_rbg,
							'extra_price' => $extra_price,
							'extra_price_type' => $extra_price_type,
						)
					);

				}

				if ($task == "update") {

					// Create an associative array to store the updated color data
					$temp_data = array();
					$temp_data['color_name'] = $color_name;
					$temp_data['color_hex'] = $color_hex;
					$temp_data['is_rbg'] = $is_rbg;
					$temp_data['extra_price'] = $extra_price;
					$temp_data['extra_price_type'] = $extra_price_type;

					// Update the database with the new color data
					$wpdb->update(
						$table_name,
						$temp_data,
						array(
							'color_id' => $color_id
						)
					);
				}

			}

			if ($wpdb->last_error === '') {
				echo json_encode(array('message' => 'success'));
			} else {
				echo json_encode(array('message' => 'Some error.'));
			}

		} else {
			echo json_encode(array('message' => 'Data not inserted.'));
		}

		wp_die();
	}

	public function delete_color()
	{
		if (isset($_POST['item_id'])) {
			global $wpdb;
			$id = intval($_POST['item_id']); // Sanitize and retrieve the color_id from the POST data
			$table_name = $wpdb->prefix . 'nsc_colors';
			$query = $wpdb->prepare("SELECT * FROM $table_name WHERE color_id = %d", $id);
			$color_data = $wpdb->get_row($query);
			if ($color_data) {
				// Delete the record from the database
				$wpdb->delete($table_name, array('color_id' => $id));
				echo json_encode(array('message' => 'Item deleted successfully.'));
			} else {
				echo json_encode(array('message' => 'Font not found.'));
			}

		} else {
			echo json_encode(array('message' => 'Item not deleted.'));
		}
		wp_die();
	}

	// SIZE
	public function add_new_size()
	{
		if (
			isset($_POST['task']) && isset($_POST['size_id'])
			&& isset($_POST['size_name'])
			&& isset($_POST['size_des'])
			&& isset($_POST['size_length'])
			&& isset($_POST['size_vol'])
			&& isset($_POST['size_fee'])
			&& isset($_POST['text_width'])
			&& isset($_POST['text_height'])
			&& isset($_POST['size_char_max'])
			&& isset($_POST['size_char_min'])
		) {
			global $wpdb;

			$size_name = sanitize_text_field($_POST['size_name']);
			$size_des = sanitize_text_field($_POST['size_des']);
			$size_length = sanitize_text_field($_POST['size_length']);
			$size_vol = sanitize_text_field($_POST['size_vol']);
			$size_fee = sanitize_text_field($_POST['size_fee']);
			$text_width = sanitize_text_field($_POST['text_width']);
			$text_height = sanitize_text_field($_POST['text_height']);
			$text_line = 3;
			$size_char_max = sanitize_text_field($_POST['size_char_max']);
			$size_char_min = sanitize_text_field($_POST['size_char_min']);
			$task = sanitize_text_field($_POST['task']);
			$size_id = sanitize_text_field($_POST['size_id']);

			$valid = true;

			if ($valid) {
				// Insert data into the database
				$table_name = $wpdb->prefix . 'nsc_sizes'; // Replace with your table name
				if ($task == "add") {
					$wpdb->insert(
						$table_name,
						array(
							'size_name' => $size_name,
							'size_des' => $size_des,
							'size_length' => $size_length,
							'size_vol' => $size_vol,
							'size_fee' => $size_fee,
							'text_width' => $text_width,
							'text_height' => $text_height,
							'text_line' => $text_line,
							'size_char_max' => $size_char_max,
							'size_char_min' => $size_char_min,
						)
					);

				}

				if ($task == "update") {

					// Create an associative array to store the updated size data
					$temp_data = array();
					$temp_data['size_name'] = $size_name;
					$temp_data['size_des'] = $size_des;
					$temp_data['size_length'] = $size_length;
					$temp_data['size_vol'] = $size_vol;
					$temp_data['size_fee'] = $size_fee;
					$temp_data['text_width'] = $text_width;
					$temp_data['text_height'] = $text_height;
					// $temp_data['text_line'] = $text_line;
					$temp_data['size_char_max'] = $size_char_max;
					$temp_data['size_char_min'] = $size_char_min;

					// Update the database with the new size data
					$wpdb->update(
						$table_name,
						$temp_data,
						array(
							'size_id' => $size_id
						)
					);
				}

			}

			if ($wpdb->last_error === '') {
				echo json_encode(array('message' => 'success'));
			} else {
				echo json_encode(array('message' => 'Some error.'));
			}

		} else {
			echo json_encode(array('message' => 'Data not inserted.'));
		}

		wp_die();
	}

	public function delete_size()
	{
		if (isset($_POST['item_id'])) {
			global $wpdb;
			$id = intval($_POST['item_id']); // Sanitize and retrieve the size_id from the POST data
			$table_name = $wpdb->prefix . 'nsc_sizes';
			$query = $wpdb->prepare("SELECT * FROM $table_name WHERE size_id = %d", $id);
			$size_data = $wpdb->get_row($query);
			if ($size_data) {
				// Delete the record from the database
				$wpdb->delete($table_name, array('size_id' => $id));
				echo json_encode(array('message' => 'Item deleted successfully.'));
			} else {
				echo json_encode(array('message' => 'Font not found.'));
			}

		} else {
			echo json_encode(array('message' => 'Item not deleted.'));
		}
		wp_die();
	}

	public function add_new_price()
	{
		if (
			isset($_POST['task'])
			&& isset($_POST['price_id'])
			&& isset($_POST['price_name'])
			&& isset($_POST['size_id'])
			&& isset($_POST['line_one_base'])
			&& isset($_POST['line_one_letter'])
			&& isset($_POST['line_two_base'])
			&& isset($_POST['line_two_letter'])
			&& isset($_POST['line_three_base'])
			&& isset($_POST['line_three_letter'])
		) {
			global $wpdb;

			$price_name = sanitize_text_field($_POST['price_name']);
			$size_id = sanitize_text_field($_POST['size_id']);
			$line_one_base = sanitize_text_field($_POST['line_one_base']);
			$line_one_letter = sanitize_text_field($_POST['line_one_letter']);
			$line_two_base = sanitize_text_field($_POST['line_two_base']);
			$line_two_letter = sanitize_text_field($_POST['line_two_letter']);
			$line_three_base = sanitize_text_field($_POST['line_three_base']);
			$line_three_letter = sanitize_text_field($_POST['line_three_letter']);
			$task = sanitize_text_field($_POST['task']);
			$price_id = sanitize_text_field($_POST['price_id']);

			$valid = true;

			if ($valid) {
				// Insert data into the database
				$table_name = $wpdb->prefix . 'nsc_prices'; // Replace with your table name
				if ($task == "add") {
					$wpdb->insert(
						$table_name,
						array(
							'price_name' => $price_name,
							'size_id' => $size_id,
							'line_one_base' => $line_one_base,
							'line_one_letter' => $line_one_letter,
							'line_two_base' => $line_two_base,
							'line_two_letter' => $line_two_letter,
							'line_three_base' => $line_three_base,
							'line_three_letter' => $line_three_letter,
						)
					);

				}

				if ($task == "update") {
					// Create an associative array to store the updated price data
					$temp_data = array();
					$temp_data['price_name'] = $price_name;
					$temp_data['size_id'] = $size_id;
					$temp_data['line_one_base'] = $line_one_base;
					$temp_data['line_one_letter'] = $line_one_letter;
					$temp_data['line_two_base'] = $line_two_base;
					$temp_data['line_two_letter'] = $line_two_letter;
					$temp_data['line_three_base'] = $line_three_base;
					$temp_data['line_three_letter'] = $line_three_letter;

					// Update the database with the new price data
					$wpdb->update(
						$table_name,
						$temp_data,
						array(
							'price_id' => $price_id
						)
					);
				}

			}

			if ($wpdb->last_error === '') {
				echo json_encode(array('message' => 'success'));
			} else {
				echo json_encode(array('message' => $wpdb->last_error));
			}

		} else {
			echo json_encode(array('message' => 'Data not inserted.'));
		}

		wp_die();
	}

	public function delete_price()
	{
		if (isset($_POST['item_id'])) {
			global $wpdb;
			$id = intval($_POST['item_id']); // Sanitize and retrieve the price_id from the POST data
			$nsc_prices = $wpdb->prefix . 'nsc_prices';
			$nsc_font_price = $wpdb->prefix . 'nsc_font_price';
			$query = $wpdb->prepare("SELECT * FROM $nsc_prices WHERE price_id = %d", $id);
			$price_data = $wpdb->get_row($query);
			if ($price_data) {
				// Delete the record from the database
				$wpdb->delete($nsc_prices, array('price_id' => $id));
				$wpdb->delete($nsc_font_price, array('price_id' => $id));
				echo json_encode(array('message' => 'Item deleted successfully.'));
			} else {
				echo json_encode(array('message' => 'Font not found.'));
			}

		} else {
			echo json_encode(array('message' => 'Item not deleted.'));
		}
		wp_die();
	}

	// BACKBOARD
	public function add_new_backboard()
	{
		if (isset($_POST['task']) && isset($_POST['backboard_id']) && isset($_POST['backboard_name']) && isset($_POST['backboard_des']) && isset($_FILES['backboard_img']) && isset($_POST['extra_price']) && isset($_POST['extra_price_type'])) {
			global $wpdb;

			$backboard_name = sanitize_text_field($_POST['backboard_name']);
			$backboard_des = sanitize_text_field($_POST['backboard_des']);
			$backboard_img = $_FILES['backboard_img'];
			$extra_price = sanitize_text_field($_POST['extra_price']);
			$extra_price_type = sanitize_text_field($_POST['extra_price_type']);
			$task = sanitize_text_field($_POST['task']);
			$backboard_id = sanitize_text_field($_POST['backboard_id']);

			$valid = true;
			$time = time();
			$upload_img = '';
			$target_img = '';

			if ($backboard_img['size'] != 0) {
				$upload_img = $time . '.' . pathinfo($backboard_img['name'], PATHINFO_EXTENSION);
				$target_img = MY_ROOT_PLUGIN_PATH . '/public/partials/backboard_img/' . $upload_img;

				// Check target & upload image
				if (move_uploaded_file($backboard_img['tmp_name'], $target_img)) {
					$valid = true;
				} else {
					$valid = false;
				}
			}

			if ($valid) {
				// Insert data into the database
				$table_name = $wpdb->prefix . 'nsc_backboard'; // Replace with your table name
				if ($task == "add") {
					$insert_result = $wpdb->insert(
						$table_name,
						array(
							'backboard_name' => $backboard_name,
							'backboard_des' => $backboard_des,
							'backboard_img' => $upload_img,
							'extra_price' => $extra_price,
							'extra_price_type' => $extra_price_type,
						)
					);
					if ($insert_result === false) {
						echo json_encode(array('message' => 'Insert Error.'));
					} else {
						$backboard_colors = $_POST['backboard_colors'];
						$inserted_id = $wpdb->insert_id;
						foreach ($backboard_colors as $data) {
							$wpdb->insert(
								$wpdb->prefix . 'nsc_backboard_color_link',
								array(
									'backboard_id' => $inserted_id,
									'backboard_color_id' => sanitize_text_field($data),
								)
							);
						}
					}

				}

				if ($task == "update") {
					// Retrieve the existing backboard data including old file paths
					$query = $wpdb->prepare("SELECT backboard_img FROM $table_name WHERE backboard_id = %s", $backboard_id);
					$existing_backboard_data = $wpdb->get_row($query);

					// Create an associative array to store the updated backboard data
					$temp_data = array();
					$temp_data['backboard_name'] = $backboard_name;
					$temp_data['backboard_des'] = $backboard_des;
					$temp_data['extra_price'] = $extra_price;
					$temp_data['extra_price_type'] = $extra_price_type;


					// Check if a new backboard image file was uploaded
					if ($backboard_img['size'] != 0) {
						$temp_data['backboard_img'] = $upload_img;

						// Delete old backboard image file if it exists
						if (!empty($existing_backboard_data->backboard_img)) {
							$old_backboard_img_path = MY_ROOT_PLUGIN_PATH . '/public/partials/backboard_img/' . $existing_backboard_data->backboard_img; // Adjust the path as needed
							if (file_exists($old_backboard_img_path)) {
								unlink($old_backboard_img_path);
							}
						}
					}

					// Update the database with the new backboard data
					$nsc_backboard_color_link = $wpdb->prefix . 'nsc_backboard_color_link';
					$wpdb->delete($nsc_backboard_color_link, array('backboard_id' => $backboard_id));
					$update_result = $wpdb->update(
						$table_name,
						$temp_data,
						array(
							'backboard_id' => $backboard_id
						)
					);

					if ($update_result === false) {
						echo json_encode(array('message' => 'update Error.'));
					} else {
						$backboard_colors = $_POST['backboard_colors'];
						foreach ($backboard_colors as $data) {
							$wpdb->insert(
								$wpdb->prefix . 'nsc_backboard_color_link',
								array(
									'backboard_id' => $backboard_id,
									'backboard_color_id' => sanitize_text_field($data),
								)
							);
						}
					}
				}

			}

			if ($wpdb->last_error === '') {
				echo json_encode(array('message' => 'success'));
			} else {
				echo json_encode(array('message' => $wpdb->last_error));
			}

		} else {
			echo json_encode(array('message' => 'Data not inserted.'));
		}

		wp_die();
	}

	// BACKBOARD COLOR
	public function add_new_backboard_color()
	{
		if (isset($_POST['task']) && isset($_POST['backboard_color_id']) && isset($_POST['backboard_color_name']) && isset($_POST['backboard_color_hex']) && isset($_FILES['backboard_color_img']) && isset($_POST['extra_price']) && isset($_POST['extra_price_type'])) {
			global $wpdb;

			$backboard_color_name = sanitize_text_field($_POST['backboard_color_name']);
			$backboard_color_hex = sanitize_text_field($_POST['backboard_color_hex']);
			$backboard_color_img = $_FILES['backboard_color_img'];
			$extra_price = sanitize_text_field($_POST['extra_price']);
			$extra_price_type = sanitize_text_field($_POST['extra_price_type']);
			$task = sanitize_text_field($_POST['task']);
			$backboard_color_id = sanitize_text_field($_POST['backboard_color_id']);

			$valid = true;
			$time = time();
			$upload_img = '';
			$target_img = '';

			if ($backboard_color_img['size'] != 0) {
				$upload_img = $time . '.' . pathinfo($backboard_color_img['name'], PATHINFO_EXTENSION);
				$target_img = MY_ROOT_PLUGIN_PATH . '/public/partials/backboard_color_img/' . $upload_img;


				// Check target & upload image
				if (move_uploaded_file($backboard_color_img['tmp_name'], $target_img)) {
					$valid = true;
				} else {
					$valid = false;
				}
			}

			if ($valid) {
				// Insert data into the database
				$table_name = $wpdb->prefix . 'nsc_backboard_color'; // Replace with your table name
				if ($task == "add") {
					$wpdb->insert(
						$table_name,
						array(
							'backboard_color_name' => $backboard_color_name,
							'backboard_color_hex' => $backboard_color_hex,
							'backboard_color_img' => $upload_img,
							'extra_price' => $extra_price,
							'extra_price_type' => $extra_price_type,
						)
					);

				}

				if ($task == "update") {
					// Retrieve the existing backboard_color data including old file paths
					$query = $wpdb->prepare("SELECT backboard_color_img FROM $table_name WHERE backboard_color_id = %s", $backboard_color_id);
					$existing_backboard_color_data = $wpdb->get_row($query);

					// Create an associative array to store the updated backboard_color data
					$temp_data = array();
					$temp_data['backboard_color_name'] = $backboard_color_name;
					$temp_data['backboard_color_hex'] = $backboard_color_hex;
					$temp_data['extra_price'] = $extra_price;
					$temp_data['extra_price_type'] = $extra_price_type;


					// Check if a new backboard_color image file was uploaded
					if ($backboard_color_img['size'] != 0) {
						$temp_data['backboard_color_img'] = $upload_img;

						// Delete old backboard_color image file if it exists
						if (!empty($existing_backboard_color_data->backboard_color_img)) {
							$old_backboard_color_img_path = MY_ROOT_PLUGIN_PATH . '/public/partials/backboard_color_img/' . $existing_backboard_color_data->backboard_color_img; // Adjust the path as needed
							if (file_exists($old_backboard_color_img_path)) {
								unlink($old_backboard_color_img_path);
							}
						}
					}

					// Update the database with the new backboard_color data
					$wpdb->update(
						$table_name,
						$temp_data,
						array(
							'backboard_color_id' => $backboard_color_id
						)
					);
				}

			}

			if ($wpdb->last_error === '') {
				echo json_encode(array('message' => 'success'));
			} else {
				echo json_encode(array('message' => 'Some error.'));
			}

		} else {
			echo json_encode(array('message' => 'Data not inserted.'));
		}

		wp_die();
	}

	public function delete_backboard_color()
	{
		if (isset($_POST['item_id'])) {
			global $wpdb;
			$id = intval($_POST['item_id']); // Sanitize and retrieve the backboard_color_id from the POST data
			$table_name = $wpdb->prefix . 'nsc_backboard_color';
			$query = $wpdb->prepare("SELECT backboard_color_img FROM $table_name WHERE backboard_color_id = %d", $id);
			$backboard_color_data = $wpdb->get_row($query);
			// Call helper
			$this->delete_nsc_helper($table_name, $backboard_color_data, array('backboard_color_img'), array('backboard_color_id' => $id));

		} else {
			echo json_encode(array('message' => 'Item not deleted.'));
		}
		wp_die();
	}

	// ADDITIONAL
	public function add_new_additional()
	{
		if (isset($_POST['task']) && isset($_POST['return']) && isset($_POST['additional_id']) && isset($_POST['additional_name']) && isset($_POST['additional_des']) && isset($_FILES['additional_img']) && isset($_POST['extra_price']) && isset($_POST['extra_price_type'])) {
			global $wpdb;

			$additional_name = sanitize_text_field($_POST['additional_name']);
			$additional_des = sanitize_text_field($_POST['additional_des']);
			$additional_img = $_FILES['additional_img'];
			$extra_price = sanitize_text_field($_POST['extra_price']);
			$extra_price_type = sanitize_text_field($_POST['extra_price_type']);
			$task = sanitize_text_field($_POST['task']);
			$additional_id = sanitize_text_field($_POST['additional_id']);
			$type = sanitize_text_field($_POST['return']);

			$valid = true;
			$time = time();
			$upload_img = '';
			$target_img = '';

			if ($additional_img['size'] != 0) {
				$upload_img = $type . '_' . $time . '.' . pathinfo($additional_img['name'], PATHINFO_EXTENSION);
				$target_img = MY_ROOT_PLUGIN_PATH . '/public/partials/additional_img/' . $upload_img;

				// Check target & upload image
				if (move_uploaded_file($additional_img['tmp_name'], $target_img)) {
					$valid = true;
				} else {
					$valid = false;
				}
			}

			if ($valid) {
				// Insert data into the database
				$table_name = $wpdb->prefix . 'nsc_additional'; // Replace with your table name
				if ($task == "add") {
					$wpdb->insert(
						$table_name,
						array(
							'additional_name' => $additional_name,
							'additional_des' => $additional_des,
							'additional_img' => $upload_img,
							'additional_type' => $type,
							'extra_price' => $extra_price,
							'extra_price_type' => $extra_price_type,
						)
					);

				}

				if ($task == "update") {
					// Retrieve the existing color data including old file paths
					$query = $wpdb->prepare("SELECT additional_img FROM $table_name WHERE additional_id = %s", $additional_id);
					$existing_additional_data = $wpdb->get_row($query);

					// Create an associative array to store the updated color data
					$temp_data = array();
					$temp_data['additional_name'] = $additional_name;
					$temp_data['additional_des'] = $additional_des;
					$temp_data['extra_price'] = $extra_price;
					$temp_data['extra_price_type'] = $extra_price_type;


					// Check if a new color image file was uploaded
					if ($additional_img['size'] != 0) {
						$temp_data['additional_img'] = $upload_img;

						// Delete old color image file if it exists
						if (!empty($existing_additional_data->additional_img)) {
							$old_additional_img_path = MY_ROOT_PLUGIN_PATH . '/public/partials/additional_img/' . $existing_additional_data->additional_img; // Adjust the path as needed
							if (file_exists($old_additional_img_path)) {
								unlink($old_additional_img_path);
							}
						}
					}

					// Update the database with the new color data
					$wpdb->update(
						$table_name,
						$temp_data,
						array(
							'additional_id' => $additional_id
						)
					);
				}

			}

			if ($wpdb->last_error === '') {
				echo json_encode(array('message' => 'success'));
			} else {
				echo json_encode(array('message' => 'Some error.'));
			}

		} else {
			echo json_encode(array('message' => 'Data not inserted.'));
		}

		wp_die();
	}

	public function delete_additional()
	{
		if (isset($_POST['item_id'])) {
			global $wpdb;
			$id = intval($_POST['item_id']);
			$table_name = $wpdb->prefix . 'nsc_additional';
			$query = $wpdb->prepare("SELECT additional_img FROM $table_name WHERE additional_id = %d", $id);
			$additional_data = $wpdb->get_row($query);
			// Call helper
			$this->delete_nsc_helper($table_name, $additional_data, array('additional_img'), array('additional_id' => $id));
		} else {
			echo json_encode(array('message' => 'Item not deleted.'));
		}
		wp_die();
	}

	public function delete_nsc_helper($table_name, $data, $listCol, $delCol)
	{
		global $wpdb;
		if ($data) {
			// Delete uploaded files if they exist
			foreach ($listCol as $col) {
				if (!empty($data->$col)) {
					// Delete font URL file
					$font_url_path = MY_ROOT_PLUGIN_PATH . '/public/partials/' . $col . '/' . $data->$col;
					if (file_exists($font_url_path)) {
						unlink($font_url_path);
					}
				}
			}
			// Delete the record from the database
			$wpdb->delete($table_name, $delCol);
			echo json_encode(array('message' => 'Item deleted successfully.'));
		} else {
			echo json_encode(array('message' => 'Font not found.'));
		}
	}

	public function change_setting()
	{
		if (isset($_POST['item_id'])) {
			global $wpdb;
			$id = intval($_POST['item_id']);
			$nsc_settings = $wpdb->prefix . 'nsc_settings';
			$current_status = $wpdb->get_var($wpdb->prepare("SELECT status FROM $nsc_settings WHERE setting_id = %s", $id));
			$new_status = $current_status == 1 ? 0 : 1;

			// Update the status value in the database
			$wpdb->update(
				$nsc_settings,
				array('status' => $new_status),
				array('setting_id' => $id)
			);

		} else {
			echo json_encode(array('message' => 'Item not deleted.'));
		}
		wp_die();
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
		$parameters = array(
			'backboard' => __('BackBoard', 'woocommerce'),
			'backboard_color' => __('BackBoard Color', 'woocommerce'),
			'material' => __('Material', 'woocommerce'),
			'jacket' => __('Jacket', 'woocommerce'),
			'mounting' => __('Mounting', 'woocommerce'),
			'remote_control' => __('Remote Control', 'woocommerce'),
			'plug_type' => __('Plug Type', 'woocommerce'),
			'cable_color' => __('Cable Color', 'woocommerce'),
			'special_request' => __('Special Request', 'woocommerce')
		);

		echo '<div id="neon_sign_params" class="panel woocommerce_options_panel">
		<div class="options_group">';

		// Custom text input field
		foreach ($parameters as $param_key => $param_label) {
			woocommerce_wp_radio(
				array(
					'id' => '_' . $param_key . '_option',
					'label' => $param_label,
					'options' => array(
						'on' => __('On', 'woocommerce'),
						'off' => __('Off', 'woocommerce')
					)
				)
			);
		}

		echo '</div></div>';
	}

	// Save the custom price
	function save_custom_fields($post_id)
	{
		if (get_post_type($post_id) !== 'product') {
			return;
		}

		// Check if the product type is 'neon_sign'
		$product = wc_get_product($post_id);
		if ($product && $product->is_type('neon_sign')) {
			$parameters = array(
				'backboard' => __('BackBoard', 'woocommerce'),
				'backboard_color' => __('BackBoard Color', 'woocommerce'),
				'material' => __('Material', 'woocommerce'),
				'jacket' => __('Jacket', 'woocommerce'),
				'mounting' => __('Mounting', 'woocommerce'),
				'remote_control' => __('Remote Control', 'woocommerce'),
				'plug_type' => __('Plug Type', 'woocommerce'),
				'cable_color' => __('Cable Color', 'woocommerce'),
				'special_request' => __('Special Request', 'woocommerce')
			);

			foreach ($parameters as $param_key => $param_label) {
				$meta_key = '_' . $param_key . '_option';
				$meta_value = isset($_POST[$meta_key]) ? sanitize_text_field($_POST[$meta_key]) : 'off';
				update_post_meta($post_id, $meta_key, $meta_value);
			}
		}

	}

	// Custom metabox-1
	function custom_product_meta_box_one()
	{
		add_meta_box(
			'custom_product_meta_box_one',
			__('Custom Product Fields 1', 'woocommerce'),
			array($this, 'render_custom_product_field_one'),
			'product',
			'normal',
			'high'
		);
	}

	function render_custom_product_field_one($post)
	{
		$product = wc_get_product($post->ID);
		$content = $product->get_meta('_nsc_custom_field_one');

		echo '<div class="product_nsc_custom_field_one">';

		wp_editor($content, '_nsc_custom_field_one', ['textarea_rows' => 10]);

		echo '</div>';
	}

	function save_custom_product_meta_box_one($product)
	{
		if (isset($_POST['_nsc_custom_field_one']))
			$product->update_meta_data('_nsc_custom_field_one', wp_kses_post($_POST['_nsc_custom_field_one']));
	}

	// Custom metabox-2
	function custom_product_meta_box_two()
	{
		add_meta_box(
			'custom_product_meta_box_two',
			__('Custom Product Fields 2', 'woocommerce'),
			array($this, 'render_custom_product_field_two'),
			'product',
			'normal',
			'high'
		);
	}

	function render_custom_product_field_two($post)
	{
		$product = wc_get_product($post->ID);
		$content = $product->get_meta('_nsc_custom_field_two');

		echo '<div class="product_nsc_custom_field_two">';

		wp_editor($content, '_nsc_custom_field_two', ['textarea_rows' => 10]);

		echo '</div>';
	}

	function save_custom_product_meta_box_two($product)
	{
		if (isset($_POST['_nsc_custom_field_two']))
			$product->update_meta_data('_nsc_custom_field_two', wp_kses_post($_POST['_nsc_custom_field_two']));
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