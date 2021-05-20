jQuery(function(){

	// Ajax Zip Code Checker
	jQuery('.widget_wp_sidebarzipcodechecker form').submit(function(){
	
		var $thisform = jQuery( this );
		var action    = $thisform.attr('action');

	    jQuery('.'+sidebar_zip_code_checker_params.error_class).remove();
		jQuery('.'+sidebar_zip_code_checker_params.success_class).remove();

	    // Check required fields as a minimum
	    var zip_code = $thisform.find('input[name="log"]').val();
		var zip_code_checker_cod = $thisform.find('input[name="zip_code_checker_cod"]').val();
		var zip_code_checker_display_company_name = $thisform.find('input[name="zip_code_checker_display_company_name"]').val();

	    if ( ! zip_code ) {
	    	$thisform.prepend('<p class="' + sidebar_zip_code_checker_params.error_class + '">' + sidebar_zip_code_checker_params.zip_code_required + '</p>');
	    	return false;
	    }

		$thisform.block({ message: null, overlayCSS: {
	        backgroundColor: '#fff',
	        opacity:         0.6
	    }});

	    var data = {
			action: 		'sidebar_zip_code_checker_process',
			zip_code: 		zip_code,
		};

		// Ajax action
		jQuery.ajax({
			url: sidebar_zip_code_checker_params.ajax_url,
			data: data,
			type: 'POST',
			success: function( response ) {

				// Get the valid JSON only from the returned string
				if ( response.indexOf("<!--SBZCC-->") >= 0 )
					response = response.split("<!--SBZCC-->")[1]; // Strip off before SBZCC

				if ( response.indexOf("<!--SBZCC_END-->") >= 0 )
					response = response.split("<!--SBZCC_END-->")[0]; // Strip off anything after SBZCC_END

				// Parse
				var result = jQuery.parseJSON( response );

				if ( result.success == 1 ) {
					if ( zip_code_checker_display_company_name == 1 ) {
						$thisform.append('<p class="' + sidebar_zip_code_checker_params.success_class + '"> <b>' + sidebar_zip_code_checker_params.via + ' :</b><i>' + result.company + '</i></p>');
					}
					if ( zip_code_checker_cod == 1 ) {
						$thisform.append('<p class="' + sidebar_zip_code_checker_params.success_class + '"> <b>' + sidebar_zip_code_checker_params.cod + ' : </b><i>' + result.cod + '</i></p>');
					}
					$thisform.append('<p class="' + sidebar_zip_code_checker_params.success_class + '"> <b>' + sidebar_zip_code_checker_params.delivery + ' : </b><i>' + result.status + '</i></p>');
					$thisform.append('<p class="' + sidebar_zip_code_checker_params.success_class + '"> <b>' + sidebar_zip_code_checker_params.message + ' : </b><i>' + result.message + '</i></p>');					
					$thisform.unblock();
				} else {
					$thisform.prepend('<p class="' + sidebar_zip_code_checker_params.error_class + '">' + result.error + '</p>');
					$thisform.unblock();
				}
			}

		});

		return false;
	});

});