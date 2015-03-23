<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

class PushMonkeyReviewNoticeController { 

	/* Public */

	const REVIEW_NOTICE_DISMISS_KEY = 'push_monkey_review_dismiss';
	private $is_saas = false;

	public function render() {

		parse_str( $_SERVER['QUERY_STRING'], $params );
		$query_string = '?' . http_build_query( array_merge( $params, array( self::REVIEW_NOTICE_DISMISS_KEY => '1' ) ) );

		$review_url = "https://wordpress.org/support/view/plugin-reviews/push-monkey-desktop-push-notifications";
		if ( ! $this->is_saas ) {
			
			$review_url = "http://codecanyon.net/item/push-monkey-native-desktop-push-notifications/10543634";
		}
		$icon_src = plugins_url( '../img/review-notice-icon.png', plugin_dir_path( __FILE__ ) );
		require_once( plugin_dir_path( __FILE__ ) . '../../templates/messages/push_monkey_review_notice.php' );
	}

	/* Private */
	function __construct( $is_saas ) {

		$this->is_saas = $is_saas;
	}
}