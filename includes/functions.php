<?php
if ( ! function_exists( 'sponsors' ) ) {
	/**
	 * Function sponsor
	 *
	 * @return object
	 */
	function sponsors() {
		$path = plugin_dir_path( SPONSORS_FILE );

		// Prepare the basepath.
		$home_url              = get_home_url();
		$parsed                = wp_parse_url( $home_url );
		$base_path             = ( is_array( $parsed ) && isset( $parsed['path'] ) ) ? $parsed['path'] : '/';
		$base_path             = rtrim( $base_path, '/' ) . '/';
		$bootstrap_admin_pages = array( 'sponsor', 'biodrop-settings' );

		// Get current URL.
		$current_url = $home_url . '/' . substr( get_server( 'REQUEST_URI' ), strlen( $base_path ) );

		$info = array(
			'path'                   => $path,
			'url'                    => plugin_dir_url( SPONSORS_FILE ),
			'current_url'            => $current_url,
			'assets'                 => SPONSORS_ASSETS,
			'basename'               => plugin_basename( SPONSORS_FILE ),
			'basepath'               => $base_path,
			'alowed_bootstrap_pages' => $bootstrap_admin_pages,
			'version'                => SPONSORS_VERSION,
			'nonce_action'           => 'sponsor_nonce_action',
			'nonce'                  => '_sponsor_nonce',
			'template_path'          => apply_filters( 'sponsor_template_path', 'sponsor/' ),
		);

		return (object) $info;
	}
}
