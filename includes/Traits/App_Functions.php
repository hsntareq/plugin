<?php
/**
 * The template for displaying the footer
 */

namespace Sponsors\Traits;

trait App_Functions {

	/**
	 * Function get_request
	 *
	 * @param  string $key is a string.
	 * @return bool
	 */
	public function get_request( $key ) {
		return isset( $_POST[ $key ] ) ? $_POST[ $key ] : false;
	}

	/**
	 * Function has_error
	 *
	 * @param  string $key is a string.
	 * @return bool
	 */
	public function has_error( $key ) {
		return isset( $this->errors[ $key ] ) ? true : false;
	}

	/**
	 * Function get_error
	 *
	 * @param  mixed $key for error.
	 * @return bool
	 */
	public function get_error( $key ) {
		return isset( $this->errors[ $key ] ) ? $this->errors[ $key ] : false;
	}

}

