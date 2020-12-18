<?php
/**
 * WP_List_Table is not loaded automatically so we need to load it in our application.
 *
 * @package    Wp_Custom_Contact_Form
 * @subpackage Wp_Custom_Contact_Form/includes
 */

if( is_admin() && !class_exists( 'WP_List_Table' ) ){
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Create a new table class that will extend the WP_List_Table
 */ 
class Wp_Custom_Contact_Form_Table extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Customer', 'wp-custom-contact-form' ), //singular name of the listed records
			'plural'   => __( 'Customers', 'wp-custom-contact-form' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );
	}

	/**
	 * Retrieve customer’s data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_customers( $per_page = 5, $page_number = 1 ) {
	  global $wpdb;
	  $wp_custom_contact_form_table = WP_CUSTOM_CONTACT_FORM_TABLE;
	  $sql = "SELECT * FROM `wp_custom_contact_form_table`";
	  if ( ! empty( $_REQUEST['orderby'] ) ) {
		$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
	  }
	  $sql .= " LIMIT $per_page";
	  $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
	  $result = $wpdb->get_results( $sql, 'ARRAY_A' );
	  return $result;
	}
	
	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
	  global $wpdb;

	  $wpdb->delete(
		"{$wpdb->prefix}custom_contact_form",
		[ 'id' => $id ],
		[ '%d' ]
	  );
	}
	
	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
	  global $wpdb;

	  $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}custom_contact_form";

	  return $wpdb->get_var( $sql );
	}
	
	/** Text diwp-custom-contact-formlayed when no customer data is available */
	public function no_items() {
	  _e( 'No customers avaliable.', 'wp-custom-contact-form' );
	}
	
	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	 
	function column_name( $item ) {
		  // create a nonce
		  $delete_nonce = wp_create_nonce( 'wp-custom-contact-form_delete_customer' );

		  $title = '<strong>' . $item['name'] . '</strong>';
		  $actions = [
			'delete' => wp-custom-contact-formrintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		  ];
		  
		  return $title . $this->row_actions( $actions );
	}
	
	/**
	 * Render a column when no column wp-custom-contact-formecific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	 
	 public function column_default( $item, $column_name ) {
	  switch ( $column_name ) {
		case 'firstname':
		case 'lastname':
		  return $item[ $column_name ];
		case 'email':
		  return $item[ $column_name ];
		case 'contact_no':
		  return $item[ $column_name ];
		case 'message':
		  return $item[ $column_name ];  
		default:
		  return print_r( $item, true ); //Show the whole array for troubleshooting purposes
	  }
	}
	
	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	 
	function column_cb( $item ) {
	  return wp-custom-contact-formrintf(
		'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
	  );
	}
	
	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
	  $columns = [
		'cb'      => '<input type="checkbox" />',
		'firstname'    => __( 'Firstname', 'wp-custom-contact-form' ),
		'lastname' => __( 'Lastname', 'wp-custom-contact-form' ),
		'email'    => __( 'Email', 'wp-custom-contact-form' ),
		'contact_no' => __( 'Contact No', 'wp-custom-contact-form' ),
		'message'    => __( 'Message', 'wp-custom-contact-form' )
	  ];

	  return $columns;
	}
	
	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
	  $sortable_columns = array(
		'firstname' => array( 'firstname', true ),
		'lastname' => array( 'lastname', false ),
		'email' => array( 'email', false ),
		'contact_no' => array( 'contact_no', false ),
		'message' => array( 'message', false )
	  );

	  return $sortable_columns;
	}
	
	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	 
	public function get_bulk_actions() {
	  $actions = [
		'bulk-delete' => 'Delete'
	  ];

	  return $actions;
	}
	
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
	  $this->_column_headers = $this->get_column_info();
	  /** Process bulk action */
	  $this->process_bulk_action();
	  $per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
	  $current_page = $this->get_pagenum();
	  $total_items  = self::record_count();

	  $this->set_pagination_args( [
		'total_items' => $total_items, //WE have to calculate the total number of items
		'per_page'    => $per_page //WE have to determine how many items to show on a page
	  ] );
	  $this->items = self::get_customers( $per_page, $current_page );
	}
		
}
?>