<?php
/**
 * WCMp Email Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
 
class WCMp_University {
	private $post_type;
  public $dir;
  public $file;
  
  public function __construct() {
    $this->post_type = 'wcmp_university';
    $this->register_post_type();		
  }
  
  /**
	 * Register university post type
	 *
	 * @access public
	 * @return void
	*/
  function register_post_type() {
		global $WCMp;
		if ( post_type_exists($this->post_type) ) return;
		$labels = array(
			'name' => _x( 'Knowledgebase', 'post type general name' , 'dc-woocommerce-multi-vendor' ),
			'singular_name' => _x( 'Knowledgebase', 'post type singular name' , 'dc-woocommerce-multi-vendor' ),
			'add_new' => _x( 'Add New', $this->post_type , 'dc-woocommerce-multi-vendor' ),
			'add_new_item' => sprintf( __( 'Add New %s' , 'dc-woocommerce-multi-vendor' ), __( 'Knowledgebase' , 'dc-woocommerce-multi-vendor' ) ),
			'edit_item' => sprintf( __( 'Edit %s' , 'dc-woocommerce-multi-vendor' ), __( 'Knowledgebase' , 'dc-woocommerce-multi-vendor') ),
			'new_item' => sprintf( __( 'New %s' , 'dc-woocommerce-multi-vendor' ), __( 'Knowledgebase' , 'dc-woocommerce-multi-vendor') ),
			'all_items' => sprintf( __( 'All %s' , 'dc-woocommerce-multi-vendor' ), __( 'Knowledgebase' , 'dc-woocommerce-multi-vendor' ) ),
			'view_item' => sprintf( __( 'View %s' , 'dc-woocommerce-multi-vendor' ), __( 'Knowledgebase' , 'dc-woocommerce-multi-vendor' ) ),
			'search_items' => sprintf( __( 'Search %a' , 'dc-woocommerce-multi-vendor' ), __( 'Knowledgebase' , 'dc-woocommerce-multi-vendor' ) ),
			'not_found' =>  sprintf( __( 'No %s found' , 'dc-woocommerce-multi-vendor' ), __( 'Knowledgebase' , 'dc-woocommerce-multi-vendor' ) ),
			'not_found_in_trash' => sprintf( __( 'No %s found in trash' , 'dc-woocommerce-multi-vendor' ), __( 'Knowledgebase' , 'dc-woocommerce-multi-vendor' ) ),
			'parent_item_colon' => '',
			'all_items' => __( 'Knowledgebase' , 'dc-woocommerce-multi-vendor' ),
			'menu_name' => __( 'Knowledgebase' , 'dc-woocommerce-multi-vendor' )
		);
		
		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => current_user_can( 'manage_woocommerce' ) ? 'wcmp' : false,
			'show_in_nav_menus' => false,
			'query_var' => false,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => true,
			'supports' => array( 'title', 'editor',  'comments' ),
			'menu_position' => 15,
			'menu_icon' => $WCMp->plugin_url.'/assets/images/dualcube.png'
		);		
		register_post_type( $this->post_type, $args );
	}  
	
	
}
