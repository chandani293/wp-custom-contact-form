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
	
	public function __construct() {
		
        parent::__construct(
            array(
                'singular' => 'singular_form',
                'plural'   => 'plural_form',
                'ajax'     => false
            )
        );
    }

	/**
	 * Delete a record.
	 *
	 * @param int $id ID
	 */
	public static function delete_data( $id ) {
	  global $wpdb;
	  if ( ! empty( $id ) ) {
		  $wpdb->query( "DELETE FROM {$wpdb->prefix}custom_contact_form WHERE id='".$id."'" );
	  }
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
		
		if ($_GET['action'] == 'wp-custom-contact-form-delete' ) {
			$delete_id = $_GET['post_id'];
			$this->delete_data( $delete_id );
		}

        $data = $this->table_data($orderby,$order,$search_term);

		$user = get_current_user_id();
		$screen = get_current_screen();
		$option = $screen->get_option('per_page', 'option');
		
		$per_page_val = get_user_meta(	$user, $option, true );
		 
		if ( empty ( $per_page) || $per_page < 1 ) {
			$per_page_val = $screen->get_option( 'per_page', 'default' );
		}
	
		
        $per_page = $this->get_items_per_page('contact_per_page', 3); //3
        $currentpage = $this->get_pagenum();//Get Current Page Number
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $per_page
        ) );

        $data = array_slice($data,(($currentpage-1)*$per_page),$per_page);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
		$this->process_bulk_action();
		
    }
	

	/**
     * Define which Process Bulk Action
     *
     */
	public function process_bulk_action() {
		$entry_id = ( is_array( $_REQUEST['checked_value'] ) ) ? $_REQUEST['checked_value'] : array( $_REQUEST['checked_value'] );
		global $wpdb;
		
		$action = $this->current_action();
		if( 'delete' === $action ) {
			foreach ( $entry_id as $id ) {
				$id = absint( $id );
				$wpdb->query( "DELETE FROM wp_custom_contact_form WHERE id=".$id."" );
			}
			
			echo '<div class="notice notice-success is-dismissible"><p>Bulk Deleted..</p></div>';
			//wp_redirect( admin_url() );
		}
		
	}
	
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() //$item
    {
        $columns = array(
			'cb'           => '<input type="checkbox" />',
            'id'           => 'ID',
            'firstname'    => 'First Name',
            'lastname'     => 'Last Name',
            'email'        => 'Email',
            'contact_no'   => 'Contact No',
            'message'      => 'Message',
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
    private function table_data( $orderby='',$order='',$search_term='' )
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
     * @param  Array $item Data
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
	
	/**
	 * Get all Actions.
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete',
		);
		return $actions;
	}
	
	/**
	 * Column for checkbox
	 *
	 * @param array $item Set Items.
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" id="cb-value-%s" name="checked_value[]" value="%s">',
			$item['id'],
			$item['id']
		);
	}
	
	/**
	 * Column for First Name
	 *
	 * @param array $item Set Items.
	 */
	public function column_firstname($item){
		$action = array(
			'delete'=>sprintf("<a href='?page=%s&action=%s&post_id=%s'>Delete</a>",$_GET['page'],'wp-custom-contact-form-delete',$item['id']),
		);
		return sprintf('%1$s %2$s',$item['firstname'],$this->row_actions($action));
	}	
}
?>