<div id="push-monkey-settings-wrapper">
	<h2>Push Monkey Configuration</h2>

	<?php if ( ! $has_account_key ) { ?>
	<img class="placeholder" src="<?php echo $placeholder_url; ?>"/>
	<?php } else { ?>
	<table cellspacing="0" class="form-table stats-container">
		<tr>
			<th class="">
				<div class="number-box box-light-orange">
					<p class="box-label">Subscribers</p>
					<p class="box-value">
						<?php echo $output->subscribers; ?>
					</p>
				</div><!-- .number-box -->
				<div class="number-box box-green">
					<p class="box-label">Sent Notifications</p>
					<p class="box-value">
						<?php echo $output->sent_notifications; ?>
					</p>
				</div><!-- .number-box -->
				<div class="number-box box-orange">
					<p class="box-label">Notifications</p>
					<p class="box-value">
						<?php echo $output->notifications; ?>
					</p>
				</div><!-- .number-box -->
			</th>
			<td>
				<div class="doughnut-chart-wrapper">
					<canvas id="doughnut-chart"></canvas>
					<div class="legend-dataset-three"> Remaining Notifications </div>
					<div class="legend-dataset-two"> Sent Notifications	</div>
				</div>
			</td>
			<td class="">
				<canvas id="chart"></canvas>
				<div class="col col-chart-legend">
					<div class="legend-dataset-two"> Sent Notifications	</div>
				</div>
				<div class="col col-chart-legend">
					<div class="legend-dataset-one"> Opened Notifications </div>
				</div>
			</td>
		</tr>
	</table>
	<?php } ?>

	<form name="push_monkey_main_config" method="post">
		<table class="form-table">
			<tbody>
				<?php if( ! $this->hasAccountKey() ){?>
				<tr>
					<th></th>
					<td>
						<h3 class="mandatory">Please enter a Website Push ID. Don't have one yet? <a href="http://www.getpushmonkey.com/register?source_plugin">Click here to get one</a>. <a href="http://getpushmonkey.com/help?source=plugin#q4" target="_blank">More info &#8594;</a></h3>
					</td>
				</tr>
				<?php }?>
				<tr class="<?php if( ! $this->hasAccountKey() ){?>mandatory<?php }?>">
				<th scope="row"> <label for="<?php echo $push_monkey_account_key_key; ?>">Account Key</label> </th>
				<td> 
					<input type="text" class="regular-text" value="<?php echo $this->accountKey(); ?>" name="<?php echo $push_monkey_account_key_key; ?>" id="<?php echo $push_monkey_account_key_key; ?>"> 
					<p class="description">This Account Key is used to identify your Wordpress website. You can find it <br /> by logging into your account at pushmonkey.com</p>
				</td>
			</tr>
			<tr>
				<th scope="row"> <label for="<?php echo $website_name_key; ?>">Website name</label> </th>
				<td> 
					<input type="text" class="regular-text" value="<?php echo $this->websiteName(); ?>" name="<?php echo $website_name_key; ?>" id="<?php $website_name_key; ?>"> 
					<p class="description">By default the website name is the same as the setting in Wordpress. Use <br /> this if you want to display a different name while sending push notifications.</p>
				</td>
			</tr>
			</tbody>
		</table>
		<input type="submit" class="button button-primary" value="Save Changes" name="push_monkey_main_config_submit" id="submit">
	</form>

	<h3 class="section-margin-top">Exclude categories</h3>
	<p class="description">
		Posts which fall in the following categories will not send push notifications when they are posted.
		By default, all posts send push notifications.
	</p>
	<form method="post">
		<table class="widefat">
			<thead>
				<tr>
					<th scope="col">Category Name</th>
					<th scope="col">Exclude from Push Monkey Notifications?</th>
				</tr>
			</thead>
			<tbody id="the-list">
			<?php foreach( $cats as $cat ) { ?>
			<tr<?php if ( $alt == 1 ) { echo ' class="alternate"'; $alt = 0; } else { $alt = 1; } ?>>
				<th scope="row"><?php echo $cat->cat_name; ?></th>
				<td>
					<input type="checkbox" name="excluded_categories[]" value="<?php echo $cat->cat_ID; ?>" <?php if ( in_array( $cat->cat_ID, $options ) ) { echo 'checked="true" '; } ?>/>
				</td>
			</tr>			
			<?php }//foreach ?>
			</tbody>
		</table>
		<input type="submit" name="push_monkey_category_exclusion" value="Update" class="button button-primary" />
	</form>
</div>

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
