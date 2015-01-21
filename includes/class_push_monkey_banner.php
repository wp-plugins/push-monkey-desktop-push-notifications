<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_debugger.php' );

class PushMonkeyBanner {

	/* Public */

	const BANNER_POSITION_KEY = 'push_monkey_banner_position_key';

	public function save_position( $value ) {

		update_option( self::BANNER_POSITION_KEY, $value );
	}

	public function get_position() {

		return get_option( self::BANNER_POSITION_KEY );
	}

	/* Private */

	function __construct() {

		$this->d = new PushMonkeyDebugger();
	}
}