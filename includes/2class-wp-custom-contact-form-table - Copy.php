<?php
/**
 * WP_List_Table is not loaded automatically so we need to load it in our application.
 *
 * @package    Wp_Custom_Contact_Form
 * @subpackage Wp_Custom_Contact_Form/includes
 */

if(!class_exists( 'WP_List_Table' ) ){
    require ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Create a new table class that will extend the WP_List_Table
 */ 
class Wp_Custom_Contact_Form_Table extends WP_List_Table {

	/** class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'customer', 'wp-custom-contact-form' ), //singular name of the listed records
			'plural'   => __( 'customers', 'wp-custom-contact-form' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );
	}

	/**
	 * retrieve customer’s data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_customers( $per_page = 5, $page_number = 1 ) {
	  global $wpdb;
	 // $wp_custom_contact_form_table = wp_custom_contact_form_table;
	  $sql = "select * from `wp_custom_contact_form_table`";
	  if ( ! empty( $_request['orderby'] ) ) {
		$sql .= ' order by ' . esc_sql( $_request['orderby'] );
		$sql .= ! empty( $_request['order'] ) ? ' ' . esc_sql( $_request['order'] ) : ' asc';
	  }
	  $sql .= " limit $per_page";
	  $sql .= ' offset ' . ( $page_number - 1 ) * $per_page;
	  $result = $wpdb->get_results( $sql, 'array_a' );
	  return $result;
	}
	
	/**
	 * delete a customer record.
	 *
	 * @param int $id customer id
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
	 * returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
	  global $wpdb;

	  $sql = "select count(*) from {$wpdb->prefix}custom_contact_form";

	  return $wpdb->get_var( $sql );
	}
	
	/** text diwp-custom-contact-formlayed when no customer data is available */
	public function no_items() {
	  _e( 'no customers avaliable.', 'wp-custom-contact-form' );
	}
	
	/**
	 * method for name column
	 *
	 * @param array $item an array of db data
	 *
	 * @return string
	 */
	 
	function column_name( $item ) {
		  // create a nonce
		  $delete_nonce = wp_create_nonce( 'wp-custom-contact-form_delete_customer' );

		  $title = '<strong>' . $item['firstname'] . '</strong>';
		  $actions = [
			'delete' => wp-custom-contact-formrintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">delete</a>', esc_attr( $_request['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		  ];
		  
		  return $title . $this->row_actions( $actions );
	}
	
	/**
	 * render a column when no column wp-custom-contact-formecific method exists.
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
		  return print_r( $item, true ); //show the whole array for troubleshooting purposes
	  }
	}
	
	/**
	 * render the bulk edit checkbox
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
	 *  associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
	  $columns = [
		'cb'      => '<input type="checkbox" />',
		'firstname'    => __( 'firstname', 'wp-custom-contact-form' ),
		'lastname' => __( 'lastname', 'wp-custom-contact-form' ),
		'email'    => __( 'email', 'wp-custom-contact-form' ),
		'contact_no' => __( 'contact no', 'wp-custom-contact-form' ),
		'message'    => __( 'message', 'wp-custom-contact-form' )
	  ];

	  return $columns;
	}
	
	/**
	 * columns to make sortable.
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
	 * returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	 
	public function get_bulk_actions() {
	  $actions = [
		'bulk-delete' => 'delete'
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
	 * handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
	  //$this->_column_headers = $this->get_column_info();
	  
	  $this->process_bulk_action();
	  $per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
	  $current_page = $this->get_pagenum();
	  $total_items  = self::record_count();

	  $this->set_pagination_args( [
		'total_items' => $total_items, //we have to calculate the total number of items
		'per_page'    => $per_page //we have to determine how many items to show on a page
	  ] );
	  $this->items = self::get_customers( $per_page, $current_page );
	}
	
	public function process_bulk_action() {

	  //Detect when a bulk action is being triggered...
	  if ( 'delete' === $this->current_action() ) {
		// In our file that handles the request, verify the nonce.
		  self::delete_customer( absint( $_GET['customer'] ) );

		  wp_redirect( esc_url( add_query_arg() ) );
		  exit;
	  }
	  // If the delete bulk action is triggered
	  if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		   || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
	  ){
		$delete_ids = esc_sql( $_POST['bulk-delete'] );
		// loop over the array of record IDs and delete them
		foreach ( $delete_ids as $id ) {
		  self::delete_customer( $id );

		}
		wp_redirect( esc_url( add_query_arg() ) );
		exit;
			
		}
	}

	/**
	* Screen options
	*/
	public function screen_option() {
		$option = 'per_page';
		$args   = [
			'label'   => 'Customers',
			'default' => 5,
			'option'  => 'customers_per_page'
		];
		add_screen_option( $option, $args );
		$this->customers_obj = new Customers_List();
	}
	
	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
?>