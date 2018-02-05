<?php

/**
 * Description of WCMp_Vendor_Hooks
 *
 * @author WC Marketplace
 */
class WCMp_Vendor_Hooks {

    function __construct() {
        add_action('wcmp_vendor_dashboard_navigation', array(&$this, 'wcmp_create_vendor_dashboard_navigation'));
        add_action('wcmp_vendor_dashboard_content', array(&$this, 'wcmp_create_vendor_dashboard_content'));
        add_action('wcmp_vendor_dashboard_header', array(&$this, 'wcmp_vendor_dashboard_header'));
        add_action('before_wcmp_vendor_dashboard', array(&$this, 'save_vendor_dashboard_data'));
        
        add_action('wcmp_vendor_dashboard_vendor-announcements_endpoint', array(&$this, 'wcmp_vendor_dashboard_vendor_announcements_endpoint'));
        add_action('wcmp_vendor_dashboard_vendor-orders_endpoint', array(&$this, 'wcmp_vendor_dashboard_vendor_orders_endpoint'));
        add_action('wcmp_vendor_dashboard_shop-front_endpoint', array(&$this, 'wcmp_vendor_dashboard_shop_front_endpoint'));
        add_action('wcmp_vendor_dashboard_vendor-policies_endpoint', array(&$this, 'wcmp_vendor_dashboard_vendor_policies_endpoint'));
        add_action('wcmp_vendor_dashboard_vendor-billing_endpoint', array(&$this, 'wcmp_vendor_dashboard_vendor_billing_endpoint'));
        add_action('wcmp_vendor_dashboard_vendor-shipping_endpoint', array(&$this, 'wcmp_vendor_dashboard_vendor_shipping_endpoint'));
        add_action('wcmp_vendor_dashboard_vendor-report_endpoint', array(&$this, 'wcmp_vendor_dashboard_vendor_report_endpoint'));
        add_action('wcmp_vendor_dashboard_vendor-withdrawal_endpoint', array(&$this, 'wcmp_vendor_dashboard_vendor_withdrawal_endpoint'));
        add_action('wcmp_vendor_dashboard_transaction-details_endpoint', array(&$this, 'wcmp_vendor_dashboard_transaction_details_endpoint'));
        add_action('wcmp_vendor_dashboard_vendor-knowledgebase_endpoint', array(&$this, 'wcmp_vendor_dashboard_vendor_knowledgebase_endpoint'));
        
        add_filter('the_title', array(&$this, 'wcmp_vendor_dashboard_endpoint_title'));
        add_filter('wcmp_vendor_dashboard_menu_vendor_policies_capability', array(&$this, 'wcmp_vendor_dashboard_menu_vendor_policies_capability'));
        add_filter('wcmp_vendor_dashboard_menu_vendor_withdrawal_capability', array(&$this,'wcmp_vendor_dashboard_menu_vendor_withdrawal_capability'));
        add_filter('wcmp_vendor_dashboard_menu_vendor_shipping_capability', array(&$this, 'wcmp_vendor_dashboard_menu_vendor_shipping_capability'));
    }

