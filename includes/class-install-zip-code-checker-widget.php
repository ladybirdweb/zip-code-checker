<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Sidebar_Zip_Code_Checker_Table class.
 */
class Sidebar_Zip_Code_Checker_Table  {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		global $zip_codedb_version;
		$this->zip_code_checker_install();
		// $this->zip_code_checker_install_data(); // Remove the comment for adding dummy data to zip code table
		add_action( 'plugins_loaded', 'zip_code_checker_update_db_check' );
	}

	/**
	 * zip_code_checker_install function.
	 *
	 * @access public
	 * @return void
	 */
	public function zip_code_checker_install() {
	   	global $wpdb;
	   	global $zip_codedb_version;
		$zip_codedb_version = "1.2";

	   	$table_name = $wpdb->prefix . "zip_code_checker";

	   	$sql = "CREATE TABLE $table_name (
	  		id mediumint(9) NOT NULL AUTO_INCREMENT,
		  	zip_code varchar(25) NOT NULL,
		  	company varchar(25) NOT NULL,
		  	message text NOT NULL,
		  	status text NOT NULL,
		  	cod tinytext NOT NULL,
		  	PRIMARY KEY (zip_code, company),
		  	UNIQUE KEY id (id)
    	);";

	   	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	   	dbDelta( $sql );

	   	add_option( "zip_codedb_version", $zip_codedb_version );

		$installed_ver = get_option( "zip_codedb_version" );

		if( $installed_ver != $zip_codedb_version ) {
		   	$sql = "CREATE TABLE $table_name (
		  		id mediumint(9) NOT NULL AUTO_INCREMENT,
			  	zip_code varchar(25) NOT NULL,
			  	company varchar(25) NOT NULL,
			  	message text NOT NULL,
			  	status text NOT NULL,
			  	cod tinytext NOT NULL,
			  	PRIMARY KEY (zip_code, company),
			  	UNIQUE KEY id (id)
	    	);";

		  	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		  	dbDelta( $sql );

		  	update_option( "zip_codedb_version", $zip_codedb_version );
		}
	}

	/**
	 * zip_code_checker_install_data function.
	 *
	 * @access public
	 * @return void
	 */
	function zip_code_checker_install_data() {
		global $wpdb;
	   	$table_name = $wpdb->prefix . "zip_code_checker";
	   	$array_data = array(
						array(
							'zip_code' 	=> __( '560078', 'sidebar_zip_code_checker' ),
							'company' 	=> __( 'India Post', 'sidebar_zip_code_checker' ),
							'message'	=> __( 'done', 'sidebar_zip_code_checker' ),
							'status' 	=> __( 'Yes. Delivery time can be up to 25 days. Might have to pick up from post office', 'sidebar_zip_code_checker' ),
							'cod' 		=> __( 'No', 'sidebar_zip_code_checker' )
						),
						array(
							'zip_code' 	=> __( '560078', 'sidebar_zip_code_checker' ),
							'company' 	=> __( 'FedEx', 'sidebar_zip_code_checker' ),
							'message'	=> __( 'done', 'sidebar_zip_code_checker' ),
							'status' 	=> __( 'Yes', 'sidebar_zip_code_checker' ),
							'cod' 		=> __( 'Yes', 'sidebar_zip_code_checker' )
						),
						array(
							'zip_code' 	=> __( '560079', 'sidebar_zip_code_checker' ),
							'company' 	=> __( 'FedEx', 'sidebar_zip_code_checker' ),
							'message'	=> __( 'done', 'sidebar_zip_code_checker' ),
							'status' 	=> __( 'Yes', 'sidebar_zip_code_checker' ),
							'cod' 		=> __( 'Yes', 'sidebar_zip_code_checker' )
						),
						array(
							'zip_code' 	=> __( '560080', 'sidebar_zip_code_checker' ),
							'company' 	=> __( 'India Post', 'sidebar_zip_code_checker' ),
							'message'	=> __( 'done', 'sidebar_zip_code_checker' ),
							'status' 	=> __( 'Yes. Delivery time can be up to 25 days. Might have to pick up from post office', 'sidebar_zip_code_checker' ),
							'cod' 		=> __( 'No', 'sidebar_zip_code_checker' )
						)
		);

		$array_data_count = count($array_data);

		if (!empty($array_data_count)) {

			for ($i=0; $i<$array_data_count; $i++) {
				$rows_affected = $wpdb->insert(
					$table_name,
					$array_data[$i]
				);
			}
		}


	}

	/**
	 * zip_code_checker_update_db_check function.
	 *
	 * @access public
	 * @return void
	 */
	public function zip_code_checker_update_db_check() {
	    global $zip_codedb_version;
	    if (get_site_option( 'zip_codedb_version' ) != $zip_codedb_version) {
	        $this->zip_code_checker_install();
	    }
	}


}

new Sidebar_Zip_Code_Checker_Table();
?>