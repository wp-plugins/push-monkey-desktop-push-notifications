<?php
/*
 * Plugin Name: Push Monkey Native Desktop Push Notifications for WordPress
 * Plugin URI: https://wordpress.org/plugins/push-monkey-desktop-push-notifications/
 * Author: moWOW Studios
 * Description: Engage & delight your readers with Desktop Push Notifications - a new subscription channel directly to the desktop of your readers, without them installing any app. To start sending, just go to <a href="https://www.getpushmonkey.com?source=plugin_desc" target="_blank">getpushmonkey.com</a>, register and receive your Account Key. Currently Safari Push Notifications are active only under OSX Mavericks, Yosemite or newer (with Safari 7+). Firefox and Chrome soon to come.
 * Version: 0.9.9.2
 * Stable Tag: 0.9.9.2
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
 * v 0.9.9.2
 * - bugfixing
 *
 * v 0.9.9.1
 * - bugfixing
 *
 * v 0.9.9
 * - on-boarding workflow overhaul: now easier than ever. No more waiting. Account Key what? 
 * - layout update
 * - code cleanup
 * - bugfixing
 *
 * v 0.9.8.2
 * - bugfixing
 *
 * v 0.9.8
 * - bugfixing
 *
 * v 0.9.7
 * - bugfixing
 * - UI updates
 * - typos fixed
 *
 * v 0.9.6
 * - bugfixing
 *
 * v 0.9.5
 * - fix HTML tags in preview
 * - fix conflict with TinyMCE Advanced
 *
 * v 0.9.4
 * - fix some tags that used required the PHP setting short_open_tag to be on
 *
 * v 0.9.3
 * - fix double usage of title
 * - fix possible CSS overwrite
 *
 * v 0.9.2
 * - prepare assets and folder structure for WordPress.org SVN
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

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'includes/class_push_monkey_core.php' );

function run_push_monkey() {

	$push_monkey = new PushMonkey();
	$push_monkey->run();
}

run_push_monkey();
