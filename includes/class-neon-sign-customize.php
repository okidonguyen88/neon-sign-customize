<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://sample.com
 * @since      1.0.0
 *
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Neon_Sign_Customize
 * @subpackage Neon_Sign_Customize/includes
 * @author     Okido Nguyễn <okidonguyen88@gmail.com>
 */
class Neon_Sign_Customize
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Neon_Sign_Customize_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('NEON_SIGN_CUSTOMIZE_VERSION')) {
			$this->version = NEON_SIGN_CUSTOMIZE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'neon-sign-customize';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Neon_Sign_Customize_Loader. Orchestrates the hooks of the plugin.
	 * - Neon_Sign_Customize_i18n. Defines internationalization functionality.
	 * - Neon_Sign_Customize_Admin. Defines all hooks for the admin area.
	 * - Neon_Sign_Customize_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-neon-sign-customize-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-neon-sign-customize-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-neon-sign-customize-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-neon-sign-customize-public.php';

		$this->loader = new Neon_Sign_Customize_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Neon_Sign_Customize_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Neon_Sign_Customize_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Neon_Sign_Customize_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_neon_sign_menu');

		// custom price
		$this->loader->add_action('woocommerce_product_data_panels', $plugin_admin, 'add_custom_fields');
		$this->loader->add_action('woocommerce_process_product_meta_neon_sign', $plugin_admin, 'save_custom_fields');
		$this->loader->add_action('woocommerce_before_calculate_totals', $plugin_admin, 'calculate_custom_price_total');
		$this->loader->add_filter('woocommerce_get_item_data', $plugin_admin, 'display_custom_item_data', 10, 2);


		// Ajax manage fonts
		// add
		$this->loader->add_action('wp_ajax_add_new_font', $plugin_admin, 'add_new_font');
		$this->loader->add_action('wp_ajax_nopriv_add_new_font', $plugin_admin, 'add_new_font');
		// delete
		$this->loader->add_action('wp_ajax_delete_font', $plugin_admin, 'delete_font');
		$this->loader->add_action('wp_ajax_nopriv_delete_font', $plugin_admin, 'delete_font');





	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Neon_Sign_Customize_Public($this->get_plugin_name(), $this->get_version());

		//load script
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_filter('template_include', $plugin_public, 'load_neon_sign_template', 999);

		//update item in cart		
		$this->loader->add_action('wp_ajax_update_custom_data_in_cart', $plugin_public, 'update_custom_data_in_cart_ajax');
		$this->loader->add_action('wp_ajax_nopriv_update_custom_data_in_cart', $plugin_public, 'update_custom_data_in_cart_ajax');


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Neon_Sign_Customize_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}


}