jQuery(document).ready(function($) {
	
	$('#add-new-ticket').submit(function() {

		tinyMCE.triggerSave(true, true);
		content = $('#ticket-info-content').val();
		post_id = $('#post_id').val();		

		data = {
      		action: 'wps_add_comment',
      		wps_nonce: wps_vars.wps_nonce,
      		content: content,
      		post_id: post_id 
      	};

     	$.post(ajaxurl, data, function (response) {
			$('#ticket_div').before(response);
			tinymce.get('ticket-info-content').setContent('');			
		});	

		return false;

	});	

	$('#ticket_status').change(function() {

		$('#wps_loading').show();

		post_id = $('#post_id').val();
		status = $(this).val(); 	

		data = {
      		action: 'wps_change_status',
      		wps_nonce: wps_vars.wps_nonce,
      		post_id: post_id,
      		status: status
      	};	

     	$.post(ajaxurl, data, function (response) {
			$('#wps_loading').hide();			
		});	

	});			
		
});