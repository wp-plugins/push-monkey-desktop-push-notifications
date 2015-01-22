<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_debugger.php' );

/**
 * API Client
 */
class PushMonkeyClient {

	public $endpointURL;
	public $registerURL;

	/* Public */

	/**
 	* Calls the sign in endpoint with either an Account Key
 	* or with an API Token + API Secret combo.
	*
	* Returns false on WP errors.
	* Returns an object with the returned JSON.
 	*/
	public function sign_in( $account_key, $api_token, $api_secret ) {

		$sign_in_url = $this->endpointURL . '/clients/api/sign_in';
		$args = array( 'body' => array( 
			'account_key' => $account_key, 
			'api_token' => $api_token, 
			'api_secret' => $api_secret
			) );
		$response = wp_remote_post( $sign_in_url, $args );
		if ( is_wp_error( $response ) ) {
			
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body );
			$this->d->debug(print_r($output, true));
			return $output;				
		}
		return false;
	}
	
	public function get_stats( $account_key ) {

		$stats_api_url = $this->endpointURL . '/stats/api';
		$args = array( 'body' => array( 'account_key' => $account_key ) );
		$response = wp_remote_post( $stats_api_url, $args );
		if( is_wp_error( $response ) ) {

			$this->d->debug( $response->get_error_message() );
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body ); 
			$this->d->debug( print_r( $output, true ) );
			return $output;
		}
		return false;
	}

	public function get_website_push_ID( $account_key ) {

		$url = $this->endpointURL . '/clients/get_website_push_id';
		$args = array( 'body' => array( 'account_key' => $account_key ) );

		$response = wp_remote_post( $url, $args );

		if( is_wp_error( $response ) ) {

			return ( object ) array( 'error' => $response->get_error_message() );
		} 
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body ); 
		return $output;
	}

	public function send_push_notification( $account_key, $title, $body, $url_args, $custom ) {

		$url = $this->endpointURL . '/push_message';
		$args = array( 'body' => array( 
			'account_key' => $account_key,
			'title' => $title,
			'body' => $body, 
			'url_args' => $url_args,
		) );
		if ( $custom ) {

			$args['body']['custom'] = true;
		}

		$response = wp_remote_post( $url, $args );
		if( is_wp_error( $response ) ) {

			$this->d->debug('send_push_notification '.$response->get_error_message());
		}
	}

	public function get_plan_name( $account_key ) {

		$url = $this->endpointURL . '/clients/api/get_plan_name';
		$args = array( 'body' => array( 'account_key' => $account_key ) );

		$response = wp_remote_post( $url, $args );

		if( is_wp_error( $response ) ) {

			return ( object ) array( 'error' => $response->get_error_message() );
		} 
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body ); 
		if ( isset( $output->error ) ) {
			
			$this->d->debug('get_plan_name: ' . $output->error);
			return $output->error;
		} else {

			return $output;
		}
		return '';
	}

	/* Private */

	function __construct( $endpoint_url ) {

		$this->endpointURL = $endpoint_url;
		$this->registerURL = $endpoint_url.'/register';
		$this->d = new PushMonkeyDebugger();
	}
}
