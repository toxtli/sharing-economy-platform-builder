<?php
class WC_Woocommerce_Catalog_Enquiry_Admin {
  
  public $settings;

	public function __construct() {
		//admin script and style
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));
		
		add_action('wc_Woocommerce_Catalog_Enquiry_dualcube_admin_footer', array(&$this, 'dualcube_admin_footer_for_wc_Woocommerce_Catalog_Enquiry'));

		$this->load_class('settings');
		$this->settings = new WC_Woocommerce_Catalog_Enquiry_Settings();
		$this->init_product_settings();
	}

	function load_class($class_name = '') {
	  global $WC_Woocommerce_Catalog_Enquiry;
		if ('' != $class_name) {
			require_once ($WC_Woocommerce_Catalog_Enquiry->plugin_path . '/admin/class-' . esc_attr($WC_Woocommerce_Catalog_Enquiry->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()
	
	function dualcube_admin_footer_for_wc_Woocommerce_Catalog_Enquiry() {
    global $WC_Woocommerce_Catalog_Enquiry;
    ?>
    <div style="clear: both"></div>
    <div id="dc_admin_footer">
      <?php _e('Powered by', 'woocommerce-catalog-enquiry'); ?> <a href="http://wc-marketplace.com/" target="_blank"><img src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url.'/assets/images/dualcube.png'; ?>"></a><?php _e('WC Marketplace', 'woocommerce-catalog-enquiry'); ?> &copy; <?php echo date('Y');?>
    </div>
    <?php
	}
	
	
	public function init_product_settings() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		if(isset($settings['is_enable']) && $settings['is_enable'] == "Enable") {
			if(isset($settings['is_custom_button']) && $settings['is_custom_button'] == "Enable") {
				if(isset($settings['button_type']) && $settings['button_type'] == 3) {					
					add_filter( 'woocommerce_product_data_tabs', array( $this, 'catalog_product_data_tabs' ) );					
					add_action( 'woocommerce_product_data_panels', array( $this, 'catalog_product_data_panel' ) );
					add_action( 'woocommerce_process_product_meta_simple', array( $this, 'save_catalog_data' ) );
					add_action( 'woocommerce_process_product_meta_grouped', array( $this, 'save_catalog_data' ) );
					add_action( 'woocommerce_process_product_meta_external', array( $this, 'save_catalog_data' ) );
					add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_catalog_data' ) );					
				}	
			}
		}		
	}
	
	
	public function catalog_product_data_tabs($tabs) {
		$tabs['woo_catalog_enquiry'] = array(
			'label'  => __( 'Catalog Enquiry', 'catalog-enquiry' ),
			'target' => 'catalog_enquiry_product_data',
			'class'  => array( '' ),
		);
		return $tabs;		
	}
	
	
	/**
	 * Save meta.
	 *
	 * Save the product catalog enquiry meta data.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id ID of the post being saved.
	 */
	public function save_catalog_data( $post_id ) {

		// Save all meta
		update_post_meta( $post_id, 'woo_catalog_enquiry_product_link', esc_url($_POST['woo_catalog_enquiry_product_link']) );		

	}
	
	
	
	
	/**
	 * Output catalog individual product link.
	 *
	 * Output settings to the product link tab.
	 *
	 * @since 1.0.0
	 */
	public function catalog_product_data_panel() {
		global $WC_Woocommerce_Catalog_Enquiry;

		?><div id="catalog_enquiry_product_data" class="panel woocommerce_options_panel"><?php			

			woocommerce_wp_text_input( array(
				'id' 		=> 'woo_catalog_enquiry_product_link',
				'label' 	=> __( 'Enter product external link', 'woocommerce-catalog-enquiry' ),
				'placeholder' => __('https://www.google.com', 'woocommerce-catalog-enquiry')
			) );	
			
		?></div><?php

	}
	
	

	/**
	 * Admin Scripts
	 */

	public function enqueue_admin_script() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$screen = get_current_screen();
		
		// Enqueue admin script and stylesheet from here
		if (in_array( $screen->id, array( 'woocommerce_page_wc-Woocommerce-Catalog-Enquiry-setting-admin' ))) :   
		  $WC_Woocommerce_Catalog_Enquiry->library->load_qtip_lib();
		  $WC_Woocommerce_Catalog_Enquiry->library->load_upload_lib();
		  $WC_Woocommerce_Catalog_Enquiry->library->load_colorpicker_lib();
		  $WC_Woocommerce_Catalog_Enquiry->library->load_datepicker_lib();
		  wp_enqueue_script('catalog_admin_js', $WC_Woocommerce_Catalog_Enquiry->plugin_url.'assets/admin/js/admin.js', array('jquery'), $WC_Woocommerce_Catalog_Enquiry->version, true);
		  wp_enqueue_style('catalog_admin_css',  $WC_Woocommerce_Catalog_Enquiry->plugin_url.'assets/admin/css/admin.css', array(), $WC_Woocommerce_Catalog_Enquiry->version);
		  
	  	endif;
	}
}