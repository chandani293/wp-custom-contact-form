<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.cmsminds.com/
 * @since      1.0.0
 *
 * @package    Wp_Custom_Contact_Form
 * @subpackage Wp_Custom_Contact_Form/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Custom_Contact_Form
 * @subpackage Wp_Custom_Contact_Form/public
 * @author     Chandani Vadaria <chandani@cmsminds.com>
 */
class Wp_Custom_Contact_Form_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
		wp_enqueue_style( 'bootstrap-style', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), '4.3.1', 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-custom-contact-form-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		 
		wp_enqueue_script( 'wp-custom-contact-bootstrap-script', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), '4.3.1', false );
		wp_enqueue_script( 'wp-custom-contact-jquery-validate-script', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array( 'jquery' ), '1.19.2', false );
		wp_enqueue_script( 'wp-custom-contact-jquery-additional-methods-script', plugin_dir_url( __FILE__ ) . 'js/additional-methods.min.js', array( 'jquery' ), '1.19.2', false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-custom-contact-form-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ajax_url', array( 'url' => admin_url( 'admin-ajax.php' ) ) );

	}
	
	/**
	 * Contact Form
	 * @since 1.0.0
	 */
	public function wp_custom_contact_form_func( $atts ) {

		$fromcontent = '';
		$fromcontent .= '<div class="wp-custom-contact-form-main">';
		$fromcontent .= '<form id="wpcustomcontactform" class="wp-custom-contact-form" method="post" action="">
						<div class="form-row">
							<div class="form-group col-md-6">
								<label class="control-label" for="firstname">' . __( 'First Name', 'wp-custom-contact-form' ) . '</label>
								<input type="text" id="firstname" name="firstname" class="form-control" placeholder="Enter First Name" />
							</div>
							<div class="form-group col-md-6">
								<label class="control-label" for="lastname">' . __( 'Last Name', 'wp-custom-contact-form' ) . '</label>
								<input type="text" id="lastname" name="lastname" class="form-control" placeholder="Enter Last Name" />
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6">
								<label class="control-label" for="email">' . __( 'Email', 'wp-custom-contact-form' ) . '</label>
								<input type="email" id="email" name="email" class="form-control"  placeholder="Enter Your Email" />
							</div>
							<div class="form-group col-md-6">
								<label class="control-label" for="contact_no">' . __( 'Contact Number', 'wp-custom-contact-form' ) . '</label>
								<input type="text" id="contact_no" name="contact_no" class="form-control"  placeholder="Enter Contact Number" />
							</div>
						</div>	
						<div class="form-group">
					    	<label class="control-label" for="message">' . __( 'Comment', 'wp-custom-contact-form' ) . '</label>
					    	<textarea class="form-control" id="message" name="message"  rows="5" placeholder="Enter Your Comment"></textarea>
					  	</div>  
						<div class="form-group">
							<input type="submit" class="wp-custom-contact-from-submit" value="Submit">
						</div>
					</form>';
		$fromcontent .= '</div>';

		return $fromcontent;
	}

	/**
	 * Save Contact form data
	 */
	 
	public function wp_custom_contact_form_save_form_data() {
		global $wpdb;
		$wp_custom_contact_form_table = WP_CUSTOM_CONTACT_FORM_TABLE;
		$data  = array();
		if (!empty($_POST)){
				$firstname      = ( ! empty( $_POST['firstname'] ) ) ? esc_attr( sanitize_text_field($_POST['firstname'])) : '';
				$lastname       = ( ! empty( $_POST['lastname'] ) ) ? esc_attr( sanitize_text_field($_POST['lastname'])) : '';
				$email          = ( ! empty( $_POST['email'] ) ) ? esc_attr( sanitize_text_field($_POST['email'])) : '';
				$contact_no     = ( ! empty( $_POST['contact_no'] ) ) ? esc_attr( sanitize_text_field($_POST['contact_no'])) : '';
				$message        = ( ! empty( $_POST['message'] ) ) ? esc_attr( sanitize_text_field($_POST['message'])) : '';
				
				$insert_form_data = $wpdb->insert($wp_custom_contact_form_table,
					array(
						'firstname'     => $firstname,
						'lastname'      => $lastname,
						'email'          => $email,
						'contact_no' => $contact_no,
						'message'        => $message,
					),
					array(
						'%s','%s','%s','%s','%s',
					)
				);
				if ( $insert_form_data ) {
					$data['success'] = __( 'Your inquiry sucessfully submitted! We will try to reach you as soon as possible!', 'wp-custom-contact-form' );
				} else {
					$data['error'] = $wpdb->print_error();
				}
			}		
		echo wp_json_encode( $data );
		die();		
	}	
}
