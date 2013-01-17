<?php

// Create the function to output the contents of our Dashboard Widget

function wps_stats_widget() { ?>
	
	<!-- JS here as other js is only called on certain pages -->
	<script type="text/javascript">
		jQuery(function() {
			// Need to customise via the options API
    		    jQuery('.chart').easyPieChart({
        			animate: 2000,
        			size: 180,
        			trackColor: '#FFF',
        			scaleColor: false
    			});
    	
		});
	</script>

	<?php
		$all_tickets = new WP_Query( array( 'post_type' => 'wps_support', 'showposts' => -1, 'post_status' => 'publish' ) );
		$total = $all_tickets->found_posts;

		$resolved_tickets = new WP_Query( array( 'post_type' => 'wps_support', 'showposts' => -1, 'post_status' => 'publish', 'wps_status' => 'resolved') );
		$resolved = $resolved_tickets->found_posts;	

		$in_progress_tickets = new WP_Query( array( 'post_type' => 'wps_support', 'showposts' => -1, 'post_status' => 'publish', 'wps_status' => 'in-progress') );
		$in_progress = $in_progress_tickets->found_posts;

		$unresolved_tickets = new WP_Query( array( 'post_type' => 'wps_support', 'showposts' => -1, 'post_status' => 'publish', 'wps_status' => 'unresolved') );
		$unresolved = $unresolved_tickets->found_posts;		

		$percentage = ($resolved / $total) * 100;	
	?>

	<div class="table table_content" style="float:left; width:50%;">
		<h4>Progress</h4>
		<br />
		<?php if ($resolved > 1) { ?>
			<div class="chart" data-percent="<?php echo $percentage; ?>"><?php echo round($percentage); ?>%</div>
		<?php } else {
			
			echo 'Add some more tickets and resolve them to start seeing some data'; 
		
		} ?>
	</div>
	
	<div class="table table_discussion" style="float:left;">
		<h4>Stats</h4><br />
		<table>
			<tr class="first">
				<td class="b b-comments"><a href="edit-comments.php"><span class="total-count"><?php echo $total; ?></span></a></td>
				<td class="last t comments"><a href="edit-comments.php">Total Tickets</a></td>
			</tr>
			<tr>
				<td class="b b_approved"><a href='edit-comments.php?comment_status=approved'><span class="approved-count"><?php echo $unresolved; ?></span></a></td>
				<td class="last t"><a class='approved' href='edit-comments.php?comment_status=approved' style="color:red;">Unresolved Tickets</a></td>
			</tr>
			<tr><td class="b b-waiting"><a href='edit-comments.php?comment_status=moderated'><span class="pending-count"><?php echo $in_progress; ?></span></a></td>
				<td class="last t"><a class='waiting' href='edit-comments.php?comment_status=moderated' style="color: #E66F00;">In Progress Tickets</a></td>
			</tr>
			<tr><td class="b b-spam"><a href='edit-comments.php?comment_status=spam'><span class='spam-count'><?php echo $resolved; ?></span></a></td>
				<td class="last t"><a class='spam' href='edit-comments.php?comment_status=spam' style="color: green;">Resolved Tickets</a></td>
			</tr>
		</table>
	</div>

	<div class="clear"></div>

<?php } 

function wps_dashboard_widgets() {
	wp_add_dashboard_widget('wps_dashboard_widgets', 'Support Statistics', 'wps_stats_widget');	
} 
add_action('wp_dashboard_setup', 'wps_dashboard_widgets' ); 
?>