<?php
if (!defined('ABSPATH'))
    exit;

/**
 * @class 		WCMp Vendor Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Vendor {

    public $id;
    public $taxonomy;
    public $term;
    public $user_data;
    public $shipping_class_id;

    /**
     * Get the vendor if UserID is passed, otherwise the vendor is new and empty.
     *
     * @access public
     * @param string $id (default: '')
     * @return void
     */
    public function __construct($id = '') {

        $this->taxonomy = 'dc_vendor_shop';

        $this->term = false;

        if ($id > 0) {
            $this->get_vendor($id);
        }
    }

    public function get_reviews_and_rating($offset = 0) {
        global $WCMp, $wpdb;
        $vendor_id = $this->id;
        $posts_per_page = get_option('posts_per_page');
        if (empty($vendor_id) || $vendor_id == '' || $vendor_id == 0) {
            return 0;
        } else {
            $args_default = array(
                'status' => 'approve',
                'type' => 'wcmp_vendor_rating',
                'count' => false,
                'number' => $posts_per_page,
                'offset' => $offset,
                'meta_key' => 'vendor_rating_id',
                'meta_value' => $vendor_id,
            );
            $args = apply_filters('wcmp_vendor_review_rating_args_to_fetch', $args_default);
            return get_comments($args);
        }
    }

    public function get_review_count() {
        global $WCMp, $wpdb;
        $vendor_id = $this->id;
        if (empty($vendor_id) || $vendor_id == '' || $vendor_id == 0) {
            return 0;
        } else {
            $args_default = array(
                'status' => 'approve',
                'type' => 'wcmp_vendor_rating',
                'count' => true,
                'meta_key' => 'vendor_rating_id',
                'meta_value' => $vendor_id,
            );
            $args = apply_filters('wcmp_vendor_review_rating_args_to_fetch', $args_default);
            return get_comments($args);
        }
    }

    /**
     * Gets an Vendor User from the database.
     *
     * @access public
     * @param int $id (default: 0)
     * @return bool
     */
    public function get_vendor($id = 0) {
        if (!$id) {
            return false;
        }

        if (!is_user_wcmp_vendor($id)) {
            return false;
        }

        if ($result = get_userdata($id)) {
            $this->populate($result);
            return true;
        }
        return false;
    }

    /**
     * Populates an Vendor from the loaded user data.
     *
     * @access public
     * @param mixed $result
     * @return void
     */
    public function populate($result) {

        $this->id = $result->ID;
        $this->user_data = $result;
    }

    /**
     * __isset function.
     *
     * @access public
     * @param mixed $key
     * @return bool
     */
    public function __isset($key) {
        global $WCMp;

        if (!$this->id) {
            return false;
        }

        if (in_array($key, array('term_id', 'page_title', 'page_slug', 'link'))) {
            if ($term_id = get_user_meta($this->id, '_vendor_term_id', true)) {
                return term_exists(absint($term_id), $WCMp->taxonomy->taxonomy_name);
            } else {
                return false;
            }
        }

        return metadata_exists('user', $this->id, '_' . $key);
    }

    /**
     * __get function.
     *
     * @access public
     * @param mixed $key
     * @return mixed
     */
    public function __get($key) {
        if (!$this->id) {
            return false;
        }

        if ($key == 'page_title') {

            $value = $this->get_page_title();
        } elseif ($key == 'page_slug') {

            $value = $this->get_page_slug();
        } elseif ($key == 'permalink') {

            $value = $this->get_permalink();
        } else {
            // Get values or default if not set
            $value = get_user_meta($this->id, '_vendor_' . $key, true);
        }

        return $value;
    }

    /**
     * generate_term function
     * @access public
     * @return void
     */
    public function generate_term() {
        global $WCMp;
        if (!$this->term_id) {
            $term = wp_insert_term($this->user_data->user_login, $WCMp->taxonomy->taxonomy_name);
            if (!is_wp_error($term)) {
                update_user_meta($this->id, '_vendor_term_id', $term['term_id']);
                update_woocommerce_term_meta($term['term_id'], '_vendor_user_id', $this->id);
                $this->term_id = $term['term_id'];
            } else if ($term->get_error_code() == 'term_exists') {
                update_user_meta($this->id, '_vendor_term_id', $term->get_error_data());
                update_woocommerce_term_meta($term->get_error_data(), '_vendor_user_id', $this->id);
                $this->term_id = $term->get_error_data();
            }
        }
    }

    public function generate_shipping_class() {
        if (!$this->shipping_class_id && apply_filters('wcmp_add_vendor_shipping_class', true)) {
            $shipping_term = wp_insert_term($this->user_data->user_login . '-' . $this->id, 'product_shipping_class');
            if (!is_wp_error($shipping_term)) {
                update_user_meta($this->id, 'shipping_class_id', $shipping_term['term_id']);
                add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_id', $this->id);
                add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_shipping_origin', get_option('woocommerce_default_country'));
            } else if ($shipping_term->get_error_code() == 'term_exists') {
                update_user_meta($this->id, 'shipping_class_id', $shipping_term->get_error_data());
                add_woocommerce_term_meta($shipping_term->get_error_data(), 'vendor_id', $this->id);
                add_woocommerce_term_meta($shipping_term->get_error_data(), 'vendor_shipping_origin', get_option('woocommerce_default_country'));
            }
        }
    }

    /**
     * update_page_title function
     * @access public
     * @param $title
     * @return boolean
     */
    public function update_page_title($title = '') {
        global $WCMp;
        $this->term_id = get_user_meta($this->id, '_vendor_term_id', true);
        if (!$this->term_id) {
            $this->generate_term();
        }
        if (!empty($title) && isset($this->term_id)) {
            if (!is_wp_error(wp_update_term($this->term_id, $WCMp->taxonomy->taxonomy_name, array('name' => $title)))) {
                return true;
            }
        }
        return false;
    }

    /**
     * update_page_slug function
     * @access public
     * @param $slug
     * @return boolean
     */
    public function update_page_slug($slug = '') {
        global $WCMp;
        $this->term_id = get_user_meta($this->id, '_vendor_term_id', true);
        if (!$this->term_id) {
            $this->generate_term();
        }
        if (!empty($slug) && isset($this->term_id)) {
            if (!is_wp_error(wp_update_term($this->term_id, $WCMp->taxonomy->taxonomy_name, array('slug' => $slug)))) {
                return true;
            }
        }
        return false;
    }

    /**
     * set_term_data function
     * @access public
     * @return void
     */
    public function set_term_data() {
        global $WCMp;
        //return if term is already set
        if ($this->term)
            return;

        if (isset($this->term_id)) {
            $term = get_term($this->term_id, $WCMp->taxonomy->taxonomy_name);
            if (!is_wp_error($term)) {
                $this->term = $term;
            }
        }
    }

    /**
     * get_page_title function
     * @access public
     * @return string
     */
    public function get_page_title() {
        $this->set_term_data();
        if ($this->term) {
            return $this->term->name;
        } else {
            return '';
        }
    }

    /**
     * get_page_slug function
     * @access public
     * @return string
     */
    public function get_page_slug() {
        $this->set_term_data();
        if ($this->term) {
            return $this->term->slug;
        } else {
            return '';
        }
    }

    /**
     * get_permalink function
     * @access public
     * @return string
     */
    public function get_permalink() {
        global $WCMp;

        $link = '';
        if (isset($this->term_id)) {
            $link = get_term_link(absint($this->term_id), $WCMp->taxonomy->taxonomy_name);
        }

        return $link;
    }

    /**
     * Get all products belonging to vendor
     * @param  $args (default=array())
     * @return arr Array of product post objects
     */
    public function get_products($args = array()) {
        global $WCMp;
        $products = false;

        $default = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                    'field' => 'id',
                    'terms' => absint($this->term_id)
                )
            )
        );

        $args = wp_parse_args($args, $default);

        $products = get_posts($args);

        return $products;
    }

    /**
     * get_orders function
     * @access public
     * @return array with order id
     */
    public function get_orders($no_of = false, $offset = false, $more_args = false) {
        if (!$no_of) {
            $no_of = -1;
        }
        $vendor_id = $this->term_id;
        $commissions = false;
        $order_id = null;
        if ($vendor_id > 0) {
            $args = array(
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => (int) $no_of,
                'meta_query' => array(
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($vendor_id),
                        'compare' => '='
                    )
                )
            );
            if ($offset) {
                $args['offset'] = $offset;
            }
            if ($more_args) {
                $args = wp_parse_args($more_args, $args);
            }
            $commissions = get_posts($args);
        }

        if ($commissions) {
            $order_id = array();
            foreach ($commissions as $commission) {
                $order_id[$commission->ID] = get_post_meta($commission->ID, '_commission_order_id', true);
            }
        }
        return $order_id;
    }

    /**
     * get_vendor_items_from_order function get items of a order belongs to a vendor
     * @access public
     * @param order_id , vendor term id 
     * @return array with order item detail
     */
    public function get_vendor_items_from_order($order_id, $term_id) {
        $item_dtl = array();
        $order = new WC_Order($order_id);
        if ($order) {
            $items = $order->get_items('line_item');
            if ($items) {
                foreach ($items as $item_id => $item) {
                    $product_id = wc_get_order_item_meta($item_id, '_product_id', true);

                    if ($product_id) {
                        if ($term_id > 0) {
                            $product_vendors = get_wcmp_product_vendors($product_id);
                            if (!empty($product_vendors) && $product_vendors->term_id == $term_id) {
                                $item_dtl[$item_id] = $item;
                            }
                        }
                    }
                }
            }
        }
        return $item_dtl;
    }

    /**
     * get_vendor_items_from_order function get items of a order belongs to a vendor
     * @access public
     * @param order_id , vendor term id 
     * @return array with order item detail
     */
    public function get_vendor_shipping_from_order($order_id, $term_id) {
        $order = new WC_Order($order_id);
        if ($order) {
            $items = $order->get_items('shipping');
        }
        return $items;
    }

    /**
     * get_vendor_orders_by_product function to get orders belongs to a vendor and a product
     * @access public
     * @param product id , vendor term id 
     * @return array with order id
     */
    public function get_vendor_orders_by_product($vendor_term_id, $product_id) {
        $order_dtl = array();
        if ($product_id && $vendor_term_id) {
            $commissions = false;
            $args = array(
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => -1,
                'order' => 'asc',
                'meta_query' => array(
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($vendor_term_id),
                        'compare' => '='
                    ),
                    array(
                        'key' => '_commission_product',
                        'value' => absint($product_id),
                        'compare' => 'LIKE'
                    ),
                ),
            );
            $commissions = get_posts($args);
            if (!empty($commissions)) {
                foreach ($commissions as $commission) {
                    $order_dtl[] = get_post_meta($commission->ID, '_commission_order_id', true);
                }
            }
        }
        return $order_dtl;
    }

    /**
     * get_vendor_commissions_by_product function to get orders belongs to a vendor and a product
     * @access public
     * @param product id , vendor term id 
     * @return array with order id
     */
    public function get_vendor_commissions_by_product($order_id, $product_id) {
        $order_dtl = array();
        if ($product_id && $order_id) {
            $commissions = false;
            $args = array(
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => -1,
                'order' => 'asc',
                'meta_query' => array(
                    array(
                        'key' => '_commission_order_id',
                        'value' => absint($order_id),
                        'compare' => '='
                    ),
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($this->term_id),
                        'compare' => '='
                    ),
                ),
            );
            $commissions = get_posts($args);

            if (!empty($commissions)) {
                foreach ($commissions as $commission) {
                    $order_dtl[] = $commission->ID;
                }
            }
        }
        return $order_dtl;
    }

    /**
     * vendor_order_item_table function to get the html of item table of a vendor.
     * @access public
     * @param order id , vendor term id 
     */
    public function vendor_order_item_table($order, $vendor_id, $is_ship = false) {
        global $WCMp;
        require_once ( 'class-wcmp-calculate-commission.php' );
        $commission_obj = new WCMp_Calculate_Commission();
        $vendor_items = $this->get_vendor_items_from_order($order->get_id(), $vendor_id);
        foreach ($vendor_items as $item_id => $item) {
            $_product = apply_filters('wcmp_woocommerce_order_item_product', $order->get_product_from_item($item), $item);
            ?>
            <tr class="">
                <td scope="col" style="text-align:left; border: 1px solid #eee;" class="product-name">
                    <?php
                    if ($_product && !$_product->is_visible()) {
                        echo apply_filters('wcmp_woocommerce_order_item_name', $item['name'], $item);
                    } else {
                        echo apply_filters('woocommerce_order_item_name', sprintf('<a href="%s">%s</a>', get_permalink($item['product_id']), $item['name']), $item);
                    }
                    wc_display_item_meta($item);
                    ?>
                </td>
                <td scope="col" style="text-align:left; border: 1px solid #eee;">	
                    <?php
                    echo $item['qty'];
                    ?>
                </td>
                <td scope="col" style="text-align:left; border: 1px solid #eee;">
                    <?php
                    $variation_id = 0;
                    if (isset($item['variation_id']) && !empty($item['variation_id'])) {
                        $variation_id = $item['variation_id'];
                    }
                    $product_id = $item['product_id'];
                    $commission_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id(), 'product_id' => $product_id, 'variation_id' => $variation_id, 'order_item_id' => $item_id));
                    if ($is_ship) {
                        echo $order->get_formatted_line_subtotal($item);
                    } else {
                        echo wc_price($commission_amount['commission_amount']);
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
    }

    /**
     * plain_vendor_order_item_table function to get the plain html of item table of a vendor.
     * @access public
     * @param order id , vendor term id 
     */
    public function plain_vendor_order_item_table($order, $vendor_id, $is_ship = false) {
        global $WCMp;
        require_once ( 'class-wcmp-calculate-commission.php' );
        $commission_obj = new WCMp_Calculate_Commission();
        $vendor_items = $this->get_vendor_items_from_order($order->get_id(), $vendor_id);
        foreach ($vendor_items as $item_id => $item) {
            $_product = apply_filters('woocommerce_order_item_product', $order->get_product_from_item($item), $item);

            // Title
            echo apply_filters('woocommerce_order_item_name', $item['name'], $item);


            // Variation
            wc_display_item_meta($item);

            // Quantity
            echo "\n" . sprintf(__('Quantity: %s', 'dc-woocommerce-multi-vendor'), $item['qty']);
            $variation_id = 0;
            if (isset($item['variation_id']) && !empty($item['variation_id'])) {
                $variation_id = $item['variation_id'];
            }
            $product_id = $item['product_id'];
            $commission_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id(), 'product_id' => $product_id, 'variation_id' => $variation_id, 'order_item_id' => $item_id));
            if ($is_ship)
                echo "\n" . sprintf(__('Total: %s', 'dc-woocommerce-multi-vendor'), $order->get_formatted_line_subtotal($item));
            else
                echo "\n" . sprintf(__('Commission: %s', 'dc-woocommerce-multi-vendor'), wc_price($commission_amount['commission_amount']));

            echo "\n\n";
        }
    }

    /**
     * wcmp_get_vendor_part_from_order function to get vendor due from an order.
     * @access public
     * @param order , vendor term id 
     */
    public function wcmp_get_vendor_part_from_order($order, $vendor_term_id) {
        global $WCMp;
        $order_id = $order->get_id();
        $vendor = get_wcmp_vendor_by_term($vendor_term_id);
        $vendor_part = get_wcmp_vendor_order_amount(array('order_id' => $order_id, 'vendor_id' => $vendor->id));
        $vendor_due = array(
            'commission' => $vendor_part['commission_amount'],
            'shipping' => $vendor_part['shipping_amount'],
            'tax' => $vendor_part['tax_amount'],
            'shipping_tax' => $vendor_part['shipping_tax_amount']
        );
        return apply_filters('vendor_due_per_order', $vendor_due, $order, $vendor_term_id);
    }

    /**
     * wcmp_vendor_get_total_amount_due function to get vendor due from an order.
     * @access public
     * @param order , vendor term id 
     */
    public function wcmp_vendor_get_total_amount_due() {
        global $WCMp;
        $vendor = get_wcmp_vendor_by_term($this->term_id);
        $vendor_orders = get_wcmp_vendor_order_amount(array('vendor_id' => $vendor->id, 'commission_status' => 'unpaid'));
        return (float) ($vendor_orders['commission_amount'] + $vendor_orders['shipping_amount'] + $vendor_orders['tax_amount'] + $vendor_orders['shipping_tax_amount']);
    }

    /**
     * wcmp_get_vendor_part_from_order function to get vendor due from an order.
     * @access public
     * @param order , vendor term id 
     */
    public function wcmp_vendor_transaction() {
        global $WCMp;
        $transactions = $paid_array = array();
        $vendor = get_wcmp_vendor_by_term($this->term_id);
        if ($this->term_id > 0) {
            $args = array(
                'post_type' => 'wcmp_transaction',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => -1,
                'post_author' => $vendor->id
            );
            $transactions = get_posts($args);
        }

        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                $paid_array[] = $transaction->ID;
            }
        }
        return $paid_array;
    }

    /**
     * wcmp_vendor_get_order_item_totals function to get order item table of a vendor.
     * @access public
     * @param order id , vendor term id 
     */
    public function wcmp_vendor_get_order_item_totals($order, $term_id) {
        global $WCMp;
        $vendor = get_wcmp_vendor_by_term($term_id);
        $vendor_totals = get_wcmp_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order));
        return array(
            'commission_subtotal' => array(
                'label' => __('Commission Subtotal:', 'dc-woocommerce-multi-vendor'),
                'value' => wc_price($vendor_totals['commission_amount'])
            ),
            'tax_subtotal' => array(
                'label' => __('Tax Subtotal:', 'dc-woocommerce-multi-vendor'),
                'value' => wc_price($vendor_totals['tax_amount'] + $vendor_totals['shipping_tax_amount'])
            ),
            'shipping_subtotal' => array(
                'label' => __('Shipping Subtotal:', 'dc-woocommerce-multi-vendor'),
                'value' => wc_price($vendor_totals['shipping_amount'])
            ),
            'total' => array(
                'label' => __('Total:', 'dc-woocommerce-multi-vendor'),
                'value' => wc_price($vendor_totals['commission_amount'] + $vendor_totals['tax_amount'] + $vendor_totals['shipping_tax_amount'] + $vendor_totals['shipping_amount'])
            )
        );
    }

    /**
     * @deprecated since version 2.6.6
     * @param object | id $order
     * @param object | id $product
     * @return array
     */
    public function get_vendor_total_tax_and_shipping($order, $product = false) {
        _deprecated_function('get_vendor_total_tax_and_shipping', '2.6.6', 'get_wcmp_vendor_order_amount');
        return get_wcmp_vendor_order_amount(array('vendor_id' => $this->id, 'order_id' => $order, 'product_id' => $product));
    }

    public function is_shipping_enable() {
        global $WCMp;
        $is_enable = false;
        $is_capability_shipping_tab_enable = get_wcmp_vendor_settings('shipping', 'capabilities', 'product');
        if ($WCMp->vendor_caps->vendor_payment_settings('give_shipping') && !get_user_meta($this->id, '_vendor_give_shipping', true) && $is_capability_shipping_tab_enable == 'Enable' && wc_shipping_enabled()) {
            $is_enable = true;
        }
        return apply_filters('is_wcmp_vendor_shipping_enable', $is_enable);
    }

    public function is_shipping_tab_enable() {
        $is_enable_flat_rate = false;
        $raw_zones = WC_Shipping_Zones::get_zones();
        $raw_zones[] = array('id' => 0);
        foreach ($raw_zones as $raw_zone) {
            $zone = new WC_Shipping_Zone($raw_zone['id']);
            $raw_methods = $zone->get_shipping_methods();
            foreach ($raw_methods as $raw_method) {
                if ($raw_method->id == 'flat_rate') {
                    $is_enable_flat_rate = true;
                }
            }
        }
        $is_shipping_flat_rate_enable = false;
        if ($this->is_shipping_enable() && $is_enable_flat_rate) {
            $is_shipping_flat_rate_enable = true;
        }
        return apply_filters('is_wcmp_vendor_shipping_tab_enable', $is_shipping_flat_rate_enable, $this->is_shipping_enable());
    }

    /**
     * format_order_details function
     * @access public
     * @param order id , product_id
     * @return array of order details
     */
    public function format_order_details($orders, $product_id) {
        $body = $items = array();
        $product = wc_get_product($product_id)->get_title();
        foreach (array_unique($orders) as $order) {
            $i = $order;
            $order = new WC_Order($i);
            $body[$i] = array(
                'order_number' => $order->get_order_number(),
                'product' => $product,
                'name' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                'address' => $order->get_shipping_address_1(),
                'city' => $order->get_shipping_city(),
                'state' => $order->get_shipping_state(),
                'zip' => $order->get_shipping_postcode(),
                'email' => $order->get_billing_email(),
                'date' => $order->get_date_created(),
                'comments' => wptexturize($order->get_customer_note()),
            );

            $items[$i]['total_qty'] = 0;
            foreach ($order->get_items() as $line_id => $item) {
                if ($item['product_id'] != $product_id && $item['variation_id'] != $product_id) {
                    continue;
                }

                $items[$i]['items'][] = $item;
                $items[$i]['total_qty'] += $item['qty'];
            }
        }

        return array('body' => $body, 'items' => $items, 'product_id' => $product_id);
    }

}
?>