<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class 		WCMp_Capabilities
 * @version		1.0.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Capabilities {

    public $capability;
    public $general_cap;
    public $vendor_cap;
    public $frontend_cap;
    public $payment_cap;
    public $wcmp_capability = array();

    public function __construct() {
        $this->wcmp_capability = array_merge(
                $this->wcmp_capability
                , (array) get_option('wcmp_general_settings_name', array())
                , (array) get_option('wcmp_capabilities_product_settings_name', array())
                , (array) get_option('wcmp_capabilities_order_settings_name', array())
                , (array) get_option('wcmp_capabilities_miscellaneous_settings_name', array())
        );
        $this->frontend_cap = get_option("wcmp_frontend_settings_name");
        $this->payment_cap = get_option("wcmp_payment_settings_name");

        add_filter('product_type_selector', array(&$this, 'wcmp_product_type_selector'), 10, 1);
        add_filter('product_type_options', array(&$this, 'wcmp_product_type_options'), 10);
        add_filter('wc_product_sku_enabled', array(&$this, 'wcmp_wc_product_sku_enabled'), 30);
        add_filter('woocommerce_product_data_tabs', array(&$this, 'wcmp_woocommerce_product_data_tabs'), 30);
        add_action('admin_print_styles', array(&$this, 'output_capability_css'));
        add_action('woocommerce_get_item_data', array(&$this, 'add_sold_by_text_cart'), 30, 2);
        add_action('woocommerce_add_order_item_meta', array(&$this, 'order_item_meta_2'), 20, 2);
        add_action('woocommerce_after_shop_loop_item_title', array($this, 'wcmp_after_add_to_cart_form'), 30);
        /* for single product */
        add_action('woocommerce_product_meta_start', array($this, 'wcmp_after_add_to_cart_form'), 25);
        add_action('update_option_wcmp_capabilities_product_settings_name', array(&$this, 'update_wcmp_vendor_role_capability'), 10);
    }

    /**
     * Vendor Capability from Product Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_can($cap) {
        if (is_array($this->wcmp_capability) && array_key_exists($cap, $this->wcmp_capability)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vendor Capability from General Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_general_settings($cap) {
        if (is_array($this->wcmp_capability) && array_key_exists($cap, $this->wcmp_capability)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vendor Capability from Capability Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_capabilities_settings($cap, $default = array()) {
        $this->wcmp_capability = !empty($default) ? $default : $this->wcmp_capability;
        if (is_array($this->wcmp_capability) && array_key_exists($cap, $this->wcmp_capability)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vendor Capability from Capability Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_frontend_settings($cap) {
        if (is_array($this->frontend_cap) && array_key_exists($cap, $this->frontend_cap)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vendor Capability from Capability Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_payment_settings($cap) {
        if (is_array($this->payment_cap) && array_key_exists($cap, $this->payment_cap)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Vendor Product Types
     *
     * @param product_types
     * @return product_types 
     */
    public function wcmp_product_type_selector($product_types) {
        $user = wp_get_current_user();
        if (is_user_wcmp_vendor($user) && $product_types) {
            foreach ($product_types as $product_type => $value) {
                $vendor_can = $this->vendor_can($product_type);
                if (!$vendor_can) {
                    unset($product_types[$product_type]);
                }
            }
        }
        return $product_types;
    }

    /**
     * Get Vendor Product Types Options
     *
     * @param product_type_options
     * @return product_type_options 
     */
    public function wcmp_product_type_options($product_type_options) {
        $user = wp_get_current_user();
        if (is_user_wcmp_vendor($user) && $product_type_options) {
            foreach ($product_type_options as $product_type_option => $value) {
                $vendor_can = $this->vendor_can($product_type_option);
                if (!$vendor_can) {
                    unset($product_type_options[$product_type_option]);
                }
            }
        }
        return $product_type_options;
    }

    /**
     * Check if Vendor Product SKU Enable
     *
     * @param state
     * @return boolean 
     */
    public function wcmp_wc_product_sku_enabled($state) {
        $user = wp_get_current_user();
        if (is_user_wcmp_vendor($user)) {
            $vendor_can = $this->vendor_can('sku');
            if ($vendor_can) {
                return true;
            } else
                return false;
        }
        return true;
    }

    /**
     * Set woocommerce product tab according settings
     *
     * @param panels
     * @return panels 
     */
    public function wcmp_woocommerce_product_data_tabs($panels) {
        $settings_product = get_option('wcmp_product_settings_name');
        $user = wp_get_current_user();
        if (is_user_wcmp_vendor($user)) {
            $vendor_can = $this->vendor_can('inventory');
            if (!$vendor_can) {
                unset($panels['inventory']);
            }
            $vendor_can = $this->vendor_can('shipping');
            if (!$vendor_can) {
                unset($panels['shipping']);
            }
            if (!$this->vendor_can('linked_products')) {
                unset($panels['linked_product']);
            }
            $vendor_can = $this->vendor_can('attribute');
            if (!$vendor_can) {
                unset($panels['attribute']);
            }
            $vendor_can = $this->vendor_can('advanced');
            if (!$vendor_can) {
                unset($panels['advanced']);
            }
        }
        return $panels;
    }

    /**
     * Set output capability css
     */
    public function output_capability_css() {
        global $post;
        $screen = get_current_screen();

        $custom_css = '';
        if (isset($screen->id) && in_array($screen->id, array('product','edit-product'))) {
            if (is_user_wcmp_vendor(get_current_vendor_id())) {
                if (!$this->vendor_can('taxes')) {
                    $custom_css .= '
					._tax_status_field, ._tax_class_field {
						display: none !important;
					}
					';
                }
                if (!$this->vendor_can('add_comment')) {
                    $custom_css .= '
					.comments-box {
						display: none !important;
					}
					';
                }
                if (!$this->vendor_can('comment_box')) {
                    $custom_css .= '
					#add-new-comment {
						display: none !important;
					}
					';
                }
                if ($this->vendor_can('stylesheet')) {
                    $custom_css .= $this->wcmp_capability['stylesheet'];
                }

                $vendor_id = get_current_vendor_id();
                $vendor = get_wcmp_vendor($vendor_id);
                if ($vendor && $post->post_author != $vendor_id) {
                    $custom_css .= '.options_group.pricing.show_if_simple.show_if_external {
														display: none !important;
													}';
                }
                wp_add_inline_style('woocommerce_admin_styles', $custom_css);
            }
        }
    }

    /**
     * Add Sold by Vendor text
     *
     * @param array, cart_item
     * @return array 
     */
    public function add_sold_by_text_cart($array, $cart_item) {
        if ($this->vendor_frontend_settings('sold_by_cart_and_checkout')) {
            $general_cap = isset($this->frontend_cap['sold_by_text']) ? $this->frontend_cap['sold_by_text'] : '';
            if (!$general_cap) {
                $general_cap = __('Sold By', 'dc-woocommerce-multi-vendor');
            }
            $vendor = get_wcmp_product_vendors($cart_item['product_id']);
            if ($vendor) {
                $array = array_merge($array, array(array('name' => $general_cap, 'value' => $vendor->user_data->display_name)));
                do_action('after_sold_by_text_cart_page', $vendor);
            }
        }
        return $array;
    }

    /**
     * Add Sold by Vendor text
     *
     * @return void 
     */
    public function wcmp_after_add_to_cart_form() {
        global $post;
        if ($this->vendor_frontend_settings('sold_by_catalog')) {
            $vendor = get_wcmp_product_vendors($post->ID);
            $general_cap = isset($this->frontend_cap['sold_by_text']) ? $this->frontend_cap['sold_by_text'] : '';
            if (!$general_cap)
                $general_cap = __('Sold By', 'dc-woocommerce-multi-vendor');
            if ($vendor) {
                echo '<a class="by-vendor-name-link" style="display: block;" href="' . $vendor->permalink . '">' . $general_cap . ' ' . $vendor->user_data->display_name . '</a>';
                do_action('after_sold_by_text_shop_page', $vendor);
            }
        }
    }

    /**
     * Save sold by text in database
     *
     * @param item_id, cart_item
     * @return void 
     */
    public function order_item_meta_2($item_id, $cart_item) {
        global $WCMp;
        if ($WCMp->vendor_caps->vendor_frontend_settings('sold_by_cart_and_checkout')) {
            $general_cap = isset($this->frontend_cap['sold_by_text']) ? $this->frontend_cap['sold_by_text'] : '';
            if (!$general_cap) {
                $general_cap = 'Sold By';
            }
            $vendor = get_wcmp_product_vendors($cart_item['product_id']);
            if ($vendor) {
                wc_add_order_item_meta($item_id, $general_cap, $vendor->user_data->display_name);
                wc_add_order_item_meta($item_id, '_vendor_id', $vendor->id);
            }
        }
    }

    public function update_wcmp_vendor_role_capability() {
        global $wp_roles;

        if (!class_exists('WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        $capabilities = $this->get_vendor_caps();
        foreach ($capabilities as $cap => $is_enable) {
            $wp_roles->add_cap('dc_vendor', $cap, $is_enable);
        }
        do_action('wcmp_after_update_vendor_role_capability', $capabilities, $wp_roles);
    }

    /**
     * Set up array of vendor admin capabilities
     * 
     * @since 2.7.6
     * @access public
     * @return arr Vendor capabilities
     */
    public function get_vendor_caps() {
        $caps = array();
        $capability = get_option('wcmp_capabilities_product_settings_name', array());
        if ($this->vendor_capabilities_settings('is_upload_files', $capability)) {
            $caps['upload_files'] = true;
        } else {
            $caps['upload_files'] = false;
        }
        if ($this->vendor_capabilities_settings('is_submit_product', $capability)) {
            $caps['edit_product'] = true;
            $caps['delete_product'] = true;
            $caps['edit_products'] = true;
            $caps['delete_products'] = true;
            if ($this->vendor_capabilities_settings('is_published_product', $capability)) {
                $caps['publish_products'] = true;
            } else {
                $caps['publish_products'] = false;
            }
            if ($this->vendor_capabilities_settings('is_edit_delete_published_product', $capability)) {
                $caps['edit_published_products'] = true;
                $caps['delete_published_products'] = true;
            } else {
                $caps['edit_published_products'] = false;
                $caps['delete_published_products'] = false;
            }
        } else {
            $caps['edit_product'] = false;
            $caps['delete_product'] = false;
            $caps['edit_products'] = false;
            $caps['delete_products'] = false;
            $caps['publish_products'] = false;
            $caps['edit_published_products'] = false;
            $caps['delete_published_products'] = false;
        }

        if ($this->vendor_capabilities_settings('is_submit_coupon', $capability)) {
            $caps['edit_shop_coupon'] = true;
            $caps['edit_shop_coupons'] = true;
            $caps['delete_shop_coupon'] = true;
            $caps['delete_shop_coupons'] = true;
            if ($this->vendor_capabilities_settings('is_published_coupon', $capability)) {
                $caps['publish_shop_coupons'] = true;
            } else {
                $caps['publish_shop_coupons'] = false;
            }
            if ($this->vendor_capabilities_settings('is_edit_delete_published_coupon', $capability)) {
                $caps['edit_published_shop_coupons'] = true;
                $caps['delete_published_shop_coupons'] = true;
            } else {
                $caps['edit_published_shop_coupons'] = false;
                $caps['delete_published_shop_coupons'] = false;
            }
        } else {
            $caps['edit_shop_coupon'] = false;
            $caps['edit_shop_coupons'] = false;
            $caps['delete_shop_coupon'] = false;
            $caps['delete_shop_coupons'] = false;
            $caps['publish_shop_coupons'] = false;
            $caps['edit_published_shop_coupons'] = false;
            $caps['delete_published_shop_coupons'] = false;
        }
        return apply_filters('wcmp_vendor_capabilities', $caps);
    }

}
