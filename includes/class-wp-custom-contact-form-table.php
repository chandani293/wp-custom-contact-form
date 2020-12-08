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
			'singular' => __( 'Customer', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Customers', 'sp' ), //plural name of the listed records
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
	  $sql = "SELECT * FROM {$wpdb->prefix}customers";
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
		"{$wpdb->prefix}customers",
		[ 'ID' => $id ],
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

	  $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}customers";

	  return $wpdb->get_var( $sql );
	}
	
	/** Text displayed when no customer data is available */
	public function no_items() {
	  _e( 'No customers avaliable.', 'sp' );
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
		  $delete_nonce = wp_create_nonce( 'sp_delete_customer' );

		  $title = '<strong>' . $item['name'] . '</strong>';
		  $actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		  ];
		  
		  return $title . $this->row_actions( $actions );
	}
	
	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	 
	 public function column_default( $item, $column_name ) {
	  switch ( $column_name ) {
		case 'address':
		case 'city':
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
	  return sprintf(
		'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
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
		'name'    => __( 'Name', 'sp' ),
		'address' => __( 'Address', 'sp' ),
		'city'    => __( 'City', 'sp' )
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
		'name' => array( 'name', true ),
		'city' => array( 'city', false )
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
		
}
?>