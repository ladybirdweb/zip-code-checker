jQuery( document ).ready(function() {
	jQuery(function(){

		// Bulk action checking
		jQuery('form#zip_codes-filter').submit(function(){

			var bulk_request = confirm( sidebar_zip_code_checker_params.confirm );
			if ( bulk_request != true ) {
				return false;
			}
		});
		
		jQuery('form#zip_codes-filter .row-actions span a').click(function(){
			
			var bulk_request = confirm( sidebar_zip_code_checker_params.confirm );
			if ( bulk_request != true ) {
				return false;
			}
			
		});

	});
});