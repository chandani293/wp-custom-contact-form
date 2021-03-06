<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.cmsminds.com/
 * @since      1.0.0
 *
 * @package    Wp_Custom_Contact_Form
 * @subpackage Wp_Custom_Contact_Form/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Custom_Contact_Form
 * @subpackage Wp_Custom_Contact_Form/admin
 * @author     Chandani Vadaria <chandani@cmsminds.com>
 */
class Wp_Custom_Contact_Form_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Custom_Contact_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Custom_Contact_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-custom-contact-form-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Custom_Contact_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Custom_Contact_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-custom-contact-form-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Create admin menu page backend
	 * @since    1.0.0
	 */
	public function wp_custom_contact_create_admin_menu() {
		
		$hook = add_menu_page(
			__( 'WP Custom Contact Form', 'wp-custom-contact-form' ),
			__( 'WP Custom Contact Form', 'wp-custom-contact-form' ),
			'manage_options',
			'wp-custom-contact-form-entries',
			array( $this, 'wp_custom_contact_form_list' ),
		);
		
		add_action( "load-$hook", 'add_options' );

		function add_options() {
		  $option = 'per_page';
		  $args = array(
				 'label' => 'Number of items per page:',
				 'default' => 3,
				 'option' => 'contact_per_page'
				 );
		  add_screen_option( $option, $args );
		}
	}
	
	public function set_screen( $status, $option, $value ) {
		if ( 'contact_per_page' == $option ) return $value;
	}

	/**
	* Show Admin menu page contents
	*
	* @since    1.0.0
	*/
	 
	public function wp_custom_contact_form_list() {
		
		$contact_form_list_table = new Wp_Custom_Contact_Form_Table();
		echo '<h2>Contact Form List</h2>';
		$contact_form_list_table->prepare_items();
		
		echo "<form method='post' name='frm_search_post' action='".$_SERVER['PHP_SELF']."?page=wp-custom-contact-form-entries'>";
		$contact_form_list_table->search_box("Search Data","search_post_id");
		$contact_form_list_table->display(); 
		echo "</form>";
		
		
	}
	
	/**
	 * Check if page already available
	 *
	 * Return True/False Value
	 */
	public function wp_custom_contact_page_available( $page_slug ) {
		$page = get_page_by_path( $page_slug, OBJECT );
		if ( isset( $page ) ) {
			return true;
		} else {
			return false;
		}	
	}
	
	/**
	 * Create Contact Form Page Added Shortcode
	 *
	 */
	public function wp_custom_contact_page() {

		if ( ! $this->wp_custom_contact_page_available( 'wp-custom-contact-form' ) ) {
			$default_page_details = array(
				'post_title'   => 'WP Custom Contact Form',
				'post_name'    => 'wp-custom-contact-form',
				'post_type'    => 'page',
				'post_content' => '[wp-custom-contact-form]',
				'post_status'  => 'publish',	
			);
			wp_insert_post( $default_page_details );
		}
	}

}
