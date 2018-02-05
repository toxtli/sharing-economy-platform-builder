<?php 

/*
 * Plugin Name:       FMA Additional Registration Attributes(Free)
 * Plugin URI:        http://fmeaddons.com/wordpress/fmeaddon-add-registration-atributes
 * Description:       FME Addons Add Registration Attributes provaide facility to manage fileds on registration page. By using this module you can gather extra valueable information from your customers.
 * Version:           1.0.4
 * Author:            FME Addons
 * Developed By:  	  Raja Usman Mehmood
 * Author URI:        http://fmeaddons.com/
 * Support:		  	  http://support.fmeaddons.com/
 * Text Domain:       fmeaddon-add-registration-atributes
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if WooCommerce is active
 * if wooCommerce is not active FME Tabs module will not work.
 **/
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	echo 'This plugin required woocommerce installed!';
}

if ( !class_exists( 'FME_Registration_Attributes' ) ) { 

	class FME_Registration_Attributes {


		function __construct() {

			$this->module_constants();
			$this->module_tables();

			if ( is_admin() ) {
				require_once( FMERA_PLUGIN_DIR . 'admin/class-fme-registration-attributes-admin.php' );
				register_activation_hook( __FILE__, array( $this, 'install_module' ) );
				add_filter( 'extra_plugin_headers', array($this, 'fme_extra_plugin_headers' ));
				
			} else {
				require_once( FMERA_PLUGIN_DIR . 'front/class-fme-registration-attributes-front.php' );
			}

			
		}

		function fme_extra_plugin_headers($headers) {

			$headers['support'] = 'Support';
			return $headers;		}



		public function module_constants() {
            
            if ( !defined( 'FMERA_URL' ) )
                define( 'FMERA_URL', plugin_dir_url( __FILE__ ) );

            if ( !defined( 'FMERA_BASENAME' ) )
                define( 'FMERA_BASENAME', plugin_basename( __FILE__ ) );

            if ( ! defined( 'FMERA_PLUGIN_DIR' ) )
                define( 'FMERA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }

        public function install_module() { 
        	$this->module_tables();
        }

        private function module_tables() {
            
			global $wpdb;
		
			$wpdb->fmera_fields = $wpdb->prefix . 'fmera_fields';
			$wpdb->fmera_meta = $wpdb->prefix . 'fmera_meta';

			$this->create_tables();
		}


		



        public function create_tables() { 
            
			global $wpdb;
			
			$charset_collate = '';
		
			if ( !empty( $wpdb->charset ) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( !empty( $wpdb->collate ) )
				$charset_collate .= " COLLATE $wpdb->collate";	
				
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmera_meta'" ) != $wpdb->fmera_meta ) { 
				$sql1 = "CREATE TABLE " . $wpdb->fmera_meta . " (
									 meta_id int(25) NOT NULL auto_increment,
									 field_id varchar(255) NULL,
									 meta_key varchar(255) NULL,
									 meta_value text(255) NULL,
									 
									 PRIMARY KEY (meta_id)
									 ) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql1 );
			}


			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmera_fields'" ) != $wpdb->fmera_fields ) {
				$sql = "CREATE TABLE " . $wpdb->fmera_fields . " (
									 field_id int(25) NOT NULL auto_increment,
									 field_name varchar(255) NULL,
									 field_label varchar(255) NULL,
									 field_placeholder varchar(255) NULL,
									 is_required int(25) NOT NULL,
									 is_hide int(25) NOT NULL,
									 width varchar(255) NULL,
									 sort_order int(25) NOT NULL,
									 field_type varchar(255) NULL,
									 type varchar(255) NULL,
									 field_mode varchar(255) NULL,
									 field_message text NULL,
									 showif varchar(255),
									 cfield varchar(255),
									 ccondition varchar(255),
									 ccondition_value varchar(255),
									 
									 PRIMARY KEY (field_id)
									 ) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			}



		}

		


		

        


	}

	$fmera = new FME_Registration_Attributes();


}

?>
