<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCMp_Widget_Vendor_Product_Categories extends WC_Widget {

    public $vendor_term_id;

    public function __construct() {
        $this->widget_cssclass = 'wcmp woocommerce wcmp_widget_vendor_product_categories widget_product_categories';
        $this->widget_description = __('A list or dropdown of product categories.', 'woocommerce');
        $this->widget_id = 'wcmp_vendor_product_categories';
        $this->widget_name = __('WCMp product categories', 'woocommerce');
        $this->settings = array(
            'title' => array(
                'type' => 'text',
                'std' => __('Vendor Product categories', 'woocommerce'),
                'label' => __('Title', 'woocommerce'),
            ),
            'count' => array(
                'type' => 'checkbox',
                'std' => 1,
                'label' => __('Show product counts', 'woocommerce'),
            ),
            'hide_empty' => array(
                'type' => 'checkbox',
                'std' => 0,
                'label' => __('Hide empty categories', 'woocommerce'),
            ),
        );
        parent::__construct();
    }

    public function widget($args, $instance) {
        global $wp_query;
        if (!is_tax('dc_vendor_shop')) {
            return;
        }
        $count = isset($instance['count']) ? $instance['count'] : $this->settings['count']['std'];
        $hide_empty = isset($instance['hide_empty']) ? $instance['hide_empty'] : $this->settings['hide_empty']['std'];
        
        $this->vendor_term_id = $wp_query->queried_object->term_id;
        $this->widget_start($args, $instance);
        $vendor = get_wcmp_vendor_by_term($this->vendor_term_id);
        $vendor_products = $vendor->get_products();
        $product_ids = wp_list_pluck($vendor_products, 'ID');
        $associated_terms = array();
        foreach ($product_ids as $product_id) {
            $term_ids = wp_list_pluck(get_the_terms($product_id, 'product_cat'), 'term_id');
            foreach ($term_ids as $term_id) {
                $associated_terms[$term_id][] = $product_id;
            }
        }
        $list_args = array('taxonomy' => 'product_cat');
        $product_cats = get_terms($list_args);
        echo '<ul class="product-categories">';
        foreach ($product_cats as $product_cat) {
            $term_count = isset($associated_terms[$product_cat->term_id]) ? count(array_unique($associated_terms[$product_cat->term_id])) : 0;
            if (!$hide_empty || $term_count) {
                echo '<li class="cat-item cat-item-' . $product_cat->term_id . '"><a href="?category=' . $product_cat->slug . '">' . $product_cat->name . '</a>';
                if ($count) {
                    echo '<span class="count">(' . $term_count . ')</span>';
                }
                echo '</li>';
            }
        }
        echo '</ul>';
        $this->widget_end($args);
    }

}
