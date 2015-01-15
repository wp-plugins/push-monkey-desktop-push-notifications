<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_client.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_debugger.php' );


/**
 * PushMonkey
 */
class PushMonkey { 

	/* Public */

	public $endpointURL;
	public $apiClient;

	public function run() {

		$this->add_actions();
	}

	public function has_account_key() {

		if( $this->account_key() ) {

			return true;
		}
		return false;
	}

	public function account_key() {

		$account_key = get_option( self::ACCOUNT_KEY_KEY, '' );
		if( ! $this->account_key_is_valid( $account_key ) ) {

			return NULL;
		}
		return $account_key;
	}

	function account_key_is_valid( $account_key ) {

		if( ! strlen( $account_key ) ) {

			return false;
		}
		return true;
	}

	public function signed_in() {

		return get_option( self::USER_SIGNED_IN );
	}

	public function sign_in( $account_key, $api_token, $api_secret ) {

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
				return true;
			} 
		}
		if ( isset( $response->error ) ) {
			
			$this->sign_in_error = $response->error;
		}
		return false;
	}

	public function sign_out() {

		update_option( self::USER_SIGNED_IN, false );
		delete_option( self::ACCOUNT_KEY_KEY );
		delete_option( self::EMAIL_KEY );
	}

	public function get_email_text() {

		$email = get_option( self::EMAIL_KEY, '' );
		if ( strlen( $email ) ) {
			
			return "Hi " . $email . '!';
		}
		return '';
	}

	/* Private */

	const ACCOUNT_KEY_KEY = 'push_monkey_account_key';
	const EMAIL_KEY = 'push_monkey_account_email_key';
	const WEBSITE_PUSH_ID_KEY = 'push_monkey_website_push_id_key';
	const WEBSITE_NAME_KEY = 'push_monkey_website_name';
	const EXCLUDED_CATEGORIES_KEY = 'push_monkey_excluded_categories';
	const USER_SIGNED_IN = 'push_monkey_user_signed_in';
	const POST_TYPES_KEY = 'push_monkey_post_types';

	function __construct() {

		if ( is_ssl() ) {

			$this->endpointURL = "https://www.getpushmonkey.com"; //live			
		} else {

			$this->endpointURL = "http://www.getpushmonkey.com"; //live
		}
		$this->apiClient = new PushMonkeyClient( $this->endpointURL );
		$this->d = new PushMonkeyDebugger();
	}

	function set_defaults() {

		// By default all posts should send push notifications
		$post_types = get_option( self::POST_TYPES_KEY );
		if ( ! $post_types ) {
			
			$post_types = $this->get_all_post_types();
			add_option( self::POST_TYPES_KEY, $post_types );
		}
	}


	function add_actions() {

		add_action( 'init', array( $this, 'process_forms' ) );

		add_action( 'init', array( $this, 'enqueue_scripts' ) );

		add_action( 'init', array( $this, 'enqueue_styles' ) );

		add_action( 'init', array( $this, 'set_defaults'), 20 );

		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );

		add_action( 'admin_menu', array( $this, 'register_settings_screen' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		add_action( 'transition_post_status', array( $this, 'post_published' ), 10, 3 );

		add_action( 'admin_enqueue_scripts', array( $this, 'notification_preview_scripts' ) );

		if( ! $this->has_account_key() ) {

			add_action( 'admin_notices', array( $this, 'big_invalid_account_key_notice' ) );
		}
	}

	function add_dashboard_widgets() {

		//TODO: Use only _ or -, not both!
		wp_add_dashboard_widget( 'push-monkey-push-dashboard-widget', 'Send Push Notification - Push Monkey', array( $this, 'push_widget' ) );	
		wp_add_dashboard_widget( 'push-monkey-stats-dashboard-widget', 'Stats - Push Monkey', array( $this, 'stats_widget') );	
	}

	function push_widget() {

		$posted = isset( $_GET['posted'] );

		$account_key = false;
		if( $this->has_account_key() ) {

			$account_key = $this->account_key();
		}
		$settings_url = admin_url( 'admin.php?page=push_monkey_main_config&push_monkey_signup=1' );
		require_once( plugin_dir_path( __FILE__ ) . '../templates/widgets/push_monkey_push_widget.php' ); //TODO: move template loading to a TemplateLoader
	}

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

			$account_key = $this->account_key();
			$response = $this->apiClient->get_stats( $account_key );
			if( is_wp_error( $response ) ) {

				echo 'Error Found ( '.$response->get_error_message().' )';
				echo '<img class="placeholder" src="' . plugins_url( 'img/plugin-stats-placeholder-small.jpg', plugin_dir_path( __FILE__ ) ) . '"/>';
			} else {

				$body = wp_remote_retrieve_body( $response );
				$output = json_decode( $body ); 
				require_once( plugin_dir_path( __FILE__ ) . '../templates/widgets/push_monkey_stats_widget.php' );
			}
		}
		echo '<a href="' . admin_url( 'admin.php?page=push_monkey_main_config' ) . '">What is this?</a>';
	}

	function register_settings_screen() {

		$icon_url =	plugins_url( 'img/plugin-icon.png', plugin_dir_path( __FILE__ ) );
		//NOTE: call a function to load this page. Loading a file instead of a function doesn't execute the page hook suffix.
		$hook_suffix = add_menu_page( 'Push Monkey ', 'Push Monkey', 'manage_options', 'push_monkey_main_config', array( $this, 'settings_screen' ), $icon_url );
		add_action( 'load-' . $hook_suffix , array( $this, 'settings_screen_loaded' ) );
		add_action( 'admin_print_styles-' . $hook_suffix , array( $this, 'enqueue_styles_main_config' ) );
	}

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

		$has_account_key = false;
		$output = NULL;
		$placeholder_url = plugins_url( 'img/plugin-stats-placeholder.jpg', plugin_dir_path( __FILE__ ) );
		$img_notifs_src = plugins_url( 'img/plugin-feature-image-notifications.png', plugin_dir_path( __FILE__ ) );
		$img_stats_src = plugins_url( 'img/plugin-feature-image-stats.png', plugin_dir_path( __FILE__ ) );
		$img_filter_src = plugins_url( 'img/plugin-feature-image-filter.png', plugin_dir_path( __FILE__ ) );
		if ( $this->has_account_key() ) {

			$has_account_key = true;
			$account_key = $this->account_key();
			$response = $this->apiClient->get_stats( $account_key );
			if( is_wp_error( $response ) ) {

				$this->d->debug( $response->get_error_message() );
			} else {

				$body = wp_remote_retrieve_body( $response );
				$output = json_decode( $body ); 
				$this->d->debug( print_r( $output, true ) );
			}
		}
		$register_url = $this->apiClient->registerURL;
		$forgot_password_url = $this->apiClient->endpointURL . '/password_reset';
		$return_url = admin_url( 'admin.php?page=push_monkey_main_config' );
		$website_name = $this->website_name(); 
		$website_url = site_url();
		$logout_url = admin_url( 'admin.php?page=push_monkey_main_config&logout=1' );
		$email = $this->get_email_text();
		require_once( plugin_dir_path( __FILE__ ) . '../templates/push_monkey_settings.php' );
	}

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

	function add_meta_box() {
		
		$post_types = $this->get_all_post_types();
		foreach ($post_types as $key => $value) {
		
			add_meta_box( 'push_monkey_post_opt_out', 'Push Monkey Options', 
				array( $this, 'notification_preview_meta_box' ), $key, 'side', 'high' );		
		}
	}

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

		$account_key = '11';
		if( $this->has_account_key() ) {

			$account_key = $this->account_key();
		}

		$max_len_title = 33;
		$title = strip_tags($post->post_title);
		if ( strlen( $title ) > $max_len_title ) {

			$title = substr( $title, 0, $max_len_title ) . '...';
		}

		$max_len_body = 70;
		$body = strip_tags($post->post_content);
		if ( strlen( $body ) > $max_len_body ) {

			$body = substr( $body, 0, $max_len_body ) . '...';
		}

