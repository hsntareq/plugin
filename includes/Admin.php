<?php

namespace Sponsor;

/**
 * Admin
 */
class Admin {

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$protocol = new Admin\Sponsors();
		$this->dispatch_actions( $protocol );
		new Admin\Menu( $protocol );
	}

	/**
	 * Function to dispatch_actions
	 *
	 * @return void
	 */
	public function dispatch_actions( $protocol ) {
		add_action( 'admin_init', array( $protocol, 'form_handler' ) );
		add_action( 'admin_post_sp-po-delete-action', array( $protocol, 'delete_protocol' ) );
	}
}