    /**
     * Create vendor dashboard menu
     * @global object $WCMp
     */
    public function wcmp_create_vendor_dashboard_navigation($args = array()) {
        global $WCMp;
        $vendor_nav = array(
            'dashboard' => array(
                'label' => __('Dashboard', 'dc-woocommerce-multi-vendor')
                , 'url' => wcmp_get_vendor_dashboard_endpoint_url('dashboard')
                , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_dashboard_capability', true)
                , 'position' => 0
                , 'submenu' => array()
                , 'link_target' => '_self'
                , 'nav_icon' => 'dashicons-dashboard'
            ),
            'store-settings' => array(
                'label' => __('Store Settings', 'dc-woocommerce-multi-vendor')
                , 'url' => '#'
                , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_store_settings_capability', true)
                , 'position' => 10
                , 'submenu' => array(
                    'shop-front' => array(
                        'label' => __('Shop Front', 'dc-woocommerce-multi-vendor')
                        , 'url' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_store_settings_endpoint', 'vendor', 'general', 'shop-front'))
                        , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_shop_front_capability', true)
                        , 'position' => 10
                        , 'link_target' => '_self'
                    ),
                    'vendor-policies' => array(
                        'label' => __('Policies', 'dc-woocommerce-multi-vendor')
                        , 'url' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_policies_endpoint', 'vendor', 'general', 'vendor-policies'))
                        , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_policies_capability', false)
                        , 'position' => 20
                        , 'link_target' => '_self'
                    ),
                    'vendor-billing' => array(
                        'label' => __('Billing', 'dc-woocommerce-multi-vendor')
                        , 'url' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_billing_endpoint', 'vendor', 'general', 'vendor-billing'))
                        , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_billing_capability', true)
                        , 'position' => 30
                        , 'link_target' => '_self'
                    ),
                    'vendor-shipping' => array(
                        'label' => __('Shipping', 'dc-woocommerce-multi-vendor')
                        , 'url' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_shipping_endpoint', 'vendor', 'general', 'vendor-shipping'))
                        , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_shipping_capability', wc_shipping_enabled())
                        , 'position' => 40
                        , 'link_target' => '_self'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon' => 'dashicons-admin-generic'
            ),
            'vendor-products' => array(
                'label' => __('Product Manager', 'dc-woocommerce-multi-vendor')
                , 'url' => apply_filters('wcmp_vendor_submit_product', admin_url('edit.php?post_type=product'))
                , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_products_capability', 'edit_products')
                , 'position' => 20
                , 'submenu' => array()
                , 'link_target' => '_self'
                , 'nav_icon' => 'dashicons-cart'
            ),
            'vendor-promte' => array(
                'label' => __('Promote', 'dc-woocommerce-multi-vendor')
                , 'url' => '#'
                , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_promte_capability', 'edit_shop_coupons')
                , 'position' => 30
                , 'submenu' => array(
                    'add-new-coupon' => array(
                        'label' => __('Add Coupon', 'dc-woocommerce-multi-vendor')
                        , 'url' => apply_filters('wcmp_vendor_submit_coupon', admin_url('post-new.php?post_type=shop_coupon'))
                        , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_add_new_coupon_capability', 'edit_shop_coupons')
                        , 'position' => 10
                        , 'link_target' => '_self'
                    ),
                    'coupons' => array(
                        'label' => __('Coupons', 'dc-woocommerce-multi-vendor')
                        , 'url' => apply_filters('wcmp_vendor_coupons', admin_url('edit.php?post_type=shop_coupon'))
                        , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_coupons_capability', 'edit_shop_coupons')
                        , 'position' => 20
                        , 'link_target' => '_self'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon' => 'dashicons-megaphone'
            ),
            'vendor-report' => array(
                'label' => __('Stats / Reports', 'dc-woocommerce-multi-vendor')
                , 'url' => '#'
                , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_report_capability', true)
                , 'position' => 40
                , 'submenu' => array(
                    'vendor-report' => array(
                        'label' => __('Overview', 'dc-woocommerce-multi-vendor')
                        , 'url' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_report_endpoint', 'vendor', 'general', 'vendor-report'))
                        , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_report_capability', true)
                        , 'position' => 10
                        , 'link_target' => '_self'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon' => 'dashicons-chart-area'
            ),
            'vendor-orders' => array(
                'label' => __('Orders', 'dc-woocommerce-multi-vendor')
                , 'url' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders'))
                , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_orders_capability', true)
                , 'position' => 50
                , 'submenu' => array()
                , 'link_target' => '_self'
                , 'nav_icon' => 'dashicons-store'
            ),
            'vendor-payments' => array(
                'label' => __('Payments', 'dc-woocommerce-multi-vendor')
                , 'url' => '#'
                , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_payments_capability', true)
                , 'position' => 60
                , 'submenu' => array(
                    'vendor-withdrawal' => array(
                        'label' => __('Withdrawal', 'dc-woocommerce-multi-vendor')
                        , 'url' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_withdrawal_endpoint', 'vendor', 'general', 'vendor-withdrawal'))
                        , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_withdrawal_capability', false)
                        , 'position' => 10
                        , 'link_target' => '_self'
                    ),
                    'transaction-details' => array(
                        'label' => __('History', 'dc-woocommerce-multi-vendor')
                        , 'url' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_transaction_details_endpoint', 'vendor', 'general', 'transaction-details'))
                        , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_transaction_details_capability', true)
                        , 'position' => 20
                        , 'link_target' => '_self'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon' => 'dashicons-tickets-alt'
            ),
            'vendor-knowledgebase' => array(
                'label' => __('Knowledgebase', 'dc-woocommerce-multi-vendor')
                , 'url' => wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_knowledgebase_endpoint', 'vendor', 'general', 'vendor-knowledgebase'))
                , 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_knowledgebase_capability', get_wcmp_vendor_settings('is_university_on', 'general') ? true : false)
                , 'position' => 70
                , 'submenu' => array()
                , 'link_target' => '_self'
                , 'nav_icon' => 'dashicons-welcome-learn-more'
            ),
            'vendor-signout' => array(
                'label' => __('Sign out', 'dc-woocommerce-multi-vendor')
                , 'url' => esc_url(wp_logout_url(get_permalink(wcmp_vendor_dashboard_page_id())) )
                , 'capability' => true
                , 'position' => 80
                , 'submenu' => array()
                , 'link_target' => '_self'
                , 'nav_icon' => 'dashicons-migrate'
            )
        );
        $WCMp->template->get_template('vendor-dashboard/navigation.php', array('nav_items' => apply_filters('wcmp_vendor_dashboard_nav', $vendor_nav), 'args' => $args));
    }

    /**
     * Display Vendor dashboard Content
     * @global object $wp
     * @global object $WCMp
     * @return null
     */
    public function wcmp_create_vendor_dashboard_content() {
        global $wp, $WCMp;
        foreach ($wp->query_vars as $key => $value) {
            // Ignore pagename param.
            if ('pagename' === $key) {
                continue;
            }

            if (has_action('wcmp_vendor_dashboard_' . $key . '_endpoint')) {
                do_action('wcmp_vendor_dashboard_' . $key . '_endpoint', $value);
                return;
            }
        }
        $WCMp->template->get_template('vendor-dashboard/dashboard.php');
    }

    /**
     * Display Vendor Announcements content
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_vendor_announcements_endpoint() {
        global $WCMp;
        $frontend_style_path = $WCMp->plugin_url . 'assets/frontend/css/';
        $frontend_style_path = str_replace(array('http:', 'https:'), '', $frontend_style_path);
        $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_style('font-vendor_announcements', '//fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700,700italic,900,900italic', array(), $WCMp->version);
        wp_enqueue_style('ui_vendor_announcements', '//code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css', array(), $WCMp->version);
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('wcmp_new_vandor_announcements_js', $frontend_script_path . 'wcmp_vendor_announcements' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        //wp_enqueue_script('jquery');
        
        //wp_enqueue_script('wcmp_new_vandor_announcements_js_lib_ui', '//code.jquery.com/ui/1.10.4/jquery-ui.js', array('jquery'), $WCMp->version, true);
        $WCMp->template->get_template('vendor-dashboard/vendor-announcements.php');
    }

    /**
     * Display vendor dashboard shop front content
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_shop_front_endpoint() {
        global $WCMp;
        $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script('wcmp_profile_edit_js', $frontend_script_path . '/profile_edit' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        $vendor = get_wcmp_vendor(get_current_vendor_id());
        $user_array = $WCMp->user->get_vendor_fields($vendor->id);
        $WCMp->library->load_upload_lib();
        $WCMp->template->get_template('vendor-dashboard/shop-front.php', $user_array);
    }

    /**
     * display vendor policies content
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_vendor_policies_endpoint() {
        global $WCMp;
        $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script('wcmp_profile_edit_js', $frontend_script_path . '/profile_edit' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        $vendor = get_wcmp_vendor(get_current_vendor_id());
        $user_array = $WCMp->user->get_vendor_fields($vendor->id);
        $WCMp->template->get_template('vendor-dashboard/vendor-policy.php', $user_array);
    }

    /**
     * Display Vendor billing settings content
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_vendor_billing_endpoint() {
        global $WCMp;
        $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        $pluginURL = str_replace(array('http:', 'https:'), '', $WCMp->plugin_url);
        $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script('wcmp_profile_edit_js', $frontend_script_path . '/profile_edit' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        $vendor = get_wcmp_vendor(get_current_vendor_id());
        $user_array = $WCMp->user->get_vendor_fields($vendor->id);
        $WCMp->template->get_template('vendor-dashboard/vendor-billing.php', $user_array);
    }

    /**
     * Display vendor shipping content
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_vendor_shipping_endpoint() {
        global $WCMp;
        $frontend_style_path = $WCMp->plugin_url . 'assets/frontend/css/';
        $frontend_style_path = str_replace(array('http:', 'https:'), '', $frontend_style_path);
        $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script('wcmp_profile_edit_js', $frontend_script_path . '/profile_edit' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        $wcmp_payment_settings_name = get_option('wcmp_payment_settings_name');
        $_vendor_give_shipping = get_user_meta(get_current_vendor_id(), '_vendor_give_shipping', true);
        if (isset($wcmp_payment_settings_name['give_shipping']) && empty($_vendor_give_shipping)) {
            $WCMp->template->get_template('vendor-dashboard/vendor-shipping.php');
        } else {
            echo '<p class="wcmp_headding3">' . __('Sorry you are not authorized for this pages. Please contact with admin.', 'dc-woocommerce-multi-vendor') . '</p>';
        }
    }

    /**
     * Display vendor report content
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_vendor_report_endpoint() {
        global $WCMp;
        if (isset($_POST['wcmp_stat_start_dt'])) {
            $start_date = $_POST['wcmp_stat_start_dt'];
        } else {
            // hard-coded '01' for first day     
            $start_date = date('01-m-Y');
        }

        if (isset($_POST['wcmp_stat_end_dt'])) {
            $end_date = $_POST['wcmp_stat_end_dt'];
        } else {
            // hard-coded '01' for first day
            $end_date = date('t-m-Y');
        }
        $vendor = get_wcmp_vendor(get_current_vendor_id());
        $WCMp_Plugin_Post_Reports = new WCMp_Report();
        $array_report = $WCMp_Plugin_Post_Reports->vendor_sales_stat_overview($vendor, $start_date, $end_date);
        $WCMp->template->get_template('vendor-dashboard/vendor-report.php', $array_report);
    }

    /**
     * Dashboard order endpoint contect
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_vendor_orders_endpoint() {
        global $WCMp, $wpdb, $wp;
        $vendor_order = $wp->query_vars[get_wcmp_vendor_settings('wcmp_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders')];
        if (!empty($vendor_order)) {
            $WCMp->template->get_template('vendor-dashboard/vendor-orders/vendor-order-details.php', array('order_id' => $vendor_order));
        } else {
            $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
            $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
            $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_script('vendor_orders_js', $frontend_script_path . 'vendor_orders' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            wp_localize_script('vendor_orders_js', 'wcmp_mark_shipped_text', array('text' => __('Order is marked as shipped.', 'dc-woocommerce-multi-vendor'), 'image' => $WCMp->plugin_url . 'assets/images/roket-green.png'));
            $user = wp_get_current_user();
            $vendor = get_wcmp_vendor($user->ID);
            $vendor = apply_filters('wcmp_order_vendor', $vendor);
            if ($vendor) {
                if (!empty($_POST['wcmp_start_date_order'])) {
                    $start_date = $_POST['wcmp_start_date_order'];
                } else {
                    // hard-coded '01' for first day    
                    $start_date = date('01-m-Y');
                }

                if (!empty($_POST['wcmp_end_date_order'])) {
                    $end_date = $_POST['wcmp_end_date_order'];
                } else {
                    // hard-coded '01' for first day
                    $end_date = date('t-m-Y');
                }

                $start_date = date('Y-m-d G:i:s', strtotime($start_date));
                $end_date = date('Y-m-d G:i:s', strtotime($end_date . ' +1 day'));
                $customer_orders = $wpdb->get_results("SELECT DISTINCT order_id from `{$wpdb->prefix}wcmp_vendor_orders` where commission_id > 0 AND vendor_id = '" . $vendor->id . "' AND (`created` >= '" . $start_date . "' AND `created` <= '" . $end_date . "') and `is_trashed` != 1 ORDER BY `created` DESC", ARRAY_A);
                $orders_array = array();
                if (!empty($customer_orders)) {
                    foreach ($customer_orders as $order_obj) {
                        if (isset($order_obj['order_id'])) {
                            if (get_post_status($order_obj['order_id']) == 'wc-completed') {
                                $orders_array['completed'][] = $order_obj['order_id'];
                            } else if (get_post_status($order_obj['order_id']) == 'wc-processing') {
                                $orders_array['processing'][] = $order_obj['order_id'];
                            }
                            $orders_array['all'][] = $order_obj['order_id'];
                        }
                    }
                }
                if (!isset($orders_array['all'])) {
                    $orders_array['all'] = array();
                }
                if (!isset($orders_array['processing'])) {
                    $orders_array['processing'] = array();
                }
                if (!isset($orders_array['completed'])) {
                    $orders_array['completed'] = array();
                }
                $WCMp->template->get_template('vendor-dashboard/vendor-orders.php', array('vendor' => $vendor, 'customer_orders' => $orders_array));
                wp_localize_script('vendor_orders_js', 'wcmp_vendor_all_orders_array', $orders_array['all']);
                wp_localize_script('vendor_orders_js', 'wcmp_vendor_processing_orders_array', $orders_array['processing']);
                wp_localize_script('vendor_orders_js', 'wcmp_vendor_completed_orders_array', $orders_array['completed']);
            }
        }
    }

    /**
     * Display Vendor Withdrawal Content
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_vendor_withdrawal_endpoint() {
        global $WCMp, $wp;
        $transaction_id = $wp->query_vars[get_wcmp_vendor_settings('wcmp_vendor_withdrawal_endpoint', 'vendor', 'general', 'vendor-withdrawal')];
        if (!empty($transaction_id)) {
            $WCMp->template->get_template('vendor-dashboard/vendor-withdrawal/vendor-withdrawal-request.php', array('transaction_id' => $transaction_id));
        } else {
            $vendor = get_wcmp_vendor(get_current_vendor_id());
            if ($vendor) {
                $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
                $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
                $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
                wp_enqueue_script('vendor_withdrawal_js', $frontend_script_path . 'vendor_withdrawal' . $suffix . '.js', array('jquery'), $WCMp->version, true);
                $meta_query['meta_query'] = array(
                    array(
                        'key' => '_paid_status',
                        'value' => 'unpaid',
                        'compare' => '='
                    ),
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($vendor->term_id),
                        'compare' => '='
                    )
                );
                $vendor_all_orders = $vendor->get_orders(false, false, $meta_query);

                if ($vendor_all_orders) {
                    $count_orders = count($vendor_all_orders);
                } else {
                    $count_orders = 0;
                }
                $customer_orders = $vendor->get_orders(6, 0, $meta_query);
                $WCMp->template->get_template('vendor-dashboard/vendor-withdrawal.php', array('vendor' => $vendor, 'commissions' => $customer_orders, 'total_orders' => $count_orders));
            }
        }
    }

    /**
     * Display transaction details content
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_transaction_details_endpoint() {
        global $WCMp;
        $transaction_ids = array();
        $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery_ui_css', $WCMp->plugin_url . 'assets/frontend/css/jquery-ui' . $suffix . '.css', array(), $WCMp->version);
        $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        wp_enqueue_script('trans_dtl_js', $frontend_script_path . 'transaction_detail' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        $user_id = get_current_vendor_id();
        if (is_user_wcmp_vendor($user_id)) {
            $vendor = get_wcmp_vendor($user_id);
            $vendor = apply_filters('wcmp_transaction_vendor', $vendor);
            $start_date = date('01-m-Y');
            $end_date = date('t-m-Y');
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
                    $transaction_details = $WCMp->transaction->get_transactions($vendor->term_id, $_GET['from_date'], $_GET['to_date'], false);
                } else if (!empty($_GET['from_date'])) {
                    $transaction_details = $WCMp->transaction->get_transactions($vendor->term_id, $_GET['from_date'], date('j-n-Y'), false);
                } else {
                    $transaction_details = $WCMp->transaction->get_transactions($vendor->term_id, $start_date, $end_date, false);
                }
            } else {
                $transaction_details = $WCMp->transaction->get_transactions($vendor->term_id, $start_date, $end_date, false);
            }
            if (!empty($transaction_details)) {
                foreach ($transaction_details as $transaction_id => $detail) {
                    $transaction_ids[] = $transaction_id;
                }
            }
            $WCMp->template->get_template('vendor-dashboard/vendor-transactions.php', array('transactions' => $transaction_ids));
            wp_localize_script('trans_dtl_js', 'wcmp_vendor_transactions_array', $transaction_ids);
        }
    }

    /**
     * Display Vendor university content
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_vendor_knowledgebase_endpoint() {
        global $WCMp;
        wp_enqueue_style( 'jquery-ui-style' );
        wp_enqueue_script('jquery-ui-accordion');
        $WCMp->template->get_template('vendor-dashboard/vendor-university.php');
    }

    public function save_vendor_dashboard_data() {
        global $WCMp;
        $vendor = get_wcmp_vendor(get_current_vendor_id());
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            switch ($WCMp->endpoints->get_current_endpoint()) {
                case 'shop-front':
                case 'vendor-policies':
                case 'vendor-billing':
                    $error = $WCMp->vendor_dashboard->save_store_settings($vendor->id, $_POST);
                    if (empty($error)) {
                        wc_add_notice(__('All Options Saved', 'dc-woocommerce-multi-vendor'), 'success');
                    } else {
                        wc_add_notice($error, 'error');
                    }
                    break;
                case 'vendor-shipping':
                    $WCMp->vendor_dashboard->save_vendor_shipping($vendor->id, $_POST);
                    break;
                default :
                    break;
            }
        }
    }

    /**
     * Change endpoint page title
     * @global object $wp_query
     * @global object $WCMp
     * @param string $title
     * @return string
     */
    public function wcmp_vendor_dashboard_endpoint_title($title) {
        global $wp_query, $WCMp;
        if (!is_null($wp_query) && !is_admin() && is_main_query() && in_the_loop() && is_page() && is_wcmp_endpoint_url()) {
            $endpoint = $WCMp->endpoints->get_current_endpoint();

            if (isset($WCMp->endpoints->wcmp_query_vars[$endpoint]['label']) && $endpoint_title = $WCMp->endpoints->wcmp_query_vars[$endpoint]['label']) {
                $title = $endpoint_title;
            }

            remove_filter('the_title', array(&$this, 'wcmp_vendor_dashboard_endpoint_title'));
        }

        return $title;
    }

    /**
     * set policies tab cap
     * @param Boolean $cap
     * @return Boolean
     */
    public function wcmp_vendor_dashboard_menu_vendor_policies_capability($cap) {
        if ((get_wcmp_vendor_settings('is_policy_on', 'general') && (get_wcmp_vendor_settings('is_cancellation_on', 'general', 'policies') || get_wcmp_vendor_settings('is_refund_on', 'general', 'policies') || get_wcmp_vendor_settings('is_shipping_on', 'general', 'policies')) && (get_wcmp_vendor_settings('can_vendor_edit_policy_tab_label', 'general', 'policies') || get_wcmp_vendor_settings('can_vendor_edit_cancellation_policy', 'general', 'policies') || get_wcmp_vendor_settings('can_vendor_edit_refund_policy', 'general', 'policies') || get_wcmp_vendor_settings('can_vendor_edit_shipping_policy', 'general', 'policies') ) )|| (get_wcmp_vendor_settings('is_customer_support_details', 'general') && get_wcmp_vendor_settings('can_vendor_add_customer_support_details', 'general', 'customer_support_details'))) {
            $cap = true;
        }
        return $cap;
    }
    
    public function wcmp_vendor_dashboard_menu_vendor_withdrawal_capability($cap){
        if(get_wcmp_vendor_settings('wcmp_disbursal_mode_vendor', 'payment')){
            $cap = true;
        }
        return $cap;
    }
    
    public function wcmp_vendor_dashboard_menu_vendor_shipping_capability($cap){
        $vendor = get_wcmp_vendor(get_current_vendor_id());
        if($vendor){
            return $vendor->is_shipping_tab_enable();
        } else{
            return false;
        }
    }

    /**
     * Generate Vendor Dashboard Header
     * @global object $WCMp
     */
    public function wcmp_vendor_dashboard_header() {
        global $WCMp;
        switch ($WCMp->endpoints->get_current_endpoint()) {
            case 'shop-front':
                echo '<ul>';
                echo '<li>' . __('Store Settings ', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '<li class="next"> < </li>';
                echo '<li>' . __('Store Front', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                echo '<button class="wcmp_ass_btn edit_shop_settings">' . __('Edit', 'dc-woocommerce-multi-vendor') . '</button>';
                break;
            case 'vendor-policies':
                echo '<ul>';
                echo '<li>' . __('Store Settings ', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '<li class="next"> < </li>';
                echo '<li>' . __('Policies', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                echo '<button class="wcmp_ass_btn edit_policy">' . __('Edit', 'dc-woocommerce-multi-vendor') . '</button>';
                break;
            case 'vendor-billing':
                echo '<ul>';
                echo '<li>' . __('Store Settings ', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '<li class="next"> < </li>';
                echo '<li>' . __('Billing', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                echo '<button class="wcmp_ass_btn edit_billing">' . __('Edit', 'dc-woocommerce-multi-vendor') . '</button>';
                break;
            case 'vendor-shipping':
                echo '<ul>';
                echo '<li>' . __('Store Settings ', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '<li class="next"> < </li>';
                echo '<li>' . __('Shipping', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                echo '<button class="wcmp_ass_btn edit_shipping">' . __('Edit', 'dc-woocommerce-multi-vendor') . '</button>';
                break;
            case 'vendor-report':
                echo '<ul>';
                echo '<li>' . __('Stats & Reports', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '<li class="next"> > </li>';
                echo '<li>' . __('Overview', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                break;
            case 'vendor-orders':
                echo '<ul>';
                echo '<li>' . __('Order &amp; Shipping', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                break;
            case 'vendor-withdrawal':
                echo '<ul>';
                echo '<li>' . __('Payments', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '<li class="next"> > </li>';
                echo '<li>' . __('Withdrawals', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                break;
            case 'transaction-details':
                echo '<ul>';
                echo '<li>' . __('Payments', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '<li class="next"> > </li>';
                echo '<li>' . __('History', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                break;
            case 'vendor-knowledgebase':
                echo '<ul>';
                echo '<li>' . __('Knowledgebase', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                break;
            case 'vendor-announcements':
                echo '<ul>';
                echo '<li>' . __('Announcements', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                break;
            case '':
                echo '<ul>';
                echo '<li>' . __('Dashboard', 'dc-woocommerce-multi-vendor') . '</li>';
                echo '</ul>';
                echo '<span>' . Date('d M Y') . '</span>';
                break;
            default :

                break;
        }
    }

}
