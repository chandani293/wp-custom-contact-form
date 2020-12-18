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
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
	  global $wpdb;
	  $wpdb->delete(
		"{$wpdb->prefix}custom_contact_form`",
		[ 'ID' => $id ],
		[ '%d' ]
	  );
	}

	
	/**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
		
		$search_term = isset($_POST['s']) ? trim($_POST['s']): "";
		$orderby = isset($_GET['orderby'])? trim($_GET['orderby']):"";
		$order = isset($_GET['order'])? trim($_GET['order']):"";
		
        $data = $this->table_data($orderby,$order,$search_term);

        $per_page = 3;
        $currentpage = $this->get_pagenum();//Get Current Page Number
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $per_page
        ) );

        $data = array_slice($data,(($currentpage-1)*$per_page),$per_page);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() //$item
    {
        $columns = array(
            'id'           => 'ID',
            'firstname'    => 'First Name',
            'lastname'     => 'Last Name',
            'email'        => 'Email',
            'contact_no'   => 'Contact No',
            'message'      => 'Message',
			//'edit'         => sprintf('<a href="?page=%s&action=%s&book=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
            //'delete'       => sprintf('<a href="?page=%s&action=%s&book=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array(
			'firstname' => array('firstname', true),
		);
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data($orderby='',$order='',$search_term='')
    {
		global $wpdb;
		
		if(!empty($search_term)){
			 $sql = "SELECT * FROM {$wpdb->prefix}custom_contact_form where `firstname` LIKE '%$search_term%' OR `lastname` LIKE '%$search_term%' OR `email` LIKE '%$search_term%' OR `message` LIKE '%$search_term%'";
			 $data = $wpdb->get_results($sql , 'ARRAY_A');
		}
		else{
			if($orderby == 'firstname' && $order == 'asc'){
				$sql = "SELECT * FROM {$wpdb->prefix}custom_contact_form ORDER BY `firstname` ASC";
				$data = $wpdb->get_results( $sql, 'ARRAY_A' );
			}else if($orderby =='firstname' && $order == 'desc'){
				$sql = "SELECT * FROM {$wpdb->prefix}custom_contact_form ORDER BY `firstname` DESC";
				$data = $wpdb->get_results( $sql, 'ARRAY_A' );
			}else{
				$sql = "SELECT * FROM {$wpdb->prefix}custom_contact_form";
				$data = $wpdb->get_results( $sql, 'ARRAY_A' );
			}
		}
		return $data;
	
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'id':
            case 'firstname':
            case 'lastname':
            case 'email':
            case 'contact_no':
            case 'message':
                return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;
        }
    }
}
?>