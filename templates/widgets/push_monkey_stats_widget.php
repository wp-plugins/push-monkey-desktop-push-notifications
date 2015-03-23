<div class="stats-container">
	<?php if ( $notice ) { $notice->render(); } ?>
	<div class="row clearfix">
		<div class="col-left col">
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
		</div><!-- .col-left -->
		<div class="col-right col">
			<div class="doughnut-chart-wrapper">
				<canvas id="doughnut-chart"></canvas>
				<div class="">
					<div class="legend-dataset-two"> Sent Notifications	</div>
				</div>
				<div class="">
					<div class="legend-dataset-three"> Remaining Notifications </div>
				</div>
			</div> 
		</div><!-- .col-right -->
	</div><!-- .row -->
	<div class="row clearfix">
		<div class="chart-wrapper">
			<canvas id="chart"></canvas>
		</div>
	</div><!-- .row -->
	<div class="row clearfix">
		<div class="col col-chart-legend">
			<div class="legend-dataset-two"> Sent Notifications	</div>
		</div>
		<div class="col col-chart-legend">
			<div class="legend-dataset-one"> Opened Notifications </div>
		</div>
	</div><!-- .row -->
</div><!-- .stats-container -->

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
