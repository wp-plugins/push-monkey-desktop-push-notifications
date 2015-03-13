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
class PushMonkeyBanner extends PushMonkeyModel {

	/* Public */

	const BANNER_TEXT_KEY = 'push_monkey_banner_text';
	const BANNER_COLOR_KEY = 'push_monkey_banner_color';
	const DEFAULT_TEXT = 'Stay up to date with {% site_name %}{% separator %} Open this website in Safari to sign up for Desktop Push Notifications';
	const DEFAULT_COLOR = '#2FCC70';
	const BANNER_POSITION_KEY = 'push_monkey_banner_position_key';
	const BANNER_DISABLED_ON_HOME = 'push_monkey_banner_disabled_home';

	public function set_raw_text( $text ) {

		update_option( self::BANNER_TEXT_KEY, $text );
	}

	public function get_raw_text() {

		$text = get_option( self::BANNER_TEXT_KEY );
		if ( ! $text ) {
			
			update_option( self::BANNER_TEXT_KEY, self::DEFAULT_TEXT );
			$text = self::DEFAULT_TEXT;
		}
		return $text;
	}

	public function get_text( $website_name ) {

		return $this->process_raw_text( $this->get_raw_text(), $website_name );
	}

	public function set_color( $color ) {

		update_option( self::BANNER_COLOR_KEY, $color );
	}

	public function get_color() {

		$color = get_option( self::BANNER_COLOR_KEY );
		if ( ! $color ) {
			
			update_option( self::BANNER_COLOR_KEY, self::DEFAULT_COLOR );
			$color = self::DEFAULT_COLOR;
		}
		return $color;
	}

	public function save_position( $value ) {

		update_option( self::BANNER_POSITION_KEY, $value );
	}

	public function get_position() {

		$val = get_option( self::BANNER_POSITION_KEY );

		if ( ! $val ) {

			$val = 'top';
			$this->save_position( $val );
		}
		return $val;
	}

	public function set_disabled_on_home( $val ) {

		update_option( self::BANNER_DISABLED_ON_HOME, $val);
	}

	public function get_disabled_on_home() {

		$val = get_option( self::BANNER_DISABLED_ON_HOME );
		if ( ! $val ) {
			
			$val = false;
			$this->set_disabled_on_home( $val );
		}
		return $val;
	}

	public function uninstall() {

		delete_option( self::BANNER_TEXT_KEY );
		delete_option( self::BANNER_COLOR_KEY );
		delete_option( self::DEFAULT_TEXT );
		delete_option( self::DEFAULT_COLOR );
		delete_option( self::BANNER_POSITION_KEY );
		delete_option( self::BANNER_DISABLED_ON_HOME );
	}

	/* Private */

	function __construct( ) {

		$this->d = new PushMonkeyDebugger();
	}

	function process_raw_text( $text, $website_name ) {

		$pos = $this->get_position();
		$new_text = '';
		if ( $pos == 'top' || $pos == 'bottom' ) {
			
			$new_text = str_replace( "{% site_name %}", "<strong>" . $website_name . "</strong>", $text );
			$new_text = str_replace( "{% separator %}", " &#8212;", $new_text );
		} else {

			$new_text = str_replace( "{% site_name %}", "<strong>" . $website_name . "</strong>", $text );
			$new_text = str_replace( "{% separator %}", "<br /><br />", $new_text );			
		}
		return $new_text;
	}
}