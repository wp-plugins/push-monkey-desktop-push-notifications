<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_client.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_ajax.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_debugger.php' );
require_once( plugin_dir_path( __FILE__ ) . './controllers/class_push_monkey_review_notice_controller.php' );
require_once( plugin_dir_path( __FILE__ ) . '../models/class_push_monkey_banner.php' );
require_once( plugin_dir_path( __FILE__ ) . '../models/class_push_monkey_notification_config.php' );
require_once( plugin_dir_path( __FILE__ ) . '../models/class_push_monkey_review_notice.php' );

/**
 * Main class that connects the WordPress API
 * with the Push Monkey API
 */
class PushMonkey { 

	/* Public */

	public $endpointURL;
	public $apiClient;

	/**
	 * Hooks up with the required WordPress actions.
	 */
	public function run() {

		$this->add_actions();
	}

	/**
	 * Checks if an Account Key is stored.
	 * @return boolean
	 */
	public function has_account_key() {

		if( $this->account_key() ) {

			return true;
		}
		return false;
	}

	/**
	 * Returns the stored Account Key.
	 * @return string - the Account Key
	 */
	public function account_key() {

		$account_key = get_option( self::ACCOUNT_KEY_KEY, '' );
		if( ! $this->account_key_is_valid( $account_key ) ) {

			return NULL;
		}
		return $account_key;
	}

	/**
	 * Checks if an Account Key is valid.
	 * @param string $account_key - the Account Key checked.
	 * @return boolean
	 */
	public function account_key_is_valid( $account_key ) {

		if( ! strlen( $account_key ) ) {

			return false;
		}
		return true;
	}

	/**
	 * Checks if a user is signed in.
	 * @return boolean
	 */
	public function signed_in() {

		return get_option( self::USER_SIGNED_IN );
	}

	/**
	 * Signs in a user with an Account Key or a Token-Secret combination.
	 * @param string $account_key 
	 * @param string $api_token 
	 * @param string $api_secret 
	 * @return boolean
	 */
	public function sign_in( $account_key, $api_token, $api_secret ) {

		delete_option( PushMonkeyClient::PLAN_NAME_KEY );
		$response = $this->apiClient->sign_in( $account_key, $api_token, $api_secret );
		if ( isset( $response->signed_in ) ) {
			
			if ( $response->signed_in ) {

				update_option( self::USER_SIGNED_IN, true );
				if ( isset( $response->account_key ) ) {
					
					update_option( self::ACCOUNT_KEY_KEY, $response->account_key );
				}
				if ( isset( $response->email ) ) {
					
					update_option( self::EMAIL_KEY, $response->email );
				}
				$this->review_notice->setSignInDate( new DateTime() );
				return true;
			} 
		}
		if ( isset( $response->error ) ) {
			
			$this->sign_in_error = $response->error;
		}
		return false;
	}

	/**
	 * Signs out an user.
	 */
	public function sign_out() {

		delete_option( self::USER_SIGNED_IN );
		delete_option( self::ACCOUNT_KEY_KEY );
		delete_option( self::EMAIL_KEY );
		delete_option( self::WEBSITE_PUSH_ID_KEY );
		delete_option( PushMonkeyClient::PLAN_NAME_KEY );
	}

	/**
	 * Puts together the welcome text displayed on the top 
	 * right of the page, for signed in users.
	 * @return string
	 */
	public function get_email_text() {

		$email = get_option( self::EMAIL_KEY, '' );
		if ( strlen( $email ) ) {
			
			return "Hi " . $email . '!';
		}
		return '';
	}

	/**
	 * Check if this is the subscription version of Push Monkey
	 * @return boolean
	 */
	public function is_saas() {

		return file_exists( plugin_dir_path( __FILE__ ) . '../.saas' ); 
	}

	const ACCOUNT_KEY_KEY = 'push_monkey_account_key';
	const EMAIL_KEY = 'push_monkey_account_email_key';
	const WEBSITE_PUSH_ID_KEY = 'push_monkey_website_push_id_key';
	const WEBSITE_NAME_KEY = 'push_monkey_website_name';
	const EXCLUDED_CATEGORIES_KEY = 'push_monkey_excluded_categories';
	const USER_SIGNED_IN = 'push_monkey_user_signed_in';
	const POST_TYPES_KEY = 'push_monkey_post_types';

	/* Private */

	/**
	 * Constructor that initializes the Push Monkey class.
	 */
	function __construct() {

		if ( is_ssl() ) {

			$this->endpointURL = "https://www.getpushmonkey.com"; //live			
		} else {

			$this->endpointURL = "http://www.getpushmonkey.com"; //live
		}
		$this->apiClient = new PushMonkeyClient( $this->endpointURL );
		$this->d = new PushMonkeyDebugger();
		$this->ajax = new PushMonkeyAjax();
		$this->banner = new PushMonkeyBanner();
		$this->notif_config = new PushMonkeyNotificationConfig();
		$this->review_notice = new PushMonkeyReviewNotice();
	}

