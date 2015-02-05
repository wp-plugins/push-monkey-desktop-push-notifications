<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_debugger.php' );


/**
 * Cache Manager that uses WordPress get_option and update_option.
 */
class PushMonkeyCache {

	/* Public */

	const DATE_FORMAT = 'Y-m-d H:i:s';

	public function store( $key, $value, $minutes = 15 ) {

		$store = array();
		$now = new DateTime();
		$now->add( new DateInterval( "PT{$minutes}M" ) );
		$new_time = $now->format( self::DATE_FORMAT );
		$store['expiration'] = $new_time;
		$store['value'] = $value;
		update_option( $key, $store );
		$this->d->debug('store for key: ' . $key);
	}

	public function get( $key ) {

		$store = get_option( $key, false );
		if ( ! $store ) {
			
			$this->d->debug('nothing stored for key: ' . $key);
			return false;
		}
		$now = new DateTime();
		$stored_time = DateTime::createFromFormat( self::DATE_FORMAT, $store['expiration'] );
		$this->d->debug('now: ' .$now->format( self::DATE_FORMAT ));
		$this->d->debug('expires at: '.$stored_time->format(self::DATE_FORMAT));
		$interval = $stored_time->getTimestamp() - $now->getTimestamp();
		$this->d->debug('seconds diff: ' . $interval);
		if ( $interval < 0 ) {

			$this->d->debug('expired for key: ' . $key);			
			return false;
		}
		$this->d->debug('return for key: ' . $key);
		return $store['value'];
	}

	/* Private */

	function __construct( ) {

		$this->d = new PushMonkeyDebugger();
	}
}