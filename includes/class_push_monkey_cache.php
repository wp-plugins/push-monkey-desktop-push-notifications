<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_debugger.php' );

/**
 * Extend DateTime to ensure PHP 5.2 compatibility
 */
class PushMonkeyDateTime extends DateTime {

    public static function createFromFormat( $format, $time, $timezone = null ) {

        if( ! $timezone ) {

        	$timezone = new DateTimeZone( date_default_timezone_get() );
        }
        if ( method_exists( 'DateTime', 'createFromFormat' ) ) {
        	
        	return parent::createFromFormat( $format, $time, $timezone );
        }
        return new PushMonkeyDateTime( date( $format, strtotime( $time ) ), $timezone );
    }

    public function getTimestamp() {

         return method_exists( 'DateTime', 'getTimestamp' ) ? 

             parent::getTimestamp() : $this->format( 'U' );
    }
}

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
		$this->d->debug2("pre modify: ", print_r( $now, true ) );
		$now->modify( "+{$minutes} minutes" );
		$this->d->debug2("post modify: ", print_r( $now, true ) );
		$new_time = $now->format( self::DATE_FORMAT );
		$store['expiration'] = $new_time;
		$store['value'] = $value;
		update_option( $key, $store );
		$this->d->debug('store for key: ' . $key);
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
			
			$this->d->debug('nothing stored for key: ' . $key);
			return false;
		}
		$now = new PushMonkeyDateTime();
		$stored_time = PushMonkeyDateTime::createFromFormat( self::DATE_FORMAT, $store['expiration'] );
		$this->d->debug( 'now: ' .$now->format( self::DATE_FORMAT ) );
		$this->d->debug( 'expires at: '.$stored_time->format( self::DATE_FORMAT ) );
		$interval = $stored_time->getTimestamp() - $now->getTimestamp();
		$this->d->debug( 'seconds diff: ' . $interval );
		if ( $interval < 0 ) {

			$this->d->debug( 'expired for key: ' . $key );			
			return false;
		}
		$this->d->debug( 'return for key: ' . $key );
		return $store['value'];
	}

	/* Private */

	function __construct() {

		$this->d = new PushMonkeyDebugger();
	}
}