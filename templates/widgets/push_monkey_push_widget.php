<div class="container">
	<?php if( $posted ) { ?>
		<div class="updated-message"><p>Push Notification Sent. Yay!</p></div>
	<?php } ?>
	<?php if(!$account_key) { ?>
	<div class="error-message"> 
		<p>
			Sign in before you can use Push Monkey. Don't have an account yet? 
			<a href="<?php echo $settings_url; ?>">Click here to sign up</a>. 
			<a href="http://www.getpushmonkey.com/help?source=plugin#q16" target="_blank">More info about this &#8594;</a>
		</p>
	</div>
	<?php } ?>
	<form method="post">
		<div class="row">
			<label for="title">
				Title
				<span>of the push message. 25 characters or less.</span>
			</label>
			<input type="text" class="regular-text" name="title" maxlength="25"/>
		</div>
		
		<div class="row">
			<label>
				Message
				<span>120 characters or less.</span>
			</label>
			<textarea class="regular-text" name="message" maxlength="120"></textarea>
		</div>

		<div class="row">
			<label>
				URL
				<span>Where the reader will land after clicking on the notification.</span>
			</label>
			<input type="text" class="regular-text" maxlength="100" name="url"/>
		</div>

		<input type="hidden" name="push_monkey_push_submit" value="1" />

		<div class="row">
			<a class="button button-primary" rel="leanModal" href="#push_monkey_confirmation_modal">Send</a>
		</div>
	</form>
</div>

<!-- Confirmation Modal -->
<div class="push_monkey_modal" id="push_monkey_confirmation_modal" style="display:none;">
	<div class="push_monkey_modal_inner">
		<p>
			Are you sure that you want to send this custom desktop push notification?
		</p>
	</div>
	<div class="push_monkey_modal_footer">
		<a class="button button-secondary close_modal" href="javascript:void(0);" >No</a>
		<a class="button button-primary push_monkey_submit" href="javascript:void(0);" >Yes. Send it.</a>
	</div>
</div>
