jQuery(document).ready(function () {
     
     /**
    * verify the api code
    * @since 1.0
    */
    jQuery(document).on('click', '#save-gs-code', function () {
        jQuery( ".loading-sign" ).addClass( "loading" );
        var data = {
        action: 'verify_gs_integation',
        code: jQuery('#gs-code').val(),
        security: jQuery('#gs-ajax-nonce').val()
      };
      jQuery.post(ajaxurl, data, function (response ) {
          if( ! response.success ) { 
            jQuery( ".loading-sign" ).removeClass( "loading" );
            jQuery( "#gs-validation-message" ).empty();
            jQuery("<span class='error-message'>Access code Can't be blank</span>").appendTo('#gs-validation-message');
          } else {
            jQuery( ".loading-sign" ).removeClass( "loading" );
            jQuery( "#gs-validation-message" ).empty();
            jQuery("<span class='gs-valid-message'>Access Code Saved. But do check the debug log for invalid access code.</span>").appendTo('#gs-validation-message'); 
          }
      });
      
    });   
         
});
