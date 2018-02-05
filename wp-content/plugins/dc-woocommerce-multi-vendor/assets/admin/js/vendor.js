jQuery(function ($) {
    var $product_screen = $('.edit-php.post-type-product'),
            $woocommerce_impirt_button = $product_screen.find('.page-title-action:eq( 1 )'),
            $woocommerce_export_button = $product_screen.find('.page-title-action:eq( 2 )');
            $woocommerce_impirt_button.remove();
            $woocommerce_export_button.remove();
});


