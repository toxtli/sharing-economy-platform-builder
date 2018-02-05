<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WCMp Admin Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Admin {

    public $settings;

    public function __construct() {
        // Admin script and style
        add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'), 30);
        add_action('dualcube_admin_footer', array(&$this, 'dualcube_admin_footer_for_wcmp'));
        add_action('admin_bar_menu', array(&$this, 'add_toolbar_items'), 100);
        add_action('admin_head', array(&$this, 'admin_header'));
        add_action('current_screen', array($this, 'conditonal_includes'));
        add_action('delete_post', array($this, 'remove_commission_from_sales_report'), 10);
        add_action('trashed_post', array($this, 'remove_commission_from_sales_report'), 10);
        add_action('untrashed_post', array($this, 'restore_commission_from_sales_report'), 10);
        add_action('woocommerce_order_status_changed', array($this, 'change_commission_status'), 20, 3);
        if (get_wcmp_vendor_settings('is_singleproductmultiseller', 'general') == 'Enable') {
            add_action('admin_enqueue_scripts', array($this, 'wcmp_kill_auto_save'));
        }
        $this->load_class('settings');
        $this->settings = new WCMp_Settings();
        add_filter('woocommerce_hidden_order_itemmeta', array(&$this, 'add_hidden_order_items'));

        add_filter('manage_wcmp_vendorrequest_posts_columns', array(&$this, 'wcmp_vendorrequest_columns'));
        add_action('manage_wcmp_vendorrequest_posts_custom_column', array(&$this, 'custom_wcmp_vendorrequest_column'), 10, 2);
        add_filter('post_row_actions', array(&$this, 'modify_wcmp_vendorrequest_row_actions'), 10, 2);
        add_filter('bulk_actions-edit-wcmp_vendorrequest', array(&$this, 'wcmp_vendorrequest_bulk_actions'));
        add_action('admin_menu', array(&$this, 'remove_wcmp_vendorrequest_meta_boxes'));
        add_action('add_meta_boxes', array(&$this, 'adding_vendor_application_meta_boxes'), 10, 2);

        add_action('admin_menu', array(&$this, 'wcmp_admin_menu'));
        add_action('admin_head', array($this, 'menu_commission_count'));
        if (!get_option('_is_dismiss_service_notice', false) && current_user_can('manage_options')) {
            add_action('admin_notices', array(&$this, 'wcmp_service_page_notice'));
        }
        add_action('wp_dashboard_setup', array(&$this, 'wcmp_remove_wp_dashboard_widget'));
    }

    function adding_vendor_application_meta_boxes($post_type, $post) {
        add_meta_box(
                'vendor-form-data', __('Vendor Form Data', 'dc-woocommerce-multi-vendor'), array(&$this, 'render_vendor_meta_box'), 'wcmp_vendorrequest', 'normal', 'default'
        );
    }

    function render_vendor_meta_box($post, $metabox) {
        $post_id = $post->ID;
        $form_data = get_post_meta($post_id, 'wcmp_vendor_fields', true);
        if (!empty($form_data) && is_array($form_data)) {
            foreach ($form_data as $key => $value) {
                echo '<div class="wcmp-form-field">';
                echo '<label>' . html_entity_decode($value['label']) . ':</label>';
                if ($value['type'] == 'file') {
                    if (!empty($value['value']) && is_array($value['value'])) {
                        foreach ($value['value'] as $attacment_id) {
                            echo '<span> <a href="' . wp_get_attachment_url($attacment_id) . '" download>' . get_the_title($attacment_id) . '</a> </span>';
                        }
                    }
                } else {
                    if (is_array($value['value'])) {
                        echo '<span> ' . implode(', ', $value['value']) . '</span>';
                    } else {
                        echo '<span> ' . $value['value'] . '</span>';
                    }
                }
                echo '</div>';
            }
        }
    }

    function remove_wcmp_vendorrequest_meta_boxes() {
        if (current_user_can('manage_options')) {
            remove_meta_box('submitdiv', 'wcmp_vendorrequest', 'side');
        }
    }

    function wcmp_vendorrequest_bulk_actions($actions) {
        unset($actions['edit']);
        return $actions;
    }

    function modify_wcmp_vendorrequest_row_actions($actions, $post) {
        if ($post->post_type == "wcmp_vendorrequest") {
            unset($actions['view']);
            unset($actions['edit']);
            unset($actions['inline hide-if-no-js']);
            $user_id = get_post_meta($post->ID, 'user_id', true);
            $user = new WP_User($user_id);
            $user_data = get_userdata($user_id);
            $actions['view'] = '<a href="' . get_edit_post_link($post->ID, 'display') . '" title="" rel="permalink">' . __('View', 'dc-woocommerce-multi-vendor') . '</a>';
            if (!in_array('dc_vendor', $user->roles) && !in_array('dc_rejected_vendor', $user->roles) && $user_data != false) {
                $actions['aprove'] = '<a class="activate_vendor" href="#" data-id="' . $user_id . '" title="" rel="permalink">' . __('Approve', 'dc-woocommerce-multi-vendor') . '</a>';
                $actions['reject'] = '<a class="reject_vendor" href="#" data-id="' . $user_id . '" title="" rel="permalink">' . __('Reject', 'dc-woocommerce-multi-vendor') . '</a>';
            }
        }
        return $actions;
    }

    function wcmp_vendorrequest_columns($columns) {
        unset($columns['title'], $columns['date']);
        $new_columns = array(
            'userid' => __('Username', 'dc-woocommerce-multi-vendor'),
            'email' => __('Email', 'dc-woocommerce-multi-vendor'),
            'date' => __('Date', 'dc-woocommerce-multi-vendor')
        );
        return array_merge($columns, $new_columns);
    }

    function custom_wcmp_vendorrequest_column($column, $post_id) {
        switch ($column) {
            case 'userid' :
                echo get_post_meta($post_id, 'username', true);
                break;
            case 'email' :
                echo get_post_meta($post_id, 'email', true);
                break;
        }
    }

    function add_hidden_order_items($order_items) {
        $order_items[] = '_give_tax_to_vendor';
        $order_items[] = '_give_shipping_to_vendor';
        // and so on...
        return $order_items;
    }

    public function change_commission_status($order_id, $old_status, $new_status) {
        global $wpdb;
        $myorder = get_post($order_id);
        $post_type = $myorder->post_type;
        if ($old_status == 'on-hold' || $old_status == 'pending' || $old_status == 'cancelled' || $old_status == 'refunded' || $old_status == 'failed') {
            if ($new_status == 'processing' || $new_status == 'completed') {
                if ($post_type == 'shop_order') {
                    $args = array(
                        'posts_per_page' => -1,
                        'offset' => 0,
                        'meta_key' => '_commission_order_id',
                        'meta_value' => $order_id,
                        'post_type' => 'dc_commission',
                        'post_status' => 'trash',
                        'suppress_filters' => true
                    );
                    $commission_array = get_posts($args);
                    foreach ($commission_array as $commission) {
                        $to_be_restore_commission = array();
                        $to_be_restore_commission['ID'] = $commission->ID;
                        $to_be_restore_commission['post_status'] = 'private';
                        wp_update_post($to_be_restore_commission);
                    }
                    $order_query = "update " . $wpdb->prefix . "wcmp_vendor_orders set 	is_trashed = '' where `order_id` = " . $order_id;
                    $wpdb->query($order_query);
                }
            }
        } elseif ($old_status == 'processing' || $old_status == 'completed') {
            if ($new_status == 'on-hold' || $new_status == 'pending' || $new_status == 'cancelled' || $new_status == 'refunded' || $new_status == 'failed') {
                if ($post_type == 'shop_order') {
                    $args = array(
                        'posts_per_page' => -1,
                        'offset' => 0,
                        'meta_key' => '_commission_order_id',
                        'meta_value' => $order_id,
                        'post_type' => 'dc_commission',
                        'post_status' => array('publish', 'private'),
                        'suppress_filters' => true
                    );
                    $commission_array = get_posts($args);
                    foreach ($commission_array as $commission) {
                        $to_be_deleted_commission = array();
                        $to_be_deleted_commission['ID'] = $commission->ID;
                        $to_be_deleted_commission['post_status'] = 'trash';
                        wp_update_post($to_be_deleted_commission);
                    }
                    $order_query = "update " . $wpdb->prefix . "wcmp_vendor_orders set 	is_trashed = '1' where `order_id` = " . $order_id;
                    $wpdb->query($order_query);
                }
            }
        }
    }

    public function remove_commission_from_sales_report($order_id) {
        global $wpdb;
        $myorder = get_post($order_id);
        $post_type = $myorder->post_type;
        if ($post_type == 'shop_order') {
            $args = array(
                'posts_per_page' => -1,
                'offset' => 0,
                'meta_key' => '_commission_order_id',
                'meta_value' => $order_id,
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'suppress_filters' => true
            );
            $commission_array = get_posts($args);
            foreach ($commission_array as $commission) {
                $to_be_deleted_commission = array();
                $to_be_deleted_commission['ID'] = $commission->ID;
                $to_be_deleted_commission['post_status'] = 'trash';
                wp_update_post($to_be_deleted_commission);
            }
            $order_query = "update " . $wpdb->prefix . "wcmp_vendor_orders set 	is_trashed = '1' where `order_id` = " . $order_id;
            $wpdb->query($order_query);
        }
    }

    public function restore_commission_from_sales_report($order_id) {
        global $wpdb;
        $myorder = get_post($order_id);
        $post_type = $myorder->post_type;
        if ($post_type == 'shop_order') {
            $args = array(
                'posts_per_page' => -1,
                'offset' => 0,
                'meta_key' => '_commission_order_id',
                'meta_value' => $order_id,
                'post_type' => 'dc_commission',
                'post_status' => 'trash',
                'suppress_filters' => true
            );
            $commission_array = get_posts($args);
            foreach ($commission_array as $commission) {
                $to_be_restore_commission = array();
                $to_be_restore_commission['ID'] = $commission->ID;
                $to_be_restore_commission['post_status'] = 'private';
                wp_update_post($to_be_restore_commission);
            }
            $order_query = "update " . $wpdb->prefix . "wcmp_vendor_orders set 	is_trashed = '' where `order_id` = " . $order_id;
            $wpdb->query($order_query);
        }
    }

    function conditonal_includes() {
        $screen = get_current_screen();

        if (in_array($screen->id, array('options-permalink'))) {
            $this->permalink_settings_init();
            $this->permalink_settings_save();
        }
    }

    function permalink_settings_init() {
        // Add our settings
        add_settings_field(
                'dc_product_vendor_taxonomy_slug', // id
                __('Vendor Shop Base', 'dc-woocommerce-multi-vendor'), // setting title
                array(&$this, 'wcmp_taxonomy_slug_input'), // display callback
                'permalink', // settings page
                'optional'                                      // settings section
        );
    }

    function wcmp_taxonomy_slug_input() {
        $permalinks = get_option('dc_vendors_permalinks');
        ?>
        <input name="dc_product_vendor_taxonomy_slug" type="text" class="regular-text code" value="<?php if (isset($permalinks['vendor_shop_base'])) echo esc_attr($permalinks['vendor_shop_base']); ?>" placeholder="<?php echo _x('vendor', 'slug', 'dc-woocommerce-multi-vendor') ?>" />
        <?php
    }

    function permalink_settings_save() {
        if (!is_admin()) {
            return;
        }
        // We need to save the options ourselves; settings api does not trigger save for the permalinks page
        if (isset($_POST['permalink_structure']) || isset($_POST['dc_product_vendor_taxonomy_slug'])) {

            // Cat and tag bases
            $dc_product_vendor_taxonomy_slug = wc_clean($_POST['dc_product_vendor_taxonomy_slug']);
            $permalinks = get_option('dc_vendors_permalinks');

            if (!$permalinks) {
                $permalinks = array();
            }

            $permalinks['vendor_shop_base'] = untrailingslashit($dc_product_vendor_taxonomy_slug);
            update_option('dc_vendors_permalinks', $permalinks);
        }
    }

    /**
     * Add Toolbar for vendor user 
     *
     * @access public
     * @param admin bar
     * @return void
     */
    function add_toolbar_items($admin_bar) {
        $user = wp_get_current_user();
        if (is_user_wcmp_vendor($user)) {
            $admin_bar->add_menu(
                    array(
                        'id' => 'vendor_dashboard',
                        'title' => __('Frontend  Dashboard', 'dc-woocommerce-multi-vendor'),
                        'href' => get_permalink(wcmp_vendor_dashboard_page_id()),
                        'meta' => array(
                            'title' => __('Frontend Dashboard', 'dc-woocommerce-multi-vendor'),
                            'target' => '_blank',
                            'class' => 'shop-settings'
                        ),
                    )
            );
            $admin_bar->add_menu(
                    array(
                        'id' => 'shop_settings',
                        'title' => __('Shop Settings', 'dc-woocommerce-multi-vendor'),
                        'href' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_store_settings_endpoint', 'vendor', 'general', 'shop-front')),
                        'meta' => array(
                            'title' => __('Shop Settings', 'dc-woocommerce-multi-vendor'),
                            'target' => '_blank',
                            'class' => 'shop-settings'
                        ),
                    )
            );
        }
    }

    function load_class($class_name = '') {
        global $WCMp;
        if ('' != $class_name) {
            require_once ($WCMp->plugin_path . '/admin/class-' . esc_attr($WCMp->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

// End load_class()

    /**
     * Add dualcube footer text on plugin settings page
     *
     * @access public
     * @param admin bar
     * @return void
     */
    function dualcube_admin_footer_for_wcmp() {
        global $WCMp;
        ?>
        <div style="clear: both"></div>
        <div id="dc_admin_footer">
        <?php _e('Powered by', 'dc-woocommerce-multi-vendor'); ?> <a href="https://wc-marketplace.com/" target="_blank"><img src="<?php echo $WCMp->plugin_url . 'assets/images/dualcube.png'; ?>"></a><?php _e('WC Marketplace', 'dc-woocommerce-multi-vendor'); ?> &copy; <?php echo date('Y'); ?>
        </div>
        <?php
    }

    /**
     * Add css on admin header
     *
     * @access public
     * @return void
     */
    function admin_header() {
        $screen = get_current_screen();
        if (is_user_logged_in()) {
            if (isset($screen->id) && in_array($screen->id, array('edit-dc_commission', 'edit-wcmp_university', 'edit-wcmp_vendor_notice'))) {
                ?>
                <script>
                    jQuery(document).ready(function ($) {
                        var target_ele = $(".wrap .wp-header-end");
                        var targethtml = target_ele.html();
                        //targethtml = targethtml + '<a href="<?php echo trailingslashit(get_admin_url()) . 'admin.php?page=wcmp-setting-admin'; ?>" class="page-title-action">Back To WCMp Settings</a>';
                        //target_ele.html(targethtml);
                <?php if (in_array($screen->id, array('edit-wcmp_university'))) { ?>
                            target_ele.before('<p><b><?php echo __('"Knowledgebase" section is visible only to vendors through the vendor dashboard. You may use this section to onboard your vendors. Share tutorials, best practices, "how to" guides or whatever you feel is appropriate with your vendors.', 'dc-woocommerce-multi-vendor'); ?></b></p>');
                <?php } ?>
                <?php if (in_array($screen->id, array('edit-wcmp_vendor_notice'))) { ?>
                            target_ele.before('<p><b><?php echo __('Announcements are visible only to vendors through the vendor dashboard(message section). You may use this section to broadcast your announcements.', 'dc-woocommerce-multi-vendor'); ?></b></p>');
                <?php } ?>
                    });

                </script>
                <?php
            }
        }
    }

    public function wcmp_admin_menu() {
        if(is_user_wcmp_vendor(get_current_vendor_id())){
            remove_menu_page('edit.php');
            remove_menu_page('edit-comments.php');
            remove_menu_page('tools.php');
        }
    }

    public function menu_commission_count() {
        global $submenu;
        if (isset($submenu['wcmp'])) {
            if (apply_filters('wcmp_include_unpaid_commission_count_in_menu', true) && current_user_can('manage_woocommerce') && ( $order_count = wcmp_count_commission()->unpaid )) {
                foreach ($submenu['wcmp'] as $key => $menu_item) {
                    if (0 === strpos($menu_item[0], _x('Commissions', 'Admin menu name', 'wcmp'))) {
                        $submenu['wcmp'][$key][0] .= ' <span class="awaiting-mod update-plugins count-' . $order_count . '"><span class="processing-count">' . number_format_i18n($order_count) . '</span></span>';
                        break;
                    }
                }
            }
        }
    }

    /**
     * Admin Scripts
     */
    public function enqueue_admin_script() {
        global $WCMp;
        $screen = get_current_screen();
        $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
        $general_singleproductmultisellersettings = get_option('wcmp_general_singleproductmultiseller_settings_name');
        //echo $screen->id;
        // Enqueue admin script and stylesheet from here
        if (in_array($screen->id, array('wcmp_page_wcmp-setting-admin', 'wcmp_page_wcmp-to-do'))) :
            $WCMp->library->load_qtip_lib();
            $WCMp->library->load_upload_lib();
            $WCMp->library->load_colorpicker_lib();
            $WCMp->library->load_datepicker_lib();
            wp_enqueue_script('wcmp_admin_js', $WCMp->plugin_url . 'assets/admin/js/admin' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_enqueue_style('wcmp_admin_css', $WCMp->plugin_url . 'assets/admin/css/admin' . $suffix . '.css', array(), $WCMp->version);

        endif;
        if (in_array($screen->id, array('wcmp_page_wcmp-to-do', 'edit-wcmp_vendorrequest'))) {
            wp_enqueue_script('dc_to_do_list_js', $WCMp->plugin_url . 'assets/admin/js/to_do_list' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        }
        if (in_array($screen->id, array('wcmp_page_wcmp-to-do'))) {
            wp_enqueue_style('wcmp_admin_todo_list', $WCMp->plugin_url . 'assets/admin/css/admin-to_do_list' . $suffix . '.css', array(), $WCMp->version);
        }

        if (in_array($screen->id, array('dc_commission', 'woocommerce_page_wc-reports', 'toplevel_page_wc-reports'))) :
            $WCMp->library->load_qtip_lib();
            wp_enqueue_script('wcmp_admin_js', $WCMp->plugin_url . 'assets/admin/js/admin' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_enqueue_style('wcmp_admin_css', $WCMp->plugin_url . 'assets/admin/css/admin' . $suffix . '.css', array(), $WCMp->version);
            if (!wp_style_is('woocommerce_chosen_styles', 'queue')) {
                // wp_enqueue_style( 'woocommerce_chosen_styles', $woocommerce->plugin_url() . '/assets/css/chosen.css' );
                wp_enqueue_style('woocommerce_chosen_styles', $WCMp->plugin_url . '/assets/admin/css/chosen' . $suffix . '.css');
            }
            // Load Chosen JS
            // wp_enqueue_script( 'ajax-chosen' );
            // wp_enqueue_script( 'chosen' );
            wp_enqueue_script('WCMp_chosen', $WCMp->plugin_url . 'assets/admin/js/chosen.jquery' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_enqueue_script('WCMp_ajax-chosen', $WCMp->plugin_url . 'assets/admin/js/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'WCMp_chosen'), $WCMp->version, true);
            wp_enqueue_script('wcmp-admin-commission-js', $WCMp->plugin_url . 'assets/admin/js/commission' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_localize_script('wcmp-admin-commission-js', 'dc_vendor_object', array('security' => wp_create_nonce("search-products")));
        endif;



        if (in_array($screen->id, array('product', 'edit-product'))) :
            $WCMp->library->load_qtip_lib();
            wp_enqueue_script('wcmp_admin_js', $WCMp->plugin_url . 'assets/admin/js/admin' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_enqueue_style('wcmp_admin_css', $WCMp->plugin_url . 'assets/admin/css/admin' . $suffix . '.css', array(), $WCMp->version);
            if (!wp_style_is('woocommerce_chosen_styles', 'queue')) {
                wp_enqueue_style('woocommerce_chosen_styles', $WCMp->plugin_url . '/assets/admin/css/chosen' . $suffix . '.css');
            }
            // Load Chosen JS
            // wp_enqueue_script( 'ajax-chosen' );
            // wp_enqueue_script( 'chosen' );

            wp_enqueue_script('WCMp_chosen', $WCMp->plugin_url . 'assets/admin/js/chosen.jquery' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_enqueue_script('WCMp_ajax-chosen', $WCMp->plugin_url . 'assets/admin/js/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'WCMp_chosen'), $WCMp->version, true);
            wp_enqueue_script('wcmp-admin-product-js', $WCMp->plugin_url . 'assets/admin/js/product' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_localize_script('wcmp-admin-product-js', 'dc_vendor_object', array('security' => wp_create_nonce("search-products")));
            if (get_wcmp_vendor_settings('is_singleproductmultiseller', 'general') == 'Enable') {
                wp_enqueue_script('wcmp_admin_product_auto_search_js', $WCMp->plugin_url . 'assets/admin/js/admin-product-auto-search' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            }
        endif;

        if (in_array($screen->id, array('user-edit', 'profile'))) :
            $WCMp->library->load_qtip_lib();
            $WCMp->library->load_upload_lib();
            wp_enqueue_script('wcmp_admin_js', $WCMp->plugin_url . 'assets/admin/js/admin' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_enqueue_style('admin_user', $WCMp->plugin_url . 'assets/admin/css/admin-user' . $suffix . '.css', array(), $WCMp->version);
            wp_enqueue_script('edit_user_js', $WCMp->plugin_url . 'assets/admin/js/edit_user' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        endif;

        if (in_array($screen->id, array('users'))) :
            wp_enqueue_script('dc_users_js', $WCMp->plugin_url . 'assets/admin/js/to_do_list' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        endif;

        if (in_array($screen->id, array('woocommerce_page_wc-reports', 'toplevel_page_wc-reports'))) :
            // wp_enqueue_script( 'ajax-chosen' );
            // wp_enqueue_script( 'chosen' );
            wp_enqueue_script('WCMp_chosen', $WCMp->plugin_url . 'assets/admin/js/chosen.jquery' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_enqueue_script('WCMp_ajax-chosen', $WCMp->plugin_url . 'assets/admin/js/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'WCMp_chosen'), $WCMp->version, true);
            wp_enqueue_script('product_js', $WCMp->plugin_url . 'assets/admin/js/product' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_localize_script('product_js', 'dc_vendor_object', array('security' => wp_create_nonce("search-products")));
        endif;
        if (in_array($screen->id, array('wcmp_vendorrequest'))) :
            wp_enqueue_style('admin-vendor_registration-css', $WCMp->plugin_url . 'assets/admin/css/admin-vendor_registration' . $suffix . '.css', array(), $WCMp->version);
        endif;

        if (in_array($screen->id, array('woocommerce_page_wc-reports', 'toplevel_page_wc-reports'))) :
            wp_enqueue_script('wcmp_report_js', $WCMp->plugin_url . 'assets/admin/js/report' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_enqueue_style('wcmp_report_css', $WCMp->plugin_url . 'assets/admin/css/report' . $suffix . '.css', array(), $WCMp->version);
            $WCMp->library->font_awesome_lib();
        endif;
        if (in_array($screen->id, array('wcmp_page_wcmp-extensions'))) :
            wp_enqueue_style('admin-extensions', $WCMp->plugin_url . 'assets/admin/css/admin-extensions' . $suffix . '.css', array(), $WCMp->version);
        endif;

        if (is_user_wcmp_vendor(get_current_vendor_id())) {
            wp_enqueue_script('wcmp_vendor_js', $WCMp->plugin_url . 'assets/admin/js/vendor' . $suffix . '.js', array('jquery', 'woocommerce_admin'), $WCMp->version, true);
        }
    }

    function wcmp_kill_auto_save() {
        if ('product' == get_post_type()) {
            wp_dequeue_script('autosave');
        }
    }

    /**
     * Display WCMp service notice in admin panel
     */
    public function wcmp_service_page_notice() {
        ?>
        <div class="updated wcmp_admin_new_banner">
            <div class="round"></div>
            <div class="round1"></div>
            <div class="round2"></div>
            <div class="round3"></div>
            <div class="round4"></div>
            <div class="wcmp_banner-content">
                <span class="txt">Get WC Marketplace setup as per your store requirements, totally FREE! Excited?  </span>
                <div class="rightside">        
                    <a href="https://wc-marketplace.com/wcmp-services/?install=1" target="_blank" class="wcmp_btn_service_claim_now" onclick="dismiss_servive_notice(event);">Claim Now</a>
                    <span class="link"><a href="javascript:void(0)" onclick="dismiss_servive_notice(event);">No, Thanks!</a></span>
                </div>

            </div>
        </div>
        <style type="text/css">.clearfix{clear:both}.wcmp_admin_new_banner.updated{border-left:0}.wcmp_admin_new_banner{box-shadow:0 3px 1px 1px rgba(0,0,0,.2);padding:10px 30px;background:#fff;position:relative;overflow:hidden;clear:both;border-top:2px solid #7b589e;text-align:left;background-size:contain}.wcmp_admin_new_banner .round{width:200px;height:200px;position:absolute;border-radius:100%;border:30px solid rgba(157,42,255,.05);top:-150px;left:73px;z-index:1}.wcmp_admin_new_banner .round1{position:absolute;border-radius:100%;border:45px solid rgba(194,108,144,.05);bottom:-82px;right:-58px;width:180px;height:180px;z-index:1}.wcmp_admin_new_banner .round2,.wcmp_admin_new_banner .round3{border-radius:100%;width:180px;height:180px;position:absolute;z-index:1}.wcmp_admin_new_banner .round2{border:18px solid rgba(194,108,144,.05);top:35px;left:249px}.wcmp_admin_new_banner .round3{border:45px solid rgba(31,194,255,.05);top:2px;right:40%}.wcmp_admin_new_banner .round4{position:absolute;border-radius:100%;border:31px solid rgba(31,194,255,.05);top:11px;left:-49px;width:100px;height:100px;z-index:1}.wcmp_banner-content{display: -webkit-box;display: -moz-box;display: -ms-flexbox;display: -webkit-flex;display: flex;align-items:center}.wcmp_admin_new_banner .txt{color:#333;font-size:18px;line-height:1.4;width:calc(100% - 330px);position:relative;z-index:2;display:inline-block;font-weight:400;float:left;padding-left:8px}.wcmp_admin_new_banner .link,.wcmp_admin_new_banner .wcmp_btn_service_claim_now{font-weight:400;display:inline-block;z-index:2;padding:0 20px;position:relative}.wcmp_admin_new_banner .rightside{float:right;width:303px}.wcmp_admin_new_banner .wcmp_btn_service_claim_now{cursor:pointer;background:#7b589e;height:40px;color:#fff;font-size:20px;text-align:center;border:none;margin:5px 13px;border-radius:5px;text-decoration:none;line-height:40px}.wcmp_admin_new_banner button:hover{opacity:.8;transition:.5s}.wcmp_admin_new_banner .link{font-size:18px;line-height:49px;background:0 0;height:50px}.wcmp_admin_new_banner .link a{color:#333;text-decoration:none}@media (max-width:990px){.wcmp_admin_new_banner::before{left:-4%;top:-12%}}@media (max-width:767px){.wcmp_admin_new_banner::before{left:0;top:0;transform:rotate(0);width:10px}.wcmp_admin_new_banner .txt{width:400px;max-width:100%;text-align:center;padding:0;margin:0 auto 5px;float:none;display:block;font-size:17px;line-height:1.6}.wcmp_admin_new_banner .rightside{width:100%;padding-left:10px;text-align:center;box-sizing:border-box}.wcmp_admin_new_banner .wcmp_btn_service_claim_now{margin:10px 0}.wcmp_banner-content{display:block}}</style>
        <script type="text/javascript">
            function dismiss_servive_notice(e, i) {
                jQuery.post(ajaxurl, {action: "dismiss_wcmp_servive_notice"}, function (e) {
                    e && (jQuery(".wcmp_admin_new_banner").addClass("hidden"), void 0 !== i && (window.open(i, '_blank')))
                })
            }
        </script>
        <?php
    }

    /**
     * Remove wp dashboard widget for vendor
     * @global array $wp_meta_boxes
     */
    public function wcmp_remove_wp_dashboard_widget() {
        global $wp_meta_boxes;
        if (is_user_wcmp_vendor(get_current_vendor_id())) {
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
            unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
            unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        }
    }

}
