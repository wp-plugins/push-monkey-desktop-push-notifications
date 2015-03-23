=== Push Monkey - Native Desktop Push Notifications for WordPress ===

Contributors: mowow
Tags: push notifications, push messages, Safari, Apple, Mavericks, Mac, push, notifications, OSX, desktop notifications, subscribe, mac users, native push message, branded notifications, new post, new content, configurable, filters, subscribe via push, subscribe via notifications

Requires at least: 3.8
Tested up to: 4.1
Stable tag: 0.9.9.9.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Engage & delight your readers with Safari Push Notifications sent directly to their desktops, while enjoying clear stats and seamless integration.

== Description ==

Push Monkey lets you send push notifications directly to your readers’ desktops when new content is fresh from the oven.

![Intro image](https://dl.dropboxusercontent.com/u/1618599/cdn/push-monkey/post-push-notification-announce-gif.gif)

= Why Push Monkey? =

= Increased Engagement =

Readers can be informed about your content at all times: when reading other websites or while working in other apps, with the browser closed. Even when the computer is not active - it displays all missed notifications the moment it wakes up.

= Native outreach =

Your readers don't have to install any additional apps or plugins. They just accept receiving notifications from your website and presto!

= Granular Filtering =

Easily decide and control for what type of content to send out notifications. Filter by custom post type and by category for standard posts. No content clutter around here!

= Automatic =

By default, push notifications are sent automatically when you publish a new post. This workflow doesn't sound cool? No worries, we have a more granular filtering and custom push notifications.

= Statistics =

The plugin provides essential usage and engagement data available directly in your Wordpress dashboard so you can create the best experience for your subscribers.

= Visually branded for your needs =

Push Monkey is fully white-label. Your readers don't get to see any crazy bananas from us. Your logo, your text, your rules!

= Raised awareness for Mac readers not using Safari =

Even if your readers visit your website using a different browser than Safari, they will see a banner letting them know that they can subscribe to Desktop Push Notifications.

= Non-intrusive =

Push notifications reach a sweet balance between informative and non-intrusive. They appear above all other windows and if not clicked on, disappear after a few seconds.
  
= Super fast setup. Zero coding required =
Fast and easy to set up and no coding or API configuration required. The monkeys do all the pushing so you can focus on your content and readers.

= No Monkey Business =

Sending notifications on a big and reliable scale is not trivial, but our solid high-availability cloud back-end has already delivered millions of notifications...and it's not stopping here. 


**NOTE:**
Currently only Safari from OS X Mavericks (and newer) is supported. Firefox and Chrome are coming soon.

This plugin connects the WordPress API with the Push Monkey server (getpushmonkey.com) - server which actually delivers the notifications. An account on getpushmonkey.com is required. Don't worry, you can setup an account faster than you can say ba-na-na.

== Installation ==

= Minimum Requirements =

* WordPress 3.8 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

1. Upload the Push Monkey plugin to your blog (Or install it via the "Add New Plugin" option in your WordPress dashboard)

2. Activate it

3. Sign in with your Push Monkey account or create a new account. More info at: [Push Monkey](https://www.getpushmonkey.com?source=readme).

4. Tell your readers about it :)

= Updating =

Automatic updates should work without any problems; If you do encounter any problems, please [let us know](https://www.getpushmonkey.com/#contact?source=readme).

== Screenshots ==

1. Sample of how desktop push notifications look
2. The stats widget visible on the WordPress Dashboard
3. Granular Filtering of which post categories don't send push notifications
4. Notification preview while editing a post
5. Informational banner seen by Mac readers who come to your website from other browsers than Safari

== Changelog ==

= v 0.9.9.9.4 =
 * bugfixing: scheduled posts now send push notifications again.

= v 0.9.9.9.3 =
 * update stats layout
 * added new "Notification Format" feature. You can now configure what the notification content is. Currently two options are available: Post title and Post body OR custom title and post title.

= v 0.9.9.9.2 =
 * add option to disable CTA banners on homepage only, while being enabled on all other pages

= v 0.9.9.9.1 =
 * fix PHP 5.2 compatibility

= v 0.9.9.9.0 =
 * language adjusting

= v 0.9.9.8.9 =
 * allow CTA Banner customisation of color and text

= v 0.9.9.8.8 =
 * caching update
 * update Sign Up screen

= v 0.9.9.8.7 =
 * bugfixing

= v 0.9.9.8.6 =
 * bugfixing

= v 0.9.9.8.5 = 
 * bugfixing

= v 0.9.9.8.4 =
 * remove shortcodes from Push Notification
 * fix double escaping of Custom Push Notifications
 * add Welcome Notice
 * fix notification before trial expires

= v 0.9.9.8.3 =
 * display notification before trial plan expires

= v 0.9.9.8.2 =
 * display notification when the trial plan expired

= v 0.9.9.8.1 =
 * bugfix

= v 0.9.9.8 =
 * fewer requests to Push Monkey API to improve page load speed
 * allow websites to upgrade the price plan 
 * show notification for expired plans

= v 0.9.9.7 =
 * CSS bugfixing for some WP Themes

= v 0.9.9.6 =
 * banner improvements: remember users who disabled the banner, improved animations
 * you can now filter which custom post types send Desktop Push Notifications

= v 0.9.9.5 =
 * more advanced granular filtering. You can now choose which custom post types send Safari Push Notifications

= v 0.9.9.4 =
 * bugfixing

= v 0.9.9.3 =
 * bugfixing

= v 0.9.9.2 =
 * bugfixing

= v 0.9.9.1 =
 * bugfixing

= v 0.9.9 =
 * on-boarding workflow overhaul: now easier than ever. No more waiting. Account Key what? 
 * layout update
 * code cleanup
 * bugfixing

= v 0.9.8.2 =
 * bugfixing

= v 0.9.8 =
 * bugfixing

= v 0.9.7 =
 * bugfixing
 * UI updates
 * typos fixed

= v 0.9.6 =
 * bugfixing

= v 0.9.5 =
 * fix HTML tags in preview
 * fix conflict with TinyMCE Advanced

= v 0.9.4 =
 * fix some tags that used required the PHP setting short_open_tag to be on

= v 0.9.3 =
 * fix double usage of title
 * fix possible CSS overwrite

= v 0.9.2 =
 * prepare assets and folder structure for WordPress.org SVN

= v 0.9.1 =
 * replace iframes with API calls
 * reorganize code in classes

= v 0.9 =
 * add confirmation for Custom Push Notification widget
 * minor code cleanup

= 0.8.8 =
 * add option to disable push notifications while editing a Post
 * add push notification preview while editing a Post
 * bugfix: cURL conflicts with safe_mode.
 * moved settings page to top level
 * test on WP 4.0

= v 0.8.7 =
 * add dashboard widget for custom notifications

= v 0.8.6 = 
 * add menu page for configuring this plugin.
 * add option to exclude certain post categories from sending push notifications
 * visual tweaks

= v 0.8.5 = 
 * move the endpoint URL to a more generic location.

= v 0.8.4 =
 * limit $post->post_type to 'post', to filter out pages.
 * test on WP 3.9.
 * add uninstall.php

