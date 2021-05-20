<?php
/**
 *
 * Import data as posts from a CSV file.
 * CSV Importer
 *
 *
 */

class Sidebar_Zip_Code_Checker_CSV_Importer {
    var $defaults = array(
        'zip_code'      => null,
        'status'       	=> null,
        'cod'       	=> null,
        'company'    	=> null,
        'message'		=> null
    );

    var $log = array();

    /**
     * Determine value of option $name from database, $default value or $params,
     * save it to the db if needed and return it.
     *
     * @param string $name
     * @param mixed  $default
     * @param array  $params
     * @return string
     */
    function process_option($name, $default, $params) {
        if (array_key_exists($name, $params)) {
            $value = stripslashes($params[$name]);
        } elseif (array_key_exists('_'.$name, $params)) {
            // unchecked checkbox value
            $value = stripslashes($params['_'.$name]);
        } else {
            $value = null;
        }
        $stored_value = get_option($name);
        if ($value == null) {
            if ($stored_value === false) {
                if (is_callable($default) &&
                    method_exists($default[0], $default[1])) {
                    $value = call_user_func($default);
                } else {
                    $value = $default;
                }
                add_option($name, $value);
            } else {
                $value = $stored_value;
            }
        } else {
            if ($stored_value === false) {
                add_option($name, $value);
            } elseif ($stored_value != $value) {
                update_option($name, $value);
            }
        }
        return $value;
    }

    /**
     * Plugin's interface
     *
     * @return void
     */
    function form() {
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $this->post();
        }

        // form HTML {{{
?>

<div class="wrap">
    <h2><?php _e( 'Import Zip Codes in CSV Format', 'sidebar_zip_code_checker' ); ?></h2>
    <form class="add:the-list: validate" method="post" enctype="multipart/form-data">
        <!-- File input -->
        <p><label for="csv_import"><?php _e( 'Upload file:', 'sidebar_zip_code_checker' ); ?></label><br/>
            <input name="csv_import" id="csv_import" type="file" value="" aria-required="true" /></p>
        <p class="submit"><input type="submit" class="button" name="submit" value="<?php _e( 'Import', 'sidebar_zip_code_checker' ); ?>" /></p>
    </form>
</div><!-- end wrap -->

<?php
        // end form HTML }}}

    }

    function print_messages() {
        if (!empty($this->log)) {

        // messages HTML {{{
?>

<div class="wrap">
    <?php if (!empty($this->log['error'])): ?>

    <div class="error">

        <?php foreach ($this->log['error'] as $error): ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>

    </div>

    <?php endif; ?>

    <?php if (!empty($this->log['notice'])): ?>

    <div class="updated fade">

        <?php foreach ($this->log['notice'] as $notice): ?>
            <p><?php echo $notice; ?></p>
        <?php endforeach; ?>

    </div>

    <?php endif; ?>
</div><!-- end wrap -->

<?php
        // end messages HTML }}}

            $this->log = array();
        }
    }

    /**
     * Handle POST submission
     *
     * @param void
     * @return void
     */
    function post() {
        if (empty($_FILES['csv_import']['tmp_name'])) {
            $this->log['error'][] = __( 'No file uploaded, aborting.', 'sidebar_zip_code_checker' );
            $this->print_messages();
            return;
        }

        if (!current_user_can('publish_pages') || !current_user_can('publish_posts')) {
            $this->log['error'][] = __( 'You don\'t have the permissions to publish posts and pages. Please contact the blog\'s administrator.', 'sidebar_zip_code_checker' );
            $this->print_messages();
            return;
        }

        require_once 'File_CSV_DataSource/DataSource.php';

        $time_start = microtime(true);
        $csv = new File_CSV_DataSource;
        $file = $_FILES['csv_import']['tmp_name'];
        $this->stripBOM($file);

        if (!$csv->load($file)) {
            $this->log['error'][] = __( 'Failed to load file, aborting.', 'sidebar_zip_code_checker' );
            $this->print_messages();
            return;
        }

        // pad shorter rows with empty values
        $csv->symmetrize();

        // WordPress sets the correct timezone for date functions somewhere
        // in the bowels of wp_insert_post(). We need strtotime() to return
        // correct time before the call to wp_insert_post().
        $tz = get_option('timezone_string');
        if ($tz && function_exists('date_default_timezone_set')) {
            date_default_timezone_set($tz);
        }

        $skipped = 0;
        $imported = 0;
        foreach ($csv->connect() as $csv_data) {
            if ($post_id = $this->create_post($csv_data)) {
                $imported++;
            } else {
                $skipped++;
            }
        }

        if (file_exists($file)) {
            @unlink($file);
        }

        $exec_time = microtime(true) - $time_start;

        if ($skipped) {
            $this->log['notice'][] = '<b>' .__( 'Skipped', 'sidebar_zip_code_checker' ). " ". $skipped . __( ' rows (most likely due to empty values or duplicate entries.)', 'sidebar_zip_code_checker' ).'</b>';
        }
        $this->log['notice'][] = sprintf('<b>' .__( 'Imported', 'sidebar_zip_code_checker' ). " ". $imported .__( ' rows in %.2f seconds', 'sidebar_zip_code_checker' ). '</b>', $exec_time);
        $this->print_messages();
    }

    function create_post($data) {
    	global $wpdb;
		$wpdb->hide_errors();
	   	$table_name = $wpdb->prefix . "zip_code_checker";

        $data = array_merge($this->defaults, $data);

        $new_post = array(
            'zip_code'  => $data['zip_code'],
            'company' 	=> $data['company'],
            'message'	=> $data['message'],
            'status' 	=> $data['status'],
            'cod'		=> $data['cod']
        );

        if( !empty( $new_post['zip_code'] ) ) {
	        // create!
	        $id = $wpdb->insert(
				$table_name,
				$new_post
			);

	        return $id;
		}

    }


    /**
     * Delete BOM from UTF-8 file.
     *
     * @param string $fname
     * @return void
     */
    function stripBOM($fname) {
        $res = fopen($fname, 'rb');
        if (false !== $res) {
            $bytes = fread($res, 3);
            if ($bytes == pack('CCC', 0xef, 0xbb, 0xbf)) {
                $this->log['notice'][] = __( 'Getting rid of byte order mark...', 'sidebar_zip_code_checker' );
                fclose($res);

                $contents = file_get_contents($fname);
                if (false === $contents) {
                    trigger_error( __( 'Failed to get file contents.', 'sidebar_zip_code_checker' ), E_USER_WARNING);
                }
                $contents = substr($contents, 3);
                $success = file_put_contents($fname, $contents);
                if (false === $success) {
                    trigger_error( __( 'Failed to put file contents.', 'sidebar_zip_code_checker' ), E_USER_WARNING );
                }
            } else {
                fclose($res);
            }
        } else {
            $this->log['error'][] = __( 'Failed to open file, aborting.', 'sidebar_zip_code_checker' );
        }
    }
}


function sidebar_zip_code_checker_csv_admin_menu() {
    require_once ABSPATH . '/wp-admin/admin.php';
    $plugin = new Sidebar_Zip_Code_Checker_CSV_Importer;
    // add_menu_page('edit.php', 'CSV Importer For Zip Code', 'manage_options', __FILE__, array($plugin, 'form'));
    add_submenu_page( 'zip_code_list_table', 'CSV Importer For Zip Code', __( 'CSV Importer For Zip Code', 'sidebar_zip_code_checker' ), 'activate_plugins', 'zip_code_list_table_import', array($plugin, 'form') );
}

add_action('admin_menu', 'sidebar_zip_code_checker_csv_admin_menu');

?>