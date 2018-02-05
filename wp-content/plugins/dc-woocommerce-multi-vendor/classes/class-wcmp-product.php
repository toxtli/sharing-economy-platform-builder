<?php
if (!defined('ABSPATH'))
    exit;

/**
 * @class       WCMp Product Class
 *
 * @version     2.2.0
 * @package     WCMp
 * @author      WC Marketplace
 */
class WCMp_Product {

    public $loop;
    public $variation_data = array();
    public $variation;
    public $more_product_array;

    public function __construct() {
        global $WCMp;
        add_action('woocommerce_product_write_panel_tabs', array(&$this, 'add_vendor_tab'), 30);
        add_action('woocommerce_product_data_panels', array(&$this, 'output_vendor_tab'), 30);
        add_action('save_post', array(&$this, 'process_vendor_data'));
        $settings_policies = get_option('wcmp_general_policies_settings_name');
        if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable') {
            if ((isset($settings_policies['is_cancellation_on']) || isset($settings_policies['is_refund_on']) || isset($settings_policies['is_shipping_on'])) && (isset($settings_policies['is_cancellation_product_level_on']) || isset($settings_policies['is_refund_product_level_on']) || isset($settings_policies['is_shipping_product_level_on']))) {
                $current_user_id = get_current_vendor_id();
                if ((is_user_wcmp_vendor($current_user_id) && (isset($settings_policies['can_vendor_edit_cancellation_policy']) || isset($settings_policies['can_vendor_edit_refund_policy']) || isset($settings_policies['can_vendor_edit_shipping_policy']) )) || current_user_can('manage_woocommerce')) {
                    add_action('woocommerce_product_write_panel_tabs', array(&$this, 'add_policies_tab'), 30);
                    add_action('woocommerce_product_data_panels', array(&$this, 'output_policies_tab'), 30);
                    add_action('save_post', array(&$this, 'process_policies_data'));
                }
            }
            add_filter('woocommerce_product_tabs', array(&$this, 'product_policy_tab'));
        }
        add_action('woocommerce_ajax_save_product_variations', array($this, 'save_variation_commission'));
        add_action('woocommerce_product_after_variable_attributes', array(&$this, 'add_variation_settings'), 10, 3);
        add_filter('pre_get_posts', array(&$this, 'convert_business_id_to_taxonomy_term_in_query'));
        if (is_admin()) {
            add_action('transition_post_status', array(&$this, 'on_all_status_transitions'), 10, 3);
        }
        add_action('woocommerce_product_meta_start', array(&$this, 'add_report_abuse_link'), 30);
        if ($WCMp->vendor_caps->vendor_frontend_settings('enable_vendor_tab')) {
            add_filter('woocommerce_product_tabs', array(&$this, 'product_vendor_tab'));
        }
        add_filter('wp_count_posts', array(&$this, 'vendor_count_products'), 10, 3);
        /* Related Products */
        add_filter('woocommerce_output_related_products_args', array($this, 'related_products_args'), 15);
        // bulk edit vendor set
        add_action('woocommerce_product_bulk_edit_end', array($this, 'add_product_vendor'));
        add_action('woocommerce_product_bulk_edit_save', array($this, 'save_vendor_bulk_edit'));

        // Filters
        add_action('restrict_manage_posts', array($this, 'restrict_manage_posts'));
        add_filter('parse_query', array($this, 'product_vendor_filters_query'));
        add_action('save_post', array(&$this, 'check_sku_is_unique'));

