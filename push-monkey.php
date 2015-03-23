<?php
/*
 * Plugin Name: Push Monkey Native Desktop Push Notifications for WordPress
 * Plugin URI: https://wordpress.org/plugins/push-monkey-desktop-push-notifications/
 * Author: moWOW Studios
 * Description: Engage & delight your readers with Desktop Push Notifications - a new subscription channel directly to the desktop of your readers, without them installing any app. To start sending, just go to <a href="https://www.getpushmonkey.com?source=plugin_desc" target="_blank">getpushmonkey.com</a>, and register. Currently Safari Push Notifications are active only under OSX Mavericks, Yosemite or newer (with Safari 7+). Firefox and Chrome soon to come.
 * Version: 0.9.9.9.4
 * Stable Tag: 0.9.9.9.4
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
 * v 0.9.9.9.4
 * - bugfixing: scheduled posts now send push notifications again.
 *
 * v 0.9.9.9.3
 * - update stats layout
 * - added new "Notification Format" feature. You can now configure what the notification content is. Currently two options are available: Post title and Post body OR 
 * custom title and post title.
 *
 * v 0.9.9.9.2
 * - add option to disable CTA banners on homepage only, while being enabled on all other pages
 *
 * v 0.9.9.9.1
 * - fix PHP 5.2 compatibility
 *
 * v 0.9.9.9.0
 * - language adjusting
 *
 * v 0.9.9.8.9
 * - allow CTA Banner customisation of color and text
 *
 * v 0.9.9.8.8
 * - caching update
 * - update Sign Up screen
 *
 * v 0.9.9.8.7
 * - bugfixing
 *
 * v 0.9.9.8.6
 * - bugfixing
 *
 * v 0.9.9.8.5
 * - bugfixing
 *
 * v 0.9.9.8.4
 * - remove shortcodes from Push Notification
 * - fix double escaping of Custom Push Notifications
 * - add Welcome Notice
 * - fix notification before trial expires
 *
 * v 0.9.9.8.3
 * - display notification before trial plan expires
 *
 * v 0.9.9.8.2
 * - display notification when the trial plan expired
 *
 * v 0.9.9.8.1
 * - bugfix
 *
 * v 0.9.9.8
 * - fewer requests to Push Monkey API to improve page load speed
 * - allow websites to upgrade the price plan 
 * - show notification for expired plans
 *
 * v 0.9.9.7
 * - CSS bugfixing for some WP Themes
 *
 * v 0.9.9.6
 * - banner improvements: remember users who disabled the banner, improved animations
 * - you can now filter which custom post types send Desktop Push Notifications
 *
 * v 0.9.9.5
 * - more advanced granular filtering. You can now choose which custom post types send Safari Push Notifications
 *
 * v 0.9.9.4
 * - bugfixing
 *
 * v 0.9.9.3
 * - bugfixing
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

/**
 * Main function that creates and 
 * runs Push Monkey.
 */
function run_push_monkey() {

	$push_monkey = new PushMonkey();
	$push_monkey->run();
}

run_push_monkey();