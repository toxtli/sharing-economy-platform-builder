<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="main_image">
    <a href="https://www.fmeaddons.com/woocommerce-plugins-extensions/customer-registration-plugin.html" target="_blank">
        <img src="<?php echo FMERA_URL ?>images/ara.png" class="cover_image" />
    </a>
</div>
<div class="fmeaddons-div">
    <p><?php _e('FMA WooCommerce Additional Registration Attribute Module Options.','fmera'); ?></p>
</div>

<div id="fmeaddons-tabs">

    <div class="fmeaddons-tabs-ulli">
    <ul>
        <li><a href="#tabs-1"><span class="dashicons dashicons-sos"></span><?php _e('General Settings', 'fmera'); ?></a></li>
    </ul>

    </div>

    <div class="fmeaddons-tabs-content">
        <form id="extendfaq_setting_optionform" action="" method="">
            <div class="fmeaddons-top-content">
                    
                    <div class="fmeaddons-support-actions">
                        <div class="actions fmeaddons-support-links">
                            <ul class="collapsed-fmeaddons">
                                <li id="coll"><a href="#"><span class="dashicons dashicons-arrow-left"></span><?php _e('', 'fmera'); ?></a></li>
                            </ul>
                        </div>
                        <div class="actions fmeaddons-submit">
                            <span id="ajax-msg"></span>
                            <input onclick="fmesave_setting()" class="button button-primary" type="button" name="" value="Save Changes">
                            <?php wp_nonce_field(); ?>
                        </div>
                    </div>

                    <div class="fmeaddons-singletab" id="tabs-1">
                        <h2><?php _e('General Settings', 'fmera'); ?></h2>
                        <table class="fmeaddons-table-optoin">
                            <tbody>
                                <tr class="fmeaddons-option-field">
                                    <th>
                                        <div class="option-head">
                                            <h3><?php _e('Account Section Title', 'fmera'); ?></h3>
                                        </div>
                                        <span class="description">
                                            <p><?php _e('Main heading of the account section', 'fmera'); ?></p>
                                        </span>
                                    </th>
                                    <td>
                                        <input id="account_title" value="<?php echo get_option('account_title'); ?>"  class="fmeaddons-input-field" type="text">
                                    </td>
                                </tr>

                                <tr class="fmeaddons-option-field">
                                    <th>
                                        <div class="option-head">
                                            <h3><?php _e('Profile Section Title', 'fmera'); ?></h3>
                                        </div>
                                        <span class="description">
                                            <p><?php _e('Main heading of the profile section.', 'fmera'); ?></p>
                                        </span>
                                    </th>
                                    <td>
                                        <input id="profile_title" value="<?php echo get_option('profile_title'); ?>"  class="fmeaddons-input-field" type="text">
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>



                </div> 
        </form>
    </div>
    
</div>

<script>
            
    jQuery( function() {
        jQuery( "#fmeaddons-tabs" ).tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
    });

    jQuery(document).ready(function(){
                
        jQuery("#coll").click(function() {
            
            jQuery('.fmeaddons-tabs-ulli').toggleClass('red');
            jQuery(".fmeaddons-logo-ui h2").toggleClass('reddisnon');
            jQuery('.fmeaddons-tabs-content').toggleClass('green');
            jQuery('#coll span.dashicons').toggleClass('dashicons-arrow-left dashicons-arrow-right');
            
            if (jQuery('.fmeaddons-tabs-ulli').hasClass('red')){
                
                jQuery('#ui-id-1').get(0).lastChild.nodeValue = "";
                jQuery('#coll a').get(0).lastChild.nodeValue = "";
            
            } else {
                
                jQuery('.fmeaddons-tabs-ulli').addClass('redd');
                jQuery('#ui-id-1').get(0).lastChild.nodeValue = "General Setting";
            }
        });
    });


    function fmesave_setting() {
                
                var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
                
                var condition = 'fmera_setting';

                var account_title = jQuery('#account_title').val();
                var profile_title = jQuery('#profile_title').val();                 
                
                jQuery('#ajax-msg').show();
                    jQuery.ajax({
                        url : ajaxurl,
                        type : 'post',
                        data : {
                            action : 'fmera_settings_opt',
                            
                            condition : condition,

                            account_title : account_title,
                            profile_title : profile_title,
                            
                        },
                        success : function(response) {
                            jQuery("#option-success").show().delay(3000).fadeOut("slow");
                        },
                        complete: function(){
                            jQuery('#ajax-msg').hide();
                        }
                    });
            }
</script>


