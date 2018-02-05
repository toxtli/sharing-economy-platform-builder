<?php

/**
 * WCmp Vendor Order Page 
 * 
 * @author WC Marketplace
 * @package WCMp 
 * @extends WP_List_Table
 */
if (!class_exists('WP_List_Table'))
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

class WCMp_Vendor_Order_Page extends WP_List_Table {

    public $index;
    public $vendor;

    function __construct() {
        global $status, $page;

        $this->index = 0;
        $this->vendor = get_wcmp_vendor(get_current_vendor_id());

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'order',
            'plural' => 'orders',
            'ajax' => false,
            'screen' => 'dc-vendor-orders',
        ));
    }

    /**
     * Default column function
     *
     * @param object $item
     * @param mixed  $column_name
     *
     * @return void
     */
    function column_default($item, $column_name) {
        global $wpdb;

        switch ($column_name) {
            case 'order_id' :
                return $item->order_id;
            case 'customer' :
                return $item->customer;
            case 'products' :
                return $item->products;
            case 'total' :
                return $item->total;
            case 'date' :
                return $item->date;
            case 'status' :
                return $item->status;
        }

        do_action( "wcmp_manage_dc-vendor-orders_column_data", $item, $column_name );
    }

    /**
     * column_cb function
     *
     * @param mixed $item
     * @return void
     */
    function column_cb($item) {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', 'order_id', $item->order_id);
    }

    /**
     * Get order columns
     *
     * @return void
     */
    function get_columns() {
        global $WCMp;
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'order_id' => __('Order ID', 'dc-woocommerce-multi-vendor'),
            'customer' => __('Customer', 'dc-woocommerce-multi-vendor'),
            'products' => __('Products', 'dc-woocommerce-multi-vendor'),
            'total' => __('Total', 'dc-woocommerce-multi-vendor'),
            'date' => __('Date', 'dc-woocommerce-multi-vendor'),
        );
        if ($this->vendor->is_shipping_enable()) {
            $columns['status'] = __('Shipped', 'dc-woocommerce-multi-vendor');
        }

        return apply_filters( "wcmp_manage_dc-vendor-orders_columns", $columns );
    }

    /**
     * Sortable columns
     *
     * @return void
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'order_id' => array('order_id', false),
            'total' => array('total', false),
            'status' => array('status', false),
        );

        return apply_filters( "wcmp_manage_dc-vendor-orders_sortable_columns", $sortable_columns );
    }

    /**
     * Get bulk actions
     *
     * @return void
     */
    function get_bulk_actions() {
        global $WCMp;
        $actions = array();
        if ($this->vendor->is_shipping_enable()) {
            $actions['mark_shipped'] = __('Mark as Shipped', 'dc-woocommerce-multi-vendor');
        }
        return apply_filters( "wcmp_bulk_actions-dc-vendor-orders", $actions );
    }

    /**
     * Process bulk actions
     *
     * @return void
     */
    function process_bulk_action() {
        global $WCMp;
        if (!isset($_GET['order_id'])) {
            return;
        }

        $items = array_map('intval', $_GET['order_id']);

        switch ($this->current_action()) {
            case 'mark_shipped':

                $result = $this->mark_shipped($items);

                if ($result)
                    echo '<div class="updated"><p>' . __('Orders Marked as shipped.', 'dc-woocommerce-multi-vendor') . '</p></div>';
                break;

            default:
                // code...
                break;
        }

        do_action( "wcmp_process_bulk_action-dc-vendor-orders", $items, $this->current_action());
    }

    /**
     *  Mark orders as shipped 
     *
     * @param unknown $ids (optional)
     * @return void
     */
    public function mark_shipped($ids = array()) {
        global $woocommerce, $WCMp, $wpdb;

        $user_id = get_current_vendor_id();
        $vendor = get_wcmp_vendor($user_id);

        if (!empty($ids)) {
            foreach ($ids as $order_id) {
                $shippers = (array) get_post_meta($order_id, 'dc_pv_shipped', true);
                if (!in_array($user_id, $shippers)) {
                    $shippers[] = $user_id;
                    $mails = WC()->mailer()->emails['WC_Email_Notify_Shipped'];
                    if (!empty($mails)) {
                        $customer_email = get_post_meta($order_id, '_billing_email', true);
                        $mails->trigger($order_id, $customer_email, $vendor->term_id);
                    }
                    do_action('wcmp_vendors_vendor_ship', $order_id, $vendor->term_id);
                    array_push($shippers, $user_id);
                    if (!empty($shippers)) {
                        array_unique($shippers);
                    }
                    update_post_meta($order_id, 'dc_pv_shipped', $shippers);
                    $wpdb->query("UPDATE `{$wpdb->prefix}wcmp_vendor_orders` SET shipping_status = 1 WHERE order_id =" . $order_id . " AND vendor_id = " . $vendor->id);
                }
                $order = new WC_Order($order_id);
                $comment_id = $order->add_order_note('Vendor ' . $vendor->user_data->display_name . ' has shipped his part of order to customer.', true);
                add_comment_meta($comment_id, '_vendor_id', $user_id);
            }
            return true;
        }
        return false;
    }

    /**
     *  Get current vendor orders
     *
     * @return array
     */
    function wcmp_get_vendor_orders() {
        global $WCMp;
        $user_id = get_current_vendor_id();
        $vendor = get_wcmp_vendor($user_id);
        $vendor = apply_filters('wcmp_get_vendor_orders_vendor', $vendor);
        $orders = array();

        $vendor_orders_array = $vendor->get_orders();
        if (!$vendor_orders_array)
            $vendor_orders_array = array();
        $_orders = array_unique($vendor_orders_array);

        if (!empty($_orders)) {
            foreach ($_orders as $order_id) {
                $order = new WC_Order($order_id);
                $valid_items = $vendor->get_vendor_items_from_order($order->get_id(), $vendor->term_id);

                $products = '';
                foreach ($valid_items as $key => $item) {
                    $products .= '<strong>' . $item['qty'] . ' x ' . $item['name'] . '</strong><br />';
                }

                $shippers = (array) get_post_meta($order->get_id(), 'dc_pv_shipped', true);
                $shipped = in_array($user_id, $shippers) ? 'Yes' : 'No';

                if ($order->get_id() && $vendor->term_id) {

                    $vendor_orders_amount = get_wcmp_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order->get_id()));
                    $commission_total = $vendor_orders_amount['commission_amount'] + $vendor_orders_amount['shipping_amount'] + $vendor_orders_amount['tax_amount'] + $vendor_orders_amount['shipping_tax_amount'];
                }

                $extra_checkout_fields_for_brazil_active_datas = '';
                if (WC_Dependencies_Product_Vendor::woocommerce_extra_checkout_fields_for_brazil_active_check()) {
                    $settings = get_option('wcbcf_settings');
                    if (0 != $settings['person_type']) {

                        // Person type information.
                        if (( 1 == $order->billing_persontype && 1 == $settings['person_type'] ) || 2 == $settings['person_type']) {
                            $extra_checkout_fields_for_brazil_active_datas .= '<strong>' . __('CPF', 'dc-woocommerce-multi-vendor') . ': </strong>' . esc_html($order->billing_cpf) . '<br />';

                            if (isset($settings['rg'])) {
                                $extra_checkout_fields_for_brazil_active_datas .= '<strong>' . __('RG', 'dc-woocommerce-multi-vendor') . ': </strong>' . esc_html($order->billing_rg) . '<br />';
                            }
                        }

                        if (( 2 == $order->billing_persontype && 1 == $settings['person_type'] ) || 3 == $settings['person_type']) {
                            $extra_checkout_fields_for_brazil_active_datas .= '<strong>' . __('Company Name', 'dc-woocommerce-multi-vendor') . ': </strong>' . esc_html($order->get_billing_company()) . '<br />';
                            $extra_checkout_fields_for_brazil_active_datas .= '<strong>' . __('CNPJ', 'dc-woocommerce-multi-vendor') . ': </strong>' . esc_html($order->billing_cnpj) . '<br />';

                            if (isset($settings['ie'])) {
                                $extra_checkout_fields_for_brazil_active_datas .= '<strong>' . __('State Registration', 'dc-woocommerce-multi-vendor') . ': </strong>' . esc_html($order->billing_ie) . '<br />';
                            }
                        }
                    } else {
                        $extra_checkout_fields_for_brazil_active_datas .= '<strong>' . __('Company', 'dc-woocommerce-multi-vendor') . ': </strong>' . esc_html($order->get_billing_company()) . '<br />';
                    }

                    if (isset($settings['birthdate_sex'])) {

                        // Birthdate information.
                        $extra_checkout_fields_for_brazil_active_datas .= '<strong>' . __('Birthdate', 'dc-woocommerce-multi-vendor') . ': </strong>' . esc_html($order->billing_birthdate) . '<br />';

                        // Sex Information.
                        $extra_checkout_fields_for_brazil_active_datas .= '<strong>' . __('Sex', 'dc-woocommerce-multi-vendor') . ': </strong>' . esc_html($order->billing_sex) . '<br />';
                    }

                    $extra_checkout_fields_for_brazil_active_datas .= '<strong>' . __('Phone', 'dc-woocommerce-multi-vendor') . ': </strong>' . esc_html($order->get_billing_phone()) . '<br />';

                    // Cell Phone Information.
                    if (!empty($order->billing_cellphone)) {
                        $extra_checkout_fields_for_brazil_active_datas .= '<strong>' . __('Cell Phone', 'dc-woocommerce-multi-vendor') . ': </strong>' . esc_html($order->billing_cellphone) . '<br />';
                    }
                }

                $customer_user_name = get_post_meta($order->get_id(), '_shipping_first_name', true) . ' ' . get_post_meta($order->get_id(), '_shipping_last_name', true);
                $order_items = array();
                $order_items['order_id'] = $order->get_id();
                $order_items['customer'] = $customer_user_name . '<br>' . apply_filters('wcmp_dashboard_google_maps_link', '<a target="_blank" href="' . esc_url('http://maps.google.com/maps?&q=' . urlencode(esc_html(preg_replace('#<br\s*/?>#i', ', ', $order->get_formatted_shipping_address()))) . '&z=16') . '">' . esc_html(preg_replace('#<br\s*/?>#i', ', ', $order->get_formatted_shipping_address())) . '</a><br />' . $extra_checkout_fields_for_brazil_active_datas);
                $order_items['products'] = $products;
                $order_items['total'] = wc_price($commission_total);
                $order_items['date'] = date_i18n(wc_date_format(), strtotime($order->get_date_created()));
                $order_items['status'] = $shipped;

                $orders[] = (object) $order_items;
            }
        }
        return $orders;
    }

    /**
     * Prepare order page items
     *
     */
    function wcmp_prepare_order_page_items() {

        $this->_column_headers = $this->get_column_info();

        $this->process_bulk_action();

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($this->wcmp_get_vendor_orders());

        $found_data = array_slice($this->wcmp_get_vendor_orders(), ( ( $current_page - 1 ) * $per_page), $per_page);
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page
        ));
        $this->items = $found_data;
    }

}
