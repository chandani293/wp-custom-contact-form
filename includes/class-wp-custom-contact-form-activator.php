<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.cmsminds.com/
 * @since      1.0.0
 *
 * @package    Wp_Custom_Contact_Form
 * @subpackage Wp_Custom_Contact_Form/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Custom_Contact_Form
 * @subpackage Wp_Custom_Contact_Form/includes
 * @author     Chandani Vadaria <chandani@cmsminds.com>
 */
class Wp_Custom_Contact_Form_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'custom_contact_form';
        $sql_tbl_query = "CREATE TABLE $table_name (
                    id INT NOT NULL AUTO_INCREMENT,
                    firstname varchar(100) NOT NULL,
                    lastname varchar(100) NOT NULL,
					email varchar(100) NOT NULL,
					contact_no varchar(100) NOT NULL,
					message varchar(300) NOT NULL,
                    PRIMARY KEY (id) )";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_tbl_query );
	}

}
