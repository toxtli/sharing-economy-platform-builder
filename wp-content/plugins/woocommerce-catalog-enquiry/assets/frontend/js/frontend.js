jQuery( function( $ ) { 
	
//	$( ".variations_form" ).on( "woocommerce_variation_select_change", function (e) {
//		var variation_name = $(this).parent().parent().find(".label label").html();
//		var select_value = $(".variations select").val();
//		var product_id = $('form.variations_form').attr("data-product_id");
//		var variation_real_name = $(this).attr("name");			
//		var ajax_url = catalog_enquiry_front.ajaxurl;
//		var variation_array = $('form.variations_form').attr('data-product_variations');
//
//		var data = {
//			'action': 'add_variation_for_enquiry_mail',
//			'variation_name': variation_name,
//			'variation_real_name': variation_real_name,
//			'variation_value': select_value,
//			'product_id': product_id,
//			'variation_array': variation_array
//		};
//		$.post(ajax_url, data, function(response) { 
//			console.log(response);														
//		});							
//	});
	// variation id
	$(window).bind('found_variation', function(event, variation) {
		if (variation == null) {
		}else{
                        var variation_data = {};
			var count  = 0;
			var chosen = 0;
			var variation_selector = '';
			var variation_id = '';
			if(event.hasOwnProperty('target')){
				variation_selector = event.target;
			}else{
				variation_selector = 'form.variations_form.cart';
			}

			$(variation_selector).find( '.variations select' ).each( function() {
				var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
				var value          = $( this ).val() || '';

				if ( value.length > 0 ) {
					chosen ++;
				}

				count ++;
				variation_data[ attribute_name ] = value;
			});
                        
			if(variation.hasOwnProperty('id')){
                                variation_id = variation.id;
				$('#product_id_for_enquiry').val(variation.id);
			}else if(variation.hasOwnProperty('variation_id')){
                                variation_id = variation.variation_id;
				$('#product_id_for_enquiry').val(variation.variation_id);
			}else{
                                variation_id = $('form.variations_form').attr("data-product_id");
				$('#product_id_for_enquiry').val($('form.variations_form').attr("data-product_id"));
			}
                        
                        var ajax_url = catalog_enquiry_front.ajaxurl;
                        var data = {
                                'action': 'add_variation_for_enquiry_mail',
                                'product_id': variation_id,
                                'variation_data': variation_data
                        };
                        $.post(ajax_url, data, function(response) { 
                                console.log(response);														
                        });					
		}
	});
	$('.variations_form').trigger( 'found_variation' );

	// Modal Close
	$("#woo_catalog .catalog_modal .close, #woo_catalog .catalog_modal .btn-default").on('click',function(){
		//$("#responsive").hide();
		$("#responsive").slideToggle(500);
	});

	$('#woo_catalog .woo_catalog_enquiry_btn').on('click', function(){
		$("#woo_catalog #responsive").slideToggle(1000);	
	});
});

