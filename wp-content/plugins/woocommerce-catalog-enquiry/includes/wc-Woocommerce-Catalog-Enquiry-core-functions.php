<?php
if(!function_exists('get_Woocommerce_Catalog_Enquiry_settings')) {
  function get_Woocommerce_Catalog_Enquiry_settings($name = '', $tab = '') {
    if(empty($tab) && empty($name)) return '';
    if(empty($tab)) return get_option($name);
    if(empty($name)) return get_option("dc_{$tab}_settings_name");
    $settings = get_option("dc_{$tab}_settings_name");
    if(!isset($settings[$name])) return '';
    return $settings[$name];
  }
}


if(!function_exists('woocommerce_catalog_enquiry_alert_notice')) {
	 function woocommerce_catalog_enquiry_alert_notice() {
    ?>
    <div id="message" class="error">
      <p><?php printf( __( '%sWoocommerce Catalog Enquiry is inactive.%s The %sWooCommerce plugin%s must be active for the Woocommerce Catalog Enquiry to work. Please %sinstall & activate WooCommerce%s', WC_WOOCOMMERCE_CATALOG_ENQUIRY_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
    </div>
		<?php
  }
}

?>