	/**
	 * Adds all the WordPress action hooks required by Push Monkey.
	 */
	function add_actions() {

		add_action( 'init', array( $this, 'process_forms' ) );

		add_action( 'init', array( $this, 'enqueue_scripts' ) );

		add_action( 'init', array( $this, 'enqueue_styles' ) );

		add_action( 'init', array( $this, 'catch_review_dismiss') );

		add_action( 'init', array( $this, 'set_defaults'), 20 );

		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );

		add_action( 'admin_menu', array( $this, 'register_settings_screen' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		add_action( 'transition_post_status', array( $this, 'post_published' ), 10, 3 );

		add_action( 'admin_enqueue_scripts', array( $this, 'notification_preview_scripts' ) );

		// If not signed in, display an admin_notice prompting the user to sign in.
		if( ! $this->signed_in() ) {

			add_action( 'admin_notices', array( $this, 'big_sign_in_notice' ) );
		}

		// If the plan is expired, present an admin_notice informing the user.
		if ( $this->can_show_expiration_notice() ) {
			
			add_action( 'admin_notices', array( $this, 'big_expired_plan_notice' ) );
		}

		add_action( 'admin_notices', array( $this, 'big_upsell_notice' ) );

		add_action( 'wp_ajax_push_monkey_banner_position', array( $this->ajax, 'banner_position_changed' ) );
	}

	/**
	 * Set some default values.
	 */
	function set_defaults() {

		// By default all posts should send push notifications
		$post_types = get_option( self::POST_TYPES_KEY );
		if ( ! $post_types ) {
			
			$post_types = $this->get_all_post_types();
			add_option( self::POST_TYPES_KEY, $post_types );
		}
	}

	/**
	 * Callback to add the dashboard widgets.
	 */
	function add_dashboard_widgets() {

		wp_add_dashboard_widget( 'push-monkey-push-dashboard-widget', 'Send Push Notification - Push Monkey', array( $this, 'push_widget' ) );	
		wp_add_dashboard_widget( 'push-monkey-stats-dashboard-widget', 'Stats - Push Monkey', array( $this, 'stats_widget') );	
	}

	/**
	 * Render the Custom Push Dashboard Widget.
	 */
	function push_widget() {

		$posted = isset( $_GET['posted'] );

		$account_key = false;
		if( $this->has_account_key() ) {

			$account_key = $this->account_key();
		}
		$settings_url = admin_url( 'admin.php?page=push_monkey_main_config&push_monkey_signup=1' );
		require_once( plugin_dir_path( __FILE__ ) . '../templates/widgets/push_monkey_push_widget.php' ); 
	}

	/**
	 * Render the Stats Dashboard Widget.
	 */
	function stats_widget() {

		if( ! $this->has_account_key() ) { 

			$settings_url = admin_url( 'admin.php?page=push_monkey_main_config&push_monkey_signup=1' );
?>			
			<div class="error-message"> 
				<p>
					Sign in before you can use Push Monkey. Don't have an account yet? 
					<a href="<?php echo $settings_url; ?>">Click here to sign up</a>. 
					<a href="http://www.getpushmonkey.com/help?source=plugin#q16" target="_blank">More info about this &#8594;</a>
				</p>
			</div>
<?php
			echo '<img class="placeholder" src="' . plugins_url( 'img/plugin-stats-placeholder-small.jpg', plugin_dir_path( __FILE__ ) ) . '"/>';
		} else {

			$notice = null;
			if ( $this->review_notice->canDisplayNotice() ) {

				$notice = new PushMonkeyReviewNoticeController( $this->is_saas() );
			}
			$account_key = $this->account_key();
			$output = $this->apiClient->get_stats( $account_key );
			require_once( plugin_dir_path( __FILE__ ) . '../templates/widgets/push_monkey_stats_widget.php' );
		}
		echo '<a href="http://www.getpushmonkey.com/help?source=plugin#q4">What is this?</a>';
	}

	/**
	 * See if the review notice has been dismissed
	 */
	function catch_review_dismiss() {

		if ( isset( $_GET[PushMonkeyReviewNoticeController::REVIEW_NOTICE_DISMISS_KEY] ) ) {
			
			$this->review_notice->setDismiss( true );
		}
	}

	/**
	 * Register the Settings screen - the screen where Push Monkey is configured.
	 */
	function register_settings_screen() {

		$icon_url =	plugins_url( 'img/plugin-icon.png', plugin_dir_path( __FILE__ ) );
		//NOTE: call a function to load this page. Loading a file instead of a function doesn't execute the page hook suffix.
		$hook_suffix = add_menu_page( 'Push Monkey ', 'Push Monkey', 'manage_options', 'push_monkey_main_config', array( $this, 'settings_screen' ), $icon_url );
		add_action( 'load-' . $hook_suffix , array( $this, 'settings_screen_loaded' ) );
		add_action( 'admin_print_styles-' . $hook_suffix , array( $this, 'enqueue_styles_main_config' ) );
	}

	/**
	 * Render the Settings Screen, where Push Monkey is configured.
	 */
	function settings_screen() {

		$website_name_key = self::WEBSITE_NAME_KEY;
		$push_monkey_account_key_key = self::ACCOUNT_KEY_KEY;
		$registered = false;
		if ( isset( $_GET['push_monkey_registered'] ) && isset( $_GET['push_monkey_package_pending'] ) ) {
			
			$this->sign_in_error = "You have signed up and we will verify your account soon.";
		} else if ( isset( $_GET['push_monkey_registered'] ) ) {

			$registered = ( $_GET['push_monkey_registered'] == '1' );
			$account_key = $_GET['push_monkey_account_key'];
			$this->sign_in( $account_key, null, null );
		}
		if ( isset( $this->sign_in_error ) ) {

			$sign_in_error = $this->sign_in_error;
		}
		$sign_up = false;
		if ( isset( $_GET['push_monkey_signup'] ) ) {
			
			$sign_up = true;
		}
		$signed_in = $this->signed_in();

		$options = $this->get_excluded_categories();
		$cats = $this->get_all_categories();
		$set_post_types = $this->get_set_post_types();
		$post_types = $this->get_all_post_types();
		// Banner options
		$banner_position = $this->banner->get_position();
		$banner_position_classes = array( 
			'top' => 'banner-top', 
			'bottom' => 'banner-bottom',
			'disabled' => 'banner-disabled',
			'topLeft' => 'banner-top-left',
			'topRight' => 'banner-top-right',
			'bottomLeft' => 'banner-bottom-left',
			'bottomRight' => 'banner-bottom-right',
			'centerLeft' => 'banner-center-left',
			'centerRight' => 'banner-center-right'
			 );
		$banner_text = $this->banner->get_raw_text();
		$banner_color = $this->banner->get_color();
		$banner_disabled_home = $this->banner->get_disabled_on_home();
		
		$has_account_key = false;
		$output = NULL;
		$plan_name = NULL;
		$plan_can_upgrade = false;
		$plan_expired = false;
		// Define image sources here, to have access to relative paths.
		$placeholder_url = plugins_url( 'img/plugin-stats-placeholder.jpg', plugin_dir_path( __FILE__ ) );
		$img_notifs_src = plugins_url( 'img/plugin-feature-image-notifications.png', plugin_dir_path( __FILE__ ) );
		$img_stats_src = plugins_url( 'img/plugin-feature-image-stats.png', plugin_dir_path( __FILE__ ) );
		$img_filter_src = plugins_url( 'img/plugin-feature-image-filter.png', plugin_dir_path( __FILE__ ) );
		$img_free_trial_src = plugins_url( 'img/push-monkey-plugin-free-trial.png', plugin_dir_path( __FILE__ ) );
		if ( $this->signed_in() ) {

			$has_account_key = true;
			$account_key = $this->account_key();
			$output = $this->apiClient->get_stats( $account_key );
			$plan_response = $this->apiClient->get_plan_name( $this->account_key() );
			$plan_name = isset( $plan_response->plan_name ) ? $plan_response->plan_name : NULL;
			$plan_can_upgrade = isset( $plan_response->can_upgrade ) ? $plan_response->can_upgrade : false;
			$plan_expired = isset( $plan_response->expired ) ? $plan_response->expired : false;
		}
		$register_url = $this->apiClient->registerURL;
		$forgot_password_url = $this->apiClient->endpointURL . '/password_reset';
		$return_url = admin_url( 'admin.php?page=push_monkey_main_config' );
		$website_name = $this->website_name(); 
		$website_url = site_url();
		$logout_url = admin_url( 'admin.php?page=push_monkey_main_config&logout=1' );
		$email = $this->get_email_text();
		$upgrade_url = $this->apiClient->endpointURL . '/dashboard?upgrade_plan=1&source=plugin';
		$is_subscription_version = $this->is_saas();

		//
		// Notification Format
		//
		$notification_format_image = plugins_url( 'img/notification-image-upload-placeholder.png', plugin_dir_path( __FILE__ ) );
		$notification_format = $this->notif_config->get_format();
		$notification_is_custom = $this->notif_config->is_custom_text();
		$notification_custom_text = $this->notif_config->get_custom_text();
		require_once( plugin_dir_path( __FILE__ ) . '../templates/push_monkey_settings.php' );
	}

	/**
	 * Get all the post categories.
	 * @return array
	 */
	function get_all_categories() {

		$args=array(
			'hide_empty' => 0,
			'order' => 'ASC'
		);
		$cats = get_categories( $args );
		return $cats;
	}

	function get_set_post_types() {

		return get_option( self::POST_TYPES_KEY );
	}

	function get_all_post_types() {

		$postargs = array(
			'public'   => true,
			'_builtin' => false
			);
		$raw_post_types = get_post_types( $postargs, 'objects', 'and' );
		$post_types = array();
		foreach ( $raw_post_types as $key => $post_type ) {

			$post_types[$key] = $post_type->labels->name;
		}
		$post_types['post'] = "Standard Posts";
		return $post_types;
	}

	/**
	 * Register the meta box for the Notification Preview, when adding a new Post.
	 */
	function add_meta_box() {
		
		$post_types = $this->get_all_post_types();
		foreach ($post_types as $key => $value) {
		
			add_meta_box( 'push_monkey_post_opt_out', 'Push Monkey Options', 
				array( $this, 'notification_preview_meta_box' ), $key, 'side', 'high' );		
		}
	}

	/**
	 * Render the meta box for Notification Preview.
	 */
	function notification_preview_meta_box( $post ) {

		wp_nonce_field( 'push_monkey_meta_box', 'push_monkey_meta_box_nonce' );

		$value = get_post_meta( $post->ID, '_push_monkey_opt_out', true );
		$checked = '';
		if( $value == 'on' ) {

			$checked = ' checked';
		}	

		$disabled = '';
		if( $post->post_status == 'publish' ) {

			$disabled = ' disabled="disabled"';
		}

		$account_key = '';
		if( $this->has_account_key() ) {

			$account_key = $this->account_key();
		}

		$max_len_title = 33;
		$title = strip_tags($post->post_title);
		if ( $this->notif_config->is_custom_text() ) {

			$title = $this->notif_config->get_custom_text();
		}
		if ( strlen( $title ) > $max_len_title ) {

			$title = substr( $title, 0, $max_len_title ) . '...';
		}

		$max_len_body = 70;
		$body = strip_tags(strip_shortcodes($post->post_content));
		if ( $this->notif_config->is_custom_text() ) {

			$body = strip_tags($post->post_title);
		}
		if ( strlen( $body ) > $max_len_body ) {

			$body = substr( $body, 0, $max_len_body ) . '...';
		}
		$register_url = $return_url = admin_url( 'admin.php?page=push_monkey_main_config' );

?>
	<div class="preview-container">
		<?php if( ! $account_key ) { ?>
		<div class="error-message"> <p>Sign In before you can use Push Monkey. Don't have an account yet? <a href="<?php echo $register_url; ?>">Click here to Sign Up</a>. <a href="http://www.getpushmonkey.com/help?source=plugin#q4">More info about this</a>.</p> </div>
		<?php } ?>
		<h4>Notification Preview</h4>
		<div class="notification">

		<img class="icon" src="<?php echo $this->endpointURL; ?>/clients/icon/<?php echo $account_key; ?>" />		

			<p>
				<strong id="push_monkey_preview_title"><?php echo $title; ?></strong> 
				<br /> 
				<span id="push_monkey_preview_content"><?php echo $body; ?></span>
			</p>

		</div>
	</div>
<?php
		echo '<input type="checkbox" id="push_monkey_opt_out" name="push_monkey_opt_out"' . $disabled . $checked . '/>';
		echo '<label for="push_monkey_opt_out">Don\'t send push notification for this post</label> ';
		echo '<p class="howto">Disabling push notifications doesn\'t send notifications even if the marked post category normally does. <a href="http://www.getpushmonkey.com/help#q9">Help? &#8594;</a></p>';
	}

	/**
	 * Load scripts for Notification Preview Metabox
	 */
	function notification_preview_scripts( $hook_suffix ) {

		if( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) {

			wp_enqueue_script( 'custom_js', plugins_url('js/push_monkey_optout_metabox.js', dirname( __FILE__ ) ), array( 'jquery' ));
			$local_vars = array(
				'is_custom_text' => $this->notif_config->is_custom_text()
			);
			wp_localize_script( 'custom_js', 'push_monkey_preview_locals', $local_vars );
		}
	}

	/**
	 * Action executed when the Settings Screen has loaded
	 */
	function settings_screen_loaded() {

		remove_action( 'admin_notices', array( $this, 'big_sign_in_notice' ) );
		remove_action( 'admin_notices', array( $this, 'big_expired_plan_notice' ) );
		add_action( 'admin_notices', array( $this, 'big_welcome_notice' ) );
	}

	/**
	 * Action executed when a new post transitions its status.
	 */
	function post_published( $new_status, $old_status, $post ) {

		if ( isset( $_POST['push_monkey_opt_out'] ) ) {

			$optout = $_POST['push_monkey_opt_out'];
			update_post_meta( $post->ID, '_push_monkey_opt_out', $optout );
		}

		if ( ! $this->has_account_key() ) {

			return;
		}
		if ( $old_status == 'publish' || $new_status != 'publish' ) {

			return;
		} 
		$included_post_types = $this->get_set_post_types();
		if ( ! array_key_exists( $post->post_type, $included_post_types ) ) {

			return;			
		}
		if( ! $this->can_verify_optout() && $old_status != 'future' ) {

			return;
		}
		$optout = get_post_meta( $post->ID, '_push_monkey_opt_out', true );
		$can_send_push = false;
		if( $optout != 'on' ) {

			if( ! $this->post_has_excluded_category( $post ) ){

				$can_send_push = true;
			}
		}
		if( $can_send_push ) {

			$title = $post->post_title;
			$body = strip_tags(strip_shortcodes($post->post_content));
			if ( $this->notif_config->is_custom_text() ) {

				$title = $this->notif_config->get_custom_text();
				$body = $post->post_title;
			}
			$post_id = $post->ID;
			$this->send_push_notification( $title, $body, $post_id, false );
		}
	} 

	/**
	 * Checks if the author did not manually disable push notification for
	 * this specific Post, but clicking on the opt-out checkbox.
	 */
	function can_verify_optout() {

		// Check if our nonce is set.
		if ( ! isset( $_POST['push_monkey_meta_box_nonce'] ) ) {

			return false;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['push_monkey_meta_box_nonce'], 'push_monkey_meta_box' ) ) {

			return false;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {

			return false;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {

			return false;
		}

		return true;
	}

	/**
	 * Checks if a Post object is excluded from sending desktop push notifications.
	 * @param object $post 
	 * @return boolean
	 */
	function post_has_excluded_category( $post ) {

		$excluded_categories = $this->get_excluded_categories();

		$category_objects = get_the_category( $post->ID );
		$categories = array();
		foreach( $category_objects as $cat ) {

			$categories[] = $cat->cat_ID;
		}

		$excludable_categories = array_intersect( $excluded_categories, $categories );

		if ( count( $excludable_categories ) ) {

			return true;
		}
		return false;
	}

	/**
	 * Get an array of categories which are marked for not sending desktop push notifications.
	 * @return array of category IDs
	 */
	function get_excluded_categories() {

		$defaults = array();
		$options = get_option( self::EXCLUDED_CATEGORIES_KEY );

		if ( !is_array( $options ) ){

			$options = $defaults;
			update_option( self::EXCLUDED_CATEGORIES_KEY, $options );
		}
		return $options;
	}

	/**
	 * This is the actual point when the Push Monkey API is contacted and the notification is sent.
	 * @param string $title 
	 * @param string $body 
	 * @param string $url_args 
	 * @param boolean $custom 
	 */
	function send_push_notification( $title, $body, $url_args, $custom ) {

		$account_key = $this->account_key();
		$clean_title = trim($title);
		$clean_body = trim($body);
		$payloadVars = 'title=' . $clean_title . '&body=' . $clean_body . '&url_args=' . $url_args;

		$maxPayloadLength = 150;
		$maxTitleLength = 40;
		$maxBodyLength = 100;
		if( strlen( $payloadVars ) > $maxPayloadLength ){

			$clean_title = substr( $clean_title, 0, $maxTitleLength );
			$clean_body = substr( $clean_body, 0, $maxBodyLength );
		}

		$this->apiClient->send_push_notification( $account_key, $title, $body, $url_args, $custom );
	}

	/**
	 * Get the name of the website. Can be either from get_bloginfo() or
	 * from a previously saved value.
	 * @return string
	 */
	function website_name() {

		$name = get_option( self::WEBSITE_NAME_KEY, false );
		if( ! $name ) {

			$name = get_bloginfo( 'name' );
		}
		return $name;
	}

	/**
	 * Get the Website Push ID stored.
	 * @return string
	 */
	function website_push_ID() {

		$stored_website_push_id = get_option( self::WEBSITE_PUSH_ID_KEY, false);

		if ( $stored_website_push_id ) {
		
			return $stored_website_push_id;
		}

		$resp = $this->apiClient->get_website_push_ID( $this->account_key() );
		if ( isset( $resp->website_push_id ) ) {

			update_option( self::WEBSITE_PUSH_ID_KEY, $resp->website_push_id );
			return $resp->website_push_id;
		}
		if ( isset( $resp->error ) ) {
			
			$this->error = $resp->error;
		}
		return '';
	}

	/**
	 * Enqueue all the JS files required.
	 */
	function enqueue_scripts() {

		if ( ! is_admin() ) {

			wp_enqueue_script( 'push_monkey_noty', plugins_url( 'js/jquery.noty.packaged.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ) );
			wp_enqueue_script( 'push_monkey_pgwBrowser', plugins_url( 'js/pgwbrowser.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ) );
			wp_enqueue_script( 'push_monkey_wp', plugins_url( 'js/push_monkey_wp.js', plugin_dir_path( __FILE__ ) ), array( 'push_monkey_pgwBrowser' ) );
			$local_vars = array(
				'website_push_id' => $this->website_push_ID(),
				'website_name' => $this->website_name(),
				'endpoint_url' => str_replace( 'http:', 'https:', $this->endpointURL ),
				'banner_icon_url' => plugins_url( 'img/banner-icon.png', dirname( __FILE__ ) ),
				'banner_icon_url_v2' => plugins_url( 'img/banner-icon-v2.png', dirname( __FILE__ ) ),
				'banner_position' => $this->banner->get_position(),
				'banner_text' => $this->banner->get_text( $this->website_name() ),
				'banner_color' => $this->banner->get_color(),
				'home_url' => home_url($path = '/'),
				'disabled_on_home' => $this->banner->get_disabled_on_home()
			);
			wp_localize_script( 'push_monkey_wp', 'push_monkey_locals', $local_vars );
		} else {

			wp_enqueue_script( 'push_monkey_modal', plugins_url( 'js/jquery.leanModal.min.js', plugin_dir_path( __FILE__ ) ), array('jquery') );
			wp_enqueue_script( 'push_monkey_push_widget', plugins_url( 'js/push_monkey_push_widget.js', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_script( 'push_monkey_charts', plugins_url( 'js/Chart.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ) );
			wp_enqueue_script( 'push_monkey_bootstrap_js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'push_monkey_bootstrap_switch', plugins_url( 'js/bootstrap-switch.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ) );			
			wp_enqueue_script( 'push_monkey_bootstrap_select', plugins_url( 'js/bootstrap-select.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery', 'push_monkey_bootstrap_js' ) );			
			wp_enqueue_script( 'push_monkey_bootstrap_picker', plugins_url( 'js/bootstrap-colorpicker.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ) );
			wp_enqueue_script( 'push_monkey_admin', plugins_url( 'js/push_monkey_admin.js', plugin_dir_path( __FILE__ ) ), 
				array( 'jquery', 'push_monkey_bootstrap_switch', 'push_monkey_bootstrap_select', 'push_monkey_bootstrap_picker' ) );
		}
	}

	/**
	 * Enqueue all the CSS required.
	 */
	function enqueue_styles( $hook_suffix ) {

		if ( is_admin() ) {

			wp_enqueue_style( 'push_monkey_bootstrap_style', plugins_url( 'css/push-monkey-bootstrap.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'push_monkey_dashboard_widget_style', plugins_url( 'css/widgets.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'push_monkey_style', plugins_url( 'css/style.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'push_monkey_bootstrap_switch_style', plugins_url( 'css/bootstrap-switch.min.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'push_monkey_bootstrap_select', plugins_url( 'css/bootstrap-select.min.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'push_monkey_bootstrap_picker', plugins_url( 'css/bootstrap-colorpicker.min.css', plugin_dir_path( __FILE__ ) ) );
		} else {

			wp_enqueue_style( 'push_monkey_animate', plugins_url( 'css/animate.css', plugin_dir_path( __FILE__ ) ) );			
			wp_enqueue_style( 'push_monkey', plugins_url( 'css/push-monkey.css', plugin_dir_path( __FILE__ ) ) );			
		}
	}

	/**
	 * Enqueue the CSS for the Settings page
	 */
	function enqueue_styles_main_config( ) {

		wp_enqueue_style( 'push_monkey_config_style', plugins_url( 'css/main-config.css', plugin_dir_path( __FILE__ ) ) );
	}

	/**
	 * Central point to process forms.
	 */
	function process_forms() {

		if ( isset( $_GET['logout'] ) ) {
			
			$this->sign_out();
			wp_redirect( admin_url( 'admin.php?page=push_monkey_main_config' ) ); 
			exit;
		}

		if( isset( $_POST['push_monkey_main_config_submit'] ) ) {

			$this->process_main_config( $_POST );
		} else if( isset( $_POST['push_monkey_category_exclusion'] ) ) {

			$this->process_category_exclusion( $_POST );
		} else if( isset( $_POST['push_monkey_push_submit'] ) ) {

			$this->process_push( $_POST );
		} else if ( isset( $_POST['push_monkey_sign_in'] ) ) {	
			
			$this->process_sign_in( $_POST );
		} else if ( isset( $_POST['push_monkey_post_type_inclusion'] ) ) {

			$this->process_post_type_inclusion( $_POST );
		} else if ( isset( $_POST['push_monkey_banner'] ) ) {

			$this->process_banner_customisation( $_POST );
		} else if ( isset( $_POST['push_monkey_notification_config'] ) ) {

			$this->process_notif_format( $_POST );
		}
	}

	/**
	 * Process the Sign In form.
	 */
	function process_sign_in( $post ) {

		$api_token = $post['username'];
		$api_secret = $post['password'];
		if ( ! strlen( $api_token ) || ! strlen( $api_secret ) ) {

			$this->sign_in_error = "The two fields can't be empty.";
			return;
		}

		$signed_in = $this->sign_in( null, $api_token, $api_secret );
		if ( $signed_in ) {
			
			wp_redirect( admin_url( 'admin.php?page=push_monkey_main_config' ) ); 
			exit;
		}
	}

	/**
	 * Process the form with the website name field, from the Settings page.
	 */
	function process_main_config( $post ) {

		$website_name = $post[self::WEBSITE_NAME_KEY];
		if( $website_name ) {

			update_option( self::WEBSITE_NAME_KEY, $website_name );
		}
	}

	/**
	 * Process the form that marks which Post Categories don't sent desktop push notifications.
	 */
	function process_category_exclusion( $post ) {

		$categories = array();
		if ( isset( $post['excluded_categories'] ) ) {

			$categories = $post['excluded_categories'];
		}
		update_option( self::EXCLUDED_CATEGORIES_KEY, $categories );
		add_action( 'admin_notices', array( $this, 'excluded_categories_saved_notice' ) );
	}

	/**
	 * Process the form that marks which Post Types send desktop push notifications.
	 */
	function process_post_type_inclusion( $post ) {

		$post_types = array();
		if ( isset( $post['included_post_types'] ) ) {

			foreach ( $post['included_post_types'] as $value ) {
				
				$post_types[$value] = 1;
			}
		}
		update_option( self::POST_TYPES_KEY, $post_types );
		add_action( 'admin_notices', array( $this, 'included_post_types_saved_notice' ) );
	}

	/**
	 * Process the custom push notification, from the widget in the Dashboard.
	 */
	function process_push( $post ) {

		$title = stripcslashes( $post['title'] );
		$body = stripcslashes( $post['message'] );
		$url_args = $post['url'];
		$this->send_push_notification( $title, $body, $url_args, true );
		wp_redirect( admin_url( '?posted=1' ) ); 
		exit();
	}

	/**
	 * Process the options to customise the banner.
	 */
	function process_banner_customisation( $post ) {

		if ( isset( $post['push_monkey_banner_text'] ) ) {

			$text = $post['push_monkey_banner_text'];			
			$this->banner->set_raw_text( $text );
		}
		if ( isset( $post['push_monkey_banner_color'] ) ) {
			
			$color = $post['push_monkey_banner_color'];
			$this->banner->set_color( $color );
		}
		$disabled_on_home = isset( $post['push_monkey_banner_disabled_on_home'] );			
		$this->banner->set_disabled_on_home( $disabled_on_home );
		add_action( 'admin_notices', array( $this, 'banner_saved_notice' ) );
	}

	/**
	 * Process the notification format 
	 */
	function process_notif_format( $post ) {

		$this->d->debug( 'notif format' );

		if ( isset( $post['push_monkey_notification_format'] ) ) {
			
			$format = $post['push_monkey_notification_format'];

			$this->d->debug( $format );
			$this->notif_config->set_format( $format );
		} 
		if ( isset( $post['custom-text'] ) ) {

			$text = $post['custom-text'];
			$this->notif_config->set_custom_text( $text );
		}
		add_action( 'admin_notices', array( $this, 'notif_format_saved_notice' ) );
	}

	/**
	 * Renders the admin notice that prompts the user to sign in.
	 */
	function big_sign_in_notice() {

		$image_url = plugins_url( 'img/plugin-big-message-image.png', plugin_dir_path( __FILE__ ) );
		$settings_url = admin_url( 'admin.php?page=push_monkey_main_config' );
		require_once( plugin_dir_path( __FILE__ ) . '../templates/messages/push_monkey_big_message.php' );
	}

	/**
	 * Renders an admin notice to say that excluded categories are saved.
	 */
	function excluded_categories_saved_notice() {

		echo '<div class="updated"><p>Excluded categories successfuly updated! *victory dance*</p></div>';
	}

	/**
	 * Renders an admin notice to say that post types are saved.
	 */
	function included_post_types_saved_notice() {

		echo '<div class="updated"><p>Included Post Types successfuly updated! *high five*</p></div>';	
	}

	/**
	 * Renders an admin notice to say that the banner customisation has been saved.
	 */
	function banner_saved_notice() {

		echo '<div class="updated"><p>Banner saved! *high five*</p></div>';	
	}

	/**
	 * Renders an admin notice to say that the notification format has been saved.
	 */
	function notif_format_saved_notice() {

		echo '<div class="updated"><p>Notification format saved! *yay*</p></div>';	
	}

	/**
	 * Renders a notice to say that the chosen plan is expired.
	 */
	function big_expired_plan_notice() {

		if ( ! $this->signed_in() ) {

			return;
		}

		$account_key = $this->account_key();
		$plan_response = $this->apiClient->get_plan_name( $account_key );
		$plan_expired = isset( $plan_response->expired ) ? $plan_response->expired : false;
		if ( ! $plan_expired ) {
			
			return;
		}
		$stats = $this->apiClient->get_stats( $account_key );
		if ( ! isset( $stats->subscribers ) ) {
			
			return;
		}

		$subscribers = $stats->subscribers;
		$upgrade_url = $this->apiClient->endpointURL . '/dashboard?upgrade_plan=1&source=plugin';
		$image_url = plugins_url( 'img/plugin-big-expiration-notice.png', plugin_dir_path( __FILE__ ) );
		$settings_url = admin_url( 'admin.php?page=push_monkey_main_config' );
		require_once( plugin_dir_path( __FILE__ ) . '../templates/messages/push_monkey_big_expiration_notice.php' );
	}

	/**
	 * Checks the Push Monkey API to see if the current price plan expired.
	 * @return boolean
	 */
	function can_show_expiration_notice() {

		if ( ! $this->signed_in() ) {
			
			return false;
		}
		$plan_response = $this->apiClient->get_plan_name( $this->account_key() );
		$plan_expired = isset( $plan_response->expired ) ? $plan_response->expired : false;
		return $plan_expired;
	}

	/**
	 * Renders an admin notice asking the user for an upgrade.
	 */
	function big_upsell_notice() {

		global $hook_suffix;	
		if ( $hook_suffix != 'plugins.php' ) {

			return;
		}

		if ( ! $this->signed_in() ) {
			
			return;
		}

		$plan_response = $this->apiClient->get_plan_name( $this->account_key() );
		$plan_expired = isset( $plan_response->expired ) ? $plan_response->expired : false;
		$plan_can_upgrade = isset( $plan_response->can_upgrade ) ? $plan_response->can_upgrade : false;

		$push_monkey_us_notice_cookie = isset( $_COOKIE['push_monkey_us_notice'] ) ? $_COOKIE['push_monkey_us_notice'] : false;

		if ( $push_monkey_us_notice_cookie ) {
			
			return;		
		}

		if ( ! $plan_expired && $plan_can_upgrade ) {

			$upgrade_url = $this->apiClient->endpointURL . '/dashboard?upgrade_plan=1&source=us-notice';
			$price_plans = $this->apiClient->endpointURL . '/#plans';
			$image_url = plugins_url( 'img/plugin-big-message-image.png', plugin_dir_path( __FILE__ ) );
			$close_url = plugins_url( 'img/banner-close-dark.png', plugin_dir_path( __FILE__ ) );
			require_once( plugin_dir_path( __FILE__ ) . '../templates/messages/push_monkey_upsell_notice.php' );				
		}
	}

	/**
	 * Renders an admin notice for a first time user. Displays a few useful links to get started.
	 */
	function big_welcome_notice() {

		$push_monkey_welcome_notice_cookie = isset( $_COOKIE['push_monkey_welcome_notice'] ) ? $_COOKIE['push_monkey_welcome_notice'] : false;

		if ( ! $this->signed_in() ) {
			
			return;
		}

		if ( $push_monkey_welcome_notice_cookie ) {
			
			return;
		}

		$image_url = plugins_url( 'img/logo-party.png', plugin_dir_path( __FILE__ ) );
		$close_url = plugins_url( 'img/banner-close-dark.png', plugin_dir_path( __FILE__ ) );
		require_once( plugin_dir_path( __FILE__ ) . '../templates/messages/push_monkey_welcome_notice.php' );
	}
}
