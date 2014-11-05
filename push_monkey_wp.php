<?php
/*
 * Plugin Name: Push Monkey Wordpress
 * Plugin URI: http://wordpress.org/plugins/hello-dolly/
 * Author: moWOW Studios
 * Description: Enable Safari 7 Push Notifications (Mac OS Mavericks) on each new post published.
 * Version: 0.9.1
 * Author URI: http://www.getpushmonkey.com/?source=plugin
 * License: GPL2
 */

/*  
Push Monkey - Desktop Push Notifications for WordPress
Copyright (C) 2014 moWOW Studios (email : hey@mowowstudios.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/* CHANGELOG:
 *
 * v 0.9.1
 * - replace iframes with API calls
 * - reorganize code in classes
 *
 * v 0.9
 * - add confirmation for Custom Push Notification widget
 * - minor code cleanup
 *
 * v 0.8.8
 * - add option to disable push notifications while editing a Post
 * - add push notification preview while editing a Post
 * - moved settings page to top leve
 * - test on WP 4.0
 *
 * v 0.8.7
 * - add dashboard widget for custom notifications
 *
 * v 0.8.6
 * - add menu page for configuring this plugin.
 * - add option to exclude certain post categories 
 *   from sending push notifications
 * - visual tweaks
 *
 * v 0.8.5
 * - move the endpoint URL to a more generic location.
 *
 * v 0.8.4
 * - limit $post->post_type to 'post', to filter out pages.
 * - test on WP 3.9.
 * - add uninstall.php
 */


if ( ! defined( 'WPINC' ) ) {

    die;
}

require_once( dirname( __FILE__ ) . '/push_monkey_functions.php' );

function run_push_monkey() {

	$push_monkey = new PushMonkey();
	$push_monkey->run();
}

run_push_monkey();