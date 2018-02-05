<?php
class WC_Woocommerce_Catalog_Enquiry_Settings_Exclusion {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  private $tab;
  
  public $all_users = array();
  
  public $all_products = array();
  
   public $all_product_cat = array();
  
  

  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $this->get_all_users();
    $this->get_all_products();
    $this->get_all_product_category();
    $this->options = get_option( "dc_{$this->tab}_settings_name" );
    $this->settings_page_init();	
  }
  
  
  public function get_all_users() {
  	global $WC_Woocommerce_Catalog_Enquiry;  	
  	$users = get_users();
    $this->all_users = array();
    foreach($users as $user) {					
			$this->all_users[$user->data->ID] = $user->data->display_name;	 			
		}
  	
  }
  
  
  
  public function get_all_products() {
  	global $WC_Woocommerce_Catalog_Enquiry;
  	$args = array( 'posts_per_page' => -1, 'post_type' => 'product', 'orderby' => 'title', 'order' => 'ASC' );
		$myposts = get_posts( $args );
		foreach ( $myposts as $post ) : setup_postdata( $post ); 	
		$this->all_products[$post->ID] = $post->post_title;		
		endforeach; 
		wp_reset_postdata();
  	
  }
  
  
  public function get_all_product_category() { 
  	$args = array( 'orderby' => 'name', 'order' => 'ASC' );
  	$terms = get_terms( 'product_cat', $args );
  	foreach ( $terms as $term) {
  		$this->all_product_cat[$term->term_id] = $term->name;
  	}
  	
  }
  
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $WC_Woocommerce_Catalog_Enquiry;  
     
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array(
                                                      "exclusion_settings_section" => array("title" =>  __('Woocommerce Catalog Enquiry Settings', 'woocommerce-catalog-enquiry'), // Section one
                                                                                         "fields" => array("is_exclusion" => array('title' => __('Enable Exclusion List', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_exclusion', 'label_for' => 'is_exclusion', 'name' => 'is_exclusion', 'desc' => __('Enable the exclusion list',  'woocommerce-catalog-enquiry'), 'value' => 'Enable'),
                                                                                         	 								 "myuser_list" => array('title' => __('User List Excluded from catalog', 'woocommerce-catalog-enquiry'),  'type' => 'multiselect', 'id' => 'myuser_list', 'label_for' => 'myuser_list', 'name' => 'myuser_list', 'desc' => __('select the user who can puchase from website', 'woocommerce-catalog-enquiry'), 'hints' => __('select the user who can puchase from website.', 'woocommerce-catalog-enquiry'),  'options'=>$this->all_users), // is catalog enable
                                                                                         	 								 "myproduct_list" => array('title' => __('Product List Excluded from catalog', 'woocommerce-catalog-enquiry'),  'type' => 'multiselect', 'id' => 'myproduct_list', 'label_for' => 'myproduct_list', 'name' => 'myproduct_list', 'desc' => __('select the products will be excluted from catalog list', 'woocommerce-catalog-enquiry'), 'hints' => __('select the products will be excluted from catalog list.', 'woocommerce-catalog-enquiry'),  'options'=>$this->all_products), // is catalog enable 
                                                                                         	 								 "mycategory_list" => array('title' => __('Category List Excluded from catalog', 'woocommerce-catalog-enquiry'),  'type' => 'multiselect', 'id' => 'mycategory_list', 'label_for' => 'mycategory_list', 'name' => 'mycategory_list', 'desc' => __('select the products will be excluted from catalog list', 'woocommerce-catalog-enquiry'), 'hints' => __('select the products will be excluted from catalog list.', 'woocommerce-catalog-enquiry'),  'options'=>$this->all_product_cat) // is catalog enable 
                                                                                                           
                                                                                                           
                                                                                                          
                                                                                                           )
                                                                                         ) 
                                                      
                                                      )
                                  );
    
    $WC_Woocommerce_Catalog_Enquiry->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function dc_wc_Woocommerce_Catalog_Enquiry_exclusion_settings_sanitize( $input ) {
    global $WC_Woocommerce_Catalog_Enquiry;
    $new_input = array();
   
    
    $hasError = false;
    
    
    if( isset( $input['myuser_list'] ) )
      $new_input['myuser_list'] = $input['myuser_list'];
    
    if( isset( $input['myproduct_list'] ) )
      $new_input['myproduct_list'] = $input['myproduct_list'];
    if( isset( $input['mycategory_list'] ) )
      $new_input['mycategory_list'] = $input['mycategory_list'];
    if( isset( $input['is_exclusion'] ) )
      $new_input['is_exclusion'] = sanitize_text_field($input['is_exclusion']);
    
    
    
    
    
    
    if(!$hasError) {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_updated" ),
        __('Exclusion settings updated', 'woocommerce-catalog-enquiry'),
        'updated'
      );
    }

    return $new_input;
  }

  /** 
   * Print the Section text
   */
  public function exclusion_settings_section_info() {
    global $WC_Woocommerce_Catalog_Enquiry;
    _e('Configure the exclusion list', 'woocommerce-catalog-enquiry');
  }
  
 
  
}