function validateEmail($email) {
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	return emailReg.test( $email );
}
function submitthis(str) {
	var name = document.getElementById('woo_user_name').value;				
	var email = document.getElementById('woo_user_email').value;
	var nonce = document.getElementById('wc_catalog_enq').value;
	var enquiry_product_type = document.getElementById('enquiry_product_type').value;
	var subject = '';
	var phone = '';
	var address = '';
	var comment = '';
	var fd = new FormData(); 
	var json_arr = catalog_enquiry_front.json_arr;					
	if(json_arr.indexOf("subject") != -1) {
		subject = document.getElementById('woo_user_subject').value;					
	}				
	if(json_arr.indexOf("phone") != -1) {
		phone = document.getElementById('woo_user_phone').value;					
	}				
	if(json_arr.indexOf("address") != -1) {
		address = document.getElementById('woo_user_address').value;					
	}				
	if(json_arr.indexOf("comment") != -1) {
		comment = document.getElementById('woo_user_comment').value;					
	}
	if(json_arr.indexOf("fileupload") != -1) {
		var files_data = jQuery('#woo_user_fileupload'); 
        jQuery.each(jQuery(files_data), function(i, obj) {
            jQuery.each(obj.files,function(j,file){
                fd.append('fileupload[' + j + ']', file);
            })
        });
	}									
	var product_name = document.getElementById('product_name_for_enquiry').value;				
	var product_url = document.getElementById('product_url_for_enquiry').value;
	var product_id = document.getElementById('product_id_for_enquiry').value;	
	if( typeof(catalog_enquiry_front.settings.is_captcha) != 'undefined' && catalog_enquiry_front.settings.is_captcha !== null && catalog_enquiry_front.settings.is_captcha =="Enable") { 
	var captcha = document.getElementById('woo_catalog_captcha');	
    }
	
	if(name == '' || name == ' ') {
		document.getElementById('msg_for_enquiry_error').innerHTML= catalog_enquiry_front.error_levels.name_required;					
		document.getElementById('woo_user_name').focus();
		return false;					
	}
	
	if(email == '' || email == ' ') {
		document.getElementById('msg_for_enquiry_error').innerHTML= catalog_enquiry_front.error_levels.email_required;				
		document.getElementById('woo_user_email').focus();
		return false;					
	}
	if( !validateEmail(email)) {
		document.getElementById('msg_for_enquiry_error').innerHTML= catalog_enquiry_front.error_levels.email_valid;
		document.getElementById('woo_user_email').focus();
		return false;
	}
	
	if( typeof(catalog_enquiry_front.settings.is_captcha) != 'undefined' && catalog_enquiry_front.settings.is_captcha !== null && catalog_enquiry_front.settings.is_captcha=="Enable") { 
	
		if(captcha.value == '' || captcha.value == ' ' ) {					
			document.getElementById('msg_for_enquiry_error').innerHTML= catalog_enquiry_front.error_levels.captcha_required;					
			document.getElementById('woo_catalog_captcha').focus();
			return false;					
		}
		if(captcha.value != catalog_enquiry_front.captcha ) {
			document.getElementById('msg_for_enquiry_error').innerHTML=catalog_enquiry_front.error_levels.captcha_valid;					
			document.getElementById('woo_catalog_captcha').focus();
			return false;					
		}
	}
	jQuery("#loader_after_sumitting_the_form").show();
	document.getElementById('msg_for_enquiry_error').innerHTML='';

	var ajax_url = catalog_enquiry_front.ajaxurl;
	if(json_arr.indexOf("fileupload") != -1) {
		fd.append('action', 'send_enquiry_mail'); 
		fd.append('wc_catalog_enq', nonce);  
        fd.append('woo_customer_name', name); 
        fd.append('woo_customer_email', email); 
        fd.append('woo_customer_subject', subject); 
        fd.append('woo_customer_phone', phone); 
        fd.append('woo_customer_address', address); 
        fd.append('woo_customer_comment', comment); 
        fd.append('woo_customer_product_name', product_name); 
        fd.append('woo_customer_product_url', product_url); 
        fd.append('woo_customer_product_id', product_id); 
        fd.append('enquiry_product_type', enquiry_product_type); 
		jQuery.ajax({
	        type : 'post',
	        url : ajax_url,
	        data : fd,
	        contentType: false,       
			cache: false,             
			processData:false,
	        success : function( response ) { 
	        	if(response.status==1) {	
					jQuery("#loader_after_sumitting_the_form").hide();
					jQuery('#msg_for_enquiry_sucesss').html('');	 	 
					jQuery('#msg_for_enquiry_sucesss').html(catalog_enquiry_front.ajax_success_msg);											
					jQuery('#woo_user_name').val('');
					jQuery('#woo_user_email').val('');
					jQuery('#woo_catalog_captcha').val('');
					if(json_arr.indexOf("subject") != -1) {
						jQuery('#woo_user_subject').val('');																									
					}				
					if(json_arr.indexOf("phone") != -1) {
						jQuery('#woo_user_phone').val('');																									
					}				
					if(json_arr.indexOf("address") != -1) {
						jQuery('#woo_user_address').val('');
											
					}				
					if(json_arr.indexOf("comment") != -1) {
						jQuery('#woo_user_comment').val('');																									
					}
					if(json_arr.indexOf("fileupload") != -1) {
						jQuery('#woo_user_fileupload').val('');					
					}
					
					if(typeof(catalog_enquiry_front.settings.is_page_redirect) != 'undefined' && catalog_enquiry_front.settings.is_page_redirect !== null) {
						window.location.href=catalog_enquiry_front.redirect_link;
					}											 
				}else if(response.status==2){
					jQuery("#loader_after_sumitting_the_form").hide();
					jQuery('#msg_for_enquiry_sucesss').html('');
					jQuery('#msg_for_enquiry_error').html(catalog_enquiry_front.error_levels.filetype_error);	
				}else if(response.status==3){
					jQuery("#loader_after_sumitting_the_form").hide();
					jQuery('#msg_for_enquiry_sucesss').html('');
					jQuery('#msg_for_enquiry_error').html(catalog_enquiry_front.error_levels.filesize_error);	
				}
				else {	
					jQuery("#loader_after_sumitting_the_form").hide();
					jQuery('#msg_for_enquiry_sucesss').html('');
					if(response.error_report != ''){
						jQuery('#msg_for_enquiry_error').html(response.error_report);	
					}else{
						jQuery('#msg_for_enquiry_error').html(catalog_enquiry_front.error_levels.ajax_error);	
					}									 
				}
			}
	    });
	
	}else{
		var data = {
			'action': 'send_enquiry_mail',
			'wc_catalog_enq': nonce,
			'woo_customer_name': name,
			'woo_customer_email': email,
			'woo_customer_subject': subject,
			'woo_customer_phone': phone,
			'woo_customer_address': address,
			'woo_customer_comment': comment,
			'woo_customer_product_name': product_name,
			'woo_customer_product_url': product_url,
			'woo_customer_product_id' : product_id,
			'enquiry_product_type' : enquiry_product_type
								 
		};
		jQuery.post(ajax_url, data, function(response) {					
				
			if(response.status==1) {	
				jQuery("#loader_after_sumitting_the_form").hide();					 	 												
				jQuery('#msg_for_enquiry_sucesss').html('');
				jQuery('#msg_for_enquiry_sucesss').html(catalog_enquiry_front.ajax_success_msg);
				jQuery('#woo_user_name').val('');
				jQuery('#woo_user_email').val('');
				jQuery('#woo_catalog_captcha').val('');
				if(json_arr.indexOf("subject") != -1) {
					jQuery('#woo_user_subject').val('');																									
				}				
				if(json_arr.indexOf("phone") != -1) {
					jQuery('#woo_user_phone').val('');																									
				}				
				if(json_arr.indexOf("address") != -1) {
					jQuery('#woo_user_address').val('');
										
				}				
				if(json_arr.indexOf("comment") != -1) {
					jQuery('#woo_user_comment').val('');																									
				}
				if(typeof(catalog_enquiry_front.settings.is_page_redirect) != 'undefined' && catalog_enquiry_front.settings.is_page_redirect !== null) {
					window.location.href=catalog_enquiry_front.redirect_link;
				}												 
			}else if(response.status==2){
				jQuery("#loader_after_sumitting_the_form").hide();
				jQuery('#msg_for_enquiry_sucesss').html('');
				jQuery('#msg_for_enquiry_error').html(catalog_enquiry_front.error_levels.filetype_error);	
			}else if(response.status==3){
				jQuery("#loader_after_sumitting_the_form").hide();
				jQuery('#msg_for_enquiry_sucesss').html('');
				jQuery('#msg_for_enquiry_error').html(catalog_enquiry_front.error_levels.filesize_error);	
			}
			else {	
				jQuery("#loader_after_sumitting_the_form").hide();
				jQuery('#msg_for_enquiry_sucesss').html('');
				if(response.error_report != ''){
					jQuery('#msg_for_enquiry_error').html(response.error_report);	
				}else{
					jQuery('#msg_for_enquiry_error').html(catalog_enquiry_front.error_levels.ajax_error);	
				}									 
			}					
		});	
	}					
}

var modal = document.getElementById('responsive');
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}