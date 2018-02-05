<?php

class WCMp_Settings_Vendor_Dashboard {

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
        $pages = get_pages();
        $woocommerce_pages = array(wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
        foreach ($pages as $page) {
            if (!in_array($page->ID, $woocommerce_pages)) {
                $pages_array[$page->ID] = $page->post_title;
            }
        }
        $template_options = apply_filters('wcmp_vendor_shop_template_options', array('template1' => $WCMp->plugin_url.'assets/images/template1.png', 'template2' => $WCMp->plugin_url.'assets/images/template2.png', 'template3' => $WCMp->plugin_url.'assets/images/template3.png'));
        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "wcmp_vendor_color_scheme" => array("title" => __('Vendor Dashboard Color Scheme', 'dc-woocommerce-multi-vendor'), // Section one
                    "fields" => array(
                        "wcmp_background_color" => array('title' => __('Background Color', 'dc-woocommerce-multi-vendor'), 'type' => 'colorpicker', 'value' => '#f5f5f5', 'id' => 'wcmp_background_color', 'label_for' => 'wcmp_background_color', 'name' => 'wcmp_background_color', 'hints' => __('Choose your preferred dashboard background Color.', 'dc-woocommerce-multi-vendor')),
                        "wcmp_menu_background_color" => array('title' => __('Menu Background Color', 'dc-woocommerce-multi-vendor'), 'type' => 'colorpicker', 'value' => '#dcdcdc', 'id' => 'wcmp_menu_background_color', 'label_for' => 'wcmp_menu_background_color', 'name' => 'wcmp_menu_background_color', 'hints' => __('Choose your preferred dashboard menu Background Color.', 'dc-woocommerce-multi-vendor')),
                        "wcmp_menu_color" => array('title' => __('Menu Color', 'dc-woocommerce-multi-vendor'), 'type' => 'colorpicker', 'value' => '#7a7a7a', 'id' => 'wcmp_menu_color', 'label_for' => 'wcmp_menu_color', 'name' => 'wcmp_menu_color', 'hints' => __('Choose your preferred dashboard menu Color.', 'dc-woocommerce-multi-vendor')),
                        "wcmp_menu_hover_background_color" => array('title' => __('Menu Hover Background Color', 'dc-woocommerce-multi-vendor'), 'type' => 'colorpicker', 'value' => '#fff', 'id' => 'wcmp_menu_hover_background_color', 'label_for' => 'wcmp_menu_hover_background_color', 'name' => 'wcmp_menu_hover_background_color', 'hints' => __('Choose your preferred dashboard menu hover Background Color.', 'dc-woocommerce-multi-vendor')),
                        "wcmp_menu_hover_color" => array('title' => __('Menu Hover Color', 'dc-woocommerce-multi-vendor'), 'type' => 'colorpicker', 'value' => '#fc482f', 'id' => 'wcmp_menu_hover_color', 'label_for' => 'wcmp_menu_hover_color', 'name' => 'wcmp_menu_hover_color', 'hints' => __('Choose your preferred dashboard menu hover color.', 'dc-woocommerce-multi-vendor')),
                    ),
                ),
                'wcmp_vendor_shop_template' => array(
                    'title' => __('Vendor Shop', 'dc-woocommerce-multi-vendor'),
                    'fields' => array(
                        "wcmp_vendor_shop_template" => array('title' => __('Vendor Shop Template', 'dc-woocommerce-multi-vendor'), 'type' => 'radio_select', 'id' => 'wcmp_vendor_shop_template', 'label_for' => 'wcmp_vendor_shop_template', 'name' => 'wcmp_vendor_shop_template', 'dfvalue' => 'vendor', 'options' => $template_options, 'value' => 'template1', 'desc' => ''), // Radio
                        "can_vendor_edit_shop_template" => array('title' => __('Vendor Can Edit', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'can_vendor_edit_shop_template', 'label_for' => 'can_vendor_edit_shop_template', 'name' => 'can_vendor_edit_shop_template', 'value' => 'Enable', 'text' => __('Allow vendors to edit the shop template.', 'dc-woocommerce-multi-vendor')), // Checkbox
                    )
                )
            ),
        );

        $WCMp->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_vendor_dashboard_settings_sanitize($input) {
        $new_input = array();
        $hasError = false;

        if (isset($input['wcmp_background_color'])) {
            $new_input['wcmp_background_color'] = $input['wcmp_background_color'] != '#fbfbfb' ? $input['wcmp_background_color'] : '#f5f5f5';
        }
        if (isset($input['wcmp_menu_background_color'])) {
            $new_input['wcmp_menu_background_color'] = $input['wcmp_menu_background_color'] != '#fbfbfb' ? $input['wcmp_menu_background_color'] : '#dcdcdc';
        }
        if (isset($input['wcmp_menu_color'])) {
            $new_input['wcmp_menu_color'] = $input['wcmp_menu_color'] != '#fbfbfb' ? $input['wcmp_menu_color'] : '#7a7a7a';
        }
        if (isset($input['wcmp_menu_hover_background_color'])) {
            $new_input['wcmp_menu_hover_background_color'] = $input['wcmp_menu_hover_background_color'] != '#fbfbfb' ? $input['wcmp_menu_hover_background_color'] : '#fff';
        }
        if (isset($input['wcmp_menu_hover_color'])) {
            $new_input['wcmp_menu_hover_color'] = $input['wcmp_menu_hover_color'] != '#fbfbfb' ? $input['wcmp_menu_hover_color'] : '#fc482f';
        }
        if(isset($input['wcmp_vendor_shop_template'])){
            $new_input['wcmp_vendor_shop_template'] = sanitize_text_field($input['wcmp_vendor_shop_template']);
        }
        if(isset($input['can_vendor_edit_shop_template'])){
            $new_input['can_vendor_edit_shop_template'] = sanitize_text_field($input['can_vendor_edit_shop_template']);
        }
        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Vendor Settings Updated', 'dc-woocommerce-multi-vendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

}
