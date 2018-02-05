<?php

if(!function_exists('wjm_woocommerce_inactive_notice')) {
	function wjm_woocommerce_inactive_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sWCMp Job Manager is inactive.%s The %sWooCommerce plugin%s must be active for the WCMp Job Manager to work. Please %sinstall & activate WooCommerce%s', WCMP_WP_JOB_MANAGER_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugin-install.php?tab=search&s=woocommerce' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
		</div>
		<?php
	}
}

if(!function_exists('wjm_wcmp_inactive_notice')) {
	function wjm_wcmp_inactive_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sWCMp Job Manager is inactive.%s The %sWC Marketplace%s must be active for the WCMp Job Manager to work. Please %sinstall & activate WC Marketplace%s', WCMP_WP_JOB_MANAGER_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="https://wordpress.org/plugins/dc-woocommerce-multi-vendor/">', '</a>', '<a href="' . admin_url( 'plugin-install.php?tab=search&s=wc+marketplace' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
		</div>
		<?php
	}
}
if(!function_exists('wjm_job_manager_inactive_notice')) {
	function wjm_job_manager_inactive_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sWCMp Job Manager is inactive.%s The %sWP Job Manager%s must be active for the WCMp Job Manager to work. Please %sinstall & activate WP Job Manager%s', WCMP_WP_JOB_MANAGER_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="https://wordpress.org/plugins/wp-job-manager/">', '</a>', '<a href="' . admin_url( 'plugin-install.php?tab=search&s=WP+Job+Manager' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
		</div>
		<?php
	}
}

if(!function_exists('wjm_job_manager_paid_listing_inactive_notice')) {
	function wjm_job_manager_paid_listing_inactive_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sWCMp Job Manager is inactive.%s The %sWP Job Manager - WooCommerce Paid Listings%s must be active for the WCMp Job Manager to work. Please %sinstall & activate WP Job Manager - WooCommerce Paid Listings%s', WCMP_WP_JOB_MANAGER_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="https://wpjobmanager.com/add-ons/wc-paid-listings/">', '</a>', '<a href="https://wpjobmanager.com/add-ons/wc-paid-listings/">', '&nbsp;&raquo;</a>' ); ?></p>
		</div>
		<?php
	}
}


