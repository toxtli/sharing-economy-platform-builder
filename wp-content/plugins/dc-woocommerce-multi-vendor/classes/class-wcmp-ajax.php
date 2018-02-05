<?php

/**
 * WCMp Ajax Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Ajax {

    public function __construct() {
        //$general_singleproductmultisellersettings = get_option('wcmp_general_singleproductmultiseller_settings_name');
        add_action('wp_ajax_woocommerce_json_search_vendors', array(&$this, 'woocommerce_json_search_vendors'));
        add_action('wp_ajax_activate_pending_vendor', array(&$this, 'activate_pending_vendor'));
        add_action('wp_ajax_reject_pending_vendor', array(&$this, 'reject_pending_vendor'));
        add_action('wp_ajax_send_report_abuse', array(&$this, 'send_report_abuse'));
        add_action('wp_ajax_nopriv_send_report_abuse', array(&$this, 'send_report_abuse'));
        add_action('wp_ajax_dismiss_vendor_to_do_list', array(&$this, 'dismiss_vendor_to_do_list'));
        add_action('wp_ajax_get_more_orders', array(&$this, 'get_more_orders'));
        add_action('wp_ajax_withdrawal_more_orders', array(&$this, 'withdrawal_more_orders'));
        add_action('wp_ajax_show_more_transaction', array(&$this, 'show_more_transaction'));
        add_action('wp_ajax_nopriv_get_more_orders', array(&$this, 'get_more_orders'));
        add_action('wp_ajax_order_mark_as_shipped', array(&$this, 'order_mark_as_shipped'));
        add_action('wp_ajax_nopriv_order_mark_as_shipped', array(&$this, 'order_mark_as_shipped'));
        add_action('wp_ajax_transaction_done_button', array(&$this, 'transaction_done_button'));
        add_action('wp_ajax_wcmp_vendor_csv_download_per_order', array(&$this, 'wcmp_vendor_csv_download_per_order'));
        add_filter('ajax_query_attachments_args', array(&$this, 'show_current_user_attachments'), 10, 1);
        add_filter('wp_ajax_vendor_report_sort', array($this, 'vendor_report_sort'));
        add_filter('wp_ajax_vendor_search', array($this, 'search_vendor_data'));
        add_filter('wp_ajax_product_report_sort', array($this, 'product_report_sort'));
        add_filter('wp_ajax_product_search', array($this, 'search_product_data'));
        // woocommerce product enquiry form support
        if (WC_Dependencies_Product_Vendor::woocommerce_product_enquiry_form_active_check()) {
            add_filter('product_enquiry_send_to', array($this, 'send_enquiry_to_vendor'), 10, 2);
        }

        // Unsign vendor from product
        add_action('wp_ajax_unassign_vendor', array($this, 'unassign_vendor'));
        add_action('wp_ajax_wcmp_frontend_sale_get_row', array(&$this, 'wcmp_frontend_sale_get_row_callback'));
        add_action('wp_ajax_nopriv_wcmp_frontend_sale_get_row', array(&$this, 'wcmp_frontend_sale_get_row_callback'));
        add_action('wp_ajax_wcmp_frontend_pending_shipping_get_row', array(&$this, 'wcmp_frontend_pending_shipping_get_row_callback'));
        add_action('wp_ajax_nopriv_wcmp_frontend_pending_shipping_get_row', array(&$this, 'wcmp_frontend_pending_shipping_get_row_callback'));

        add_action('wp_ajax_wcmp_vendor_announcements_operation', array($this, 'wcmp_vendor_messages_operation'));
        add_action('wp_ajax_nopriv_wcmp_vendor_announcements_operation', array($this, 'wcmp_vendor_messages_operation'));
        add_action('wp_ajax_wcmp_announcements_refresh_tab_data', array($this, 'wcmp_msg_refresh_tab_data'));
        add_action('wp_ajax_nopriv_wcmp_announcements_refresh_tab_data', array($this, 'wcmp_msg_refresh_tab_data'));
        add_action('wp_ajax_wcmp_dismiss_dashboard_announcements', array($this, 'wcmp_dismiss_dashboard_message'));
        add_action('wp_ajax_nopriv_wcmp_dismiss_dashboard_announcements', array($this, 'wcmp_dismiss_dashboard_message'));

        if (get_wcmp_vendor_settings('is_singleproductmultiseller', 'general') == 'Enable') {
            // Product auto suggestion
            add_action('wp_ajax_wcmp_auto_search_product', array($this, 'wcmp_auto_suggesion_product'));
            add_action('wp_ajax_nopriv_wcmp_auto_search_product', array($this, 'wcmp_auto_suggesion_product'));
            // Product duplicate
            add_action('wp_ajax_wcmp_copy_to_new_draft', array($this, 'wcmp_copy_to_new_draft'));
            add_action('wp_ajax_nopriv_wcmp_copy_to_new_draft', array($this, 'wcmp_copy_to_new_draft'));
            add_action('wp_ajax_get_loadmorebutton_single_product_multiple_vendors', array($this, 'wcmp_get_loadmorebutton_single_product_multiple_vendors'));
            add_action('wp_ajax_nopriv_get_loadmorebutton_single_product_multiple_vendors', array($this, 'wcmp_get_loadmorebutton_single_product_multiple_vendors'));
            add_action('wp_ajax_single_product_multiple_vendors_sorting', array($this, 'single_product_multiple_vendors_sorting'));
            add_action('wp_ajax_nopriv_single_product_multiple_vendors_sorting', array($this, 'single_product_multiple_vendors_sorting'));
        }
        add_action('wp_ajax_wcmp_add_review_rating_vendor', array($this, 'wcmp_add_review_rating_vendor'));
        add_action('wp_ajax_nopriv_wcmp_add_review_rating_vendor', array($this, 'wcmp_add_review_rating_vendor'));
        // load more vendor review
        add_action('wp_ajax_wcmp_load_more_review_rating_vendor', array($this, 'wcmp_load_more_review_rating_vendor'));
        add_action('wp_ajax_nopriv_wcmp_load_more_review_rating_vendor', array($this, 'wcmp_load_more_review_rating_vendor'));

        add_action('wp_ajax_wcmp_save_vendor_registration_form', array(&$this, 'wcmp_save_vendor_registration_form_callback'));
        
        add_action('wp_ajax_dismiss_wcmp_servive_notice', array(&$this, 'dismiss_wcmp_servive_notice'));
        // search filter vendors from widget
        add_action('wp_ajax_vendor_list_by_search_keyword', array($this, 'vendor_list_by_search_keyword'));
        add_action('wp_ajax_nopriv_vendor_list_by_search_keyword', array($this, 'vendor_list_by_search_keyword'));
    }

    public function wcmp_save_vendor_registration_form_callback() {
        $form_data = json_decode(stripslashes_deep($_REQUEST['form_data']), true);
        if (!empty($form_data) && is_array($form_data)) {
            foreach ($form_data as $key => $value) {
                $form_data[$key]['hidden'] = true;
            }
        }

        update_option('wcmp_vendor_registration_form_data', $form_data);
        die;
    }

    function single_product_multiple_vendors_sorting() {
        global $WCMp;
        $sorting_value = $_POST['sorting_value'];
        $attrid = $_POST['attrid'];
        $more_products = $WCMp->product->get_multiple_vendors_array_for_single_product($attrid);
        $more_product_array = $more_products['more_product_array'];
        $results = $more_products['results'];
        $WCMp->template->get_template('single-product/multiple_vendors_products_body.php', array('more_product_array' => $more_product_array, 'sorting' => $sorting_value));
        die;
    }

    function wcmp_get_loadmorebutton_single_product_multiple_vendors() {
        global $WCMp;
        $WCMp->template->get_template('single-product/load-more-button.php');
        die;
    }

    function wcmp_load_more_review_rating_vendor() {
        global $WCMp, $wpdb;

        if (!empty($_POST['pageno']) && !empty($_POST['term_id'])) {
            $vendor = get_wcmp_vendor_by_term($_POST['term_id']);
            $vendor_id = $vendor->id;
            $offset = $_POST['postperpage'] * $_POST['pageno'];
            $reviews_lists = $vendor->get_reviews_and_rating($offset);
            $WCMp->template->get_template('review/wcmp-vendor-review.php', array('reviews_lists' => $reviews_lists, 'vendor_term_id' => $_POST['term_id']));
        }
        die;
    }

    function wcmp_add_review_rating_vendor() {
        global $WCMp, $wpdb;
        $review = $_POST['comment'];
        $rating = $_POST['rating'];
        $vendor_id = $_POST['vendor_id'];
        $current_user = wp_get_current_user();
        $comment_approve_by_settings = get_option('comment_moderation') ? 0 : 1;
        if (!empty($review) && !empty($rating)) {
            $time = current_time('mysql');
            if ($current_user->ID > 0) {
                $data = array(
                    'comment_post_ID' => wcmp_vendor_dashboard_page_id(),
                    'comment_author' => $current_user->display_name,
                    'comment_author_email' => $current_user->user_email,
                    'comment_author_url' => $current_user->user_url,
                    'comment_content' => $review,
                    'comment_type' => 'wcmp_vendor_rating',
                    'comment_parent' => 0,
                    'user_id' => $current_user->ID,
                    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'comment_date' => $time,
                    'comment_approved' => $comment_approve_by_settings,
                );
                $comment_id = wp_insert_comment($data);
                if ($comment_id) {
                    $meta_key = 'vendor_rating';
                    $meta_value = $rating;
                    $is_updated = update_comment_meta($comment_id, $meta_key, $meta_value);
                    $is_updated = update_comment_meta($comment_id, 'vendor_rating_id', $vendor_id);
                    if ($is_updated) {
                        echo 1;
                    }
                }
            }
        } else {
            echo 0;
        }
        die;
    }

    function wcmp_copy_to_new_draft() {
        $post_id = $_POST['postid'];
        $post = get_post($post_id);
        echo wp_nonce_url(admin_url('edit.php?post_type=product&action=duplicate_product&post=' . $post->ID), 'woocommerce-duplicate-product_' . $post->ID);
        die;
    }

    function wcmp_auto_suggesion_product() {
        global $WCMp, $wpdb;
        $searchstr = $_POST['protitle'];
        $querystr = "select DISTINCT post_title, ID from {$wpdb->prefix}posts where post_title like '{$searchstr}%' and post_status = 'publish' and post_type = 'product' GROUP BY post_title order by post_title  LIMIT 0,10";
        $results = $wpdb->get_results($querystr);
        if (count($results) > 0) {
            echo "<ul>";
            foreach ($results as $result) {
                echo "<li data-element='{$result->ID}'><a href='" . wp_nonce_url(admin_url('edit.php?post_type=product&action=duplicate_product&singleproductmultiseller=1&post=' . $result->ID), 'woocommerce-duplicate-product_' . $result->ID) . "'>{$result->post_title}</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<div>" . __('No Suggestion found', 'dc-woocommerce-multi-vendor') . "</div>";
        }
        die;
    }

    public function wcmp_dismiss_dashboard_message() {
        global $wpdb, $WCMp;
        $post_id = $_POST['post_id'];
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        $data_msg_deleted = get_user_meta($current_user_id, '_wcmp_vendor_message_deleted', true);
        if (!empty($data_msg_deleted)) {
            $data_arr = explode(',', $data_msg_deleted);
            $data_arr[] = $post_id;
            $data_str = implode(',', $data_arr);
        } else {
            $data_arr[] = $post_id;
            $data_str = implode(',', $data_arr);
        }
        $is_updated = update_user_meta($current_user_id, '_wcmp_vendor_message_deleted', $data_str);
        if ($is_updated) {
            $dismiss_notices_ids_array = array();
            $dismiss_notices_ids = get_user_meta($current_user_id, '_wcmp_vendor_message_deleted', true);
            if (!empty($dismiss_notices_ids)) {
                $dismiss_notices_ids_array = explode(',', $dismiss_notices_ids);
            } else {
                $dismiss_notices_ids_array = array();
            }
            $args_msg = array(
                'posts_per_page' => 1,
                'offset' => 0,
                'post__not_in' => $dismiss_notices_ids_array,
                'orderby' => 'date',
                'order' => 'DESC',
                'post_type' => 'wcmp_vendor_notice',
                'post_status' => 'publish',
                'suppress_filters' => true
            );
            $msgs_array = get_posts($args_msg);
            if (is_array($msgs_array) && !empty($msgs_array) && count($msgs_array) > 0) {
                $msg = $msgs_array[0];
                ?>
                <h2><?php echo __('Admin Message:', 'dc-woocommerce-multi-vendor'); ?> </h2>
                <span> <?php echo $msg->post_title; ?> </span><br/>
                <span class="mormaltext" style="font-weight:normal;"> <?php
                    echo $short_content = substr(stripslashes(strip_tags($msg->post_content)), 0, 155);
                    if (strlen(stripslashes(strip_tags($msg->post_content))) > 155) {
                        echo '...';
                    }
                    ?> </span><br/>
                <a href="<?php echo get_permalink(get_option('wcmp_product_vendor_messages_page_id')); ?>"><button><?php echo __('DETAILS', 'dc-woocommerce-multi-vendor'); ?></button></a>
                <div class="clear"></div>
                <a href="#" id="cross-admin" data-element = "<?php echo $msg->ID; ?>"  class="wcmp_cross wcmp_delate_message_dashboard"><i class="fa fa-times-circle"></i></a>
                    <?php
                } else {
                    ?>
                <h2><?php echo __('No Messages Found:', 'dc-woocommerce-multi-vendor'); ?> </h2>
                <?php
            }
        } else {
            ?>
            <h2><?php echo __('Error in process:', 'dc-woocommerce-multi-vendor'); ?> </h2>
            <?php
        }
        die;
    }

    public function wcmp_msg_refresh_tab_data() {
        global $wpdb, $WCMp;
        $tab = $_POST['tabname'];
        $WCMp->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements' . str_replace("_","-",$tab) . '.php');
        die;
    }

    public function wcmp_vendor_messages_operation() {
        global $wpdb, $WCMp;
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        $post_id = $_POST['msg_id'];
        $actionmode = $_POST['actionmode'];
        if ($actionmode == "mark_delete") {
            $data_msg_deleted = get_user_meta($current_user_id, '_wcmp_vendor_message_deleted', true);
            if (!empty($data_msg_deleted)) {
                $data_arr = explode(',', $data_msg_deleted);
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            } else {
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_wcmp_vendor_message_deleted', $data_str)) {
                echo 1;
            } else {
                echo 0;
            }
        } elseif ($actionmode == "mark_read") {
            $data_msg_readed = get_user_meta($current_user_id, '_wcmp_vendor_message_readed', true);
            if (!empty($data_msg_readed)) {
                $data_arr = explode(',', $data_msg_readed);
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            } else {
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_wcmp_vendor_message_readed', $data_str)) {
                echo __('Mark Unread', 'dc-woocommerce-multi-vendor');
            } else {
                echo 0;
            }
        } elseif ($actionmode == "mark_unread") {
            $data_msg_readed = get_user_meta($current_user_id, '_wcmp_vendor_message_readed', true);
            if (!empty($data_msg_readed)) {
                $data_arr = explode(',', $data_msg_readed);
                if (is_array($data_arr)) {
                    if (($key = array_search($post_id, $data_arr)) !== false) {
                        unset($data_arr[$key]);
                    }
                }
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_wcmp_vendor_message_readed', $data_str)) {
                echo __('Mark Read', 'dc-woocommerce-multi-vendor');
            } else {
                echo 0;
            }
        } elseif ($actionmode == "mark_restore") {
            $data_msg_deleted = get_user_meta($current_user_id, '_wcmp_vendor_message_deleted', true);
            if (!empty($data_msg_deleted)) {
                $data_arr = explode(',', $data_msg_deleted);
                if (is_array($data_arr)) {
                    if (($key = array_search($post_id, $data_arr)) !== false) {
                        unset($data_arr[$key]);
                    }
                }
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_wcmp_vendor_message_deleted', $data_str)) {
                echo __('Mark Restore', 'dc-woocommerce-multi-vendor');
            } else {
                echo 0;
            }
        }
        die;
    }

    public function wcmp_frontend_sale_get_row_callback() {
        global $wpdb, $WCMp;
        $user = wp_get_current_user();
        $vendor = get_wcmp_vendor($user->ID);
        $today_or_weekly = $_POST['today_or_weekly'];
        $current_page = $_POST['current_page'];
        $next_page = $_POST['next_page'];
        $total_page = $_POST['total_page'];
        $perpagedata = $_POST['perpagedata'];
        if ($next_page <= $total_page) {
            if ($next_page > 1) {
                $start = ($next_page - 1) * $perpagedata;
                $WCMp->template->get_template('vendor-dashboard/dashboard/vendor-dashboard-sales-item.php', array('vendor' => $vendor, 'today_or_weekly' => $today_or_weekly, 'start' => $start, 'to' => $perpagedata));
            }
        } else {
            echo "<tr><td colspan='5'>" . __('no more data found', 'dc-woocommerce-multi-vendor') . "</td></tr>";
        }
        die;
    }

    public function wcmp_frontend_pending_shipping_get_row_callback() {
        global $wpdb, $WCMp;
        $user = wp_get_current_user();
        $vendor = get_wcmp_vendor($user->ID);
        $today_or_weekly = $_POST['today_or_weekly'];
        $current_page = $_POST['current_page'];
        $next_page = $_POST['next_page'];
        $total_page = $_POST['total_page'];
        $perpagedata = $_POST['perpagedata'];
        if ($next_page <= $total_page) {
            if ($next_page > 1) {
                $start = ($next_page - 1) * $perpagedata;
                $WCMp->template->get_template('vendor-dashboard/dashboard/vendor-dasboard-pending-shipping-items.php', array('vendor' => $vendor, 'today_or_weekly' => $today_or_weekly, 'start' => $start, 'to' => $perpagedata));
            }
        } else {
            echo "<tr><td colspan='5'>" . __('no more data found', 'dc-woocommerce-multi-vendor') . "</td></tr>";
        }
        die;
    }

    function show_more_transaction() {
        global $WCMp;
        $data_to_show = $_POST['data_to_show'];
        $WCMp->template->get_template('vendor-dashboard/vendor-transactions/vendor-transaction-items.php', array('transactions' => $data_to_show));
        die;
    }

    function withdrawal_more_orders() {
        global $WCMp;
        $user = wp_get_current_user();
        $vendor = get_wcmp_vendor($user->ID);
        $offset = $_POST['offset'];
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
        $customer_orders = $vendor->get_orders(6, $offset, $meta_query);
        $WCMp->template->get_template('vendor-dashboard/vendor-withdrawal/vendor-withdrawal-items.php', array('vendor' => $vendor, 'commissions' => $customer_orders));
        die;
    }

    function wcmp_vendor_csv_download_per_order() {
        global $WCMp, $wpdb;

        if (isset($_GET['action']) && isset($_GET['order_id']) && isset($_GET['nonce'])) {
            $action = $_GET['action'];
            $order_id = $_GET['order_id'];
            $nonce = $_REQUEST["nonce"];

            if (!wp_verify_nonce($nonce, $action))
                die('Invalid request');

            $vendor = get_wcmp_vendor(get_current_vendor_id());
            $vendor = apply_filters('wcmp_csv_download_per_order_vendor', $vendor);
            if (!$vendor)
                die('Invalid request');
            $order_data = array();
            $customer_orders = $wpdb->get_results("SELECT DISTINCT commission_id from `{$wpdb->prefix}wcmp_vendor_orders` where vendor_id = " . $vendor->id . " AND order_id = " . $order_id, ARRAY_A);
            if (!empty($customer_orders)) {
                $commission_id = $customer_orders[0]['commission_id'];
                $order_data[$commission_id] = $order_id;
                $WCMp->vendor_dashboard->generate_csv($order_data, $vendor);
            }
            die;
        }
    }

    /**
     * Unassign vendor from a product
     */
    function unassign_vendor() {
        global $WCMp;

        $product_id = $_POST['product_id'];
        $vendor = get_wcmp_product_vendors($product_id);
        $admin_id = get_current_user_id();

        $_product = wc_get_product($product_id);
        $orders = array();
        if ($_product->is_type('variable')) {
            $get_children = $_product->get_children();
            if (!empty($get_children)) {
                foreach ($get_children as $child) {
                    $orders = array_merge($orders, $vendor->get_vendor_orders_by_product($vendor->term_id, $child));
                }
                $orders = array_unique($orders);
            }
        } else {
            $orders = array_unique($vendor->get_vendor_orders_by_product($vendor->term_id, $product_id));
        }

        foreach ($orders as $order_id) {
            $order = new WC_Order($order_id);
            $items = $order->get_items('line_item');
            foreach ($items as $item_id => $item) {
                wc_add_order_item_meta($item_id, '_vendor_id', $vendor->id);
            }
        }

        wp_delete_object_term_relationships($product_id, 'dc_vendor_shop');
        wp_delete_object_term_relationships($product_id, 'product_shipping_class');
        wp_update_post(array('ID' => $product_id, 'post_author' => $admin_id));
        delete_post_meta($product_id, '_commission_per_product');
        delete_post_meta($product_id, '_commission_percentage_per_product');
        delete_post_meta($product_id, '_commission_fixed_with_percentage_qty');
        delete_post_meta($product_id, '_commission_fixed_with_percentage');

        $product_obj = wc_get_product($product_id);
        if ($product_obj->is_type('variable')) {
            $child_ids = $product_obj->get_children();
            if (isset($child_ids) && !empty($child_ids)) {
                foreach ($child_ids as $child_id) {
                    delete_post_meta($child_id, '_commission_fixed_with_percentage');
                    delete_post_meta($child_id, '_product_vendors_commission_percentage');
                    delete_post_meta($child_id, '_product_vendors_commission_fixed_per_trans');
                    delete_post_meta($child_id, '_product_vendors_commission_fixed_per_qty');
                }
            }
        }

        die;
    }

    /**
     * WCMp Product Report sorting
     */
    function product_report_sort() {
        global $WCMp;

        $sort_choosen = isset($_POST['sort_choosen']) ? $_POST['sort_choosen'] : '';
        $report_array = isset($_POST['report_array']) ? $_POST['report_array'] : array();
        $report_bk = isset($_POST['report_bk']) ? $_POST['report_bk'] : array();
        $max_total_sales = isset($_POST['max_total_sales']) ? $_POST['max_total_sales'] : 0;
        $total_sales_sort = isset($_POST['total_sales_sort']) ? $_POST['total_sales_sort'] : array();
        $admin_earning_sort = isset($_POST['admin_earning_sort']) ? $_POST['admin_earning_sort'] : array();
        ;

        $i = 0;
        $max_value = 10;
        $report_sort_arr = array();

        if ($sort_choosen == 'total_sales_desc') {
            arsort($total_sales_sort);
            foreach ($total_sales_sort as $product_id => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
                    $report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
                }
            }
        } else if ($sort_choosen == 'total_sales_asc') {
            asort($total_sales_sort);
            foreach ($total_sales_sort as $product_id => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
                    $report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
                }
            }
        } else if ($sort_choosen == 'admin_earning_desc') {
            arsort($admin_earning_sort);
            foreach ($admin_earning_sort as $product_id => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
                    $report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
                }
            }
        } else if ($sort_choosen == 'admin_earning_asc') {
            asort($admin_earning_sort);
            foreach ($admin_earning_sort as $product_id => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
                    $report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
                }
            }
        }

        $report_chart = $report_html = '';

        if (sizeof($report_sort_arr) > 0) {
            foreach ($report_sort_arr as $product_id => $sales_report) {
                $width = ( $sales_report['total_sales'] > 0 ) ? ( round($sales_report['total_sales']) / round($max_total_sales) ) * 100 : 0;
                $width2 = ( $sales_report['admin_earning'] > 0 ) ? ( round($sales_report['admin_earning']) / round($max_total_sales) ) * 100 : 0;

                $product = new WC_Product($product_id);
                $product_url = admin_url('post.php?post=' . $product_id . '&action=edit');

                $report_chart .= '<tr><th><a href="' . $product_url . '">' . $product->get_title() . '</a></th>
					<td width="1%"><span>' . wc_price($sales_report['total_sales']) . '</span><span class="alt">' . wc_price($sales_report['admin_earning']) . '</span></td>
					<td class="bars">
						<span style="width:' . esc_attr($width) . '%">&nbsp;</span>
						<span class="alt" style="width:' . esc_attr($width2) . '%">&nbsp;</span>
					</td></tr>';
            }

            $report_html = '
				<h4>' . __("Sales and Earnings", 'dc-woocommerce-multi-vendor') . '</h4>
				<div class="bar_indecator">
					<div class="bar1">&nbsp;</div>
					<span class="">' . __("Gross Sales", 'dc-woocommerce-multi-vendor') . '</span>
					<div class="bar2">&nbsp;</div>
					<span class="">' . __("My Earnings", 'dc-woocommerce-multi-vendor') . '</span>
				</div>
				<table class="bar_chart">
					<thead>
						<tr>
							<th>' . __("Month", 'dc-woocommerce-multi-vendor') . '</th>
							<th colspan="2">' . __("Sales Report", 'dc-woocommerce-multi-vendor') . '</th>
						</tr>
					</thead>
					<tbody>
						' . $report_chart . '
					</tbody>
				</table>
			';
        } else {
            $report_html = '<tr><td colspan="3">' . __('No product was sold in the given period.', 'dc-woocommerce-multi-vendor') . '</td></tr>';
        }

        echo $report_html;

        die;
    }

    function send_enquiry_to_vendor($send_to, $product_id) {
        global $WCMp;
        $vendor = get_wcmp_product_vendors($product_id);
        if ($vendor) {
            $send_to = $vendor->user_data->data->user_email;
        }
        return $send_to;
    }

    /**
     * WCMp Product Data Searching
     */
    function search_product_data() {
        global $WCMp;

        $product_id = $_POST['product_id'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        $report_chart = $report_html = '';

        if ($product_id) {

            $total_sales = $admin_earnings = array();
            $max_total_sales = 0;

            $product_orders = get_wcmp_vendor_orders(array('product_id' => $product_id));

            if (!empty($product_orders)) {

                $gross_sales = $my_earning = $vendor_earning = 0;
                foreach ($product_orders as $order_obj) {
                    $order = new WC_Order($order_obj->order_id);

                    if (strtotime($order->get_date_created()) > $start_date && strtotime($order->get_date_created()) < $end_date) {
                        // Get date
                        $date = date('Ym', strtotime($order->get_date_created()));

                        $item = new WC_Order_Item_Product($order_obj->order_item_id);
                        $gross_sales += $item->get_subtotal();
                        $total_sales[$date] = isset($total_sales[$date]) ? ( $total_sales[$date] + $item->get_subtotal() ) : $item->get_subtotal();
                        $vendors_orders_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id(), 'product_id' => $order_obj->product_id));

                        $vendor_earning = $vendors_orders_amount['commission_amount'];
                        if ($vendor = get_wcmp_vendor(get_current_vendor_id()))
                            $admin_earnings[$date] = isset($admin_earnings[$date]) ? ( $admin_earnings[$date] + $vendor_earning ) : $vendor_earning;
                        else
                            $admin_earnings[$date] = isset($admin_earnings[$date]) ? ( $admin_earnings[$date] + $item->get_subtotal() - $vendor_earning ) : $item->get_subtotal() - $vendor_earning;

                        if ($total_sales[$date] > $max_total_sales)
                            $max_total_sales = $total_sales[$date];
                    }
                }
            }


            if (sizeof($total_sales) > 0) {
                foreach ($total_sales as $date => $sales) {
                    $width = ( $sales > 0 ) ? ( round($sales) / round($max_total_sales) ) * 100 : 0;
                    $width2 = ( $admin_earnings[$date] > 0 ) ? ( round($admin_earnings[$date]) / round($max_total_sales) ) * 100 : 0;

                    $report_chart .= '<tr><th>' . date_i18n('F', strtotime($date . '01')) . '</th>
						<td width="1%"><span>' . wc_price($sales) . '</span><span class="alt">' . wc_price($admin_earnings[$date]) . '</span></td>
						<td class="bars">
							<span style="width:' . esc_attr($width) . '%">&nbsp;</span>
							<span class="alt" style="width:' . esc_attr($width2) . '%">&nbsp;</span>
						</td></tr>';
                }

                $report_html = '
					<h4>' . __("Sales and Earnings", 'dc-woocommerce-multi-vendor') . '</h4>
					<div class="bar_indecator">
						<div class="bar1">&nbsp;</div>
						<span class="">' . __("Gross Sales", 'dc-woocommerce-multi-vendor') . '</span>
						<div class="bar2">&nbsp;</div>
						<span class="">' . __("My Earnings", 'dc-woocommerce-multi-vendor') . '</span>
					</div>
					<table class="bar_chart">
						<thead>
							<tr>
								<th>' . __("Month", 'dc-woocommerce-multi-vendor') . '</th>
								<th colspan="2">' . __("Sales Report", 'dc-woocommerce-multi-vendor') . '</th>
							</tr>
						</thead>
						<tbody>
							' . $report_chart . '
						</tbody>
					</table>
				';
            } else {
                $report_html = '<tr><td colspan="3">' . __('This product was not sold in the given period.', 'dc-woocommerce-multi-vendor') . '</td></tr>';
            }

            echo $report_html;
        } else {
            echo '<tr><td colspan="3">' . __('Please select a product.', 'dc-woocommerce-multi-vendor') . '</td></tr>';
        }

        die;
    }

    /**
     * WCMp Vendor Data Searching
     */
    function search_vendor_data() {
        global $WCMp, $wpdb;

        $chosen_product_ids = $vendor_id = $vendor = false;
        $gross_sales = $my_earning = $vendor_earning = 0;
        $vendor_term_id = $_POST['vendor_id'];
        $vendor = get_wcmp_vendor_by_term($vendor_term_id);
        $vendor_id = $vendor->id;
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        if ($vendor_id) {
            if ($vendor)
                $products = $vendor->get_products();
            if (!empty($products)) {
                foreach ($products as $product) {
                    $chosen_product_ids[] = $product->ID;
                }
            }
        }

        if ($vendor_id && empty($products)) {
            $no_vendor = '<h4>' . __("Sales and Earnings", 'dc-woocommerce-multi-vendor') . '</h4>
			<table class="bar_chart">
				<thead>
					<tr>
						<th>' . __("Month", 'dc-woocommerce-multi-vendor') . '</th>
						<th colspan="2">' . __("Sales", 'dc-woocommerce-multi-vendor') . '</th>
					</tr>
				</thead>
				<tbody> 
					<tr><td colspan="3">' . __("No Sales :(", 'dc-woocommerce-multi-vendor') . '</td></tr>
				</tbody>
			</table>';

            echo $no_vendor;
            die;
        }

        $args = array(
            'post_type' => 'shop_order',
            'posts_per_page' => -1,
            'post_status' => array('wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed'),
            'meta_query' => array(
                array(
                    'key' => '_commissions_processed',
                    'value' => 'yes',
                    'compare' => '='
                )
            ),
            'date_query' => array(
                'inclusive' => true,
                'after' => array(
                    'year' => date('Y', $start_date),
                    'month' => date('n', $start_date),
                    'day' => date('j', $start_date),
                ),
                'before' => array(
                    'year' => date('Y', $end_date),
                    'month' => date('n', $end_date),
                    'day' => date('j', $end_date),
                ),
            )
        );

        $qry = new WP_Query($args);

        $orders = apply_filters('wcmp_filter_orders_report_vendor', $qry->get_posts());

        if (!empty($orders)) {

            $total_sales = $admin_earning = array();
            $max_total_sales = 0;

            foreach ($orders as $order_obj) {
                $order = new WC_Order($order_obj->ID);
                $vendors_orders = get_wcmp_vendor_orders(array('order_id' => $order->get_id()));
                $vendors_orders_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id()), $vendor_id);
                $current_vendor_orders = wp_list_filter($vendors_orders, array('vendor_id' => $vendor_id));
                $gross_sales += $vendors_orders_amount['total'] - $vendors_orders_amount['commission_amount'];
                $vendor_earning += $vendors_orders_amount['total'];

                foreach ($current_vendor_orders as $key => $vendor_order) {
                    $item = new WC_Order_Item_Product($vendor_order->order_item_id);
                    $gross_sales += $item->get_subtotal();
                }
                // Get date
                $date = date('Ym', strtotime($order->get_date_created()));

                // Set values
                $total_sales[$date] = $gross_sales;
                $admin_earning[$date] = $gross_sales - $vendor_earning;

                if ($total_sales[$date] > $max_total_sales)
                    $max_total_sales = $total_sales[$date];
            }

            $report_chart = $report_html = '';
            if (count($total_sales) > 0) {
                foreach ($total_sales as $date => $sales) {
                    $width = ( $sales > 0 ) ? ( round($sales) / round($max_total_sales) ) * 100 : 0;
                    $width2 = ( $admin_earning[$date] > 0 ) ? ( round($admin_earning[$date]) / round($max_total_sales) ) * 100 : 0;

                    $orders_link = admin_url('edit.php?s&post_status=all&post_type=shop_order&action=-1&s=' . urlencode(implode(' ', $chosen_product_titles)) . '&m=' . date('Ym', strtotime($date . '01')) . '&shop_order_status=' . implode(",", apply_filters('woocommerce_reports_order_statuses', array('completed', 'processing', 'on-hold'))));
                    $orders_link = apply_filters('woocommerce_reports_order_link', $orders_link, $chosen_product_ids, $chosen_product_titles);

                    $report_chart .= '<tr><th><a href="' . esc_url($orders_link) . '">' . date_i18n('F', strtotime($date . '01')) . '</a></th>
						<td width="1%"><span>' . wc_price($sales) . '</span><span class="alt">' . wc_price($admin_earning[$date]) . '</span></td>
						<td class="bars">
							<span class="main" style="width:' . esc_attr($width) . '%">&nbsp;</span>
							<span class="alt" style="width:' . esc_attr($width2) . '%">&nbsp;</span>
						</td></tr>';
                }

                $report_html = '
					<h4>' . $vendor_title . '</h4>
					<div class="bar_indecator">
						<div class="bar1">&nbsp;</div>
						<span class="">' . __("Gross Sales", 'dc-woocommerce-multi-vendor') . '</span>
						<div class="bar2">&nbsp;</div>
						<span class="">' . __("My Earnings", 'dc-woocommerce-multi-vendor') . '</span>
					</div>
					<table class="bar_chart">
						<thead>
							<tr>
								<th>' . __("Month", 'dc-woocommerce-multi-vendor') . '</th>
								<th colspan="2">' . __("Vendor Earnings", 'dc-woocommerce-multi-vendor') . '</th>
							</tr>
						</thead>
						<tbody>
							' . $report_chart . '
						</tbody>
					</table>
				';
            } else {
                $report_html = '<tr><td colspan="3">' . __('This vendor did not generate any sales in the given period.', 'dc-woocommerce-multi-vendor') . '</td></tr>';
            }
        }

        echo $report_html;

        die;
    }

    /**
     * WCMp Vendor Report sorting
     */
    function vendor_report_sort() {
        global $WCMp;

        $dropdown_selected = isset($_POST['sort_choosen']) ? $_POST['sort_choosen'] : '';
        $vendor_report = isset($_POST['report_array']) ? $_POST['report_array'] : array();
        $report_bk = isset($_POST['report_bk']) ? $_POST['report_bk'] : array();
        $max_total_sales = isset($_POST['max_total_sales']) ? $_POST['max_total_sales'] : 0;
        $total_sales_sort = isset($_POST['total_sales_sort']) ? $_POST['total_sales_sort'] : array();
        $admin_earning_sort = isset($_POST['admin_earning_sort']) ? $_POST['admin_earning_sort'] : array();
        $report_sort_arr = array();
        $chart_arr = '';
        $i = 0;
        $max_value = 10;

        if ($dropdown_selected == 'total_sales_desc') {
            arsort($total_sales_sort);
            foreach ($total_sales_sort as $key => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
                    $report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
                }
            }
        } else if ($dropdown_selected == 'total_sales_asc') {
            asort($total_sales_sort);
            foreach ($total_sales_sort as $key => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
                    $report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
                }
            }
        } else if ($dropdown_selected == 'admin_earning_desc') {
            arsort($admin_earning_sort);
            foreach ($admin_earning_sort as $key => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
                    $report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
                }
            }
        } else if ($dropdown_selected == 'admin_earning_asc') {
            asort($admin_earning_sort);
            foreach ($admin_earning_sort as $key => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
                    $report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
                }
            }
        }

        if (sizeof($report_sort_arr) > 0) {
            foreach ($report_sort_arr as $vendor_id => $sales_report) {
                $total_sales_width = ( $sales_report['total_sales'] > 0 ) ? $sales_report['total_sales'] / round($max_total_sales) * 100 : 0;
                $admin_earning_width = ( $sales_report['admin_earning'] > 0 ) ? ( $sales_report['admin_earning'] / round($max_total_sales) ) * 100 : 0;

                $user = get_userdata($vendor_id);
                $user_name = $user->data->display_name;

                $chart_arr .= '<tr><th><a href="user-edit.php?user_id=' . $vendor_id . '">' . $user_name . '</a></th>
				<td width="1%"><span>' . wc_price($sales_report['total_sales']) . '</span><span class="alt">' . wc_price($sales_report['admin_earning']) . '</span></td>
				<td class="bars">
					<span class="main" style="width:' . esc_attr($total_sales_width) . '%">&nbsp;</span>
					<span class="alt" style="width:' . esc_attr($admin_earning_width) . '%">&nbsp;</span>
				</td></tr>';
            }

            $html_chart = '
				<h4>' . __("Sales and Earnings", 'dc-woocommerce-multi-vendor') . '</h4>
				<div class="bar_indecator">
					<div class="bar1">&nbsp;</div>
					<span class="">' . __("Gross Sales", 'dc-woocommerce-multi-vendor') . '</span>
					<div class="bar2">&nbsp;</div>
					<span class="">' . __("My Earnings", 'dc-woocommerce-multi-vendor') . '</span>
				</div>
				<table class="bar_chart">
					<thead>
						<tr>
							<th>' . __("Vendors", 'dc-woocommerce-multi-vendor') . '</th>
							<th colspan="2">' . __("Sales Report", 'dc-woocommerce-multi-vendor') . '</th>
						</tr>
					</thead>
					<tbody>
						' . $chart_arr . '
					</tbody>
				</table>
			';
        } else {
            $html_chart = '<tr><td colspan="3">' . __('Any vendor did not generate any sales in the given period.', 'dc-woocommerce-multi-vendor') . '</td></tr>';
        }

        echo $html_chart;

        die;
    }

    /**
     * WCMp Order mark as shipped
     */
    function order_mark_as_shipped() {
        global $WCMp, $wpdb;
        $order_id = $_POST['order_id'];
        $tracking_url = $_POST['tracking_url'];
        $tracking_id = $_POST['tracking_id'];
        $user_id = get_current_vendor_id();
        $vendor = get_wcmp_vendor($user_id);
        $user_id = apply_filters('wcmp_mark_as_shipped_vendor', $user_id);
        $shippers = (array) get_post_meta($order_id, 'dc_pv_shipped', true);
        if (!in_array($user_id, $shippers)) {
            $shippers[] = $user_id;
            $mails = WC()->mailer()->emails['WC_Email_Notify_Shipped'];
            if (!empty($mails)) {
                $customer_email = get_post_meta($order_id, '_billing_email', true);
                $mails->trigger($order_id, $customer_email, $vendor->term_id, array('tracking_id' => $tracking_id, 'tracking_url' => $tracking_url));
            }
            do_action('wcmp_vendors_vendor_ship', $order_id, $vendor->term_id);
            array_push($shippers, $user_id);
            update_post_meta($order_id, 'dc_pv_shipped', $shippers);
        }
        $wpdb->query("UPDATE {$wpdb->prefix}wcmp_vendor_orders SET shipping_status = '1' WHERE order_id = $order_id and vendor_id = $user_id");
        $order = new WC_Order($order_id);
        $comment_id = $order->add_order_note('Vendor ' . $vendor->user_data->display_name . ' has shipped his part of order to customer. <br>Tracking Url : <a target="_blank" href="' . $tracking_url . '">' . $tracking_url . '</a><br> Tracking Id: ' . $tracking_id, '1', true);
        add_comment_meta($comment_id, '_vendor_id', $user_id);
        die;
    }

    /**
     * WCMp Transaction complete mark
     */
    function transaction_done_button() {
        global $WCMp;
        $transaction_id = $_POST['trans_id'];
        $vendor_id = $_POST['vendor_id'];
        update_post_meta($transaction_id, 'paid_date', date("Y-m-d H:i:s"));
        $commission_detail = get_post_meta($transaction_id, 'commission_detail', true);
        if ($commission_detail && is_array($commission_detail)) {
            foreach ($commission_detail as $commission_id) {
                wcmp_paid_commission_status($commission_id);
            }
            $email_admin = WC()->mailer()->emails['WC_Email_Vendor_Commission_Transactions'];
            $email_admin->trigger($transaction_id, $vendor_id);
            update_post_meta($transaction_id, '_dismiss_to_do_list', 'true');
            wp_update_post(array('ID' => $transaction_id, 'post_status' => 'wcmp_completed'));
        }
        die;
    }

    /**
     * WCMp get more orders
     */
    function get_more_orders() {
        global $WCMp;
        $data_to_show = isset($_POST['data_to_show']) ? $_POST['data_to_show'] : '';
        $order_status = isset($_POST['order_status']) ? $_POST['order_status'] : '';
        $vendor = get_wcmp_vendor(get_current_vendor_id());
        $WCMp->template->get_template('vendor-dashboard/vendor-orders/vendor-orders-item.php', array('vendor' => $vendor, 'orders' => $data_to_show, 'order_status' => $order_status));
        die;
    }

    /**
     * WCMp dismiss todo list
     */
    function dismiss_vendor_to_do_list() {
        global $WCMp;

        $id = $_POST['id'];
        $type = $_POST['type'];
        if ($type == 'user') {
            update_user_meta($id, '_dismiss_to_do_list', 'true');
        } else if ($type == 'shop_coupon') {
            update_post_meta($id, '_dismiss_to_do_list', 'true');
        } else if ($type == 'product') {
            update_post_meta($id, '_dismiss_to_do_list', 'true');
        } else if ($type == 'dc_commission') {
            update_post_meta($id, '_dismiss_to_do_list', 'true');
            wp_update_post(array('ID' => $id, 'post_status' => 'wcmp_canceled'));
        }
        die();
    }

    /**
     * WCMp current user attachment
     */
    function show_current_user_attachments($query = array()) {
        $user_id = get_current_vendor_id();
        if (is_user_wcmp_vendor($user_id)) {
            $query['author'] = $user_id;
        }
        return $query;
    }

    /**
     * Search vendors via AJAX
     *
     * @return void
     */
    function woocommerce_json_search_vendors() {
        global $WCMp;

        //check_ajax_referer( 'search-vendors', 'security' );

        header('Content-Type: application/json; charset=utf-8');

        $term = urldecode(stripslashes(strip_tags($_GET['term'])));

        if (empty($term))
            die();

        $found_vendors = array();

        $args = array(
            'search' => '*' . $term . '*',
            'search_columns' => array('user_login', 'display_name', 'user_email')
        );

        $vendors = get_wcmp_vendors($args);

        if (!empty($vendors) && is_array($vendors)) {
            foreach ($vendors as $vendor) {
                $found_vendors[$vendor->term_id] = $vendor->user_data->display_name;
            }
        }

        echo json_encode($found_vendors);
        die();
    }

    /**
     * Activate Pending Vendor via AJAX
     *
     * @return void
     */
    function activate_pending_vendor() {
        $user_id = filter_input(INPUT_POST, 'user_id');
        if($user_id){
            $user = new WP_User(absint($user_id));
            $user->set_role('dc_vendor');
            $user_dtl = get_userdata(absint($user_id));
            $email = WC()->mailer()->emails['WC_Email_Approved_New_Vendor_Account'];
            $email->trigger($user_id, $user_dtl->user_pass);
        }
        die();
    }

    /**
     * Reject Pending Vendor via AJAX
     *
     * @return void
     */
    function reject_pending_vendor() {
        $user_id = filter_input(INPUT_POST, 'user_id');
        if($user_id){
            $user = new WP_User(absint($user_id));
            $user->set_role('dc_rejected_vendor');
        }
        die();
    }

    /**
     * Report Abuse Vendor via AJAX
     *
     * @return void
     */
    function send_report_abuse() {
        global $WCMp;
        $check = false;
        $name = sanitize_text_field($_POST['name']);
        $from_email = sanitize_email($_POST['email']);
        $user_message = sanitize_text_field($_POST['msg']);
        $product_id = sanitize_text_field($_POST['product_id']);

        $check = !empty($name) && !empty($from_email) && !empty($user_message);

        if ($check) {
            $product = get_post(absint($product_id));
            $vendor = get_wcmp_product_vendors($product_id);

            $subject = __('Report an abuse for product', 'dc-woocommerce-multi-vendor') . get_the_title($product_id);

            $to = sanitize_email(get_option('admin_email'));
            $from_email = sanitize_email($from_email);
            $headers = "From: {$name} <{$from_email}>" . "\r\n";

            $message = sprintf(__("User %s (%s) is reporting an abuse on the following product: \n", 'dc-woocommerce-multi-vendor'), $name, $from_email);
            $message .= sprintf(__("Product details: %s (ID: #%s) \n", 'dc-woocommerce-multi-vendor'), $product->post_title, $product->ID);

            $message .= sprintf(__("Vendor shop: %s \n", 'dc-woocommerce-multi-vendor'), $vendor->user_data->display_name);

            $message .= sprintf(__("Message: %s\n", 'dc-woocommerce-multi-vendor'), $user_message);
            $message .= "\n\n\n";

            $message .= sprintf(__("Product page:: %s\n", 'dc-woocommerce-multi-vendor'), get_the_permalink($product->ID));

            /* === Send Mail === */
            $response = wp_mail($to, $subject, $message, $headers);
        }
        die();
    }
    /**
     * Set a flag while dismiss WCMp service notice
     */
    public function dismiss_wcmp_servive_notice(){
        $updated = update_option('_is_dismiss_service_notice', true);
        echo $updated;
        die();
    }

    function vendor_list_by_search_keyword() {
        global $WCMp;
        // check vendor_search_nonce
        if( !isset( $_POST['vendor_search_nonce'] ) || !wp_verify_nonce( $_POST['vendor_search_nonce'], 'wcmp_widget_vendor_search_form' ) ) {
            die();
        }
        $html = '';
        if(isset($_POST['s']) && sanitize_text_field($_POST['s'])){
            $args =array(
                'search' => '*'.esc_attr( $_POST['s'] ).'*',
                'search_columns' => array( 'display_name', 'user_login', 'user_nicename' ),
                /*'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'first_name',
                        'value'   => esc_attr( $_POST['s'] ),
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key'     => 'last_name',
                        'value'   => esc_attr( $_POST['s'] ),
                        'compare' => 'LIKE'
                    )
                )*/
            );
            $vendors = get_wcmp_vendors($args);

            if($vendors) {
                foreach($vendors as $vendors_key => $vendor) {
                    if(!$vendor->image) $vendor->image = $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
                    $html .= '<div style=" width: 100%; margin-bottom: 5px; clear: both; display: block;">
                    <div style=" width: 25%;  display: inline;">        
                    <img width="50" height="50" class="vendor_img" style="display: inline;" src="'.$vendor->image.'" id="vendor_image_display">
                    </div>
                    <div style=" width: 75%;  display: inline;  padding: 10px;">
                            <a href="'.esc_attr( $vendor->permalink ).'">
                                '.$vendor->user_data->display_name.'
                            </a>
                    </div>
                </div>';
                }
            }else{
                $html .= '<div style=" width: 100%; margin-bottom: 5px; clear: both; display: block;">
                    <div style="display: inline;  padding: 10px;">
                        '.__('No Vendor Matched!', 'dc-woocommerce-multi-vendor').'
                    </div>
                </div>';
            }
        }else{
            $vendors = get_wcmp_vendors();
            if($vendors) {
                foreach($vendors as $vendors_key => $vendor) {
                    if(!$vendor->image) $vendor->image = $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
                    $html .= '<div style=" width: 100%; margin-bottom: 5px; clear: both; display: block;">
                    <div style=" width: 25%;  display: inline;">        
                    <img width="50" height="50" class="vendor_img" style="display: inline;" src="'.$vendor->image.'" id="vendor_image_display">
                    </div>
                    <div style=" width: 75%;  display: inline;  padding: 10px;">
                            <a href="'.esc_attr( $vendor->permalink ).'">
                                '.$vendor->user_data->display_name.'
                            </a>
                    </div>
                </div>';
                }
            }
        }
        echo $html;
        die();
    }

}
