<?php
/**
 * Create List Tables using official WordPress APIs for Zip_Code_List_Table.
 *
 * @subpackage Zip_Code_List_Table
 * @package WP_List_Table
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}




/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 *
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 *
 * Our theme for this list table is going to be movies.
 */
class Zip_Code_List_Table extends WP_List_Table {

    /** ************************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query().
     *
     * @var array
     **************************************************************************/


    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'zip_code',     	//singular name of the listed records
            'plural'    => 'zip_codes',    	//plural name of the listed records
            'ajax'      => false        	//does this table support ajax?
        ) );

    }


	/**
	 * Display the table with edit.
	 *
	 * @since  1.0
	 * @access public
	 */
	function edit_display($items) {
		extract( $this->_args );
		global $wpdb;
		$wpdb->hide_errors();
		$update_count = 0;
		$count = 0;
		$table_name = $wpdb->prefix . "zip_code_checker";

		if (!empty($_POST['zip_code'])) {
			$num_count = count($_POST['zip_code']);

			for ($i=0; $i<$num_count; $i++) {
				$count++;
				$update_result = $wpdb->query( 'UPDATE '.$table_name.' SET zip_code="' . $_POST['zip_code'][$i] . '", company="' . $_POST['company'][$i] .'", message="' . $_POST['message'][$i] .'", status="' . $_POST['status'][$i] .'", cod="' . $_POST['cod'][$i] .'" WHERE id= ' . $_POST['id'][$i] , ARRAY_A );
				if ($update_result == 1) {
					$update_count++;
				}
			}
		}


		?>
		<div class="wrap">

        <div id="icon-users" class="icon32"><br/></div>
	        <h2><?php _e( 'Zip Code List Table', 'sidebar_zip_code_checker' ); ?></h2>
	        <?php if (!empty($_POST['update'])) { ?>
			<div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
	            <p><?php echo $update_count; ?><?php _e( 'Items edited.', 'sidebar_zip_code_checker' ); ?> </p>
	            <p><?php echo ( $count - $update_count ); ?><?php _e( 'Items skipped. (most likely due to empty values or duplicate entries)', 'sidebar_zip_code_checker' ); ?></p>
	        </div>
	        <?php } ?>
			<form method="post" action="">
				<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
					<caption> </caption>
					<thead>
					<tr>
						<?php $this->print_column_headers(); ?>
					</tr>
					</thead>

					<tfoot>
					<tr>
						<?php $this->print_column_headers( false ); ?>
					</tr>
					</tfoot>

					<tbody id="the-list"<?php if ( $singular ) echo " data-wp-lists='list:$singular'"; ?>>
						<?php

						$results = $wpdb->get_results( 'SELECT * FROM '.$table_name.'  WHERE id IN ('. $items .') ORDER BY id', ARRAY_A );
						foreach ($results as $key => $value) {
							?>
							<tr class="alternate">
								<th scope="row" class="check-column">
									<input type="hidden" name="id[]" value="<?php _e( $value['id'], 'sidebar_zip_code_checker' ); ?>" />
								</th>
								<td class='title column-title'>
									<input type="text" name="zip_code[]" value="<?php echo $value['zip_code'];?>" required /> <span style="color:silver">(id:<?php echo $value['id'];?>)</span>
								</td>
								<td class='company column-company'><input type="text" name="company[]" placeholder="<?php _e( 'Company Name should be FedEx,India Post.', 'sidebar_zip_code_checker' ); ?>" value="<?php _e( $value['company'], 'sidebar_zip_code_checker' ); ?>" required/></td>
								<td class='message column-message'><input type="text" name="message[]" placeholder="<?php _e( 'Add message here', 'sidebar_zip_code_checker' ); ?>" value="<?php _e( $value['message'], 'sidebar_zip_code_checker' ); ?>" required/></td>
								<td class='status column-status'>
									<select name="status[]">
									  <option value="Yes" <?php selected( $value['status'], "Yes"); ?> ><?php _e( 'Yes', 'sidebar_zip_code_checker' ); ?></option>
									  <option value="No" <?php selected( $value['status'], "No"); ?> ><?php _e( 'No', 'sidebar_zip_code_checker' ); ?></option>
									</select>
								</td>
								<td class='cod column-cod'>
									<select name="cod[]">
									  <option value="Yes" <?php selected( $value['cod'], "Yes"); ?> ><?php _e( 'Yes', 'sidebar_zip_code_checker' ); ?></option>
									  <option value="No" <?php selected( $value['cod'], "No"); ?> ><?php _e( 'No', 'sidebar_zip_code_checker' ); ?></option>
									</select>
								</td>
							</tr>
							<?php
						}
						?>
						<tr><td colspan="2"><input type="submit" name="update" value="<?php _e( 'Update', 'sidebar_zip_code_checker' ); ?>" /></td></tr>
					</tbody>
				</table>
			</form>
		</div>
		<?php
	}

	/**
	 * zip_code_checker_insert_data function.
	 *
	 * @access public
	 * @return void
	 */
	function zip_code_checker_insert_data() {
		global $wpdb;
		$wpdb->hide_errors();
	   	$table_name = $wpdb->prefix . "zip_code_checker";
		if (!empty($_POST['insert'])) {
			$array_data = array(
				'zip_code' 	=> __( $_POST['zip_code'], 'sidebar_zip_code_checker' ),
				'company' 	=> __( $_POST['company'], 'sidebar_zip_code_checker' ),
				'message' 	=> __( $_POST['message'], 'sidebar_zip_code_checker' ),
				'status' 	=> __( $_POST['status'], 'sidebar_zip_code_checker' ),
				'cod' 		=> __( $_POST['cod'], 'sidebar_zip_code_checker' )
			);

			$rows_affected = $wpdb->insert(
				$table_name,
				$array_data
			);
		}
		?>
		<div class="wrap">

        <div id="icon-users" class="icon32"><br/></div>
	        <h2><?php _e( 'Zip Code List Table', 'sidebar_zip_code_checker' ); ?></h2>
	        <?php if (!empty($_POST['insert'])) { ?>
			<div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
	            <?php if($rows_affected == 1 ) { ?><p><?php echo $rows_affected; _e( 'Item inserted.', 'sidebar_zip_code_checker' ); ?> </p> <?php } ?>
	            <?php if(empty( $rows_affected )) { ?><p><?php _e( ' Item skipped. (most likely due to empty values or duplicate entries)', 'sidebar_zip_code_checker' );?></p> <?php } ?>
	        </div>
	        <?php } ?>
			<form method="post" action="">
				<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
					<caption> </caption>
					<thead>
					<tr>
						<?php $this->print_column_headers(); ?>
					</tr>
					</thead>

					<tfoot>
					<tr>
						<?php $this->print_column_headers( false ); ?>
					</tr>
					</tfoot>

					<tbody id="the-list">
							<tr class="alternate">
								<th scope="row" class="check-column">
									<input type="hidden" name="id" value="" />
								</th>
								<td class='title column-title'>
									<input type="text" name="zip_code" value="<?php if(!empty($_POST)) echo $_POST['zip_code'];?>" required />
								</td>
								<td class='company column-company'><input type="text" name="company" placeholder="<?php _e( 'Company Name should be FedEx,India Post.', 'sidebar_zip_code_checker' );?>" value="<?php if(!empty($_POST)) _e( $_POST['company'], 'sidebar_zip_code_checker' ); ?>" required/></td>
								<td class='message column-message'><input type="text" name="message" placeholder="<?php _e( 'Add message here', 'sidebar_zip_code_checker' );?>" value="<?php if(!empty($_POST)) _e( $_POST['message'], 'sidebar_zip_code_checker' ); ?>" required/></td>
								<td class='status column-status'>
									<select name="status">
									  <option value="Yes" <?php if(!empty($_POST)) selected( $_POST['status'], "Yes"); ?> ><?php _e( 'Yes', 'sidebar_zip_code_checker' );?></option>
									  <option value="No"  <?php if(!empty($_POST)) selected( $_POST['status'], "No"); ?> ><?php _e( 'No', 'sidebar_zip_code_checker' );?></option>
									</select>
								</td>
								<td class='cod column-cod'>
									<select name="cod">
									  <option value="Yes" <?php if(!empty($_POST)) selected( $_POST['cod'], "Yes"); ?> ><?php _e( 'Yes', 'sidebar_zip_code_checker' );?></option>
									  <option value="No"  <?php if(!empty($_POST)) selected( $_POST['cod'], "No"); ?> ><?php _e( 'No', 'sidebar_zip_code_checker' );?></option>
									</select>
								</td>
							</tr>
						<tr><td colspan="2"><input type="submit" name="insert" value="<?php _e( 'Insert', 'sidebar_zip_code_checker' );?>" /></td></tr>
					</tbody>
				</table>
			</form>
		</div>
		<?php
	}


    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
            case 'company':
			case 'message':
            case 'status':
			case 'cod':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }


    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_title($item){

        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&zip_code=%s">' . __( 'Edit', 'sidebar_zip_code_checker' ) . '</a>',$_REQUEST['page'],'edit',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&zip_code=%s">' . __( 'Delete', 'sidebar_zip_code_checker' ) . '</a>',$_REQUEST['page'],'delete',$item['id']),
        );

        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['zip_code'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
        	'title'  	=> __( 'Zip Code', 'sidebar_zip_code_checker' ),
            'company'   => __( 'Company(<i> Company Name should be FedEx,India Post. </i>)', 'sidebar_zip_code_checker' ),
            'message'	=> __( 'message', 'sidebar_zip_code_checker' ),
            'status'  	=> __( 'Delivery ( <i>FedEx status must be Yes. </i>)', 'sidebar_zip_code_checker' ),
            'cod'  	=>  __( 'COD', 'sidebar_zip_code_checker' )
        );
        return $columns;
    }


    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'  	=> array('title',false),     //true means it's already sorted
            'company'   => array('company',false),
            'message' 	=> array('message',false),
            'status'  	=> array('status',false),
            'cod'  		=> array('cod',false)
        );
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete',
            'edit'    	=> 'Edit'
        );
        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
    	global $wpdb; //This is used only if making any database queries

        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
        	$table_name = $wpdb->prefix . "zip_code_checker";
			if (!empty($_GET['zip_code'])) {
				$items_to_be_deleted = $_GET['zip_code'];
				if (is_array($items_to_be_deleted)) {
					$items_to_be_deleted =  implode(",",$items_to_be_deleted);
				}
				$wpdb->query( 'DELETE FROM '.$table_name.'  WHERE id IN ('.$items_to_be_deleted.')', ARRAY_A );
			}
			?>
			<div class="wrap">

		        <div id="icon-users" class="icon32"><br/></div>
			        <h2><?php _e( 'Zip Code List Table', 'sidebar_zip_code_checker' );?></h2>

			        <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
			            <?php wp_die('Items deleted (or they would be if we had items to delete)!', 'sidebar_zip_code_checker' ); ?>
			        </div>

				</div>
			<?php

        }

		//Detect when a bulk action is being triggered...
        if( 'edit'===$this->current_action() ) {
			if (!empty($_GET['zip_code'])) {
				$items_to_be_edited = $_GET['zip_code'];
				if (is_array($items_to_be_edited)) {
					$items_to_be_edited =  implode(",",$items_to_be_edited);
				}
				$this->edit_display($items_to_be_edited);
			}
            wp_die();
        }

		//Detect when a bulk action is being triggered...
        if( isset($_GET['doaction']) && 'insert'=== $_GET['doaction'] ) {
			$this->zip_code_checker_insert_data();
            wp_die();
        }

    }


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


		 /**
         * Do a querying a database
         */
		$table_name = $wpdb->prefix . "zip_code_checker";
		if ( isset( $_POST['s'] ) ) {
			$results = $wpdb->get_results( 'SELECT * FROM '.$table_name.'  WHERE zip_code LIKE "%'.$_POST['s'].'%" ORDER BY id', ARRAY_A );
		} else {
			$results = $wpdb->get_results( 'SELECT * FROM '.$table_name.'  ORDER BY id', ARRAY_A );
		}


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $results;


        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');


        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }


}





