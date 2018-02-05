<?php
/**
 * WCMp Vendor Quick Info Widget
 *
 * @author    WC Marketplace
 * @category  Widgets
 * @package   WCMp/Widgets
 * @version   2.2.0
 * @extends   WP_Widget
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class DC_Widget_Quick_Info_Widget extends WP_Widget {

    public $response = array();

    /**
     * Construct
     */
    function __construct() {
        global $WCMp, $wp_version;

        // Widget variable settings
        $this->widget_idbase = 'dc-vendor-quick-info';
        $this->widget_title = __('WCMp Vendor Quick Info', 'dc-woocommerce-multi-vendor');
        $this->widget_description = __('Add a quick info contact form in vendor\'s store page.', 'dc-woocommerce-multi-vendor');
        $this->widget_cssclass = 'widget_wcmp_quick_info';

        // Widget settings
        $widget_ops = array('classname' => $this->widget_cssclass, 'description' => $this->widget_description);

        // Widget control settings
        $control_ops = array('width' => 250, 'height' => 350, 'id_base' => $this->widget_idbase);

        // Mail Syatem
        $this->response = array(
            0 => array(
                'message' => __('Unable to send email. Please try again.', 'dc-woocommerce-multi-vendor'),
                'class' => 'error'
            ),
            1 => array(
                'message' => __('Email sent successfully.', 'dc-woocommerce-multi-vendor'),
                'class' => 'message'
            ),
        );

        add_action('init', array($this, 'send_mail'), 20);

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
    function widget($args, $instance) {
        global $WCMp, $woocommerce, $post;

        extract($args, EXTR_SKIP);
        $vendor_id = false;
        $vendor = false;
        // Only show current vendor widget when showing a vendor's product(s)
        $show_widget = false;
        if (is_singular('product')) {
            $vendor = get_wcmp_product_vendors($post->ID);
            if ($vendor) {
                $show_widget = true;
            }
        }

        if (is_archive() && is_tax('dc_vendor_shop')) {
            $show_widget = true;
        }

        $hide_from_guests = !empty($instance['hide_from_guests']) ? true : false;
        if ($hide_from_guests) {
            $show_widget = is_user_logged_in();
        }

        if ($show_widget) {
            if (is_tax('dc_vendor_shop')) {
                $vendor_id = get_queried_object()->term_id;
                if ($vendor_id) {
                    $vendor = get_wcmp_vendor_by_term($vendor_id);
                }
            }
            $args = array(
                'instance' => $instance,
                'vendor' => isset($vendor) ? $vendor : false,
                'current_user' => wp_get_current_user(),
                'widget' => $this
            );

            // Before widget (defined by themes)
            echo $before_widget;

            // Set up widget title
            if ($instance['title']) {
                $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
            } else {
                $title = false;
            }
            // Display the widget title if one was input (before and after defined by themes).
            if ($title) {
                echo $before_title . $title . $after_title;
            }

            // Action for plugins/themes to hook onto
            do_action($this->widget_cssclass . '_top');

            $WCMp->template->get_template('widget/quick-info.php', $args);

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
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['description'] = strip_tags($new_instance['description']);
        $instance['hide_from_guests'] = strip_tags($new_instance['hide_from_guests']);
        $instance['submit_label'] = strip_tags($new_instance['submit_label']);
        return $instance;
    }

    /**
     * The form on the widget control in the widget administration area
     * @since  1.0.0
     * @param  array $instance The settings for this instance.
     * @return void
     */
    function form($instance) {
        global $WCMp;
        $defaults = array(
            'title' => __('Quick Info', 'dc-woocommerce-multi-vendor'),
            'description' => __('Do you need more information? Write to us!', 'dc-woocommerce-multi-vendor'),
            'hide_from_guests' => '',
            'submit_label' => __('Submit', 'dc-woocommerce-multi-vendor'),
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'dc-woocommerce-multi-vendor') ?>:
                <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description', 'dc-woocommerce-multi-vendor') ?>:
                <input type="text" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" value="<?php echo $instance['description']; ?>" class="widefat" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('submit_label'); ?>"><?php _e('Submit Button Label Text', 'dc-woocommerce-multi-vendor') ?>:
                <input type="text" id="<?php echo $this->get_field_id('submit_label'); ?>" name="<?php echo $this->get_field_name('submit_label'); ?>" value="<?php echo $instance['submit_label']; ?>" class="widefat" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('hide_from_guests'); ?>"><?php _e('Hide from guests', 'dc-woocommerce-multi-vendor') ?>:
                <input type="checkbox" id="<?php echo $this->get_field_id('hide_from_guests'); ?>" name="<?php echo $this->get_field_name('hide_from_guests'); ?>" value="1" <?php checked($instance['hide_from_guests'], 1, true) ?> class="widefat" />
            </label>
        </p>
        <?php
    }

    /**
     * Send the quick info form mail
     *
     * @since 1.0
     * @return void
     * @author WC Marketplace
     */
    function send_mail() {
        if ($this->check_form()) {

            /* === Sanitize Form Value === */
            $vendor = get_wcmp_vendor($_POST['quick_info']['vendor_id']);
            $to = sanitize_email($vendor->user_data->user_email);
            $subject = sanitize_text_field($_POST['quick_info']['subject']);
            $message = sanitize_text_field($_POST['quick_info']['message']);
            $from = sanitize_text_field($_POST['quick_info']['name']);
            $from_email = sanitize_email($_POST['quick_info']['email']);
            $admin_email = get_option('admin_email');
            $headers[] = "From: {$from} <{$from_email}>";
            $headers[] = "Cc: Admin <{$admin_email}>";

            /* === Send Mail === */
            $check = wp_mail($to, $subject, $message, $headers);

            /* === Prevent resubmit form === */
            unset($_POST);
            $redirect = esc_url(add_query_arg(array('message' => $check ? 1 : 0), $vendor->permalink));
            wp_redirect($redirect);
            exit;
        }
    }

    /**
     * Check form information
     *
     * @return bool
     */
    function check_form() {
        return
                !empty($_POST['dc_vendor_quick_info_submitted']) &&
                wp_verify_nonce($_POST['dc_vendor_quick_info_submitted'], 'dc_vendor_quick_info_submitted') &&
                !empty($_POST['quick_info']) &&
                !empty($_POST['quick_info']['name']) &&
                !empty($_POST['quick_info']['subject']) &&
                !empty($_POST['quick_info']['email']) &&
                !empty($_POST['quick_info']['message']) &&
                !empty($_POST['quick_info']['vendor_id']) &&
                empty($_POST['quick_info']['spam']);
    }

}
