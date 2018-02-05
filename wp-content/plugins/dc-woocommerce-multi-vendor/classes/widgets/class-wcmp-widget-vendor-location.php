<?php
/**
 * WCMp Store Location Widget
 *
 * @author    WC Marketplace
 * @category  Widgets
 * @package   WCMp/Widgets
 * @version   2.2.0
 * @extends   WP_Widget
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class DC_Woocommerce_Store_Location_Widget extends WP_Widget {

    /**
     * constructor
     *
     * @access public
     * @return void
     */
    function __construct() {
        global $WCMp, $wp_version;

        // Widget variable settings
        $this->widget_idbase = 'dc-vendor-store-location';
        $this->widget_title = __('WCMp Store Location', 'dc-woocommerce-multi-vendor');
        $this->widget_description = __('Display the vendor\'s store location in Google Maps.', 'dc-woocommerce-multi-vendor');
        $this->widget_cssclass = 'widget_wcmp_store_location';

        // Widget settings
        $widget_ops = array('classname' => $this->widget_cssclass, 'description' => $this->widget_description);

        // Widget control settings
        $control_ops = array('width' => 250, 'height' => 350, 'id_base' => $this->widget_idbase);

        // Create the widget
        if ($wp_version >= 4.3) {
            parent::__construct($this->widget_idbase, $this->widget_title, $widget_ops, $control_ops);
        } else {
            $this->WP_Widget($this->widget_idbase, $this->widget_title, $widget_ops, $control_ops);
        }
    }

    /**
     * widget function.
     *
     * @see WP_Widget
     * @access public
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget($args, $instance) {
        global $WCMp, $woocommerce;
        extract($args, EXTR_SKIP);
        $vendor_id = false;
        $vendors = false;
        // Only show current vendor widget when showing a vendor's product(s)
        $show_widget = false;
        if($instance['gmap_api_key']){
            $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
            $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
            wp_register_script('wcmp-gmaps-api', "//maps.googleapis.com/maps/api/js?key={$instance['gmap_api_key']}&sensor=false&language=en", array('jquery'));
            wp_register_script('wcmp-gmap3', $frontend_script_path . 'gmap3.min.js', array('jquery', 'wcmp-gmaps-api'), '6.0.0', false);
        }

        if (is_tax('dc_vendor_shop')) {
            $vendor_id = get_queried_object()->term_id;
            if ($vendor_id) {
                $vendor = get_wcmp_vendor_by_term($vendor_id);
                $show_widget = true;
            }
        }

        if (is_singular('product')) {
            global $post;
            $vendor = get_wcmp_product_vendors($post->ID);
            if ($vendor) {
                $show_widget = true;
            }
        }

        if ($show_widget && isset($vendor->id)) {

            wp_enqueue_script('wcmp-gmap3');
           

            $vendor_address_1 = get_user_meta($vendor->id, '_vendor_address_1', true);
            $vendor_address_2 = get_user_meta($vendor->id, '_vendor_address_2', true);
            $vendor_city = get_user_meta($vendor->id, '_vendor_city', true);
            $vendor_state = get_user_meta($vendor->id, '_vendor_state', true);
            $vendor_postcode = get_user_meta($vendor->id, '_vendor_postcode', true);
            $vendor_country = get_user_meta($vendor->id, '_vendor_country', true);
            $location = '';
            if ($vendor_address_1)
                $location = $vendor_address_1 . ' ,';
            if ($vendor_address_2)
                $location .= $vendor_address_2 . ' ,';
            if ($vendor_city)
                $location .= $vendor_city . ' ,';
            if ($vendor_state)
                $location .= $vendor_state . ' ,';
            if ($vendor_postcode)
                $location .= $vendor_postcode . ' ,';
            if ($vendor_country)
                $location .= $vendor_country;
            $args = array(
                'instance' => $instance,
                'gmaps_link' => esc_url(add_query_arg(array('q' => urlencode($location)), '//maps.google.com/')),
                'location' => $location
            );

            // Set up widget title
            if ($instance['title']) {
                $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
            } else {
                $title = false;
            }

            // Before widget (defined by themes)
            echo $before_widget;

            // Display the widget title if one was input (before and after defined by themes).
            if ($title) {
                echo $before_title . $title . $after_title;
            }

            // Action for plugins/themes to hook onto
            do_action($this->widget_cssclass . '_top');

            $WCMp->template->get_template('widget/store-location.php', $args);

            // Action for plugins/themes to hook onto
            do_action($this->widget_cssclass . '_bottom');

            // After widget (defined by themes).
            echo $after_widget;
        }
    }

    /**
     * update function.
     *
     * @see WP_Widget->update
     * @access public
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['gmap_api_key'] = strip_tags($new_instance['gmap_api_key']);
        return $instance;
    }

    /**
     * The form on the widget control in the widget administration area
     * @since  1.0.0
     * @param  array $instance The settings for this instance.
     * @return void
     */
    public function form($instance) {
        global $WCMp, $woocommerce;
        $defaults = array(
            'title' => __('Store Location', 'dc-woocommerce-multi-vendor'),
            'gmap_api_key' => __('Google Map API key', 'dc-woocommerce-multi-vendor'),
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'dc-woocommerce-multi-vendor') ?>:
                <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('gmap_api_key'); ?>"><?php _e('Google Map API key', 'dc-woocommerce-multi-vendor') ?>:
                <input type="text" id="<?php echo $this->get_field_id('gmap_api_key'); ?>" name="<?php echo $this->get_field_name('gmap_api_key'); ?>" value="<?php if($instance['gmap_api_key']) echo $instance['gmap_api_key']; ?>" class="widefat" />
                <span class="desc"><a href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key" target="_blank"><?php _e('Click here to generate key', 'dc-woocommerce-multi-vendor') ?></a> </span>
            </label>
        </p>
        <?php
    }

}
