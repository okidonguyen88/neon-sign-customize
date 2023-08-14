<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sample.com
 * @since             1.0.1
 * @package           Neon_Sign_Customize
 *
 * @wordpress-plugin
 * Plugin Name:       Neon Sign Customize
 * Plugin URI:        https://sample.com
 * Description:       Neon Sign Customize
 * Version:           1.0.1
 * Author:            Okido Nguyễn
 * Author URI:        https://sample.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       neon-sign-customize
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('NEON_SIGN_CUSTOMIZE_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-neon-sign-customize-activator.php
 */
function activate_neon_sign_customize()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-neon-sign-customize-activator.php';
	$activator = new Neon_Sign_Customize_Activator();
	$activator->activate();

}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-neon-sign-customize-deactivator.php
 */
function deactivate_neon_sign_customize()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-neon-sign-customize-deactivator.php';
	Neon_Sign_Customize_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_neon_sign_customize');
register_deactivation_hook(__FILE__, 'deactivate_neon_sign_customize');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-neon-sign-customize.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_neon_sign_customize()
{

	add_action('init', 'register_neon_sign_product_type');
	add_filter('product_type_selector', 'add_neon_sign_product');
	add_filter('woocommerce_product_data_tabs', 'add_custom_fields_tab');

	$plugin = new Neon_Sign_Customize();
	$plugin->run();

}

// remove wp version param from any enqueued scripts

function register_neon_sign_product_type()
{
	/**
	 * This should be in its own separate file.
	 */
	class WC_Product_Neon_Sign extends WC_Product
	{
		public function __construct($product)
		{
			$this->product_type = 'neon_sign';
			parent::__construct($product);
		}

		public function is_virtual()
		{
			return true;
		}

		// Allow the product to be purchased
		public function is_purchasable()
		{
			return true;
		}
	}

}

function add_neon_sign_product($types)
{
	// Key should be exactly the same as in the class product_type parameter
	$types['neon_sign'] = __('Neon Sign Product', 'neon_sign');
	return $types;

}

function add_custom_fields_tab($tabs)
{
	$tabs['custom_fields'] = array(
		'label' => __('Neon Sign', 'neon_sign'),
		'target' => 'neon_sign_params',
		'class' => array('show_if_neon_sign'),
		'priority' => 10,
	);
	return $tabs;
}

run_neon_sign_customize();