?>
	<div class="preview-container">
		<?php if( ! $account_key ) { ?>
		<div class="error-message"> <p>Set an Account Key before you can use Push Monkey. Don't have an Account Key yet? <a href="http://www.getpushmonkey.com/register?source=plugin">Click here to get one</a>. <a href="http://www.getpushmonkey.com/help?source=plugin#q4">More info about this</a>.</p> </div>
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

	function notification_preview_scripts( $hook_suffix) {

		if( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) {

			wp_enqueue_script( 'custom_js', plugins_url('js/push_monkey_optout_metabox.js', dirname( __FILE__ ) ), array( 'jquery' ));
		}
	}

	function settings_screen_loaded() {

		remove_action( 'admin_notices', array( $this, 'big_invalid_account_key_notice' ) );
	}

	function post_published( $new_status, $old_status, $post ) {

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
		if( ! $this->can_verify_optout() ) {

			return;
		}
		$optout = $_POST['push_monkey_opt_out'];
		$can_send_push = false;
		if( $optout != 'on' ) {

			if( ! $this->post_has_excluded_category( $post ) ){

				$can_send_push = true;
			}
			update_post_meta( $post->ID, '_push_monkey_opt_out', 'off' );
		} else {

			update_post_meta( $post->ID, '_push_monkey_opt_out', $optout );
		}
		if( $can_send_push ) {

			$title = $post->post_title;
			$body = strip_tags($post->post_content);
			$post_id = $post->ID;
			$this->send_push_notification( $title, $body, $post_id, false );
		}
	} 

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

	function get_excluded_categories() {

		$defaults = array();
		$options = get_option( self::EXCLUDED_CATEGORIES_KEY );

		if ( !is_array( $options ) ){

			$options = $defaults;
			update_option( self::EXCLUDED_CATEGORIES_KEY, $options );
		}
		return $options;
	}

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

	function website_name() {

		$name = get_option( self::WEBSITE_NAME_KEY, '' );
		if( !strlen( $name ) ) {

			$name = get_bloginfo( 'name' );
		}
		return $name;
	}

	function website_push_ID() {

		$resp = $this->apiClient->get_website_push_ID( $this->account_key() );
		if ( isset( $resp->website_push_id ) ) {

			update_option( self::WEBSITE_PUSH_ID_KEY, $resp->website_push_id );
			return $resp->website_push_id;
		}
		if ( isset( $resp->error ) ) {
			
			$this->error = $resp->error;
		}
	}

	function enqueue_scripts() {

		if ( ! is_admin() ) {

			wp_enqueue_script( 'push_monkey_wp', plugins_url( 'js/push_monkey_wp.js', plugin_dir_path( __FILE__ ) ) );
			$local_vars = array(
				'website_push_id' => $this->website_push_ID(),
				'website_name' => $this->website_name(),
				'endpoint_url' => str_replace( 'http:', 'https:', $this->endpointURL )
			);
			wp_localize_script( 'push_monkey_wp', 'push_monkey_locals', $local_vars );
		} else {

			wp_enqueue_script( 'push_monkey_modal', plugins_url( 'js/jquery.leanModal.min.js', plugin_dir_path( __FILE__ ) ), array('jquery') );
			wp_enqueue_script( 'push_monkey_push_widget', plugins_url( 'js/push_monkey_push_widget.js', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_script( 'push_monkey_charts', plugins_url( 'js/Chart.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ) );
			wp_enqueue_script( 'push_monkey_bootstrap_js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'push_monkey_bootstrap_switch', plugins_url( 'js/bootstrap-switch.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ) );			
			wp_enqueue_script( 'push_monkey_switch', plugins_url( 'js/push_monkey_switch.js', plugin_dir_path( __FILE__ ) ), array( 'jquery', 'push_monkey_bootstrap_switch' ) );			
		}
	}

	function enqueue_styles( $hook_suffix ) {

		if ( is_admin() ) {

			wp_enqueue_style( 'push_monkey_bootstrap_style', plugins_url( 'css/push-monkey-bootstrap.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'push_monkey_dashboard_widget_style', plugins_url( 'css/widgets.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'push_monkey_style', plugins_url( 'css/style.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'push_monkey_bootstrap_switch_style', plugins_url( 'css/bootstrap-switch.min.css', plugin_dir_path( __FILE__ ) ) );
		}
	}

	function enqueue_styles_main_config( ) {

		wp_enqueue_style( 'push_monkey_config_style', plugins_url( 'css/main-config.css', plugin_dir_path( __FILE__ ) ) );
	}

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
		}
	}

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

	function process_main_config( $post ) {

		$website_name = $post[self::WEBSITE_NAME_KEY];
		if( $website_name ) {

			update_option( self::WEBSITE_NAME_KEY, $website_name );
		}
	}

	function process_category_exclusion( $post ) {

		$categories = array();
		if ( isset( $post['excluded_categories'] ) ) {

			$categories = $post['excluded_categories'];
		}
		update_option( self::EXCLUDED_CATEGORIES_KEY, $categories );
		add_action( 'admin_notices', array( $this, 'excluded_categories_saved_notice' ) );
	}

	function process_post_type_inclusion( $post ) {

		$post_types = array();
		if ( isset($post['included_post_types'] ) ) {

			foreach ($post['included_post_types'] as $value) {
				
				$post_types[$value] = 1;
			}
		}
		update_option( self::POST_TYPES_KEY, $post_types );
		add_action( 'admin_notices', array( $this, 'included_post_types_saved_notice' ) );
	}

	function process_push( $post ) {

		$title = $post['title'];
		$body = $post['message'];
		$url_args = $post['url'];
		$this->send_push_notification( $title, $body, $url_args, true );
		wp_redirect( admin_url('?posted=1') ); 
		exit();
	}

	function big_invalid_account_key_notice() {

		$image_url = plugins_url( 'img/plugin-big-message-image.png', plugin_dir_path( __FILE__ ) );
		$settings_url = admin_url( 'admin.php?page=push_monkey_main_config' );
		require_once( plugin_dir_path( __FILE__ ) . '../templates/messages/push_monkey_big_message.php' );
	}

	function excluded_categories_saved_notice() {

		echo '<div class="updated"><p>Excluded categories successfuly updated! *victory dance*</p></div>';
	}

	function included_post_types_saved_notice() {

		echo '<div class="updated"><p>Included Post Types successfuly updated! *high five*</p></div>';	
	}
}
