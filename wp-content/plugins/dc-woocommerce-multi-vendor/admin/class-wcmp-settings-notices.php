<?php

class WCMp_Settings_Notices {

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
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;
        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "sections" => array(
                "default_settings_section" => array("title" => '', // Section one
                    "fields" => array(
                        "is_notices_on" => array('title' => __('Notices Enable/Disable :', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_notices_on', 'label_for' => 'is_notices_on', 'name' => 'is_notices_on', 'value' => 'Enable') // Checkbox
                    ),
                )
            )
        );

        $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_notices_settings_sanitize($input) {
        global $WCMp;
        $new_input = array();
        $hasError = false;

        if (isset($input['is_notices_on']))
            $new_input['is_notices_on'] = sanitize_text_field($input['is_notices_on']);

        if (isset($input['notices']))
            $new_input['notices'] = $input['notices'];

        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_settings_name", esc_attr("wcmp_{$this->tab}_settings_admin_updated"), __('Page Settings Updated', 'dc-woocommerce-multi-vendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_tab_new_input", $new_input, $input);
    }
  
}
