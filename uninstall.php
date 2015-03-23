<?php
/**
* Push Monkey Uninstall
*
* Uninstalling Push Monkey options.
*/

// If uninstall not called from Wordpress exit 
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {

	exit();
}

require_once( plugin_dir_path( __FILE__ ) . 'push-monkey.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class_push_monkey_client.php' );
require_once( plugin_dir_path( __FILE__ ) . 'models/class_push_monkey_banner.php' );
require_once( plugin_dir_path( __FILE__ ) . 'models/class_push_monkey_notification_config.php' );
require_once( plugin_dir_path( __FILE__ ) . 'models/class_push_monkey_review_notice.php' );

delete_option( PushMonkey::ACCOUNT_KEY_KEY );
delete_option( PushMonkey::EMAIL_KEY );
delete_option( PushMonkey::EXCLUDED_CATEGORIES_KEY );
delete_option( PushMonkey::POST_TYPES_KEY );
delete_option( PushMonkey::USER_SIGNED_IN );
delete_option( PushMonkey::WEBSITE_NAME_KEY );
delete_option( PushMonkey::WEBSITE_PUSH_ID_KEY );
delete_option( PushMonkeyClient::PLAN_NAME_KEY );

$banner = new PushMonkeyBanner();
$banner->uninstall();

$notif_config = new PushMonkeyNotificationConfig();
$notif_config->uninstall();

$review_notice = new PushMonkeyReviewNotice();
$review_notice->uninstall();
