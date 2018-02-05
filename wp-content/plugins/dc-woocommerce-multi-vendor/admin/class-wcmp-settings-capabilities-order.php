<?php

class WCMp_Settings_Capabilities_Order {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmp_{$this->tab}_{$this->subsection}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;
        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "vendor_order" => array(
                    "title" => __('Order Notes', 'dc-woocommerce-multi-vendor'),
                    "fields" => array(
                        "is_vendor_view_comment" => array('title' => __('View Order Note', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_vendor_view_comment', 'label_for' => 'is_vendor_view_comment', 'name' => 'is_vendor_view_comment', 'text' => __('Vendor can see order notes.', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable'), // Checkbox
                        "is_vendor_submit_comment" => array('title' => __('Add Order Note', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_vendor_submit_comment', 'label_for' => 'is_vendor_submit_comment', 'name' => 'is_vendor_submit_comment', 'text' => __('Vendor can add order notes.', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable'), // Checkbox
                    )
                ),
                "vendor_order_export" => array("title" => __('Order Export Data / Report Export Data', 'dc-woocommerce-multi-vendor'), // Section one
                    "fields" => array(
                        "is_order_csv_export" => array('title' => __('Allow vendors to export orders.', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_order_csv_export', 'label_for' => 'is_order_csv_export', 'name' => 'is_order_csv_export', 'value' => 'Enable'), // Checkbox
                        "is_show_email" => array('title' => __('Customer Name', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_show_email', 'label_for' => 'is_show_email', 'name' => 'is_show_email', 'value' => 'Enable'), // Checkbox
                        "show_customer_dtl" => array('title' => __('E-mail and Phone Number', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'show_customer_dtl', 'label_for' => 'show_customer_dtl', 'name' => 'show_customer_dtl', 'value' => 'Enable'), // Checkbox
                        "show_customer_billing" => array('title' => __('Billing Address', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'show_customer_billing', 'label_for' => 'show_customer_billing', 'name' => 'show_customer_billing', 'value' => 'Enable'), // Checkbox
                        "show_customer_shipping" => array('title' => __('Shipping Address', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'show_customer_shipping', 'label_for' => 'show_customer_shipping', 'name' => 'show_customer_shipping', 'value' => 'Enable'), // Checkbox
                    )
                ),
                "vendor_email_settings" => array("title" => __('Order Email Settings for Vendor', 'dc-woocommerce-multi-vendor'), // Section one
                    "fields" => array(
                        "show_cust_add" => array('title' => __('Name, Phone no. and Email', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'show_cust_add', 'label_for' => 'show_cust_add', 'name' => 'show_cust_add', 'value' => 'Enable'), // Checkbox
                        "show_cust_billing_add" => array('title' => __('Billing Address', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'show_cust_billing_add', 'label_for' => 'show_cust_billing_add', 'name' => 'show_cust_billing_add', 'value' => 'Enable'), // Checkbox
                        "show_cust_shipping_add" => array('title' => __('Shipping Address', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'show_cust_shipping_add', 'label_for' => 'show_cust_shipping_add', 'name' => 'show_cust_shipping_add', 'value' => 'Enable'), // Checkbox
                        "show_cust_order_calulations" => array('title' => __('Order Calculations', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'show_cust_order_calulations', 'label_for' => 'show_cust_order_calulations', 'name' => 'show_cust_order_calulations', 'value' => 'Enable'), // Checkbox
                    )
                ),
            )
        );

        $WCMp->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_capabilities_order_settings_sanitize($input) {
        global $WCMp;
        $new_input = array();

        $hasError = false;

        if (isset($input['is_vendor_view_comment'])) {
            $new_input['is_vendor_view_comment'] = sanitize_text_field($input['is_vendor_view_comment']);
        }

        if (isset($input['is_vendor_submit_comment'])) {
            $new_input['is_vendor_submit_comment'] = sanitize_text_field($input['is_vendor_submit_comment']);
        }

        if (isset($input['is_order_csv_export'])) {
            $new_input['is_order_csv_export'] = sanitize_text_field($input['is_order_csv_export']);
        }

        if (isset($input['is_show_email'])) {
            $new_input['is_show_email'] = sanitize_text_field($input['is_show_email']);
        }

        if (isset($input['show_customer_dtl'])) {
            $new_input['show_customer_dtl'] = sanitize_text_field($input['show_customer_dtl']);
        }

        if (isset($input['show_customer_billing'])) {
            $new_input['show_customer_billing'] = sanitize_text_field($input['show_customer_billing']);
        }

        if (isset($input['show_customer_shipping'])) {
            $new_input['show_customer_shipping'] = sanitize_text_field($input['show_customer_shipping']);
        }

        if (isset($input['show_cust_add'])) {
            $new_input['show_cust_add'] = sanitize_text_field($input['show_cust_add']);
        }

        if (isset($input['show_cust_billing_add'])) {
            $new_input['show_cust_billing_add'] = sanitize_text_field($input['show_cust_billing_add']);
        }

        if (isset($input['show_cust_shipping_add'])) {
            $new_input['show_cust_shipping_add'] = sanitize_text_field($input['show_cust_shipping_add']);
        }

        if (isset($input['show_cust_order_calulations'])) {
            $new_input['show_cust_order_calulations'] = sanitize_text_field($input['show_cust_order_calulations']);
        }

        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Vendor Settings Updated', 'dc-woocommerce-multi-vendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

}
