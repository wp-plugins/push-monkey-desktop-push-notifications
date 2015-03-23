<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . './class_push_monkey_model.php' );
require_once( plugin_dir_path( __FILE__ ) . '../includes/class_push_monkey_debugger.php' );
require_once( plugin_dir_path( __FILE__ ) . '../includes/class_push_monkey_date_time.php' );

/**
 * Banner model to set and get properties related to the CTA Banner.
 */
class PushMonkeyReviewNotice extends PushMonkeyModel {

	/* Public */

	const SIGN_IN_DATE_KEY = 'push_monkey_sign_in_date';
	const DISMISS_KEY = 'push_monkey_review_dismiss_key';
	const DATE_FORMAT = 'Y-m-d H:i:s';

	public function setSignInDate( $date ) {

		$new_time = $date->format( self::DATE_FORMAT );
		update_option( self::SIGN_IN_DATE_KEY, $new_time );
	}

	public function getSignInDate() {

		$date_string = get_option( self::SIGN_IN_DATE_KEY );
		if( ! $date_string ) {

			return new DateTime();
		}
		$stored_time = PushMonkeyDateTime::createFromFormat( self::DATE_FORMAT, $date_string );
		return $stored_time;
	}

	public function setDismiss( $dismiss ) {

		update_option( self::DISMISS_KEY, $dismiss );
	}

	public function getDismiss() {

		return get_option( self::DISMISS_KEY );
	}

	public function canDisplayNotice() {

		if ( $this->getDismiss() ) {

			return false;
		}
		$now = new DateTime();
		$stored_date = $this->getSignInDate();
		$interval = $now->getTimestamp() - $stored_date->getTimestamp();
		if ( $interval >= (60 * 60 * 24 * 7) ) {

			return true;
		}
		return false;
	}

	public function uninstall() {

		delete_option( self::SIGN_IN_DATE_KEY );
		delete_option( self::DISMISS_KEY );
	}

	/* Private */

	/**
	 * Constructor that initializes the Push Monkey class.
	 */
	function __construct() {

		$this->d = new PushMonkeyDebugger();
	}
}