        add_action('woocommerce_variation_options_dimensions', array($this, 'add_filter_for_shipping_class'), 10, 3);
        add_action('woocommerce_variation_options_tax', array($this, 'remove_filter_for_shipping_class'), 10, 3);
        add_action('admin_footer', array($this, 'wcmp_edit_product_footer'));
        if (get_wcmp_vendor_settings('is_singleproductmultiseller', 'general') == 'Enable') {
            //add_action('woocommerce_after_single_product_summary', array($this, 'get_multiple_vendors_products_of_single_product'),5);
            add_filter('woocommerce_duplicate_product_exclude_taxonomies', array($this, 'exclude_taxonomies_copy_to_draft'), 10, 1);
            add_filter('woocommerce_duplicate_product_exclude_meta', array($this, 'exclude_postmeta_copy_to_draft'), 10, 1);
            add_action('woocommerce_product_duplicate', array($this, 'wcmp_product_duplicate_update_meta'),10, 2);
            add_action('publish_product', array($this, 'update_data_to_products_map_table'), 10, 2);
            add_action('save_post_product', array($this, 'update_duplicate_product_title'), 10, 3);
            add_filter('woocommerce_product_tabs', array(&$this, 'product_single_product_multivendor_tab'));
            add_action('woocommerce_single_product_summary', array($this, 'product_single_product_multivendor_tab_link'), 60);

            add_action('delete_post', array($this, 'remove_product_from_multiple_seller_mapping'), 10);
            add_action('trashed_post', array($this, 'remove_product_from_multiple_seller_mapping'), 10);
            add_action('untrash_post', array($this, 'restore_multiple_seller_mapping'), 10);
            if(!defined('WCMP_HIDE_MULTIPLE_PRODUCT')){
                add_action('woocommerce_product_query', array(&$this, 'woocommerce_product_query'), 10);
            }
        }
        add_action('woocommerce_product_query_tax_query', array(&$this, 'wcmp_filter_product_category'), 10);
        $this->vendor_product_restriction();
    }

    public function remove_product_from_multiple_seller_mapping($post_id) {
        global $WCMp, $wpdb;
        $product_to_be_deleted = get_post($post_id);
        $post_type = $product_to_be_deleted->post_type;
        if ($post_type == 'product') {
            $table_name = $wpdb->prefix . 'wcmp_products_map';
            $sql_query = "select * from {$table_name} where product_title = '{$product_to_be_deleted->post_title}'";
            $results = $wpdb->get_results($sql_query);
            if (count($results) > 0) {
                foreach ($results as $result) {
                    $product_ids = $result->product_ids;
                    if (!empty($product_ids)) {
                        $p_ids_arr = explode(',', $product_ids);
                        if (is_array($p_ids_arr) && !empty($p_ids_arr) && count($p_ids_arr) > 0) {
                            if (count($p_ids_arr) == 1) {
                                $delete_query = "delete from {$table_name} where ID = {$result->ID}";
                                $wpdb->query($delete_query);
                            } else {
                                if (($key = array_search($post_id, $p_ids_arr)) !== false) {
                                    unset($p_ids_arr[$key]);
                                }
                                $p_ids = implode(',', $p_ids_arr);
                                $update_query = "update {$table_name} set product_ids='{$p_ids}'  where ID = {$result->ID}";
                                $wpdb->query($update_query);
                            }
                        }
                    }
                }
            }
        }
    }

    public function restore_multiple_seller_mapping($post_id) {
        global $WCMp, $wpdb;
        $product_to_be_restored = get_post($post_id);
        $post_type = $product_to_be_restored->post_type;
        if ($post_type == 'product') {
            $table_name = $wpdb->prefix . 'wcmp_products_map';
            $sql_query = "select * from {$table_name} where product_title = '{$product_to_be_restored->post_title}'";
            $results = $wpdb->get_results($sql_query);
            if (count($results) > 0) {
                foreach ($results as $result) {
                    $product_ids = $result->product_ids;
                    if (!empty($product_ids)) {
                        $p_ids_arr = explode(',', $product_ids);
                        $p_ids_arr[] = $post_id;
                        $p_ids = implode(',', $p_ids_arr);
                        $update_query = "update {$table_name} set product_ids='{$p_ids}'  where ID = {$result->ID}";
                        $wpdb->query($update_query);
                    } else {
                        $update_query = "update {$table_name} set product_ids='{$post_id}'  where ID = {$result->ID}";
                        $wpdb->query($update_query);
                    }
                }
            } else {
                $insert_query = "insert into {$table_name} set `product_title` = {$product_to_be_restored->post_title}, `product_ids` = {$product_to_be_restored->ID} ";
                $wpdb->query($insert_query);
            }
        }
    }

    function product_single_product_multivendor_tab_link() {
        global $WCMp;
        if (is_product()) {
            $WCMp->template->get_template('single-product/multiple_vendors_products_link.php');
        }
    }

    /**
     * Add vendor tab on single product page
     *
     * @return void
     */
    function product_single_product_multivendor_tab($tabs) {
        global $product, $WCMp;
        $title = apply_filters('wcmp_more_vendors_tab', __('More Offers', 'dc-woocommerce-multi-vendor'));
        $tabs['singleproductmultivendor'] = array(
            'title' => $title,
            'priority' => 80,
            'callback' => array($this, 'product_single_product_multivendor_tab_template')
        );

        return $tabs;
    }

    /**
     * Add vendor tab html
     *
     * @return void
     */
    function product_single_product_multivendor_tab_template() {
        global $woocommerce, $WCMp, $post, $wpdb;
        $more_product_array = array();
        $results = array();
        $more_products = $this->get_multiple_vendors_array_for_single_product($post->ID);
        $more_product_array = $more_products['more_product_array'];
        $results = $more_products['results'];
        $WCMp->template->get_template('single-product/multiple_vendors_products.php', array('results' => $results, 'more_product_array' => $more_product_array));
    }

    function get_multiple_vendors_array_for_single_product($post_id) {
        global $woocommerce, $WCMp, $wpdb;
        $post = get_post($post_id);
        $results_str = '';
        $searchstr = $post->post_title;
        $searchstr = str_replace("'", "", $searchstr);
        $searchstr = str_replace('"', '', $searchstr);
        $querystr = "select  * from {$wpdb->prefix}wcmp_products_map where replace(replace(product_title, '\'',''), '\"','') = '{$searchstr}'";
        $results_obj_arr = $wpdb->get_results($querystr);
        if (isset($results_obj_arr) && count($results_obj_arr) > 0) {
            $results_str = $results_obj_arr[0]->product_ids;
        }

        $product_id_arr = explode(',', $results_str);
        $args = array(
            'posts_per_page' => -1,
            'offset' => 0,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => 'product',
            'post__in' => $product_id_arr,
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $results = get_posts($args);
        $i = 0;
        $more_product_array = array();
        foreach ($results as $result) {
            $vendor_data = get_wcmp_product_vendors($result->ID);
            $_product = wc_get_product($result->ID);
            $other_user = new WP_User($result->post_author);
            if ($_product->is_visible() && !is_user_wcmp_pending_vendor($other_user) && !is_user_wcmp_rejected_vendor($other_user) && $post->post_author != $result->post_author) {
                if ($vendor_data) {
                    if (isset($vendor_data->user_data->data->display_name)) {
                        $more_product_array[$i]['seller_name'] = $vendor_data->user_data->data->display_name;
                        $more_product_array[$i]['is_vendor'] = 1;
                        $terms = get_the_terms($result, 'dc_vendor_shop');
                        if (!empty($terms)) {
                            $more_product_array[$i]['shop_link'] = get_term_link($terms[0], 'dc_vendor_shop');
                            $more_product_array[$i]['rating_data'] = wcmp_get_vendor_review_info($terms[0]->term_id);
                        }
                    }
                } else {
                    $more_product_array[$i]['seller_name'] = $other_user->data->display_name;
                    $more_product_array[$i]['is_vendor'] = 0;
                    $more_product_array[$i]['shop_link'] = get_permalink(wc_get_page_id('shop'));
                    $more_product_array[$i]['rating_data'] = 'admin';
                }
                $currency_symbol = get_woocommerce_currency_symbol();
                $regular_price_val = $_product->get_regular_price();
                $sale_price_val = $_product->get_sale_price();
                $price_val = $_product->get_price();
                $more_product_array[$i]['product_name'] = $result->post_title;
                $more_product_array[$i]['regular_price_val'] = $regular_price_val;
                $more_product_array[$i]['sale_price_val'] = $sale_price_val;
                $more_product_array[$i]['price_val'] = $price_val;
                $more_product_array[$i]['product_id'] = $result->ID;
                $more_product_array[$i]['product_type'] = $_product->get_type();
                if ($_product->get_type() == 'variable') {
                    $more_product_array[$i]['_min_variation_price'] = get_post_meta($result->ID, '_min_variation_price', true);
                    $more_product_array[$i]['_max_variation_price'] = get_post_meta($result->ID, '_max_variation_price', true);
                    $variable_min_sale_price = get_post_meta($result->ID, '_min_variation_sale_price', true);
                    $variable_max_sale_price = get_post_meta($result->ID, '_max_variation_sale_price', true);
                    $more_product_array[$i]['_min_variation_sale_price'] = $variable_min_sale_price ? $variable_min_sale_price : $more_product_array[$i]['_min_variation_price'];
                    $more_product_array[$i]['_max_variation_sale_price'] = $variable_max_sale_price ? $variable_max_sale_price : $more_product_array[$i]['_max_variation_price'];
                    $more_product_array[$i]['_min_variation_regular_price'] = get_post_meta($result->ID, '_min_variation_regular_price', true);
                    $more_product_array[$i]['_max_variation_regular_price'] = get_post_meta($result->ID, '_max_variation_regular_price', true);
                }
                $i++;
            }
        }
        return array('results' => $results, 'more_product_array' => $more_product_array);
    }

    function update_data_to_products_map_table($post_id, $post) {
        global $wpdb;
        $post->post_title = get_post_meta($post_id, '_wcmp_parent_product_id', true) ? get_post(get_post_meta($post_id, '_wcmp_parent_product_id', true))->post_title : $post->post_title;
        if ($post->post_type == 'product') {
            if (isset($post->post_title)) {
                $searchstr = $post->post_title;
                $searchstr = str_replace("'", "", $searchstr);
                $searchstr = str_replace('"', '', $searchstr);
                $results = $wpdb->get_results("select * from {$wpdb->prefix}wcmp_products_map where replace(replace(product_title, '\'',''), '\"','') = '{$searchstr}' ");
                if (is_array($results) && (count($results) > 0)) {
                    $id_of_similar = $results[0]->ID;
                    $product_ids = $results[0]->product_ids;
                    $product_ids_arr = explode(',', $product_ids);
                    if (is_array($product_ids_arr) && in_array($post_id, $product_ids_arr)) {
                        
                    } else {
                        $product_ids = $product_ids . ',' . $post->ID;
                        $wpdb->query("update {$wpdb->prefix}wcmp_products_map set product_ids = '{$product_ids}' where ID = {$id_of_similar}");
                    }
                } else {
                    $wpdb->query("insert into {$wpdb->prefix}wcmp_products_map set product_title='{$searchstr}', product_ids = '{$post->ID}' ");
                }
            }
        }
    }
    
    function update_duplicate_product_title($post_ID, $post, $update){
        global $wpdb;
        $parent_product_id = get_post_meta($post_ID, '_wcmp_parent_product_id', true);
        if($parent_product_id && apply_filters('wcmp_singleproductmultiseller_edit_product_title_disabled', true)){
            $parent_post = get_post(absint($parent_product_id));
            $wpdb->update( $wpdb->posts, array('post_title' => $parent_post->post_title), array('ID' => $post_ID) );
        }
    }

    function exclude_postmeta_copy_to_draft($arr = array()) {
        $exclude_arr = array('_sku', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to', '_sold_individually', '_backorders', '_upsell_ids', '_crosssell_ids', '_commission_per_product');
        $final_arr = array_merge($arr, $exclude_arr);
        return $final_arr;
    }

    function exclude_taxonomies_copy_to_draft($arr = array()) {
        $exclude_arr = array('product_shipping_class', 'dc_vendor_shop');
        $final_arr = array_merge($arr, $exclude_arr);
        return $final_arr;
    }
    
    function wcmp_product_duplicate_update_meta($duplicate, $product){
        $singleproductmultiseller = isset( $_REQUEST['singleproductmultiseller'] ) ? absint( $_REQUEST['singleproductmultiseller'] ) : '';
        if($singleproductmultiseller == 1){
            update_post_meta($duplicate->get_id(), '_wcmp_parent_product_id', $product->get_id());
        }
    }
    
    public function get_multiple_vendors_products_of_single_product() {
        global $WCMp;
        $WCMp->template->get_template('single-product/multiple_vendors_products.php');
    }

    public function add_filter_for_shipping_class($loop, $variation_data, $variation) {
        $this->loop = $loop;
        $this->variation_data = $variation_data;
        $this->variation = $variation;
        add_filter('wp_dropdown_cats', array($this, 'filter_shipping_class_for_variation'), 10, 2);
    }

    public function remove_filter_for_shipping_class($loop, $variation_data, $variation) {
        remove_filter('wp_dropdown_cats', array($this, 'filter_shipping_class_for_variation'), 10, 2);
    }

    function wcmp_edit_product_footer() {
        $screen = get_current_screen();
        // disable product title from being edit
        if (isset($_GET['post'])) {
            $current_post_id = $_GET['post'];
            if (get_post_type($current_post_id) == 'product') {
                $wcmp_have_parent_product_id = get_post_meta($current_post_id, '_wcmp_parent_product_id', true);
                if(in_array($screen->id, array('product','edit-product')) && $wcmp_have_parent_product_id && apply_filters('wcmp_singleproductmultiseller_edit_product_title_disabled', true)){
                    wp_add_inline_script( 'wcmp-admin-product-js', 
                    '(function ($) { 
                      $("#titlewrap #title").prop("disabled", true);
                  })(jQuery)');
                }
            }
        }
    }

    public function filter_shipping_class_for_variation($output, $arg) {
        global $WCMp;
        $loop = $this->loop;
        $variation_data = $this->variation_data;
        $variation = $this->variation;
        if (is_array($arg) && !empty($arg) && isset($arg['taxonomy']) && ($arg['taxonomy'] == 'product_shipping_class')) {
            $html = '';
            $classes = get_the_terms($variation->ID, 'product_shipping_class');
            if ($classes && !is_wp_error($classes)) {
                $current_shipping_class = current($classes)->term_id;
            } else {
                $current_shipping_class = false;
            }
            $product_shipping_class = get_terms('product_shipping_class', array('hide_empty' => 0));
            $current_user_id = get_current_vendor_id();
            $option = '<option value="-1">Same as parent</option>';

            if (!empty($product_shipping_class)) {
                $shipping_option_array = array();
                $vednor_shipping_option_array = array();
                if (is_user_wcmp_vendor($current_user_id)) {
                    $shipping_class_id = get_user_meta($current_user_id, 'shipping_class_id', true);
                    if (!empty($shipping_class_id)) {
                        $term_shipping_obj = get_term_by('id', $shipping_class_id, 'product_shipping_class');
                        $shipping_option_array[$term_shipping_obj->term_id] = $term_shipping_obj->name;
                    }
                } else {
                    foreach ($product_shipping_class as $product_shipping) {
                        $shipping_option_array[$product_shipping->term_id] = $product_shipping->name;
                    }
                }
                if (!empty($vednor_shipping_option_array)) {
                    $shipping_option_array = array();
                    $shipping_option_array = $vednor_shipping_option_array;
                }
                if (!empty($shipping_option_array)) {
                    foreach ($shipping_option_array as $shipping_option_array_key => $shipping_option_array_val) {
                        if ($current_shipping_class && $shipping_option_array_key == $current_shipping_class) {
                            $option .= '<option selected value="' . $shipping_option_array_key . '">' . $shipping_option_array_val . '</option>';
                        } else {
                            $option .= '<option value="' . $shipping_option_array_key . '">' . $shipping_option_array_val . '</option>';
                        }
                    }
                }
            }
            $html .= '<select name="dc_variable_shipping_class[' . $loop . ']" id="dc_variable_shipping_class[' . $loop . ']" class="postform">';
            $html .= $option;
            $html .= '</select>';
            return $html;
        } else {
            return $output;
        }
    }

    function check_sku_is_unique($post_id) {
        global $WCMp;
        if (isset($_POST) && !empty($_POST)) {
            $sku = isset($_POST['_sku']) ? $_POST['_sku'] : '';
            $post = get_post($post_id);
            if ($post->post_type == 'product' && !empty($sku)) {
                $args = array(
                    'posts_per_page' => 5,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'meta_key' => '_sku',
                    'meta_value' => $sku,
                    'post_type' => 'product',
                    'post__not_in' => array($post_id),
                    'post_status' => 'any',
                    'suppress_filters' => true
                );
                $posts_array = get_posts($args);
                $count_find = count($posts_array);
                if ($posts_array > 0) {
                    add_action('admin_notices', array($this, 'error_notice_for_sku_not_available'));
                }
            }
        }
    }

    function error_notice_for_sku_not_available() {
        global $WCMp;
        $class = "error";
        $message = __("SKU must be unique", 'dc-woocommerce-multi-vendor');
        echo"<div class=\"$class\"> <p>$message</p></div>";
    }

    function vendor_product_restriction() {
        global $WCMp;
        if (is_ajax())
            return;
        $current_user_id = get_current_vendor_id();
        if (is_user_wcmp_vendor($current_user_id)) {
            add_filter('manage_product_posts_columns', array($this, 'remove_featured_star'), 15);
            if (isset($_GET['post'])) {
                $current_post_id = $_GET['post'];
                if (get_post_type($current_post_id) == 'product') {

                    if (in_array(get_post_status($current_post_id), array('draft', 'publish', 'pending'))) {
                        $product_vendor_obj = get_wcmp_product_vendors($current_post_id);
                        if ($product_vendor_obj && $product_vendor_obj->id != $current_user_id) {
                            if (isset($_GET['action']) && $_GET['action'] == 'duplicate_product') {
                                
                            } else {
                                wp_redirect(admin_url() . 'edit.php?post_type=product');
                                exit;
                            }
                        }
                    }
                } else if (get_post_type($current_post_id) == 'shop_coupon') {
                    $coupon_obj = get_post($current_post_id);
                    if ($coupon_obj->post_author != $current_user_id) {
                        wp_redirect(admin_url() . 'edit.php?post_type=shop_coupon');
                        exit;
                    }
                }
            }
        }
    }

    public function remove_featured_star($existing_columns) {
        if (empty($existing_columns) && !is_array($existing_columns)) {
            $existing_columns = array();
        }
        unset($existing_columns['featured']);
        return $existing_columns;
    }

    function product_vendor_filters_query($query) {
        global $typenow, $wp_query;

        $taxonomy = 'dc_vendor_shop';
        $q_vars = &$query->query_vars;
        if ('product' == $typenow) {
            if (isset($q_vars['post_type']) && $q_vars['post_type'] == 'product') {
                if (isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
                    $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
                    $q_vars[$taxonomy] = $term->slug;
                }
            }
        }
    }

    function restrict_manage_posts() {
        global $typenow, $wp_query;

        $post_type = 'product';
        $taxonomy = 'dc_vendor_shop';

        if (!is_user_wcmp_vendor(get_current_vendor_id())) {
            if ('product' == $typenow) {
                if ($typenow == $post_type) {
                    $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
                    $info_taxonomy = get_taxonomy($taxonomy);
                    wp_dropdown_categories(array(
                        'show_option_all' => __("Show All {$info_taxonomy->label}"),
                        'taxonomy' => $taxonomy,
                        'name' => $taxonomy,
                        'orderby' => 'name',
                        'selected' => $selected,
                        'show_count' => true,
                        'hide_empty' => true,
                    ));
                };
            }
        }
    }

    /**
     * Save product vendor by bluk edit
     *
     * @param object $product
     */
    function save_vendor_bulk_edit($product) {
        global $WCMp;

        $product_id = $product->get_id();

        $current_user_id = get_current_vendor_id();
        if (!is_user_wcmp_vendor($current_user_id)) {

            if (isset($_REQUEST['choose_vendor_bulk']) && !empty($_REQUEST['choose_vendor_bulk'])) {
                if (is_numeric($_REQUEST['choose_vendor_bulk'])) {
                    $vendor_term = $_REQUEST['choose_vendor_bulk'];

                    $term = get_term($vendor_term, 'dc_vendor_shop');
                    //wp_delete_object_term_relationships( $product_id, 'dc_vendor_shop' );
                    wp_set_post_terms($product_id, $term->name, 'dc_vendor_shop', false);

                    $vendor = get_wcmp_vendor_by_term($vendor_term);
                    if (!wp_is_post_revision($product_id)) {
                        // unhook this function so it doesn't loop infinitely
                        remove_action('save_post', array($this, 'process_vendor_data'));
                        // update the post, which calls save_post again
                        wp_update_post(array('ID' => $post_id, 'post_author' => $vendor->id));
                        // re-hook this function
                        add_action('save_post', array($this, 'process_vendor_data'));
                    }
                }
            }
        }
    }

    /**
     * Add product vendor
     */
    function add_product_vendor() {
        global $WCMp;

        $current_user_id = get_current_vendor_id();
        if (!is_user_wcmp_vendor($current_user_id)) {
            ?>
            <label>
                <span class="title"><?php esc_html_e('Vendor', 'dc-woocommerce-multi-vendor'); ?></span>
                <span class="input-text-wrap vendor_bulk">
                    <select name="choose_vendor_bulk" id="choose_vendor_ajax_bulk" class="ajax_chosen_select_vendor" data-placeholder="<?php _e('Search for vendor', 'dc-woocommerce-multi-vendor') ?>" style="width:300px;" >
                        <option value="0"><?php _e("Choose a vendor", 'dc-woocommerce-multi-vendor') ?></option>
                    </select>
                </span>
            </span>
            </label>

            <?php
        }
    }

    /**
     * Show related products or not
     *
     * @return arg
     */
    function related_products_args($args) {
        global $product, $WCMp;
        $related = false;
        $vendor = get_wcmp_product_vendors($product->get_id());


        if (!$vendor) {
            return $args;
        }

        $frontend_cap_arr = $WCMp->vendor_caps->frontend_cap;
        if (array_key_exists('show_related_products', $frontend_cap_arr)) {
            $related = $frontend_cap_arr['show_related_products'];
        }

        if (!$related) {
            return $args;
        } else if ('disable' == $related) {
            return false;
        } elseif ('all_related' == $related) {
            return $args;
        } elseif ('vendors_related' == $related) {
            $vendor_products = $vendor->get_products();
            $vendor_product_ids = array();
            if (!empty($vendor_products)) {
                foreach ($vendor_products as $vendor_product) {
                    $vendor_product_ids[] = $vendor_product->ID;
                }
            }
            $args['post__in'] = $vendor_product_ids;
            return $args;
        }
    }

    /**
     * Filter product list as per vendor
     */
    public function filter_products_list($request) {
        global $typenow;

        $current_user = wp_get_current_user();

        if (is_admin() && is_user_wcmp_vendor($current_user) && 'product' == $typenow) {
            $request['author'] = $current_user->ID;
            $term_id = get_user_meta($current_user->ID, '_vendor_term_id', true);
            $taxquery = array(
                array(
                    'taxonomy' => 'dc_vendor_shop',
                    'field' => 'id',
                    'terms' => array($term_id),
                    'operator' => 'IN'
                )
            );

            $request['tax_query'] = $taxquery;
        }

        return $request;
    }

    /**
     * Count vendor products
     */
    public function vendor_count_products($counts, $type, $perm) {
        $current_user = wp_get_current_user();

        if (is_user_wcmp_vendor($current_user) && 'product' == $type) {
            $term_id = get_user_meta($current_user->ID, '_vendor_term_id', true);

            $args = array(
                'post_type' => $type,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'dc_vendor_shop',
                        'field' => 'id',
                        'terms' => array($term_id),
                        'operator' => 'IN'
                    ),
                ),
            );

            /**
             * Get a list of post statuses.
             */
            $stati = get_post_stati();

            // Update count object
            foreach ($stati as $status) {
                $args['post_status'] = $status;
                $query = new WP_Query($args);
                $posts = $query->get_posts();
                $counts->$status = count($posts);
            }
        }

        return $counts;
    }

    /**
     * Notify admin on publish product by vendor
     *
     * @return void
     */
    function on_all_status_transitions($new_status, $old_status, $post) {
        if ($new_status != $old_status && $post->post_status == 'pending') {
            $current_user = get_current_vendor_id();
            if ($current_user)
                $current_user_is_vendor = is_user_wcmp_vendor($current_user);
            if ($current_user_is_vendor) {
                //send mails to admin for new vendor product
                $vendor = get_wcmp_vendor_by_term(get_user_meta($current_user, '_vendor_term_id', true));
                $email_admin = WC()->mailer()->emails['WC_Email_Vendor_New_Product_Added'];
                $email_admin->trigger($post->post_id, $post, $vendor);
            }
        } else if ($new_status != $old_status && $post->post_status == 'publish') {
            $current_user = get_current_vendor_id();
            if ($current_user)
                $current_user_is_vendor = is_user_wcmp_vendor($current_user);
            if ($current_user_is_vendor) {
                //send mails to admin for new vendor product
                $vendor = get_wcmp_vendor_by_term(get_user_meta($current_user, '_vendor_term_id', true));
                $email_admin = WC()->mailer()->emails['WC_Email_Vendor_New_Product_Added'];
                $email_admin->trigger($post->post_id, $post, $vendor);
            }
        }
        if (current_user_can('administrator') && $new_status != $old_status && $post->post_status == 'publish') {
            if (isset($_POST['choose_vendor']) && !empty($_POST['choose_vendor'])) {
                $term = get_term($_POST['choose_vendor'], 'dc_vendor_shop');
                if ($term) {
                    $vendor = get_wcmp_vendor_by_term($term->term_id);
                    $email_admin = WC()->mailer()->emails['WC_Email_Admin_Added_New_Product_to_Vendor'];
                    $email_admin->trigger($post->post_id, $post, $vendor);
                }
            }
        }
    }

    /**
     * Add Vendor tab in single product page 
     *
     * @return void
     */
    function add_vendor_tab() {
        global $WCMp;
        ?>
        <li class="vendor_icon vendor_icons"><a href="#choose_vendor"><?php _e('Vendor', 'dc-woocommerce-multi-vendor'); ?></a></li>
        <?php
    }

    /**
     * Output of Vendor tab in single product page 
     *
     * @return void
     */
    function output_vendor_tab() {
        global $post, $WCMp, $woocommerce;
        $html = '';
        $vendor = get_wcmp_product_vendors($post->ID);
        $commission_per_poduct = get_post_meta($post->ID, '_commission_per_product', true);
        $current_user = get_current_vendor_id();
        if ($current_user)
            $current_user_is_vendor = is_user_wcmp_vendor($current_user);
        $html .= '<div class="options_group" > <table class="form-field form-table">';
        $html .= '<tbody>';
        if ($vendor) {
            $option = '<option value="' . $vendor->term_id . '" selected="selected">' . $vendor->user_data->display_name . '</option>';
        } else if ($current_user_is_vendor) {
            $vendor = get_wcmp_vendor_by_term(get_user_meta($current_user, '_vendor_term_id', true));
            $option = '<option value="' . $vendor->term_id . '" selected="selected">' . $vendor->user_data->display_name . '</option>';
        } else {
            $option = '<option>' . __("Choose a vendor", 'dc-woocommerce-multi-vendor') . '</option>';
        }
        $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for="' . esc_attr('vendor') . '">' . __("Vendor", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
        if (!$current_user_is_vendor) {
            $html .= '<select name="' . esc_attr('choose_vendor') . '" id="' . esc_attr('choose_vendor_ajax') . '" class="ajax_chosen_select_vendor" data-placeholder="' . __("Search for vendor", 'dc-woocommerce-multi-vendor') . '" style="width:300px;" >' . $option . '</select>';
            $html .= '<p class="description">' . 'choose vendor' . '</p>';
        } else {
            $html .= '<label id="vendor-label" for="' . esc_attr('vendor') . '">' . $vendor->user_data->display_name . '</label>';
            $html .= '<input type="hidden" name="' . esc_attr('choose_vendor') . '"   value="' . $vendor->term_id . '" />';
        }
        $html .= '</td><tr/>';

        $commission_percentage_per_poduct = get_post_meta($post->ID, '_commission_percentage_per_product', true);
        $commission_fixed_with_percentage = get_post_meta($post->ID, '_commission_fixed_with_percentage', true);
        $commission_fixed_with_percentage_qty = get_post_meta($post->ID, '_commission_fixed_with_percentage_qty', true);
        if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {

            if (!$current_user_is_vendor) {
                $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for= "Commission">' . __("Commission Percentage", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
                $html .= '<input class="input-commision" type="text" name="commission_percentage" value="' . $commission_percentage_per_poduct . '"% />';
            } else {
                if (!empty($commission_percentage_per_poduct)) {
                    $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for= "Commission">' . __("Commission Percentage", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
                    $html .= '<span>' . $commission_percentage_per_poduct . '%</span>';
                }
            }
            $html .= '</td></tr>';

            if (!$current_user_is_vendor) {
                $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for= "Commission">' . __("Commission Fixed per transaction", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
                $html .= '<input class="input-commision" type="text" name="fixed_with_percentage" value="' . $commission_fixed_with_percentage . '" />';
            } else {
                if (!empty($commission_fixed_with_percentage)) {
                    $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for= "Commission">' . __("Commission Fixed per transaction", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
                    $html .= '<span>' . $commission_fixed_with_percentage . '</span>';
                }
            }
            $html .= '</td></tr>';
        } else if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {

            if (!$current_user_is_vendor) {
                $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for= "Commission">' . __("Commission Percentage", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
                $html .= '<input class="input-commision" type="text" name="commission_percentage" value="' . $commission_percentage_per_poduct . '"% />';
            } else {
                if (!empty($commission_percentage_per_poduct)) {
                    $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for= "Commission">' . __("Commission Percentage", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
                    $html .= '<span>' . $commission_percentage_per_poduct . '%</span>';
                }
            }
            $html .= '</td></tr>';

            if (!$current_user_is_vendor) {
                $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for= "fixed amount">' . __("Commission Fixed per unit", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
                $html .= '<input class="input-commision" type="text" name="fixed_with_percentage_qty" value="' . $commission_fixed_with_percentage_qty . '" />';
            } else {
                if (!empty($commission_fixed_with_percentage_qty)) {
                    $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for= "fixed amount">' . __("Commission Fixed per unit", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
                    $html .= '<span>' . $commission_fixed_with_percentage_qty . '</span>';
                }
            }
            $html .= '</td></tr>';
        } else {

            if (!$current_user_is_vendor) {
                $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for= "Commission">' . __("Commission", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
                $html .= '<input class="input-commision" type="text" name="commision" value="' . $commission_per_poduct . '" />';
            } else {
                if (!empty($commission_per_poduct)) {
                    $html .= '<tr valign="top"><td scope="row"><label id="vendor-label" for= "Commission">' . __("Commission", 'dc-woocommerce-multi-vendor') . '</label></td><td>';
                    $html .= '<span>' . $commission_per_poduct . '</span>';
                }
            }
            $html .= '</td></tr>';
        }

        $html = apply_filters('wcmp_additional_fields_product_vendor_tab', $html);

        if ($vendor) {
            if (current_user_can('manage_options')) {
                $html .= '<tr valign="top"><td scope="row"><input type="button" class="delete_vendor_data button" value="' . __("Unassign vendor", 'dc-woocommerce-multi-vendor') . '" /></td></tr>';

                wp_localize_script('wcmp-admin-product-js', 'unassign_vendors_data', array('current_product_id' => $post->ID, 'current_user_id' => get_current_vendor_id()));
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        ?>
        <div id="choose_vendor" class="panel woocommerce_options_panel">
            <?php echo $html; ?>
        </div>
        <?php
    }

    function add_policies_tab() {
        global $WCMp;
        ?>
        <li class="policy_icon policy_icons"><a href="#set_policies"><?php _e('Policies', 'dc-woocommerce-multi-vendor'); ?></a></li>
        <?php
    }

    function output_policies_tab() {
        global $post, $WCMp, $woocommerce;
        $_wcmp_enable_policy_tab = get_post_meta($post->ID, '_wcmp_enable_policy_tab', true) ? get_post_meta($post->ID, '_wcmp_enable_policy_tab', true) : '';
        $_wcmp_cancallation_policy = get_post_meta($post->ID, '_wcmp_cancallation_policy', true) ? get_post_meta($post->ID, '_wcmp_cancallation_policy', true) : '';
        $_wcmp_refund_policy = get_post_meta($post->ID, '_wcmp_refund_policy', true) ? get_post_meta($post->ID, '_wcmp_refund_policy', true) : '';
        $_wcmp_shipping_policy = get_post_meta($post->ID, '_wcmp_shipping_policy', true) ? get_post_meta($post->ID, '_wcmp_shipping_policy', true) : '';
        $settings_policies = get_option('wcmp_general_policies_settings_name');
        $current_user_id = get_current_vendor_id();
        ?>
        <div id="set_policies" class="panel woocommerce_options_panel">
            <div class="options_group" >
                <table class="form-field form-table">
                    <tbody>
                        <?php
                        if (isset($settings_policies['is_cancellation_on'])) {
                            if (isset($settings_policies['is_cancellation_product_level_on'])) {
                                if ((is_user_wcmp_vendor($current_user_id) && (isset($settings_policies['can_vendor_edit_cancellation_policy']))) || current_user_can('manage_woocommerce')) {
                                    ?>
                                    <tr>
                                        <td>
                                            <p><strong><?php echo __('Cancellation/Return/Exchange Policy'); ?> : </strong></p>
                                            <textarea class="widefat" name="_wcmp_cancallation_policy"  ><?php echo $_wcmp_cancallation_policy; ?></textarea>                     
                                        </td>                         
                                    </tr>                                   
                                    <?php
                                }
                            }
                        }
                        ?>
                        <?php
                        if (isset($settings_policies['is_refund_on'])) {
                            if (isset($settings_policies['is_refund_product_level_on'])) {
                                if ((is_user_wcmp_vendor($current_user_id) && (isset($settings_policies['can_vendor_edit_refund_policy']))) || current_user_can('manage_woocommerce')) {
                                    ?>
                                    <tr>
                                        <td>
                                            <p><strong><?php echo __('Refund Policy'); ?> : </strong></p>
                                            <textarea class="widefat" name="_wcmp_refund_policy"  ><?php echo $_wcmp_refund_policy; ?></textarea>                     
                                        </td>                         
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                        <?php
                        if (isset($settings_policies['is_shipping_on'])) {
                            if (isset($settings_policies['is_shipping_product_level_on'])) {
                                if ((is_user_wcmp_vendor($current_user_id) && (isset($settings_policies['can_vendor_edit_shipping_policy']))) || current_user_can('manage_woocommerce')) {
                                    ?>
                                    <tr>
                                        <td>
                                            <p><strong><?php echo __('Shipping Policy'); ?> : </strong></p>
                                            <textarea class="widefat" name="_wcmp_shipping_policy"  ><?php echo $_wcmp_shipping_policy; ?></textarea>                     
                                        </td>                         
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        do_action( 'wcmp_product_options_policy_product_data' );
                        ?>
                    </tbody>
                </table>            
            </div>
        </div>

        <?php
    }

    function process_policies_data($post_id) {
        $post = get_post($post_id);
        if ($post->post_type == 'product') {
            if (isset($_POST['_wcmp_enable_policy_tab'])) {
                update_post_meta($post_id, '_wcmp_enable_policy_tab', $_POST['_wcmp_enable_policy_tab']);
            } else {
                update_post_meta($post_id, '_wcmp_enable_policy_tab', '');
            }
            if (isset($_POST['_wcmp_cancallation_policy'])) {
                update_post_meta($post_id, '_wcmp_cancallation_policy', $_POST['_wcmp_cancallation_policy']);
            }
            if (isset($_POST['_wcmp_refund_policy'])) {
                update_post_meta($post_id, '_wcmp_refund_policy', $_POST['_wcmp_refund_policy']);
            }
            if (isset($_POST['_wcmp_shipping_policy'])) {
                update_post_meta($post_id, '_wcmp_shipping_policy', $_POST['_wcmp_shipping_policy']);
            }
        }
    }

    /**
     * Save vendor related data
     *
     * @return void
     */
    function process_vendor_data($post_id) {
        $post = get_post($post_id);

        if ($post->post_type == 'product') {
            if (isset($_POST['commision'])) {
                update_post_meta($post_id, '_commission_per_product', $_POST['commision']);
            }

            if (isset($_POST['commission_percentage'])) {
                update_post_meta($post_id, '_commission_percentage_per_product', $_POST['commission_percentage']);
            }

            if (isset($_POST['fixed_with_percentage_qty'])) {
                update_post_meta($post_id, '_commission_fixed_with_percentage_qty', $_POST['fixed_with_percentage_qty']);
            }

            if (isset($_POST['fixed_with_percentage'])) {
                update_post_meta($post_id, '_commission_fixed_with_percentage', $_POST['fixed_with_percentage']);
            }

            if (isset($_POST['choose_vendor']) && !empty($_POST['choose_vendor'])) {

                $term = get_term($_POST['choose_vendor'], 'dc_vendor_shop');
                if ($term) {
                    wp_delete_object_term_relationships($post_id, 'dc_vendor_shop');
                    wp_set_post_terms($post_id, $term->slug, 'dc_vendor_shop', true);
                }

                $vendor = get_wcmp_vendor_by_term($_POST['choose_vendor']);
                if (!wp_is_post_revision($post_id)) {
                    // unhook this function so it doesn't loop infinitely
                    remove_action('save_post', array($this, 'process_vendor_data'));
                    // update the post, which calls save_post again
                    wp_update_post(array('ID' => $post_id, 'post_author' => $vendor->id));
                    // re-hook this function
                    add_action('save_post', array($this, 'process_vendor_data'));
                }
            }

            if (isset($_POST['variable_post_id']) && !empty($_POST['variable_post_id'])) {
                foreach ($_POST['variable_post_id'] as $post_key => $value) {
                    if (isset($_POST['variable_product_vendors_commission'][$post_key])) {
                        $commission = $_POST['variable_product_vendors_commission'][$post_key];
                        update_post_meta($value, '_product_vendors_commission', $commission);
                    }

                    if (isset($_POST['variable_product_vendors_commission_percentage'][$post_key])) {
                        $commission = $_POST['variable_product_vendors_commission_percentage'][$post_key];
                        update_post_meta($value, '_product_vendors_commission_percentage', $commission);
                    }

                    if (isset($_POST['variable_product_vendors_commission_fixed_per_trans'][$post_key])) {
                        $commission = $_POST['variable_product_vendors_commission_fixed_per_trans'][$post_key];
                        update_post_meta($value, '_product_vendors_commission_fixed_per_trans', $commission);
                    }

                    if (isset($_POST['variable_product_vendors_commission_fixed_per_qty'][$post_key])) {
                        $commission = $_POST['variable_product_vendors_commission_fixed_per_qty'][$post_key];
                        update_post_meta($value, '_product_vendors_commission_fixed_per_qty', $commission);
                    }

                    if (isset($_POST['dc_variable_shipping_class'][$post_key])) {
                        $_POST['dc_variable_shipping_class'][$post_key] = !empty($_POST['dc_variable_shipping_class'][$post_key]) ? (int) $_POST['dc_variable_shipping_class'][$post_key] : '';
                        $array = wp_set_object_terms($value, $_POST['dc_variable_shipping_class'][$post_key], 'product_shipping_class');
                        unset($_POST['dc_variable_shipping_class'][$post_key]);
                    }
                }
            }
        }
    }

    /**
     * Save variation product commission
     *
     * @return void
     */
    function save_variation_commission() {
        if (isset($_POST['variable_post_id']) && !empty($_POST['variable_post_id'])) {
            foreach ($_POST['variable_post_id'] as $post_key => $value) {
                if (isset($_POST['variable_product_vendors_commission'][$post_key])) {
                    $commission = $_POST['variable_product_vendors_commission'][$post_key];
                    update_post_meta($value, '_product_vendors_commission', $commission);
                    unset($_POST['variable_product_vendors_commission'][$post_key]);
                }

                if (isset($_POST['variable_product_vendors_commission_percentage'][$post_key])) {
                    $commission = $_POST['variable_product_vendors_commission_percentage'][$post_key];
                    update_post_meta($value, '_product_vendors_commission_percentage', $commission);
                    unset($_POST['variable_product_vendors_commission_percentage'][$post_key]);
                }

                if (isset($_POST['variable_product_vendors_commission_fixed_per_trans'][$post_key])) {
                    $commission = $_POST['variable_product_vendors_commission_fixed_per_trans'][$post_key];
                    update_post_meta($value, '_product_vendors_commission_fixed_per_trans', $commission);
                    unset($_POST['variable_product_vendors_commission_fixed_per_trans'][$post_key]);
                }

                if (isset($_POST['variable_product_vendors_commission_fixed_per_qty'][$post_key])) {
                    $commission = $_POST['variable_product_vendors_commission_fixed_per_qty'][$post_key];
                    update_post_meta($value, '_product_vendors_commission_fixed_per_qty', $commission);
                    unset($_POST['variable_product_vendors_commission_fixed_per_qty'][$post_key]);
                }
                if (isset($_POST['dc_variable_shipping_class'][$post_key])) {
                    $_POST['dc_variable_shipping_class'][$post_key] = !empty($_POST['dc_variable_shipping_class'][$post_key]) ? (int) $_POST['dc_variable_shipping_class'][$post_key] : '';
                    $array = wp_set_object_terms($value, $_POST['dc_variable_shipping_class'][$post_key], 'product_shipping_class');
                    unset($_POST['dc_variable_shipping_class'][$post_key]);
                }
            }
        }
    }

    /**
     * Save vendor related data for variation
     *
     * @return void
     */
    public function add_variation_settings($loop, $variation_data, $variation) {
        global $WCMp;

        $html = '';
        $commission = $commission_percentage = $commission_fixed_per_trans = $commission_fixed_per_qty = '';
        $commission = get_post_meta($variation->ID, '_product_vendors_commission', true);
        $commission_percentage = get_post_meta($variation->ID, '_product_vendors_commission_percentage', true);
        $commission_fixed_per_trans = get_post_meta($variation->ID, '_product_vendors_commission_fixed_per_trans', true);
        $commission_fixed_per_qty = get_post_meta($variation->ID, '_product_vendors_commission_fixed_per_qty', true);

        if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {

            if (is_user_wcmp_vendor(get_current_vendor_id())) {
                if (isset($commission_percentage) && !empty($commission_percentage)) {
                    $html .= '<tr>
                                            <td>
                                                <div class="_product_vendors_commission_percentage">
                                                    <label for="_product_vendors_commission_percentage_' . $loop . '">' . __('Commission (percentage)', 'dc-woocommerce-multi-vendor') . ':</label>
                                                    <span class="variable_commission_cls">' . $commission_percentage . '</span>
                                                </div>
                                            </td>
                                        </tr>';
                }
                if (isset($commission_percentage) && !empty($commission_percentage)) {
                    $html .= '<tr>
                                            <td>
                                                <div class="_product_vendors_commission_fixed_per_trans">
                                                    <label for="_product_vendors_commission_fixed_per_trans_' . $loop . '">' . __('Commission (fixed) Per Transaction', 'dc-woocommerce-multi-vendor') . ':</label>
                                                    <span class="variable_commission_cls">' . $commission_fixed_per_trans . '</span>
                                                </div>
                                            </td>
                                        </tr>';
                }
            } else {
                $html .= '<tr>
                                        <td>
                                            <div class="_product_vendors_commission_percentage">
                                                <label for="_product_vendors_commission_percentage_' . $loop . '">' . __('Commission (percentage)', 'dc-woocommerce-multi-vendor') . ':</label>
                                                <input size="4" type="text" name="variable_product_vendors_commission_percentage[' . $loop . ']" id="_product_vendors_commission_percentage_' . $loop . '" value="' . $commission_percentage . '" />
                                            </div>
                                        </td>
                                    </tr>';
                $html .= '<tr>
                                        <td>
                                            <div class="_product_vendors_commission_fixed_per_trans">
                                                <label for="_product_vendors_commission_fixed_per_trans_' . $loop . '">' . __('Commission (fixed) Per Transaction', 'dc-woocommerce-multi-vendor') . ':</label>
                                                <input size="4" type="text" name="variable_product_vendors_commission_fixed_per_trans[' . $loop . ']" id="_product_vendors_commission_fixed_per_trans__' . $loop . '" value="' . $commission_fixed_per_trans . '" />
                                            </div>
                                        </td>
                                    </tr>';
            }
        } else if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {

            if (is_user_wcmp_vendor(get_current_vendor_id())) {
                if (isset($commission_percentage) && !empty($commission_percentage)) {
                    $html .= '<tr>
                                            <td>
                                                <div class="_product_vendors_commission_percentage">
                                                    <label for="_product_vendors_commission_percentage_' . $loop . '">' . __('Commission Percentage', 'dc-woocommerce-multi-vendor') . ':</label>
                                                    <span class="variable_commission_cls">' . $commission_percentage . '</span>
                                                </div>
                                            </td>
                                        </tr>';
                }

                if (isset($commission_fixed_per_qty) && !empty($commission_fixed_per_qty)) {
                    $html .= '<tr>
                                        <td>
                                            <div class="_product_vendors_commission_fixed_per_qty">
                                                <label for="_product_vendors_commission_fixed_per_qty_' . $loop . '">' . __('Commission Fixed per unit', 'dc-woocommerce-multi-vendor') . ':</label>
                                                <span class="variable_commission_cls">' . $commission_fixed_per_qty . '</span>
                                            </div>
                                        </td>
                                    </tr';
                }
            } else {
                $html .= '<tr>
                                        <td>
                                            <div class="_product_vendors_commission_percentage">
                                                <label for="_product_vendors_commission_percentage_' . $loop . '">' . __('Commission Percentage', 'dc-woocommerce-multi-vendor') . ':</label>
                                                <input size="4" type="text" name="variable_product_vendors_commission_percentage[' . $loop . ']" id="_product_vendors_commission_percentage_' . $loop . '" value="' . $commission_percentage . '" />
                                            </div>
                                        </td>
                                    </tr>';

                $html .= '<tr>
                                        <td>
                                            <div class="_product_vendors_commission_fixed_per_qty">
                                                <label for="_product_vendors_commission_fixed_per_qty_' . $loop . '">' . __('Commission Fixed per unit', 'dc-woocommerce-multi-vendor') . ':</label>
                                                <input size="4" type="text" name="variable_product_vendors_commission_fixed_per_qty[' . $loop . ']" id="_product_vendors_commission_fixed_per_qty__' . $loop . '" value="' . $commission_fixed_per_qty . '" />
                                            </div>
                                        </td>
                                    </tr';
            }
        } else {
            if (is_user_wcmp_vendor(get_current_vendor_id())) {
                if (isset($commission) && !empty($commission)) {
                    $html .= '<tr>
                                            <td>
                                                <div class="_product_vendors_commission">
                                                    <label for="_product_vendors_commission_' . $loop . '">' . __('Commission', 'dc-woocommerce-multi-vendor') . ':</label>
                                                    <span class="variable_commission_cls">' . $commission . '</span>
                                                </div>
                                            </td>
                                        </tr>';
                }
            } else {
                $html .= '<tr>
                                        <td>
                                            <div class="_product_vendors_commission">
                                                <label for="_product_vendors_commission_' . $loop . '">' . __('Commission', 'dc-woocommerce-multi-vendor') . ':</label>
                                                <input size="4" type="text" name="variable_product_vendors_commission[' . $loop . ']" id="_product_vendors_commission_' . $loop . '" value="' . $commission . '" />
                                            </div>
                                        </td>
                                    </tr>';
            }
        }

        echo $html;
    }

    /**
     * Add vendor tab on single product page
     *
     * @return void
     */
    function product_vendor_tab($tabs) {
        global $product, $WCMp;
        if ($product) {
            $vendor = get_wcmp_product_vendors($product->get_id());
            if ($vendor) {
                $title = __('Vendor', 'dc-woocommerce-multi-vendor');
                $tabs['vendor'] = array(
                    'title' => $title,
                    'priority' => 20,
                    'callback' => array($this, 'woocommerce_product_vendor_tab')
                );
            }
        }
        return $tabs;
    }

    /**
     * Add vendor tab html
     *
     * @return void
     */
    function woocommerce_product_vendor_tab() {
        global $woocommerce, $WCMp;
        $WCMp->template->get_template('vendor_tab.php');
    }

    /**
     * Add policies tab on single product page
     *
     * @return void
     */
    function product_policy_tab($tabs) {
        global $product, $WCMp;
        if ($product) {
            $policies_can_override_by_vendor = '';
            $wcmp_capabilities_settings_name = get_option('wcmp_general_policies_settings_name');
            $can_vendor_edit_policy_tab_label_field = apply_filters('can_vendor_edit_policy_tab_label_field', true);
            $policies_settings = get_option('wcmp_general_policies_settings_name');
            if (isset($wcmp_capabilities_settings_name['can_vendor_edit_policy_tab_label']) && $can_vendor_edit_policy_tab_label_field && ( isset($policies_settings['is_cancellation_on']) || isset($policies_settings['is_refund_on']) || isset($policies_settings['is_shipping_on']) )) {
                $policies_can_override_by_vendor = 'Enable';
            }
            $title = __('Policies', 'dc-woocommerce-multi-vendor');
            $product_id = $product->get_id();
            $product_vendors = get_wcmp_product_vendors($product_id);
            if ($product_vendors) {
                $author_id = $product_vendors->id;
            } else {
                $author_id = get_post_field('post_author', $product_id);
            }
            $tab_title_by_vendor = get_user_meta($author_id, '_vendor_policy_tab_title', true);
            if (isset($policies_settings['policy_tab_title']) && (!empty($policies_settings['policy_tab_title']))) {
                $title = $policies_settings['policy_tab_title'];
            }
            if ($policies_can_override_by_vendor != '' && (!empty($tab_title_by_vendor))) {
                $title = $tab_title_by_vendor;
            }
            $tabs['policies'] = array(
                'title' => $title,
                'priority' => 30,
                'callback' => array($this, 'woocommerce_product_policies_tab')
            );
        }

        return $tabs;
    }

    /**
     * Add Polices tab html
     *
     * @return void
     */
    function woocommerce_product_policies_tab() {
        global $woocommerce, $WCMp;
        $WCMp->template->get_template('policies_tab.php');
    }

    /**
     * add tax query on product page
     * @return void
     */
    function convert_business_id_to_taxonomy_term_in_query($query) {
        global $pagenow;
        if (is_admin()) {
            if (isset($_GET['post_type']) && $_GET['post_type'] == 'product' && $pagenow == 'edit.php') {
                $current_user_id = get_current_vendor_id();
                $current_user = get_user_by('id', $current_user_id);
                if (!in_array('dc_vendor', $current_user->roles))
                    return $query;
                $term_id = get_user_meta($current_user_id, '_vendor_term_id', true);


                $taxquery = array(
                    array(
                        'taxonomy' => 'dc_vendor_shop',
                        'field' => 'id',
                        'terms' => array($term_id),
                        'operator' => 'IN'
                    )
                );

                $query->set('tax_query', $taxquery);
            }
        } else {
            if (isset($query->query['post_type']) && $query->query['post_type'] == 'product') {
                $get_block_array = array();
                $get_blocked = wcmp_get_all_blocked_vendors();
                if (!empty($get_blocked)) {
                    foreach ($get_blocked as $get_block) {
                        $get_block_array[] = (int) $get_block->term_id;
                    }
                    $taxquery = array(
                        array(
                            'taxonomy' => 'dc_vendor_shop',
                            'field' => 'id',
                            'terms' => $get_block_array,
                            'operator' => 'NOT IN'
                        )
                    );

                    $query->set('tax_query', $taxquery);
                }
            }
        }

        return $query;
    }

    /**
     * Vendor report abuse option
     */
    function add_report_abuse_link() {
        global $product, $WCMp;
        $is_display = false;
        $settings_is_display = $WCMp->vendor_caps->frontend_cap;
        if (isset($settings_is_display['show_report_abuse'])) {
            if ($settings_is_display['show_report_abuse'] == 'all_products') {
                $is_display = true;
            } else if ($settings_is_display['show_report_abuse'] == 'only_vendor_products') {

                if (get_wcmp_product_vendors($product->get_id()))
                    $is_display = true;
            } else if ($settings_is_display['show_report_abuse'] == 'disable') {

                $is_display = false;
            }
        } else {
            $is_display = true;
        }

        $report_abuse_text = $WCMp->vendor_caps->frontend_cap;
        if (isset($report_abuse_text['report_abuse_text']) && !empty($report_abuse_text['report_abuse_text'])) {
            $display_text = $report_abuse_text['report_abuse_text'];
        } else {
            $display_text = __('Report Abuse', 'dc-woocommerce-multi-vendor');
        }
        if ($is_display) {
            ?>
            <a href="#" id="report_abuse"><?php echo $display_text; ?></a><br>
            <div id="report_abuse_form" class="simplePopup"> 
                <h3 class="wcmp-abuse-report-title"><?php _e('Report an abuse for product ', 'dc-woocommerce-multi-vendor') . ' ' . the_title(); ?> </h3>
                <form action="#" method="post" id="report-abuse" class="report-abuse-form" name="report-abuse">
                    <table>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="report_abuse_name" id="report_abuse_name" name="report_abuse[name]" value="" style="width: 100%;" placeholder="<?php _e('Name', 'dc-woocommerce-multi-vendor'); ?>" required="">
                                    <span class="wcmp-report-abuse-error"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="email" class="report_abuse_email" id="report_abuse_email" name="report_abuse[email]" value="" style="width: 100%;" placeholder="<?php _e('Email', 'dc-woocommerce-multi-vendor'); ?>" required="">
                                    <span class="wcmp-report-abuse-error"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <textarea name="report_abuse[message]" class="report_abuse_msg" id="report_abuse_msg" rows="5" style="width: 100%;" placeholder="<?php _e('Leave a message explaining the reasons for your abuse report', 'dc-woocommerce-multi-vendor'); ?>" required=""></textarea>
                                    <span class="wcmp-report-abuse-error"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="hidden" class="report_abuse_product_id" value="<?php echo $product->get_id(); ?>">
                                    <input type="submit" class="submit-report-abuse submit" name="report_abuse[submit]" value="<?php _e('Report', 'dc-woocommerce-multi-vendor'); ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>                          
            <?php
        }
    }

    /**
     * filter shop loop for single product multiple vendor
     * @global Object $wpdb
     * @param WC_Query object $q
     */
    public function woocommerce_product_query($q) {
        global $wpdb;
        if (is_tax('dc_vendor_shop')) {
            return;
        }
        $exclude_products = array();
        $resualts = $wpdb->get_results("SELECT `product_ids` FROM {$wpdb->prefix}wcmp_products_map");
        if ($resualts) {
            foreach ($resualts as $resualt) {
                $product_ids = explode(',', $resualt->product_ids);
                $product_array_price = array();
                if (count($product_ids) > 1) {
                    foreach ($product_ids as $product_id) {
                        $product = wc_get_product(absint($product_id));
                        if ($product && $product->get_price()) {
                            $product_array_price[$product_id] = $product->get_price();
                        }
                    }
                }
                if ($product_array_price) {
                    $min_product = array_search(min($product_array_price), $product_array_price);
                    unset($product_array_price[$min_product]);
                    $exclude_products = array_merge($exclude_products, array_keys($product_array_price));
                }
            }
        }
        $q->set('post__not_in', array_unique($exclude_products));
    }
    /**
     * Filter product on select category from WCMp_Widget_Vendor_Product_Categories widget
     * @param array $tax_query
     * @return array
     */
    public function wcmp_filter_product_category($tax_query){
        $category = filter_input(INPUT_GET, 'category');
        if (!is_tax('dc_vendor_shop') || is_null($category)) {
            return $tax_query;
        }
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $category
        );
        return $tax_query;
    }

}