/** ************************ REGISTER THE TEST PAGE ****************************
 *******************************************************************************
 * Now we just need to define an admin page. For this example, we'll add a top-level
 * menu item to the bottom of the admin menus.
 */

function zip_code_add_menu_items(){
    add_menu_page('Zip Code Checker', __( 'Zip Code Checker', 'sidebar_zip_code_checker' ), 'activate_plugins', 'zip_code_list_table', 'zip_code_render_list_page');
	add_submenu_page( 'zip_code_list_table', 'Zip Code Checker', __( 'All Zip Codes', 'sidebar_zip_code_checker' ), 'activate_plugins', 'zip_code_list_table','zip_code_render_list_page');
	add_submenu_page( 'zip_code_list_table', 'Zip Code Checker', __( 'Add Zip Codes', 'sidebar_zip_code_checker' ), 'activate_plugins', 'zip_code_list_table&doaction=insert','zip_code_render_list_page');
} add_action('admin_menu', 'zip_code_add_menu_items');





/** *************************** RENDER TEST PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function zip_code_render_list_page(){

    //Create an instance of our package class...
    $zipCodeListTable = new Zip_Code_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $zipCodeListTable->prepare_items();

    ?>
    <div class="wrap">

        <div id="icon-users" class="icon32"><br/></div>
        <h2><?php _e( 'Zip Code List Table', 'sidebar_zip_code_checker' );?><a href="<?php echo admin_url('admin.php?page=zip_code_list_table&doaction=insert'); ?>" class="add-new-h2"><?php _e( 'Add New', 'sidebar_zip_code_checker' );?></a></h2>

        <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
            <p><?php _e( 'Here we can edit and delete zip codes.', 'sidebar_zip_code_checker' );?> <b><?php _e( 'Note : This action can not be reverted', 'sidebar_zip_code_checker' );?></b>.</p>
        </div>
        <form method="post">
		  <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		  <?php $zipCodeListTable->search_box('search', 'search_id');  ?>
		</form>
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="zip_codes-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $zipCodeListTable->display() ?>
        </form>

    </div>
    <?php
}