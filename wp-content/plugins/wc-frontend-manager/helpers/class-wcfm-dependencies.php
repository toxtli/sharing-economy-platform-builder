<?php
/**
 * WC Dependency Checker
 *
 */
class WCFM_Dependencies {
	
	private static $active_plugins;
	
	static function init() {
		self::$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() )
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}
	
	static function woocommerce_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce/woocommerce.php', self::$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', self::$active_plugins );
		return false;
	}
	
	// WC Frontend Manager - Ultimate
	static function wcfmu_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wc-frontend-manager-ultimate/wc_frontend_manager_ultimate.php', self::$active_plugins ) || array_key_exists( 'wc-frontend-manager-ultimate/wc_frontend_manager_ultimate.php', self::$active_plugins );
		return false;
	}
	
	// WC Frontend Manager Groups and Staffs - 2.5.0
	static function wcfmgs_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wc-frontend-manager-groups-staffs/wc_frontend_manager_groups_staffs.php', self::$active_plugins ) || array_key_exists( 'wc-frontend-manager-groups-staffs/wc_frontend_manager_groups_staffs.php', self::$active_plugins );
		return false;
	}
	
	// WC Frontend Manager - Analytics - 2.6.3
	static function wcfma_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wc-frontend-manager-analytics/wc_frontend_manager_analytics.php', self::$active_plugins ) || array_key_exists( 'wc-frontend-manager-analytics/wc_frontend_manager_analytics.php', self::$active_plugins );
		return false;
	}
	
	// WC Frontend Manager - Membership - 3.3.5
	static function wcfmvm_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wc-multivendor-membership/wc-multivendor-membership.php', self::$active_plugins ) || array_key_exists( 'wc-multivendor-membership/wc-multivendor-membership.php', self::$active_plugins );
		return false;
	}
	
	// WC Vendors Pro
	static function wcvpro_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wc-vendors-pro/wcvendors-pro.php', self::$active_plugins ) || array_key_exists( 'wc-vendors-pro/wcvendors-pro.php', self::$active_plugins );
		return false;
	}
	
	// WC Bookings
	static function wcfm_bookings_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-bookings/woocommerce-bookings.php', self::$active_plugins ) || array_key_exists( 'woocommerce-bookings/woocommerce-bookings.php', self::$active_plugins );
		return false;
	}
	
	// WC Subscriptions
	static function wcfm_subscriptions_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-subscriptions/woocommerce-subscriptions.php', self::$active_plugins ) || array_key_exists( 'woocommerce-subscriptions/woocommerce-subscriptions.php', self::$active_plugins );
		return false;
	}
	
	// Yoast SEO
	static function wcfm_yoast_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wordpress-seo/wp-seo.php', self::$active_plugins ) || array_key_exists( 'wordpress-seo/wp-seo.php', self::$active_plugins );
		return false;
	}
	
	// WooCommerce Custom Product Tabs Lite
	static function wcfm_wc_tabs_lite_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-custom-product-tabs-lite/woocommerce-custom-product-tabs-lite.php', self::$active_plugins ) || array_key_exists( 'woocommerce-custom-product-tabs-lite/woocommerce-custom-product-tabs-lite.php', self::$active_plugins );
		return false;
	}
	
	// WooCommerce Barcode & ISBN
	static function wcfm_wc_barcode_isbn_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-barcode-isbn/AG-barcode-ISBN.php', self::$active_plugins ) || array_key_exists( 'woocommerce-barcode-isbn/AG-barcode-ISBN.php', self::$active_plugins );
		return false;
	}
	
	// WooCommerce MSRP Pricing
	static function wcfm_wc_msrp_pricing_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-msrp-pricing/woocommerce-msrp.php', self::$active_plugins ) || array_key_exists( 'woocommerce-msrp-pricing/woocommerce-msrp.php', self::$active_plugins );
		return false;
	}
	
	// Quantities and Units for WooCommerce
	static function wcfm_wc_quantities_units_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'quantities-and-units-for-woocommerce/quantites-and-units.php', self::$active_plugins ) || array_key_exists( 'quantities-and-units-for-woocommerce/quantites-and-units.php', self::$active_plugins );
		return false;
	}
	
	// WP Job Manager
	static function wcfm_wp_job_manager_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wp-job-manager/wp-job-manager.php', self::$active_plugins ) || array_key_exists( 'wp-job-manager/wp-job-manager.php', self::$active_plugins );
		return false;
	}
	
	// WooCommerce PDF Invoices & Packing Slips Support
	static function wcfm_wc_pdf_invoices_packing_slips_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php', self::$active_plugins ) || array_key_exists( 'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php', self::$active_plugins );
		return false;
	}
	
	// GEO my Wp Support
	static function wcfm_geo_my_wp_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'geo-my-wp/geo-my-wp.php', self::$active_plugins ) || array_key_exists( 'geo-my-wp/geo-my-wp.php', self::$active_plugins );
		return false;
	}
	
	// WC Paid Listing Support
	static function wcfm_wc_paid_listing_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wp-job-manager-wc-paid-listings/wp-job-manager-wc-paid-listings.php', self::$active_plugins ) || array_key_exists( 'wp-job-manager-wc-paid-listings/wp-job-manager-wc-paid-listings.php', self::$active_plugins );
		return false;
	}
	
	// WooCommerce Product Fees Support
	static function wcfm_wc_product_fees_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-product-fees/woocommerce-product-fees.php', self::$active_plugins ) || array_key_exists( 'woocommerce-product-fees/woocommerce-product-fees.php', self::$active_plugins );
		return false;
	}
	
	// WooCommerce Bulk Discount Support
	static function wcfm_wc_bulk_discount_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-bulk-discount/woocommerce-bulk-discount.php', self::$active_plugins ) || array_key_exists( 'woocommerce-bulk-discount/woocommerce-bulk-discount.php', self::$active_plugins );
		return false;
	}
	
	// WC Rental & Booking Free Support
	static function wcfm_wc_rental_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'booking-and-rental-system-woocommerce/redq-rental-and-bookings.php', self::$active_plugins ) || array_key_exists( 'booking-and-rental-system-woocommerce/redq-rental-and-bookings.php', self::$active_plugins );
		return false;
	}
	
	// YITH Auctions Free Support
	static function wcfm_yith_auction_free_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'yith-auctions-for-woocommerce/init.php', self::$active_plugins ) || array_key_exists( 'yith-auctions-for-woocommerce/init.php', self::$active_plugins );
		return false;
	}
	
	// WC Table Rate Shipping - 2.5.1
	static function wcfm_wc_table_rates_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php', self::$active_plugins ) || array_key_exists( 'woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php', self::$active_plugins );
		return false;
	}
	
	// WCMp Advanced Shipping - 2.5.1
	static function wcfm_wcmp_advanced_shipping_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wcmp-advance-shipping/wcmp-advance-shipping.php', self::$active_plugins ) || array_key_exists( 'wcmp-advance-shipping/wcmp-advance-shipping.php', self::$active_plugins );
		return false;
	}
	
	// WCMp Stripe Connect Support - 3.1.6
	static function wcfm_wcmp_stripe_connect_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'marketplace-stripe-gateway/marketplace-stripe-gateway.php', self::$active_plugins ) || array_key_exists( 'marketplace-stripe-gateway/marketplace-stripe-gateway.php', self::$active_plugins );
		return false;
	}
	
	// WC Role Based Price Support - 3.2.8
	static function wcfm_wc_role_based_price_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-role-based-price/woocommerce-role-based-price.php', self::$active_plugins ) || array_key_exists( 'woocommerce-role-based-price/woocommerce-role-based-price.php', self::$active_plugins );
		return false;
	}
	
	// Xadaptor DHL WooCommerce Shipping support - 3.3.0
	static function wcfm_wc_dhl_shipping_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'dhl-woocommerce-shipping/dhl-woocommerce-shipping.php', self::$active_plugins ) || array_key_exists( 'dhl-woocommerce-shipping/dhl-woocommerce-shipping.php', self::$active_plugins );
		return false;
	}
	
	// Xadaptor FedEX WooCommerce Shipping support - 3.3.6
	static function wcfm_wc_fedex_shipping_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'fedex-woocommerce-shipping/fedex-woocommerce-shipping.php', self::$active_plugins ) || array_key_exists( 'fedex-woocommerce-shipping/fedex-woocommerce-shipping.php', self::$active_plugins );
		return false;
	}
	
	// Dokan Pro - 3.3.0
	static function dokanpro_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'dokan-pro/dokan-pro.php', self::$active_plugins ) || array_key_exists( 'dokan-pro/dokan-pro.php', self::$active_plugins );
		return false;
	}
	
	// Woocommerce Germanized Support - 3.3.3
	static function wcfm_woocommerce_germanized_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-germanized/woocommerce-germanized.php', self::$active_plugins ) || array_key_exists( 'woocommerce-germanized/woocommerce-germanized.php', self::$active_plugins );
		return false;
	}
	
	// BuddyPress Support - 3.3.3
	static function wcfm_biddypress_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'buddypress/bp-loader.php', self::$active_plugins ) || array_key_exists( 'buddypress/bp-loader.php', self::$active_plugins );
		return false;
	}
	
	// WC Vendors MangoPay Support - 3.4.3
	static function wcfm_wc_mangopay_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'mangopay-woocommerce/mangopay-woocommerce.php', self::$active_plugins ) || array_key_exists( 'mangopay-woocommerce/mangopay-woocommerce.php', self::$active_plugins );
		return false;
	}
	
	// WC Vendors Stripe Connect Support - 3.4.3
	static function wcfm_wcv_stripe_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wc-vendors-gateway-stripe-connect/gateway-stripe.php', self::$active_plugins ) || array_key_exists( 'wc-vendors-gateway-stripe-connect/gateway-stripe.php', self::$active_plugins );
		return false;
	}
}