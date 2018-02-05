<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/widget/store-location.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version     0.0.1
 */
extract( $instance );
global $WCMp;

?><div class="wcmp-store-location-wrapper">
		<div class="store-maps" class="gmap3" style="height: 300px;"></div>
		<a href="<?php echo $gmaps_link ?>" target="_blank"><?php _e( 'Show in Google Maps', 'dc-woocommerce-multi-vendor' ) ?></a>
</div>
<?php 
wp_add_inline_script( 'wcmp-gmap3', 
  '(function ($) {
    $(".store-maps").gmap3({
        map   : {
            options: {
                zoom                     : 15,
                disableDefaultUI         : true,
                mapTypeControl           : false,
                panControl               : false,
                zoomControl              : false,
                scaleControl             : false,
                streetViewControl        : false,
                rotateControl            : false,
                rotateControlOptions     : false,
                overviewMapControl       : false,
                OverviewMapControlOptions: false
            },
            address: "'.$location.'"
        },
        marker: {
            address: "'.$location.'",
        }
    });
})(jQuery)');