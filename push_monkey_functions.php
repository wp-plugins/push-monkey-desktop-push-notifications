<?php
/**
 * PushMonkey
 */
class PushMonkey { 

	/* Public */

	public $endpointURL;
	public $apiClient;

	public function run() {

		$this->addActions();
	}

	public function hasAccountKey() {

		if( $this->accountKey() ) {

			return true;
		}
		return false;
	}

	public function accountKey() {

		$account_key = get_option( self::ACCOUNT_KEY_KEY, '' );
		if( ! $this->accountKeyIsValid( $account_key ) ) {

			return NULL;
		}
		return $account_key;
	}

	/* Private */

	const ACCOUNT_KEY_KEY = 'push_monkey_account_key';
	const WEBSITE_PUSH_ID_KEY = 'push_monkey_website_push_id_key';
	const WEBSITE_NAME_KEY = 'push_monkey_website_name';
	const EXCLUDED_CATEGORIES_KEY = 'push_monkey_excluded_categories';

	function __construct() {

		$this->endpointURL = "https://magic-pushmonkey.rhcloud.com";
		$this->apiClient = new PushMonkeyClient( $this->endpointURL );
	}

	function addActions() {

		add_action( 'init', array( $this, 'processForms' ) );

		add_action( 'init', array( $this, 'enqueueScripts' ) );

		add_action( 'init', array( $this, 'enqueueStyles' ) );

		add_action( 'wp_dashboard_setup', array( $this, 'addDashboardWidgets' ) );

		add_action( 'admin_menu', array( $this, 'registerMenuPage' ) );

		add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );

		add_action( 'transition_post_status', array( $this, 'postPublished' ), 10, 3 );

		add_action( 'admin_enqueue_scripts', array( $this, 'notificationPreviewScripts' ) );

