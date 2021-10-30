<?php
namespace Sponsors\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Assets
 */
class Assets {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_init', array( $this, 'admin_bootstrap_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_meta_data' ) );
	}

	public function admin_scripts() {
		wp_enqueue_style( 'sponsor-admin', sponsors()->url . 'assets/css/sponsors.css', array(), sponsors()->version );

	}

	/**
	 * Function to enqueue admin_scripts
	 *
	 * @return void
	 */
	public function admin_bootstrap_init() {

		if ( in_array( get_request( 'page' ), sponsors()->alowed_bootstrap_pages ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_bootstrap_style' ) );
		}
	}

	public function admin_bootstrap_style() {
		wp_enqueue_style( 'sponsor-bootstrap', sponsors()->url . 'assets/css/bootstrap.min.css', array(), sponsors()->version );
	}


	public function load_meta_data() {
		// Localize scripts
		$localize_data = apply_filters( 'sponsor_localize_data', $this->get_default_localized_data() );
		wp_localize_script( 'sponsor-admin', '_appObject', $localize_data );
		wp_localize_script( 'sponsor-builder', '_appObject', $localize_data );

		// Inline styles
		// wp_add_inline_style( 'sponsor-frontend', $this->load_color_palette() );
		// wp_add_inline_style( 'sponsor-admin', $this->load_color_palette() );
	}


	/**
	 * Function get_default_localized_data
	 *
	 * @return array
	 */
	private function get_default_localized_data() {
		return array(
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'home_url'         => get_home_url(),
			'base_path'        => sponsors()->basepath,
			'sponsor_url'      => sponsors()->url,
			'nonce_key'        => sponsors()->nonce,
			sponsors()->nonce  => wp_create_nonce( sponsors()->nonce_action ),
			'loading_icon_url' => get_admin_url() . 'images/wpspin_light.gif',
		);
	}

}
