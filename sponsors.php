<?php
/**
 * Plugin Name: Sponsors
 * */

final class Sponsors {
	const version = '1.0';
	private $assets;
	private $utils;

	/**
	 * Class __construct.
	 */
	private function __construct() {
		$this->define_constants();
		// $this->assets = new Sponsors\Classes\Assets();
		// $this->utils = new Sponsors\Classes\Utils();

		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		add_action( 'init', array( $this, 'update_anyone_can_register' ) );
		add_action( 'init', array( $this, 'sponsor_portal_language_load' ) );
		add_filter( 'login_init', array( $this, 'sponsor_registration_redirect' ) );
	}
	/**
	 * Define the required constants.
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'SPONSORS_VERSION', self::version );
		define( 'SPONSORS_FILE', __FILE__ );
		define( 'SPONSORS_PATH', __DIR__ );
		define( 'SPONSORS_URL', plugins_url( '', SPONSORS_FILE ) );
		define( 'SPONSORS_ASSETS', SPONSORS_URL . '/assets' );
		define( 'SPONSORS_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}


	/**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	public function init_plugin() {
		if ( is_admin() ) {
			new Sponsors\Admin();
		} else {
			new Sponsors\Frontend();
		}
	}
	/**
	 * Do stuff upon plugin activation
	 *
	 * @return void
	 */
	public function activate() {
		$installer = new \Sponsors\Installer();
		$installer->run();
	}

	/**
	 * Initializes a singleton instance
	 *
	 * @return \Sponsors
	 */
	public static function init() {

		static $instance = false;
		if ( ! $instance ) {
			$instance = new self();
		}
		return $instance;

	}
}


if ( ! function_exists( 'sponsors' ) ) {
	/**
	 * Initializing the main plugin
	 *
	 * @return \Sponsors
	 */
	function initiate_plugin() {
		return Sponsors::init();
	}
}

// kick-off the plugin.
initiate_plugin();
require_once 'includes/functions.php';
