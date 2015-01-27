<div class="error clearfix push-monkey-expired-notice" style="background-image:url('<?php echo $image_url; ?>')"> 
	<div class="button-wrapper">
		<a href="<?php echo $upgrade_url; ?>" target="_blank" class="push-monkey-btn">Upgrade Now</a>
	</div>
	<div class="text-wrapper">
		<h4>Your Trial Plan Expired</h4>
		<p>
			<strong>Sad News: </strong>Because your trial plan is over, Desktop Push Notifications will 
			not be sent to any of your <strong><?php echo $subscribers; ?> subscribers.</strong>. 
		</p>
		<p>
			<strong>Good News: </strong>You can upgrade your account by <a href="<?php echo $upgradegrade_url; ?>" target="_blank">clicking here</a>. 
			If you're still not sure, 
			<a href="http://blog.getpushmonkey.com/2014/10/why-safari-desktop-push-notifications-matter/?source=big_expiration_notice">
			this article might help &#8594;</a>.
		</p>
	</div>
</div>
