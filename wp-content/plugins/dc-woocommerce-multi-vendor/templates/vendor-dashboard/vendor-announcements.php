<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-announcements.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.3.0
 */
global $WCMp;
?>
<input type="hidden" name="wcmp_msg_tab_to_be_refrash" id="wcmp_msg_tab_to_be_refrash" value="" />
<input type="hidden" name="wcmp_msg_tab_to_be_refrash2" id="wcmp_msg_tab_to_be_refrash2" value="" />
<input type="hidden" name="wcmp_msg_tab_to_be_refrash3" id="wcmp_msg_tab_to_be_refrash3" value="" />
<div id = "tabs-1">
    <ul class="wcmp_msg_tab_nav">
        <li data-element="_all"><a href = "#wcmp_msg_tab_1"><?php _e('All', 'dc-woocommerce-multi-vendor'); ?></a></li>
        <li data-element="_read"><a href = "#wcmp_msg_tab_2"><?php _e('Read', 'dc-woocommerce-multi-vendor'); ?></a></li>
        <li data-element="_unread" ><a href = "#wcmp_msg_tab_3"><?php _e('Unread', 'dc-woocommerce-multi-vendor'); ?></a></li>
        <li data-element="_archive"><a href = "#wcmp_msg_tab_4"><?php _e('Archive', 'dc-woocommerce-multi-vendor'); ?></a></li>
    </ul>
    <!--...................... start tab1 .......................... -->
    <div id = "wcmp_msg_tab_1" data-element="_all">
        <div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
        <div class="msg_container" >			
            <?php
            //show all messages
            $WCMp->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements-all.php');
            ?>			
        </div>
    </div>
    <!--...................... end of tab1 .......................... -->
    <!--...................... start tab2 .......................... -->
    <div id = "wcmp_msg_tab_2" data-element="_read">
        <div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
        <div class="msg_container" >							
            <?php
            //show read messages
            $WCMp->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements-read.php');
            ?>			
        </div>
    </div>
    <!--...................... end of tab2 .......................... -->
    <!--...................... start tab3 .......................... -->
    <div id = "wcmp_msg_tab_3" data-element="_unread">
        <div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
        <div class="msg_container" >				
            <?php
            //show unread messages
            $WCMp->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements-unread.php');
            ?>				
        </div>
    </div>
    <!--...................... end of tab3 .......................... -->
    <!--...................... start tab4 .......................... -->
    <div id = "wcmp_msg_tab_4" data-element="_archive">
        <div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
        <div class="msg_container">				
            <?php
            //show unread messages
            $WCMp->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements-archive.php');
            ?>				
        </div>
    </div>
    <!--...................... end of tab4 .......................... -->
</div>