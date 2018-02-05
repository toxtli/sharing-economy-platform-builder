<?php global $WCMp; ?>
<?php wc_print_notices(); ?>
<div class="wcmp_regi_main">
    <form class="register" role="form" method="post" enctype="multipart/form-data">
        <h2 class="reg_header1"><?php echo apply_filters('wcmp_vendor_registration_header_text',__('Vendor Registration Form','woocommerce')); ?></h2>

        <div class="wcmp_regi_form_box">
            <?php if(!is_user_logged_in()) : 
                $wcmp_vendor_general_settings_name = get_option('wcmp_vendor_general_settings_name');?>
            <h3 class="reg_header2"><?php echo apply_filters('woocommerce_section_label', __('Account Details', 'dc-woocommerce-multi-vendor')); ?></h3>
            <?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>
                <div class="wcmp-regi-12">
                    <label for="reg_username"><?php _e('Username', 'woocommerce'); ?> <span class="required">*</span></label>
                    <input type="text"  name="username" id="reg_username" value="<?php if (!empty($_POST['username'])) echo esc_attr($_POST['username']); ?>" required="required" />
                </div>
            <?php endif; ?>
            <div class="wcmp-regi-12">
                <label for="reg_email"><?php _e('Email address', 'woocommerce'); ?> <span class="required">*</span></label>
                <input type="email" required="required"  name="email" id="reg_email" value="<?php if (!empty($_POST['email'])) echo esc_attr($_POST['email']); ?>" />
            </div>
            <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
                <div class="wcmp-regi-12">
                    <label for="reg_password"><?php _e('Password', 'woocommerce'); ?> <span class="required">*</span></label>
                    <input type="password" required="required" name="password" id="reg_password" />
                </div>
            <?php endif; ?>
            <div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e('Anti-spam', 'woocommerce'); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>
            <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce');  ?>
            <?php endif; ?>
            <?php do_action('wcmp_vendor_register_form'); ?>
            <div class="clearboth"></div>
        </div>
        <?php //do_action('register_form'); ?> 
        <?php if(is_user_logged_in()){ echo '<input type="hidden" name="vendor_apply" />'; }  ?>
        <input type="hidden" value="true" name="pending_vendor" />
        <?php do_action( 'woocommerce_register_form' ); ?>
        <p class="woocomerce-FormRow form-row">
            <?php 
            $button_text = apply_filters('wcmp_vendor_registration_submit','Register');
            ?>
            <input type="submit" class="woocommerce-Button button" name="register" value="<?php esc_attr_e( $button_text, 'woocommerce' ); ?>" />
        </p>
        <?php do_action('woocommerce_register_form_end'); ?>
    </form>
</div>