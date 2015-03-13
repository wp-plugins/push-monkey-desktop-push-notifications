<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . './class_push_monkey_model.php' );
require_once( plugin_dir_path( __FILE__ ) . '../includes/class_push_monkey_debugger.php' );

/**
 * Banner model to set and get properties related to the CTA Banner.
 */
class PushMonkeyNotificationConfig extends PushMonkeyModel {

	/* Public */

	const NOTIF_FORMAT_KEY = 'push_monkey_notif_format';
	const DEFAULT_FORMAT = 'standard';
	const CUSTOM_TEXT_KEY = 'push_monkey_notif_custom_text';
	const DEFAULT_TEXT = 'Breaking News';

	public function set_format( $format ) {

		update_option( self::NOTIF_FORMAT_KEY, $format );
	}

	public function get_format() {

		$format = get_option( self::NOTIF_FORMAT_KEY );
		if ( !$format ) {
			
			update_option( self::NOTIF_FORMAT_KEY, self::DEFAULT_FORMAT );
			$format = self::DEFAULT_FORMAT;

		}
		return $format;
	}

	public function set_custom_text( $text ) {

		update_option( self::CUSTOM_TEXT_KEY, $text );
	}

	public function get_custom_text() {

		$text = get_option( self::CUSTOM_TEXT_KEY );
		if ( ! $text ) {

			update_option( self::CUSTOM_TEXT_KEY, self::DEFAULT_TEXT );
			$text = self::DEFAULT_TEXT;
		}
		return $text;
	}

	public function is_custom_text() {

		return ( $this->get_format() != self::DEFAULT_FORMAT );
	}

	public function uninstall() {

		delete_option( self::NOTIF_FORMAT_KEY );
		delete_option( self::CUSTOM_TEXT_KEY );
	}

	/* Private */

	function __construct( ) {

		$this->d = new PushMonkeyDebugger();
	}

}