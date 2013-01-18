<?php 

function wps_create_edit_ticket_subpage() {
	global $edit_ticket_page;
	$edit_ticket_page = add_submenu_page( 'edit.php?post_type=wps_support', 'Edit Ticket', 'Edit Ticket', 'publish_posts', 'edit-ticket', 'wps_create_edit_ticket_page' ); 
}
add_action('admin_menu', 'wps_create_edit_ticket_subpage'); 

/**
 * Lets get the show on the road - needs tidying up fo' sho'.
 */
function wps_create_edit_ticket_page()
{
	$issue_id = $_GET["ticket"]; 
	$issue = get_post($issue_id); 
	?>

	<div class="wrap">
		<div class="ticket-actions">
			<div class="ticket-left">

				<div class="ticket-info">
					<h2><?php echo '#' . $issue_id . ' - ' . $issue->post_title; ?></h2>
					<?php echo apply_filters('the_content', $issue->post_content); ?>
				</div>
				
				<div class="seperator"></div>

				<div id="poststuff">
					<div id="postbox-container-2" class="postbox-container">
						<div class="wps-comments"><?php
							
							$args = array( 'post_id' => $issue_id, 'order' => 'ASC', 'numberpost' => -1 );
 
							$comments_query = new WP_Comment_Query;
							$comments       = $comments_query->query( $args );

							if( $comments ) :
								foreach( $comments as $comment ) : ?>
									<div id="ticket-comment-<?php echo $comment->comment_id; ?>" class="postbox" >
							 			<div style="float: left; padding: 3px;">
							 				<?php echo get_avatar( $comment->comment_author_email, 20 ); ?>
							 			</div>
										<h3 class="hndle wps-no-hand"><span> <?php echo ' ' . $comment->comment_author; ?></span> - <?php echo $comment->comment_date; ?></h3>
							
										<div class="inside">
											<?php echo apply_filters('the_content', $comment->comment_content); ?>
										</div>
									</div>
								<?php endforeach;
							endif;
							?>
						</div>

						<div id="ticket_div"></div>
						
					</div>
						
					<div class="new" style="clear:both; border-top: 1px solid #EEE;">
						<h2 style="margin-top: 30px;">Add a new comment</h2>
						<a id="new-ticket"></a>
						<form method="POST" id="add-new-ticket">

						<?php 
		
							$args = array(
  			  					'textarea_rows' => 8,
    							'teeny' => true,
    							'quicktags' => false
							);

							wp_editor( ' ', 'ticket-info-content', $args ); 
						?>
							<input type="hidden" name="post_id" id="post_id" value="<?php echo $issue_id; ?>">
							<div class="ticket-buttons" style="margin-top:20px; float: right;">
								<input type="submit" name="wps-submit" id="wps_submit" class="button-primary" value="<?php _e('Add Comment', 'wps'); ?>"/>
							</div>
						</form>

					</div>
			

				</div>
			</div> <!-- End poststuff -->
		</div>
		<div class="ticket-right">
			<div id="poststuff" style="position: fixed;">
				<div id="postbox-container-1" class="postbox-container" style="position: ">
					<div id="side-sortables" class="meta-box-sortables"><div id="submitdiv" class="postbox" >
						<h3 class='hndle'><span>Ticket Details</span></h3>
						<div class="inside">
							<div class="submitbox" id="submitpost">
								<div id="misc-publishing-actions">
									<div class="misc-pub-section">
										<label for="post_status">
											<?php echo get_avatar( $issue->post_author, 32 ); ?>
											Opened by:
										</label>
										<span id="post-status-display">
											<?php echo get_author_name($issue->post_author); ?>
										</span>
									</div>
									<div class="misc-pub-section curtime">
										<span id="timestamp">
											Opened on: <b><?php echo $issue->post_date; ?></b>
										</span>
									</div>
										
									<div class="misc-pub-section" id="visibility">
										Comments: <span id="post-visibility-display"><?php echo get_comments_number( $issue_id ); ?></span>
									</div>										

								</div>
								<div class="clear"></div>
							</div>
							<div id="major-publishing-actions">
								<div id="delete-action">
									<a href="#new-ticket" class="button">New Comment</a>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
				<div id="pageparentdiv" class="postbox " >
					<h3 class='hndle'><span>Status</span></h3>
					<div class="inside">
						<form method="POST" id="change-status">
							<label class="screen-reader-text" for="parent_id">Parent</label>
							<?php 
								$status = wp_get_post_terms( $issue_id, 'wps_status' ); 
								$ticket_status = $status[0]->slug;
							?>
							<select name='ticket_status' id='ticket_status'>
								<option value="Unresolved" <?php if ($ticket_status == "unresolved") { echo 'selected'; } ?>>Unresolved</option>
								<option value="In Progress" <?php if ($ticket_status == "in-progress") { echo 'selected'; } ?>>In Progress</option>
								<option value="Resolved" <?php if ($ticket_status == "resolved") { echo 'selected'; } ?>>Resolved</option>			
							</select>
							<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" class="waiting" id="wps_loading" style="display:none;"/>
						</form>
					</div>	
				</div>
			</div>
		</div>
	</div>

<?php }