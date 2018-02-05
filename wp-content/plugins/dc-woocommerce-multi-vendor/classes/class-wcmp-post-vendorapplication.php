<?php

/**
 * WCMp vendor application Class
 *
 * @version		2.4.3
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Vendor_Application {

    private $post_type;
    public $dir;
    public $file;

    public function __construct() {
        $this->post_type = 'wcmp_vendorrequest';
        $this->register_post_type();
    }

    /**
     * Register vendor-application post type
     *
     * @access public
     * @return void
     */
    function register_post_type() {
        global $WCMp;
        if (post_type_exists($this->post_type))
            return;
        $post_type_visibility = false;
        if(is_super_admin(get_current_user_id())){
            $post_type_visibility = true;
        }
        $labels = array(
            'name' => _x('Vendor Application', 'dc-woocommerce-multi-vendor'),
            'singular_name' => _x('Vendor Application', 'dc-woocommerce-multi-vendor'),
            'add_new' => _x('Add New', $this->post_type, 'dc-woocommerce-multi-vendor'),
            'add_new_item' => sprintf(__('Add New %s', 'dc-woocommerce-multi-vendor'), __('Application', 'dc-woocommerce-multi-vendor')),
            'edit_item' => sprintf(__('View %s', 'dc-woocommerce-multi-vendor'), __('Application', 'dc-woocommerce-multi-vendor')),
            'new_item' => sprintf(__('New %s', 'dc-woocommerce-multi-vendor'), __('Application', 'dc-woocommerce-multi-vendor')),
            'all_items' => sprintf(__('All %s', 'dc-woocommerce-multi-vendor'), __('Application', 'dc-woocommerce-multi-vendor')),
            'view_item' => sprintf(__('View %s', 'dc-woocommerce-multi-vendor'), __('Application', 'dc-woocommerce-multi-vendor')),
            'search_items' => sprintf(__('Search %a', 'dc-woocommerce-multi-vendor'), __('Application', 'dc-woocommerce-multi-vendor')),
            'not_found' => sprintf(__('No %s found', 'dc-woocommerce-multi-vendor'), __('Application', 'dc-woocommerce-multi-vendor')),
            'not_found_in_trash' => sprintf(__('No %s found in trash', 'dc-woocommerce-multi-vendor'), __('Application', 'dc-woocommerce-multi-vendor')),
            'parent_item_colon' => '',
            'all_items' => __('Vendor Application', 'dc-woocommerce-multi-vendor'),
            'menu_name' => __('Vendor Application', 'dc-woocommerce-multi-vendor')
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => $post_type_visibility,
            'show_in_menu' => 'users.php',
            'show_in_nav_menus' => false,
            'query_var' => false,
            'rewrite' => true,
            'capability_type' => 'post',
            'capabilities' => array('create_posts' => false, 'delete_posts' => true,),
            'map_meta_cap' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'supports' => array('')
        );
        register_post_type($this->post_type, $args);
    }

}
