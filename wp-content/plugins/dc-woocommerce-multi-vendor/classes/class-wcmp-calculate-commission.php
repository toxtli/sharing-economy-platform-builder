<?php

/**
 * WCMp Calculate Commission Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Calculate_Commission {

    private $completed_statuses;
    private $reverse_statuses;

    public function __construct() {

        // WC order complete statues
        $this->completed_statuses = apply_filters('wcmp_completed_commission_statuses', array('completed', 'processing'));

        // WC order reverse statues
        $this->reverse_statuses = apply_filters('wcmp_reversed_commission_statuses', array('pending', 'refunded', 'cancelled', 'failed'));

        $this->wcmp_order_reverse_action();
        $this->wcmp_order_complete_action();
    }

    /**
     * Add action hook when an order is reversed
     *
     * @author Dualcube
     * @return void
     */
    public function wcmp_order_reverse_action() {
        foreach ($this->completed_statuses as $cmpltd) {
            foreach ($this->reverse_statuses as $revsed) {
                add_action("woocommerce_order_status_{$cmpltd}_to_{$revsed}", array($this, 'wcmp_due_commission_reverse'));
            }
        }
    }

    /**
     * WCMp reverse vendor due commission for an order
     *
     * @param int $order_id
     */
    public function wcmp_due_commission_reverse($order_id) {
        $args = array(
            'post_type' => 'dc_commission',
            'post_status' => array('publish', 'private'),
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_commission_order_id',
                    'value' => absint($order_id),
                    'compare' => '='
                )
            )
        );
        $commissions = get_posts($args);
        if ($commissions) {
            foreach ($commissions as $commission) {
                update_post_meta($commission->ID, '_paid_status', 'reverse');
            }
        }
    }

    /**
     * Add action hook only when an order manually updated
     *
     * @author Dualcube
     * @return void
     */
    public function wcmp_order_complete_action() {
        foreach ($this->completed_statuses as $cmpltd) {
            add_action('woocommerce_order_status_' . $cmpltd, array($this, 'wcmp_process_commissions'));
        }
    }

    /**
     * Process commission
     * @param  int $order_id ID of order for commission
     * @return void
     */
    public function wcmp_process_commissions($order_id) {
        global $wpdb;
        // Only process commissions once
        $order = new WC_Order($order_id);
        $processed = get_post_meta($order_id, '_commissions_processed', true);
        $order_processed = get_post_meta($order_id, '_wcmp_order_processed', true);
        if(!$order_processed){
            wcmp_process_order($order_id, $order);
        }
        $commission_ids = get_post_meta($order_id, '_commission_ids', true) ? get_post_meta($order_id, '_commission_ids', true) : array();
        if (!$processed) {
            $vendor_array = array();
            $items = $order->get_items('line_item');
            foreach ($items as $item_id => $item) {
                $vendor_id = wc_get_order_item_meta($item_id, '_vendor_id', true);
                if (!$vendor_id) {
                    $is_vendor_product = get_wcmp_product_vendors($item['product_id']);
                    if (!$is_vendor_product) {
                        continue;
                    }
                }
                $product_id = $item['product_id'];
                $variation_id = isset($item['variation_id']) && !empty($item['variation_id']) ? $item['variation_id'] : 0;
                if ($vendor_id) {
                    $vendor_obj = get_wcmp_vendor($vendor_id);
                } else {
                    $vendor_obj = get_wcmp_product_vendors($product_id);
                }
                if (in_array($vendor_obj->term_id, $vendor_array)) {
                    if ($variation_id) {
                        $query_id = $variation_id;
                    } else {
                        $query_id = $product_id;
                    }
                    $commission = $vendor_obj->get_vendor_commissions_by_product($order_id, $query_id);
                    $previous_ids = get_post_meta($commission[0], '_commission_product', true);
                    if (is_array($previous_ids)) {
                        array_push($previous_ids, $query_id);
                    }
                    update_post_meta($commission[0], '_commission_product', $previous_ids);

                    $item_commission = $this->get_item_commission($product_id, $variation_id, $item, $order_id, $item_id);

                    $wpdb->query("UPDATE `{$wpdb->prefix}wcmp_vendor_orders` SET commission_id = " . $commission[0] . ", commission_amount = '" . $item_commission . "' WHERE order_id =" . $order_id . " AND order_item_id = " . $item_id . " AND product_id = " . $product_id);
                } else {
                    $vendor_id = wc_get_order_item_meta($item_id, '_vendor_id', true);
                    if ($product_id) {
                        $commission_id = $this->record_commission($product_id, $order_id, $variation_id, $order, $vendor_obj, $item_id, $item);
                        if ($commission_id) {
                            $commission_ids[] = $commission_id;
                            update_post_meta($order_id, '_commission_ids', $commission_ids);
                        }
                        $vendor_array[] = $vendor_obj->term_id;
                    }
                }
            }
            $email_admin = WC()->mailer()->emails['WC_Email_Vendor_New_Order'];
            $email_admin->trigger($order_id);
        }
        // Mark commissions as processed
        update_post_meta($order_id, '_commissions_processed', 'yes');
        if (!empty($commission_ids) && is_array($commission_ids)) {
            foreach ($commission_ids as $commission_id) {
                $commission_amount = get_wcmp_vendor_order_amount(array('commission_id' => $commission_id, 'order_id' => $order_id));
                update_post_meta($commission_id, '_commission_amount', (float) $commission_amount['commission_amount']);
            }
        }
    }

    /**
     * Record individual commission
     * @param  int $product_id ID of product for commission
     * @param  int $line_total Line total of product
     * @return void
     */
    public function record_commission($product_id = 0, $order_id = 0, $variation_id = 0, $order, $vendor, $item_id = 0, $item) {
        if ($product_id > 0) {
            if ($vendor) {
                $vendor_due = $vendor->wcmp_get_vendor_part_from_order($order, $vendor->term_id);
                return $this->create_commission($vendor->term_id, $product_id, $vendor_due, $order_id, $variation_id, $item_id, $item, $order);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Create new commission post
     *
     * @param  int $vendor_id  ID of vendor for commission
     * @param  int $product_id ID of product for commission
     * @param  int $amount     Commission total
     * @return void
     */
    public function create_commission($vendor_id = 0, $product_id = 0, $amount = 0, $order_id = 0, $variation_id = 0, $item_id = 0, $item, $order) {
        global $wpdb;
        if ($vendor_id == 0) {
            return false;
        }
        $commission_data = array(
            'post_type' => 'dc_commission',
            'post_title' => sprintf(__('Commission - %s', 'dc-woocommerce-multi-vendor'), strftime(_x('%B %e, %Y @ %I:%M %p', 'Commission date parsed by strftime', 'dc-woocommerce-multi-vendor'))),
            'post_status' => 'private',
            'ping_status' => 'closed',
            'post_excerpt' => '',
            'post_author' => 1
        );
        $commission_id = wp_insert_post($commission_data);
        // Add meta data
        if ($vendor_id > 0) {
            update_post_meta($commission_id, '_commission_vendor', $vendor_id);
        }
        if ($variation_id > 0) {
            update_post_meta($commission_id, '_commission_product', array($variation_id));
        } else {
            update_post_meta($commission_id, '_commission_product', array($product_id));
        }
        $shipping = (float) $amount['shipping'];
        $tax = (float) ($amount['tax'] + $amount['shipping_tax']);
        update_post_meta($commission_id, '_shipping', $shipping);
        update_post_meta($commission_id, '_tax', $tax);
        if ($order_id > 0) {
            update_post_meta($commission_id, '_commission_order_id', $order_id);
        }
        // Mark commission as unpaid
        update_post_meta($commission_id, '_paid_status', 'unpaid');
        $item_commission = $this->get_item_commission($product_id, $variation_id, $item, $order_id, $item_id);
        $wpdb->query("UPDATE `{$wpdb->prefix}wcmp_vendor_orders` SET commission_id = " . $commission_id . ", commission_amount = '" . $item_commission . "' WHERE order_id =" . $order_id . " AND order_item_id = " . $item_id . " AND product_id = " . $product_id);
        do_action('wcmp_vendor_commission_created', $commission_id);
        return $commission_id;
    }

    /**
     * Get vendor commission per item for an order
     *
     * @param int $product_id
     * @param int $variation_id
     * @param array $item
     * @param int $order_id
     *
     * @return $commission_amount
     */
    public function get_item_commission($product_id, $variation_id, $item, $order_id, $item_id = '') {
        global $WCMp;
        $order = new WC_Order($order_id);
        $amount = 0;
        $commission = array();
        $product_value_total = 0;
        if (isset($WCMp->vendor_caps->payment_cap['commission_include_coupon'])) {
            $line_total = $order->get_item_total($item, false, false) * $item['qty'];
        } else {
            $line_total = $order->get_item_subtotal($item, false, false) * $item['qty'];
        }
        if ($product_id) {
            $vendor_id = wc_get_order_item_meta($item_id, '_vendor_id', true);
            if ($vendor_id) {
                $vendor = get_wcmp_vendor($vendor_id);
            } else {
                $vendor = get_wcmp_product_vendors($product_id);
            }
            if ($vendor) {
                $commission = $this->get_commission_amount($product_id, $vendor->term_id, $variation_id, $item_id, $order);
                $commission = apply_filters('wcmp_get_commission_amount', $commission, $product_id, $vendor->term_id, $variation_id, $item_id, $order);
                if (!empty($commission)) {
                    if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {
                        $amount = (float) $line_total * ( (float) $commission['commission_val'] / 100 ) + (float) $commission['commission_fixed'];
                    } else if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {
                        $amount = (float) $line_total * ( (float) $commission['commission_val'] / 100 ) + ((float) $commission['commission_fixed'] * $item['qty']);
                    } else if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'percent') {
                        $amount = (float) $line_total * ( (float) $commission['commission_val'] / 100 );
                    } else if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed') {
                        $amount = (float) $commission['commission_val'] * $item['qty'];
                    }
                    if (isset($WCMp->vendor_caps->payment_cap['revenue_sharing_mode'])) {
                        if ($WCMp->vendor_caps->payment_cap['revenue_sharing_mode'] == 'admin') {
                            $amount = (float) $line_total - (float) $amount;
                            if ($amount < 0) {
                                $amount = 0;
                            }
                        }
                    }
                    if ($variation_id == 0 || $variation_id == '') {
                        $product_id_for_value = $product_id;
                    } else {
                        $product_id_for_value = $variation_id;
                    }
                    $product_value = get_post_meta($product_id_for_value, '_price', true);
                    if (empty($product_value)) {
                        $product_value = 0;
                    }
                    $product_value_total += ($product_value * $item['qty']);
                    if ($amount > $product_value_total) {
                        $amount = $product_value_total;
                    }
                    return apply_filters('vendor_commission_amount', $amount);
                }
            }
        }
        return apply_filters('vendor_commission_amount', $amount);
    }

    /**
     * Get assigned commission percentage
     *
     * @param  int $product_id ID of product
     * @param  int $vendor_id  ID of vendor
     * @return int             Relevent commission percentage
     */
    public function get_commission_amount($product_id = 0, $vendor_id = 0, $variation_id = 0, $item_id = '', $order = array()) {
        global $WCMp;

        $data = array();
        if ($product_id > 0 && $vendor_id > 0) {
            $vendor_idd = wc_get_order_item_meta($item_id, '_vendor_id', true);
            if ($vendor_idd) {
                $vendor = get_wcmp_vendor($vendor_idd);
            } else {
                $vendor = get_wcmp_product_vendors($product_id);
            }
            if ($vendor->term_id == $vendor_id) {

                if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {

                    if ($variation_id > 0) {
                        $data['commission_val'] = get_post_meta($variation_id, '_product_vendors_commission_percentage', true);
                        $data['commission_fixed'] = get_post_meta($variation_id, '_product_vendors_commission_fixed_per_trans', true);
                        if (empty($data)) {
                            $data['commission_val'] = get_post_meta($product_id, '_commission_percentage_per_product', true);
                            $data['commission_fixed'] = get_post_meta($product_id, '_commission_fixed_with_percentage', true);
                        }
                    } else {
                        $data['commission_val'] = get_post_meta($product_id, '_commission_percentage_per_product', true);
                        $data['commission_fixed'] = get_post_meta($product_id, '_commission_fixed_with_percentage', true);
                    }
                    if (!empty($data['commission_val'])) {
                        return $data; // Use product commission percentage first
                    } else {
                        $vendor_commission_percentage = 0;
                        $vendor_commission_percentage = get_user_meta($vendor->id, '_vendor_commission_percentage', true);
                        $vendor_commission_fixed_with_percentage = 0;
                        $vendor_commission_fixed_with_percentage = get_user_meta($vendor->id, '_vendor_commission_fixed_with_percentage', true);
                        if ($vendor_commission_percentage > 0) {
                            return array('commission_val' => $vendor_commission_percentage, 'commission_fixed' => $vendor_commission_fixed_with_percentage); // Use vendor user commission percentage 
                        } else {
                            if (isset($WCMp->vendor_caps->payment_cap['default_percentage'])) {
                                return array('commission_val' => $WCMp->vendor_caps->payment_cap['default_percentage'], 'commission_fixed' => $WCMp->vendor_caps->payment_cap['fixed_with_percentage']);
                            } else
                                return false;
                        }
                    }
                } else if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {

                    if ($variation_id > 0) {
                        $data['commission_val'] = get_post_meta($variation_id, '_product_vendors_commission_percentage', true);
                        $data['commission_fixed'] = get_post_meta($variation_id, '_product_vendors_commission_fixed_per_qty', true);
                        if (!$data) {
                            $data['commission_val'] = get_post_meta($product_id, '_commission_percentage_per_product', true);
                            $data['commission_fixed'] = get_post_meta($product_id, '_commission_fixed_with_percentage_qty', true);
                        }
                    } else {
                        $data['commission_val'] = get_post_meta($product_id, '_commission_percentage_per_product', true);
                        $data['commission_fixed'] = get_post_meta($product_id, '_commission_fixed_with_percentage_qty', true);
                    }
                    if (!empty($data['commission_val'])) {
                        return $data; // Use product commission percentage first
                    } else {
                        $vendor_commission_percentage = 0;
                        $vendor_commission_fixed_with_percentage = 0;
                        $vendor_commission_percentage = get_user_meta($vendor->id, '_vendor_commission_percentage', true);
                        $vendor_commission_fixed_with_percentage = get_user_meta($vendor->id, '_vendor_commission_fixed_with_percentage_qty', true);
                        if ($vendor_commission_percentage > 0) {
                            return array('commission_val' => $vendor_commission_percentage, 'commission_fixed' => $vendor_commission_fixed_with_percentage); // Use vendor user commission percentage 
                        } else {
                            if (isset($WCMp->vendor_caps->payment_cap['default_percentage'])) {
                                return array('commission_val' => $WCMp->vendor_caps->payment_cap['default_percentage'], 'commission_fixed' => $WCMp->vendor_caps->payment_cap['fixed_with_percentage_qty']);
                            } else
                                return false;
                        }
                    }
                } else {
                    if ($variation_id > 0) {
                        $data['commission_val'] = get_post_meta($variation_id, '_product_vendors_commission', true);
                        if (!$data) {
                            $data['commission_val'] = get_post_meta($product_id, '_commission_per_product', true);
                        }
                    } else {
                        $data['commission_val'] = get_post_meta($product_id, '_commission_per_product', true);
                    }
                    if (!empty($data['commission_val'])) {
                        return $data; // Use product commission percentage first
                    } else {
                        $vendor_commission = get_user_meta($vendor->id, '_vendor_commission', true);
                        if ($vendor_commission) {
                            return array('commission_val' => $vendor_commission); // Use vendor user commission percentage 
                        } else {
                            return isset($WCMp->vendor_caps->payment_cap['default_commission']) ? array('commission_val' => $WCMp->vendor_caps->payment_cap['default_commission']) : false; // Use default commission
                        }
                    }
                }
            }
        }
        return false;
    }

}