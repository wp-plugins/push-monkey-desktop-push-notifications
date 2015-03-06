<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_debugger.php' );
require_once( plugin_dir_path( __FILE__ ) . '../models/class_push_monkey_banner.php' );

/**
 * Class that handles AJAX requests
 */
class PushMonkeyAjax {

	/* Public */

	/**
	 * Handle the AJAX request for the banner position form.
	 */
	public function banner_position_changed() {

		$value = $_POST['value'];
		$this->banner->save_position( $value );
		echo $value;
		wp_die();
	}

	/* Private */

	function __construct() {

		$this->banner = new PushMonkeyBanner();
	}
}