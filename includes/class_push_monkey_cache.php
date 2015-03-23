<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_date_time.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_debugger.php' );

/**
 * Cache Manager that uses WordPress get_option and update_option.
 */
class PushMonkeyCache {

	/* Public */

	const DATE_FORMAT = 'Y-m-d H:i:s';

	/**
	 * Store a certain value into a key, for a given amount of minutes.
	 * @param string $key 
	 * @param mixed $value 
	 * @param integer $minutes 
	 */
	public function store( $key, $value, $minutes = 30 ) {

		$store = array();
		$now = new DateTime();
		$now->modify( "+{$minutes} minutes" );
		$new_time = $now->format( self::DATE_FORMAT );
		$store['expiration'] = $new_time;
		$store['value'] = $value;
		update_option( $key, $store );
	}

	/**
	 * Get a value from cache, if available.
	 * @param string $key 
	 * @return mixed; returns false if cache is expired or nothing has been cached. Otherwise, 
	 * it returns the cached value.
	 */
	public function get( $key ) {

		$store = get_option( $key, false );
		if ( ! $store ) {
			
			return false;
		}
		$now = new PushMonkeyDateTime();
		$stored_time = PushMonkeyDateTime::createFromFormat( self::DATE_FORMAT, $store['expiration'] );
		$interval = $stored_time->getTimestamp() - $now->getTimestamp();
		if ( $interval < 0 ) {

			return false;
		}
		return $store['value'];
	}

	/* Private */

	function __construct() {

		$this->d = new PushMonkeyDebugger();
	}
}