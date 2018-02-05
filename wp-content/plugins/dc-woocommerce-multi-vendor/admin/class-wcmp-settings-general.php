<?php

class WCMp_Settings_Gneral {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = $tab;
        $this->options = get_option("wcmp_{$this->tab}_settings_name");
        $this->settings_page_init();
        //general tab migration option
        
        
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;

        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "sections" => array(
                "venor_approval_settings_section" => array("title" => '', // Section one
                    "fields" => apply_filters('wcmp_general_tab_filds', array(
                        "enable_registration" => array('title' => __('Vendor Registration', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'enable_registration', 'label_for' => 'enable_registration', 'text' => __('Anyone can register as vendor. Leave it unchecked if you want to keep your site an invite only marketpace.', 'dc-woocommerce-multi-vendor'), 'name' => 'enable_registration', 'value' => 'Enable'), // Checkbox
                        "approve_vendor_manually" => array('title' => __('Approve Vendors Manually', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'approve_vendor_manually', 'label_for' => 'approve_vendor_manually', 'text' => __('If left unchecked, every vendor applicant will be auto-approved, which is not a recommended setting.', 'dc-woocommerce-multi-vendor'), 'name' => 'approve_vendor_manually', 'value' => 'Enable'), // Checkbox
                        "is_backend_diabled" => apply_filters('is_wcmp_backend_disabled',array('title' => __('Disallow vendors wp-admin access', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_backend_diabled', 'custom_tags'=> array('disabled' => 'disabled'), 'label_for' => 'is_backend_diabled', 'text' => __('Get the <a href="https://wc-marketplace.com/product/wcmp-frontend-manager/">Front-end manager</a> to offer a single dashboard for all vendor purpose and eliminate their backend access requirement.', 'dc-woocommerce-multi-vendor'), 'name' => 'is_backend_diabled', 'value' => 'Enable', 'hints' => __('If unchecked vendor will have access of backend'))) , // Checkbox
//                        "notify_configure_vendor_store" => array('title' => __('Add Vendor Notify Section', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'notify_configure_vendor_store', 'label_for' => 'notify_configure_vendor_store', 'text' => __('Add a section in the vendor dashboard to notify vendors if they have not configured stores properly.', 'dc-woocommerce-multi-vendor'), 'name' => 'notify_configure_vendor_store', 'value' => 'Enable'), // Checkbox
                        "is_university_on" => array('title' => __('Vendor Knowledgebase', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_university_on', 'label_for' => 'is_university_on', 'name' => 'is_university_on', 'value' => 'Enable', 'text' => __('Enable "Knowledgebase" section on vendor dashboard.', 'dc-woocommerce-multi-vendor')), // Checkbox
                        "is_singleproductmultiseller" => array('title' => __('Single Product Multiple Vendors', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_singleproductmultiseller', 'label_for' => 'is_singleproductmultiseller', 'name' => 'is_singleproductmultiseller', 'value' => 'Enable', 'text' => __('Allow multiple vendors to sell same product. Buyers can choose their preffered vendor.','dc-woocommerce-multi-vendor')), // Checkbox
                        "is_sellerreview" => array('title' => __('Enable Vendor Review', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_sellerreview', 'label_for' => 'is_sellerreview', 'name' => 'is_sellerreview', 'value' => 'Enable', 'text' => __('Buyers can rate and review vendor.', 'dc-woocommerce-multi-vendor')), // Checkbox  
                        "is_sellerreview_varified" => array('title' => __('', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_sellerreview_varified', 'label_for' => 'is_sellerreview_varified', 'name' => 'is_sellerreview_varified', 'value' => 'Enable', 'text' => __('Only buyers, purchased from the vendor can rate.', 'dc-woocommerce-multi-vendor')), // Checkbox 
                        "is_policy_on" => array('title' => __('Enable Policies ', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_policy_on', 'label_for' => 'is_policy_on', 'name' => 'is_policy_on', 'value' => 'Enable', 'text' => __('If enabled a policy section will be added to single product page', 'dc-woocommerce-multi-vendor')), // Checkbox
                        "is_customer_support_details" => array('title' => __('Enable Customer Support', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_customer_support_details', 'label_for' => 'is_customer_support_details', 'name' => 'is_customer_support_details', 'value' => 'Enable', 'text' => __('Show support channel details in "Thank You" page and new order email.', 'dc-woocommerce-multi-vendor')), // Checkbox
                        )
                    ),
                ),
            ),
        );

        $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_general_settings_sanitize($input) {
        global $WCMp;
        $new_input = array();

        $hasError = false;

        if (isset($input['enable_registration']))
            $new_input['enable_registration'] = sanitize_text_field($input['enable_registration']);

        if (isset($input['notify_configure_vendor_store']))
            $new_input['notify_configure_vendor_store'] = sanitize_text_field($input['notify_configure_vendor_store']);
        if(isset($input['is_backend_diabled']))
            $new_input['is_backend_diabled'] = sanitize_text_field ($input['is_backend_diabled']);
        if (isset($input['approve_vendor_manually']))
            $new_input['approve_vendor_manually'] = sanitize_text_field($input['approve_vendor_manually']);

        if (isset($input['is_university_on']))
            $new_input['is_university_on'] = sanitize_text_field($input['is_university_on']);
        if(isset($input['is_singleproductmultiseller'])){
            $new_input['is_singleproductmultiseller'] = $input['is_singleproductmultiseller'];
        }
        if(isset($input['is_sellerreview'])){
            $new_input['is_sellerreview'] = $input['is_sellerreview'];
        }
        if(isset($input['is_sellerreview_varified'])){
            $new_input['is_sellerreview_varified'] = $input['is_sellerreview_varified'];
        }
        if(isset($input['is_policy_on'])){
            $new_input['is_policy_on'] = $input['is_policy_on'];
        }
        if(isset($input['is_customer_support_details'])){
            $new_input['is_customer_support_details'] = $input['is_customer_support_details'];
        }

        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_settings_name", esc_attr("wcmp_{$this->tab}_settings_admin_updated"), __('General Settings Updated', 'dc-woocommerce-multi-vendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_tab_new_input", $new_input, $input);
    }

}
