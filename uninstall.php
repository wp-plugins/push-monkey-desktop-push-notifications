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

require_once( dirname( __FILE__ ) . '/push_monkey_wp.php' );

delete_option( PushMonkey::ACCOUNT_KEY_KEY );
delete_option( PushMonkey::WEBSITE_NAME_KEY );
delete_option( PushMonkey::WEBSITE_PUSH_ID_KEY );
delete_option( PushMonkey::EXCLUDED_CATEGORIES_KEY );
