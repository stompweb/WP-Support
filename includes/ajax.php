<?php 

// Callback function for when a new comment is added, handled through WordPress' admin-ajax
function wps_add_comment() {
	
	if( !isset( $_POST['wps_nonce'] ) || !wp_verify_nonce($_POST['wps_nonce'], 'wps-nonce') )
		die('Permissions check failed');

	$current_user = wp_get_current_user();
	$username = $current_user->display_name;
	$user_email = $current_user->user_email;	

	$data = array(
    	'comment_post_ID' => $_POST["post_id"],
    	'comment_author' => $username,
    	'comment_author_email' => $user_email,    	
   	 	'comment_content' => $_POST["content"],
    	'user_id' => get_current_user_ID(),
    	'comment_approved' => 1,
	);

	$comment_id = wp_insert_comment($data);
	$comment = get_comment( $comment_id ); 

	?>

	<div id="ticket-comment" class="postbox" >
		<div style="float: left; padding: 3px;">
			<?php echo get_avatar( $comment->comment_author_email, 20 ); ?>
		</div>
		<h3 class="hndle wps-no-hand"><span><?php echo $comment->comment_author; ?></span> - <?php echo $comment->comment_date; ?></h3>
							
		<div class="inside">
			<?php echo apply_filters('the_content', $comment->comment_content); ?>
		</div>
	</div>	
	
	<?php die();
}
add_action('wp_ajax_wps_add_comment', 'wps_add_comment'); 

// Callback function for when a status is changed, handled through WordPress' admin-ajax
function wps_change_status() {
	
	if( !isset( $_POST['wps_nonce'] ) || !wp_verify_nonce($_POST['wps_nonce'], 'wps-nonce') )
		die('Permissions check failed');	

	wp_set_post_terms( $_POST["post_id"], $_POST["status"], 'wps_status' );
	
	die();
}
add_action('wp_ajax_wps_change_status', 'wps_change_status'); 
?>