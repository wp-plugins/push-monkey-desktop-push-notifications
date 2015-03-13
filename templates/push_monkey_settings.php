<div class="push-monkey">
	<div class="container-fluid">
		<?php if ( ! $signed_in ) { ?>
		<div class="row header">
			<div class="col-md-12">
				<img src="<?php echo plugins_url( 'img/push-monkey-logo-small.png', dirname( __FILE__ ) ); ?>" alt="Push Monkey Logo" />
			</div><!-- .col -->	
		</div><!-- .row --> 
		<div class="row content">
			<div id="sign-in-up-carousel" class="carousel slide" data-ride="carousel" data-interval="false">	
				<div class="carousel-inner" role="listbox">
					<div class="item <?php if( ! $sign_up ) { echo 'active'; } ?>">
						<div class="col-md-4 col-md-offset-2">
							<div class="login-box">
								<form method="POST" action="">
									<div class="row text-center">
										<?php if ( isset( $sign_in_error ) ) { ?>
										<div class="col-md-10 col-md-offset-1 text-left">
											<p class="bg-danger"><?php echo $sign_in_error; ?></p>
										</div><!-- .col -->
										<?php } ?>
										<div class="col-md-10 col-md-offset-1 text-left">
											<label class="first">E-mail</label>
											<input type="text" class="form-control" name="username">
										</div><!-- .col -->
										<div class="col-md-10 col-md-offset-1 text-left">
											<label>Password</label>
											<input type="password" class="form-control section-start" name="password">
										</div><!-- .col -->
										<input type="submit" value="Sign In" class="btn btn-lg btn-success" name="push_monkey_sign_in">
										<br /> 
										<a class="btn btn-primary" href="<?php echo $forgot_password_url; ?>">Forgot<br /> Password?</a>
									</div><!-- .row -->
								</form>
							</div><!-- .login-box -->
						</div><!-- .col -->	
						<div class="col-md-3">
							<div class="text-center new-account-box">
								<p>Don't have an account yet? Sign up now to start sending Desktop Push Notifications</p>
								<a class="btn btn-lg btn-success" href="#sign-in-up-carousel" role="button" data-slide="next">Sign Up</a>
							</div>
						</div><!-- .col -->
					</div><!-- .item -->
					<div class="item <?php if( $sign_up ) { echo 'active'; } ?>">
						<div class="col-md-4 col-md-offset-<?php if($is_subscription_version) { echo '2'; } else { echo '3'; } ?>">
							<div class="login-box">
								<form method="GET" action="<?php echo $register_url; ?>">
									<div class="row text-center">
										<div class="col-md-10 col-md-offset-1 text-left">
											<label class="first">First Name</label>
											<input type="text" class="form-control" name="first_name">
										</div><!-- .col -->
										<div class="col-md-10 col-md-offset-1 text-left">
											<label>E-mail</label>
											<input type="text" class="form-control section-start" name="email">
										</div><!-- .col -->
										<input type="hidden" value="<?php echo $return_url; ?>" name="returnURL"  />
										<input type="hidden" value="<?php echo $website_name; ?>" name="websiteName"  />
										<input type="hidden" value="<?php echo $website_url; ?>" name="websiteURL"  />
										<input type="hidden" value="1" name="registering"  />
										<input type="submit" value="Sign Up" class="btn btn-lg btn-success" name="submit">
										<br /> 
										<a class="btn btn-primary" href="#sign-in-up-carousel" role="button" data-slide="prev">Already have <br /> an account?</a>
									</div><!-- .row -->
								</form>
							</div><!-- .login-box -->
						</div><!-- .col -->	
						<?php if ($is_subscription_version){ ?>
						<div class="col-md-3">
								<br /><br /><br /><br />
								<br />
								<br />
								<img src="<?php echo $img_free_trial_src; ?>"/>
						</div><!-- .col -->
						<?php } ?>
					</div><!-- .item -->
				</div><!-- .carousel-inner -->
			</div><!-- .carousel -->
		</div><!-- .row -->
		<div class="row content hidden">
		</div><!-- .row -->
		<div class="row footer">
			<div class="col-md-3 text-center">
				<img src="<?php echo $img_notifs_src; ?>" class="" />
				<p>Send website notifications directly to the desktop when new content is fresh from the oven.</p>
			</div><!-- .col -->
			<div class="col-md-3 col-md-offset-1 text-center">
				<img src="<?php echo $img_stats_src; ?>" class="" />
				<p>Beautiful and easy-to-understand statistics for the Push Monkey performance are available directly in your Wordpress Dashboard</p>
			</div><!-- .col -->
			<div class="col-md-3 col-md-offset-1 text-center">
				<img src="<?php echo $img_filter_src; ?>" class="" />
				<p>With <strong>Granular Filtering</strong> you can select which post categories don't send push notifications, as easy as you can say ba-na-na. No spam around here!</p>
			</div><!-- .col -->
		</div><!-- .row -->

		<?php } else { ?>

		<div class="row header">
			<div class="col-md-6">
				<img src="<?php echo plugins_url( 'img/push-monkey-logo-small.png', dirname( __FILE__ ) ); ?>" alt="Push Monkey Logo" />
			</div><!-- .col -->	
			<div class="col-md-4 text-right">
				<span><?php echo $email; ?></span> 
				<a href="<?php echo $logout_url; ?>">Sign Out</a>
				<?php if ( $plan_name ) { ?>
				<br /><br />
				<div>
					<p>You're rocking the <strong><?php echo $plan_name; ?></strong> plan.</p>
					<?php if ( $plan_can_upgrade ) { ?>
					<a class="btn btn-success btn-xs" href="<?php echo $upgrade_url; ?>" target="_blank">Upgrade Now?</a> 
					<?php } ?>
				</div>
				<?php } else if ( $plan_expired ) { ?>
				<br /><br />
				<div>	
					<p class="text-danger">Your plan expired.</p>
					<?php if ( $plan_can_upgrade ) { ?>
					<a class="btn btn-danger btn-xs" href="<?php echo $upgrade_url; ?>" target="_blank">Upgrade Now</a> 
					<?php } ?>
				</div>
				<?php } ?>
			</div><!-- .col -->
		</div><!-- .row --> 
		<?php if( $registered ) { ?>
		<div class="row">
			<div class="col-md-10 text-left">
				<p class="bg-success">Welcome to Push Monkey! May your push notifications be merry and your readers happy!</p>
				<br /><br />
			</div><!-- .col -->
		</div><!-- .row -->
		<?php } ?>
		<div class="row stats-container">
			<div class="col-md-2">
				<div class="number-box box-green">
					<p class="box-label">Sent Notifications</p>
					<p class="box-value">
						<?php echo $output->sent_notifications; ?>
					</p>
				</div><!-- .number-box -->
				<div class="number-box box-light-orange">
					<p class="box-label">Subscribers</p>
					<p class="box-value">
						<?php echo $output->subscribers; ?>
					</p>
				</div><!-- .number-box -->
				<div class="number-box box-orange">
					<p class="box-label">Posts</p>
					<p class="box-value">
						<?php echo $output->notifications; ?>
					</p>
				</div><!-- .number-box -->
			</div><!-- .col -->
			<div class="col-md-3">
				<div class="doughnut-chart-wrapper">
					<canvas id="doughnut-chart"></canvas>
					<div class="legend-dataset-two"> Sent Notifications </div>
					<div class="legend-dataset-three"> Remaining Notifications </div>
				</div>
			</div><!-- .col -->
			<div class="col-md-5">
				<canvas id="chart"></canvas>
				<div class="col col-chart-legend">
					<div class="legend-dataset-two"> Sent Notifications </div>
				</div>
				<div class="col col-chart-legend">
					<div class="legend-dataset-one"> Opened Notifications </div>
				</div>
			</div><!-- .col -->
		</div><!-- .row -->

		<form name="push_monkey_main_config" method="post" class="settings">
			<div class="row">
				<div class="col-md-2">
					<label for="<?php echo $website_name_key; ?>">Website name</label>
					
				</div><!-- .col -->
				<div class="col-md-6">
					<input type="text" class="regular-text" value="<?php echo $website_name; ?>" name="<?php echo $website_name_key; ?>" id="<?php $website_name_key; ?>"> 
					<p class="description">
						By default the website name is the same as the setting in Wordpress. Use <br /> this 
						if you want to display a different name while sending push notifications.
					</p>
				</div><!-- .col -->
			</div><!-- .row -->
			<div class="row">
				<div class="col-md-3">
					<input type="submit" class="btn btn-success" value="Save Changes" 
					name="push_monkey_main_config_submit" id="submit">
				</div><!-- .col -->
			</div><!-- .row -->
		</form>

		<div class="row">
			<div class="col-md-8">
				<h3 class="section-margin-top">Post types that send Desktop Push Notifications</h3>
				<p class="description">
					By default, all post types send Desktop Push Notifications.
				</p>
			</div><!-- .col -->
		</div><!-- .row -->

		<form method="post" id="post-types">
			<div class="row">
				<div class="col-md-8">
					<table class="table table-striped">
						<thead>
							<tr>
								<th scope="col">Post Type</th>
								<th scope="col">Send Desktop Push Notification?</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach( $post_types as $post_type=>$post_type_name ) { ?>
							<tr>
								<td class="col-md-3"><?php echo $post_type_name; ?></td>
								<td class="col-md-3">
									<input type="checkbox" data-on-text="Yes" data-off-text="No" data-on-color="success" name="included_post_types[]" value="<?php echo $post_type; ?>" <?php if ( array_key_exists( $post_type, $set_post_types ) ) { echo 'checked="true" '; } ?>/>
								</td>
							</tr>			
							<?php }//foreach ?>
						</tbody>
					</table>
				</div><!-- .col -->			
			</div><!-- .row -->
			<div class="row">
				<div class="col-md-3">
					<input type="submit" name="push_monkey_post_type_inclusion" value="Update" class="btn btn-success" />					
				</div><!-- .col -->
			</div><!-- .row -->
		</form>

		<div class="row">
			<div class="col-md-8">
				<h3 class="section-margin-top">Exclude categories</h3>
				<p class="description">
					Standard Posts which have the following categories will not send push notifications when they are posted.
					By default, all standard posts send push notifications.
					If standard post types are configured to not send Desktop Push Notifications (from the switches above), 
					category exclusion is disabled.
				</p>
			</div><!-- .col -->
		</div><!-- .row -->

		<form method="post" id="post-categories">
			<div class="row">
				<div class="col-md-8">
					<table class="table table-striped">
						<thead>
							<tr>
								<th scope="col">Category Name</th>
								<th scope="col">Exclude from Push Monkey Notifications?</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach( $cats as $cat ) { ?>
							<tr>
								<td class="col-md-3"><?php echo $cat->cat_name; ?></td>
								<td class="col-md-3">
									<input type="checkbox" data-on-text="Yes" data-off-text="No" data-on-color="success" name="excluded_categories[]" value="<?php echo $cat->cat_ID; ?>" <?php if ( in_array( $cat->cat_ID, $options ) ) { echo 'checked="true" '; } ?>/>
								</td>
							</tr>			
							<?php }//foreach ?>
						</tbody>
					</table>
				</div><!-- .col -->			
			</div><!-- .row -->
			<div class="row">
				<div class="col-md-3">
					<input type="submit" name="push_monkey_category_exclusion" value="Update" class="btn btn-success" />					
				</div><!-- .col -->
			</div><!-- .row -->
		</form>

		<div class="row">
			<div class="col-md-8">
				<h3 class="section-margin-top">Subscribe Banner</h3>
				<p class="description">
					When readers access your website from a Mac, but not using Safari, you
					can display a Subscribe Banner, to let them know about Desktop Push Notifications.
					<a href="http://www.getpushmonkey.com/help?source=plugin#q15" target="_blank">More info about this &#8594;</a>
				</p>				
			</div><!-- .col -->
		</div><!-- .row -->

		<form class="form-horizontal" method="post">	
			<div class="row form-group">
				<label class="col-md-2 control-label">Position</label>
				<div class="col-md-4">
					<select class="form-control select-picker" data-style="btn-success">
						<option value="top" <?php if ( $banner_position == 'top' ) { echo 'selected="selected"'; } ?>>Top </option>
						<option value="topLeft" <?php if ( $banner_position == 'topLeft' ) { echo 'selected="selected"'; } ?>>Top Left</option>
						<option value="centerRight" <?php if ( $banner_position == 'centerRight' ) { echo 'selected="selected"'; } ?>>Center Right</option>
						<option value="centerLeft" <?php if ( $banner_position == 'centerLeft' ) { echo 'selected="selected"'; } ?>>Center Left</option>
						<option value="topRight" <?php if ( $banner_position == 'topRight' ) { echo 'selected="selected"'; } ?>>Top Right</option>
						<option value="bottom" <?php if ( $banner_position == 'bottom' ) { echo 'selected="selected"'; } ?>>Bottom</option>
						<option value="bottomLeft" <?php if ( $banner_position == 'bottomLeft' ) { echo 'selected="selected"'; } ?>>Bottom Left</option>
						<option value="bottomRight" <?php if ( $banner_position == 'bottomRight' ) { echo 'selected="selected"'; } ?>>Bottom Right</option>
						<option value="disabled" <?php if ( $banner_position == 'disabled' ) { echo 'selected="selected"'; } ?>>Disabled</option>
					</select>
				</div>
				<div class="col-sm-1">
					<div class="banner-demo text-center">
						<div class="banner <?php echo $banner_position_classes[$banner_position]; ?> text-center"></div>
						<span>Preview</span>
					</div>
				</div><!-- .col -->
				<div class="col-sm-2" id="position-save-cue">
					<p class="bg-success">
					<span class="glyphicon glyphicon-ok"></span> Saved
					</p>
				</div><!-- .col -->
			</div><!-- .row -->
			<div class="row form-group">
				<label class="col-md-2 control-label">Disabled on Home</label>
				<div class="col-md-4">
					<input type="checkbox" data-on-text="Yes" data-off-text="No" data-on-color="success" 
					name="push_monkey_banner_disabled_on_home" value="1" <?php if ( $banner_disabled_home ) { echo 'checked="true" '; } ?>/>
					<span class="help-block">When "Yes", the banner is disabled on the homepage, but active on all other parts of your website.</span>
				</div>
			</div><!-- .row -->
			<div class="row form-group">
				<label class="col-md-2 control-label">Banner Text</label>
				<div class="col-md-4">
					<input type="text" class="form-control" name="push_monkey_banner_text" value="<?php echo $banner_text; ?>"/>
					<span class="help-block">
					This will the text displayed inside the banner. Key words: <br />
					<em>{% site_name %}</em> - to display the site name <br />
					<em>{% separator %}</em> - for a special separator.
					</span>
				</div>
			</div><!-- .row -->
			<div class="row form-group" id="picker_container">
				<label class="col-md-2 control-label">Banner Background Color</label>
				<div class="col-md-4">
					<div class="input-group banner-color-input">
						<input type="text" value="<?php echo $banner_color; ?>" class="form-control" name="push_monkey_banner_color" />
						<span class="input-group-addon"><i></i></span>
					</div>
					<span class="help-block">Default value is #2fcc70.</span>
				</div>
			</div><!-- .row -->
			<div class="row form-group">
				<div class="col-md-3">
					<input type="submit" name="push_monkey_banner" value="Update" class="btn btn-success" />					
				</div><!-- .col -->				
			</div><!-- .row -->
		</form>

		<div class="row">
			<div class="col-md-8">
				<h3 class="section-margin-top">Notifications Format</h3>
				<p class="description">
					You can customise how your readers see the notifications they receive, currently in 2 formats. 
					Choose one of the options bellow.
				</p>				
			</div><!-- .col -->
		</div><!-- .row -->

		<form class="form-horizontal" method="post">	
			<div class="row form-group">
				<div class="col-md-3">
					<input type="radio" name="push_monkey_notification_format" value="standard" class=""/>
					<div class="selection-box <?php if ( ! $notification_is_custom ) { echo 'selected'; }?>">
						<div class="selection-inner">
							<img src="<?php echo $notification_format_image; ?>" class="img-responsive"/>
							<div class="notification">
								<img class="icon" src="<?php echo $this->endpointURL; ?>/clients/icon/<?php echo $account_key; ?>" />		
								<p>
									<strong>Fairly short post title</strong> 
									<br /> 
									<span>Post body that would fit inside the notification</span>
								</p>
							</div>
						</div>
						<div class="checkmark">
							<span class="glyphicon glyphicon-ok"></span>
						</div>
					</div>
				</div><!-- .col -->
				<div class="col-md-3">
					<input type="radio" name="push_monkey_notification_format" value="static-title" class=""/>					
					<div class="selection-box <?php if ( $notification_is_custom ) { echo 'selected'; }?>">
						<div class="selection-inner">
							<img src="<?php echo $notification_format_image; ?>" class="img-responsive"/>
							<div class="notification">
								<img class="icon" src="<?php echo $this->endpointURL; ?>/clients/icon/<?php echo $account_key; ?>" />		
								<p>
									<strong id="push_monkey_preview_title"><?php echo $notification_custom_text; ?></strong> 
									<br /> 
									<span id="push_monkey_preview_content">Longer post title that fits this are better</span>
								</p>
							</div>
						</div>
						<div class="checkmark">
							<span class="glyphicon glyphicon-ok"></span>
						</div>
					</div>
				</div><!-- .col -->
			</div><!-- .row -->
			<div class="row form-group">
				<div class="col-md-3 col-md-offset-3">
					<label for="custom-text" class="control-label">Custom Text</label>
					<input name="custom-text" id="custom-text" class="form-control" <?php if ( ! $notification_is_custom ) { echo 'disabled'; }?> 
					value="<?php echo $notification_custom_text; ?>"/>
				</div><!-- .col -->
			</div><!-- .row -->
			<div class="row form-group">
				<div class="col-md-3">
					<input type="submit" name="push_monkey_notification_config" value="Update" class="btn btn-success" />					
				</div><!-- .col -->				
			</div><!-- .row -->
		</form>
	</div><!-- .container-fluid -->
</div><!-- .push-monkey -->

<script type="text/javascript">
	var doughnutData = [
	{
		value: <?php echo $output->sent_notifications; ?>,
		color: "#2FCC70",
		highlight: "#28b263",
		label: "Sent Notifications"
	},
	{
		value: <?php echo $output->remaining_notifications?>,
		color:"#8E8E8B",
		highlight: "#727270",
		label: "Remaining"
	}
	];
	var doughnutOptions = {

		segmentShowStroke : false,
					percentageInnerCutout : 70, // This is 0 for Pie charts
					animationSteps : 100,
					animationEasing : "easeOutBounce",
					animateRotate : true,
					animateScale : true,
					responsive: true
				};

				var options = {

					scaleShowGridLines : true,
					scaleGridLineColor : "rgba(0,0,0,.05)",
					scaleGridLineWidth : 1,
					bezierCurve : true,
					bezierCurveTension : 0.4,
					pointDot : true,
					pointDotRadius : 5,
					pointDotStrokeWidth : 3,
					pointHitDetectionRadius : 20,
					datasetStroke : true,
					datasetStrokeWidth : 0,
					datasetFill : true,
					responsive: true,
					maintainAspectRatio: false
				};
				var data = {

					labels: ['<?php echo implode("','", $output->labels_dataset); ?>'],
					datasets: [
					{
						label: "Sent Notifications",
						fillColor: "#2FCC70",
						strokeColor: "#2FCC70",
						pointColor: "#2FCC70",
						pointStrokeColor: "#2FCC70",
						pointHighlightFill: "#8E8E8B",
						pointHighlightStroke: "#8E8E8B",
						data: [<?php echo implode(",", $output->sent_notifications_dataset); ?>]
					},
					{
						label: "Opened Notifications",
						fillColor: "#363636",
						strokeColor: "#363636",
						pointColor: "#363636",
						pointStrokeColor: "#363636",
						pointHighlightFill: "#8E8E8B",
						pointHighlightStroke: "#8E8E8B",
						data: [<?php echo implode(",", $output->opened_notifications_dataset); ?>]
					}
					]
				};

				jQuery(document).ready(function($) {

					var ctx = $("#chart").get(0).getContext("2d");
					var myNewChart = new Chart(ctx).Line(data, options);

					var ctx2 = $("#doughnut-chart").get(0).getContext("2d");
					var doughnutChart = new Chart(ctx2).Doughnut(doughnutData, doughnutOptions);
				});
			</script>
			<?php } ?>
