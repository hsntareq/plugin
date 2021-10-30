<?php


namespace Sponsor;

use Sponsor\Admin\SponsorForm;

class Installer {

	public function run() {
		$this->add_version();
		$this->create_tables();
		$this->add_roles();
	}

	public function add_roles() {
		add_role(
			'sponsor',
			'Sponsor',
			array(
				'read'           => true,
				'delete_posts'   => false,
				'manage_options' => true,
				'manage_sponsor' => true,
			),
		);
	}
	/**
	 * Add_version
	 *
	 * @return void
	 */
	public function add_version() {
		$installed = get_option( 'sp_installed' );
		if ( ! $installed ) {
			update_option( 'sp_installed', time() );
		}
		update_option( 'sp_version', SPONSOR_VERSION );
	}

	public function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$schema_protocol = "CREATE TABLE `{$wpdb->prefix}sp_protocol` (
			`id` int unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
			`user_id` bigint NOT NULL,
			PRIMARY KEY (`id`)
		  ) $charset_collate";

		$schema_sponsors = "CREATE TABLE `{$wpdb->prefix}sp_sponsors` (
			`id` int unsigned NOT NULL AUTO_INCREMENT,
			`username` varchar(100) DEFAULT NULL,
			`protocol_id` bigint NOT NULL,
			`sponsor_id` bigint NOT NULL,
			`email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
			`current_status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
			`last_activity` varchar(100) NOT NULL DEFAULT '',
			`created_at` datetime DEFAULT NULL,
			PRIMARY KEY (`id`)
			) $charset_collate";

		$schema_protocol_status = "CREATE TABLE `{$wpdb->prefix}sp_protocol_status` (
			`id` int unsigned NOT NULL AUTO_INCREMENT,
			`protocol_id` int NOT NULL,
			`data_status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
			`created_at` datetime NOT NULL,
			`created_by` int NOT NULL,
			PRIMARY KEY (`id`)
		  )  $charset_collate";

		$schema_status = "CREATE TABLE `{$wpdb->prefix}sp_status` (
			`id` int unsigned NOT NULL AUTO_INCREMENT,
			`username` varchar(100) DEFAULT NULL,
			`protocol_id` bigint NOT NULL,
			`sponsor_id` bigint NOT NULL,
			`email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
			`current_status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
			`last_activity` varchar(100) NOT NULL DEFAULT '',
			`created_at` datetime DEFAULT NULL,
			PRIMARY KEY (`id`)
			) $charset_collate";

		if ( ! function_exists( 'dbdelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		dbDelta( $schema_protocol );
		dbDelta( $schema_protocol_status );
		dbDelta( $schema_sponsors );
		dbDelta( $schema_status );
	}
}