		if( ! $this->hasAccountKey() ) {

			add_action( 'admin_notices', array( $this, 'bigInvalidAccountKeyNotice' ) );
		}
	}

	function accountKeyIsValid( $account_key ) {

		if( ! strlen( $account_key ) ) {

			return false;
		}
		return true;
	}

	function addDashboardWidgets() {

		wp_add_dashboard_widget( 'push_monkey_push_dashboard_widget', 'Send Push Notification - Push Monkey', array( $this, 'pushWidget' ) );	
		wp_add_dashboard_widget( 'push-monkey-stats-dashboard-widget', 'Stats - Push Monkey', array( $this, 'statsWidget') );	
	}

	function pushWidget() {

		$posted = isset( $_GET['posted'] );

		$account_key = false;
		if( $this->hasAccountKey() ) {

			$account_key = $this->accountKey();
		}
		require_once( dirname( __FILE__ ) . '/push_monkey_push_widget.php' );
	}

	function statsWidget() {

		if( ! $this->hasAccountKey() ) { 

?>
			<div class="error-message"> <p>Set an Account Key before you can use Push Monkey. Don't have an Account Key yet? <a href="http://www.getpushmonkey.com/register?source=plugin">Click here to get one</a>. <a href="http://www.getpushmonkey.com/help?source=plugin#q4" target="_blank">More info about this &#8594;</a></p> </div>
<?php
			echo '<img class="placeholder" src="' . plugins_url( '/img/plugin-stats-placeholder-small.jpg', __FILE__ ) . '"/>';
		} else {

			$account_key = $this->accountKey();
			$response = $this->apiClient->getStats( $account_key );
			if( is_wp_error( $response ) ) {

				echo 'Error Found ( '.$response->get_error_message().' )';
				echo '<img class="placeholder" src="' . plugins_url( '/img/plugin-stats-placeholder-small.jpg', __FILE__ ) . '"/>';
			} else {

				$body = wp_remote_retrieve_body( $response );
				$output = json_decode( $body ); 
				require_once( dirname( __FILE__ ) . '/push_monkey_stats_widget.php' );
			}
		}
		echo '<a href="' . admin_url( 'admin.php?page=push_monkey_main_config' ) . '">What is this?</a>';
	}

	function registerMenuPage() {

		$icon_url =	plugins_url( 'img/plugin-icon.png', __FILE__ );
		//NOTE: call a function to load this page. Loading a file instead of a function doesn't execute the page hook suffix.
		$hook_suffix = add_menu_page( 'Push Monkey ', 'Push Monkey', 'manage_options', 'push_monkey_main_config', array( $this, 'mainConfig' ), $icon_url );
		add_action( 'load-' . $hook_suffix , array( $this, 'mainConfigLoaded' ) );
		add_action( 'admin_print_styles-' . $hook_suffix , array( $this, 'enqueueStylesMainConfig' ) );
	}

	function mainConfig() {

		$website_name_key = self::WEBSITE_NAME_KEY;
		$push_monkey_account_key_key = self::ACCOUNT_KEY_KEY;

		$options = $this->getExcludedCategories();
		$args=array(
			'hide_empty' => 0,
			'order' => 'ASC'
		);
		$cats = get_categories( $args );
		$alt = 0;

		$has_account_key = false;
		$output = NULL;
		$placeholder_url = plugins_url( '/img/plugin-stats-placeholder.jpg', __FILE__ );
		if ( $this->hasAccountKey() ) {

			$has_account_key = true;
			$account_key = $this->accountKey();
			$response = $this->apiClient->getStats( $account_key );
			if( is_wp_error( $response ) ) {

				echo 'Error Found ( '.$response->get_error_message().' )';
			} else {

				$body = wp_remote_retrieve_body( $response );
				$output = json_decode( $body ); 
			}
		}
		require_once( dirname( __FILE__ ) . '/push_monkey_admin.php' );
	}

	function addMetaBox() {
		
		add_meta_box( 'push_monkey_post_opt_out', 'Push Monkey Options', array( $this, 'notificationPreviewMetaBox' ), 'post', 'side', 'high' );
	}

	function notificationPreviewMetaBox( $post ) {

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
		if( $this->hasAccountKey() ) {

			$account_key = $this->accountKey();
		}

		$max_len_title = 33;
		$title = $post->post_title;
		if ( strlen( $title ) > $max_len_title ) {

			$title = substr( $title, 0, $max_len_title ) . '...';
		}

		$max_len_body = 70;
		$body = $post->post_content;
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

	function notificationPreviewScripts( $hook_suffix) {

		if( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) {

			wp_enqueue_script( 'custom_js', plugins_url('/js/push_monkey_optout_metabox.js', __FILE__), array( 'jquery' ));
		}
	}

	function mainConfigLoaded() {

		remove_action( 'admin_notices', array( $this, 'bigInvalidAccountKeyNotice' ) );
	}

	function postPublished( $new_status, $old_status, $post ) {

		if ( ! $this->hasAccountKey() ) {

			return;
		}

		$can_send_push = false;
		if ( $old_status != 'publish' && $new_status == 'publish' && $post->post_type == 'post' ) {

			if( $this->canVerifyOptout() ) {

				$optout = $_POST['push_monkey_opt_out'];
				if( $optout != 'on' ) {

					if( ! $this->postHasExcludedCategory( $post ) ){

						$can_send_push = true;
					}
					update_post_meta( $post->ID, '_push_monkey_opt_out', 'off' );
				} else {

					update_post_meta( $post->ID, '_push_monkey_opt_out', $optout );
				}
			}
		}

		if( $can_send_push ) {

			$title = $post->post_title;
			$body = strip_tags($post->post_content);
			$post_id = $post->ID;
			$this->sendPushNotification( $title, $body, $post_id, false );
		}
	}

	function canVerifyOptout() {

		error_log( '===== canVerifyOptout' );
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

		error_log( '==== no optout' );
		return true;
	}

	function postHasExcludedCategory( $post ) {

		$excluded_categories = $this->getExcludedCategories();

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

	function getExcludedCategories() {

		$defaults = array();
		$options = get_option( self::EXCLUDED_CATEGORIES_KEY );

		if ( !is_array( $options ) ){

			$options = $defaults;
			update_option( self::EXCLUDED_CATEGORIES_KEY, $options );
		}
		return $options;
	}

	function sendPushNotification( $title, $body, $url_args, $custom ) {

		$account_key = $this->accountKey();
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

		$this->apiClient->sendPushNotification( $account_key, $title, $body, $url_args, $custom );
	}

	function websiteName() {

		$name = get_option( self::WEBSITE_NAME_KEY, '' );
		if( !strlen( $name ) ) {

			$name = get_bloginfo( 'name' );
		}
		return $name;
	}

	function websitePushID() {

		$website_push_id = get_option( self::WEBSITE_PUSH_ID_KEY, '' );
		if( strlen( $website_push_id ) ) {

			return $website_push_id;
		}

		$website_push_id = $this->apiClient->getWebsitePushID( $this->accountKey() );

		update_option( self::WEBSITE_PUSH_ID_KEY, $website_push_id );

		return $website_push_id;
	}

	function enqueueScripts() {

		if ( ! is_admin() ) {

			wp_enqueue_script( 'push_monkey_wp', plugins_url( '/js/push_monkey_wp.js', __FILE__ ) );
			$local_vars = array(
				'website_push_id' => $this->websitePushID(),
				'website_name' => $this->websiteName(),
				'endpoint_url' => $this->endpointURL
			);
			wp_localize_script( 'push_monkey_wp', 'push_monkey_locals', $local_vars );
		} else {

			wp_enqueue_script( 'push_monkey_modal', plugins_url( '/js/jquery.leanModal.min.js', __FILE__ ), array('jquery') );
			wp_enqueue_script( 'push_monkey_push_widget', plugins_url( '/js/push_monkey_push_widget.js', __FILE__ ) );
			wp_enqueue_script( 'push_monkey_charts', plugins_url( '/js/Chart.min.js', __FILE__ ), array( 'jquery' ) );
		}
	}

	function enqueueStyles( $hook_suffix ) {

		if ( is_admin() ) {

			wp_enqueue_style( 'push_monkey_dashboard_widget_style', plugins_url( 'css/widgets.css', __FILE__ ) );
			wp_enqueue_style( 'push_monkey_style', plugins_url( 'css/style.css', __FILE__ ) );
		}
	}

	function enqueueStylesMainConfig( ) {

		wp_enqueue_style( 'push_monkey_config_style', plugins_url( 'css/main-config.css', __FILE__ ) );
	}

	function processForms() {

		if( isset( $_POST['push_monkey_main_config_submit'] ) ) {

			$this->processMainConfig( $_POST );
		} else if( isset( $_POST['push_monkey_category_exclusion'] ) ) {

			$this->processCategoryExclusion( $_POST );
		} else if( isset( $_POST['push_monkey_push_submit'] ) ) {

			$this->processPush( $_POST );
		}
	}

	function processMainConfig( $post ) {

		$account_key = $post[self::ACCOUNT_KEY_KEY];
		if( $account_key ){

			//TODO: remove this hack
			if( $account_key == '00' ) {

				update_option( self::ACCOUNT_KEY_KEY, '' );
				update_option( self::WEBSITE_PUSH_ID_KEY, '' );
				return;
			}

			if( $this->accountKeyIsValid( $account_key ) ) {

				update_option( self::ACCOUNT_KEY_KEY, $account_key );
				update_option( self::WEBSITE_PUSH_ID_KEY, '' );
				remove_action( 'admin_notices', array( $this, 'invalidAccountKeyNotice' ) );

				add_action( 'admin_notices', array( $this, 'accountKeySavedNotice' ) );
			} else {

				add_action( 'admin_notices', array( $this, 'invalidAccountKeyNotice' ) );
			}
		} else {

			add_action( 'admin_notices', array( $this, 'invalidAccountKeyNotice' ) );
		}

		$website_name = $post[self::WEBSITE_NAME_KEY];
		if( $website_name ) {

			update_option( self::WEBSITE_NAME_KEY, $website_name );
		}
	}

	function processCategoryExclusion( $post ) {

		update_option( self::EXCLUDED_CATEGORIES_KEY, $post['excluded_categories'] );

		add_action( 'admin_notices', array( $this, 'excludedCategoriesSavedNotice' ) );
	}

	function processPush( $post ) {

		$title = $post['title'];
		$body = $post['message'];
		$url_args = $post['url'];
		$this->sendPushNotification( $title, $body, $url_args, true );
		wp_redirect( admin_url('?posted=1') ); 
		exit();
	}

	function bigInvalidAccountKeyNotice() {
?>
	<div class="error"> <p>Set an Account Key before you can use Push Monkey. Don't have an Account Key yet? <a href="http://www.getpushmonkey.com/register?source=plugin">Click here to get one</a>. <a href="http://www.getpushmonkey.com/help?source=plugin#q4" target="_blank">More info about this &#8594;</a>.</p> </div>
<?php
	}

	function accountKeySavedNotice() {

		echo '<div class="updated"><p>Account Key saved succesfully!</p></div>';
	}

	function invalidAccountKeyNotice() {

		echo '<div class="error"><p>You tried to save an invalid Account Key. Please <a href="http://www.getpushmonkey.com/help?source=plugin#q4" target="_blank">click here</a> for more info.</p></div>';
	}

	function excludedCategoriesSavedNotice() {

		echo '<div class="updated"><p>Excluded categories successfuly updated! *victory dance*</p></div>';
	}

}

/**
 * API Client
 */
class PushMonkeyClient {

	public $endpointURL;

	/* Public */
	
	public function getStats( $account_key ) {

		$stats_api_url = $this->endpointURL . '/stats/api';
		$args = array( 'body' => array( 'account_key' => $account_key ) );
		$response = wp_remote_post( $stats_api_url, $args );
		return $response;
	}

	public function getWebsitePushID( $account_key ) {

		$url = $this->endpointURL . '/clients/get_website_push_id';
		$args = array( 'body' => array( 'account_key' => $account_key ) );

		$response = wp_remote_post( $url, $args );

		if( is_wp_error( $response ) ) {

			echo 'Error Found ( '.$response->get_error_message().' )';
			return '';
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body ); 
		}
		return $output->website_push_id;
	}

	public function sendPushNotification( $account_key, $title, $body, $url_args, $custom ) {

		$url = $this->endpointURL . '/push_message';
		$args = array( 'body' => array( 
			'account_key' => $account_key,
			'title' => $title,
			'body' => $body, 
			'url_args' => $url_args,
		) );
		if ( $custom ) {

			$args['body']['custom'] = true;
		}

		$response = wp_remote_post( $url, $args );
		if( is_wp_error( $response ) ) {

			echo 'Error Found ( '.$response->get_error_message().' )';
		}
	}

	/* Private */

	function __construct( $endpoint_url ) {

		$this->endpointURL = $endpoint_url;
	}
}
