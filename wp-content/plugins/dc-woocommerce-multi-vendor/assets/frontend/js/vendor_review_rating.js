jQuery(document).ready(function($){
	$( '#wcmp_vendor_reviews #respond #rating' ).hide().before( '<p class="stars"><span><a class="star-1" href="#">1</a><a class="star-2" href="#">2</a><a class="star-3" href="#">3</a><a class="star-4" href="#">4</a><a class="star-5" href="#">5</a></span></p>' );

	$( 'body' )
		.on( 'click', '#wcmp_vendor_reviews #respond p.stars a', function() {
			var $star   	= $( this ),
			$rating 	= $( this ).closest( '#respond' ).find( '#rating' ),
			$container 	= $( this ).closest( '.stars' );
			$rating.val( $star.text() );
			$star.siblings( 'a' ).removeClass( 'active' );
			$star.addClass( 'active' );
			$container.addClass( 'selected' );
			return false;
		})
		.on( 'click', '#wcmp_vendor_reviews #respond #submit', function() {				
			var $rating = $( this ).closest( '#respond' ).find( '#rating' ),
			rating  = $rating.val();
			if ( $rating.size() > 0 && ! rating ) {
				window.alert( wcmp_review_rating_msg.rating_error_msg_txt );
				return false;
			}
			var comment = $('#wcmp_vendor_reviews #respond #comment').val();
			if ( comment == '' || comment.length < 10 ) {
				window.alert( wcmp_review_rating_msg.review_error_msg_txt );
				return false;
			}
			var vendor_id = $('#wcmp_vendor_reviews #respond #wcmp_vendor_for_rating').val(); 
			var data = {
				action: 'wcmp_add_review_rating_vendor',
				rating: rating,
				comment: comment,
				vendor_id: wcmp_review_rating_msg.vendor_id
			}
			$.post(wcmp_review_rating_msg.ajax_url,data, function(response) {
				if( response == 1 ) {
					$('#wcmp_vendor_reviews #respond p#wcmp_seller_review_rating')
						.html(wcmp_review_rating_msg.review_success_msg_txt)
						.addClass('success_review_msg')
						.removeClass('error_review_msg');
					$rating.val('');
					$('#wcmp_vendor_reviews #respond #comment').val('');
					$(".stars").removeClass('selected');
					setTimeout(location.reload(), 2000);
				}
				else {
					$('#wcmp_vendor_reviews #respond p#wcmp_seller_review_rating')
						.html(wcmp_review_rating_msg.review_failed_msg_txt)
						.addClass('error_review_msg')
						.removeClass('success_review_msg');
				}
			});
			
		});
		$('input#wcmp_review_load_more').click(function(e){				
			var pageno = $('#vendor_review_rating_pagi_form #wcmp_review_rating_pageno');
			var postperpage = $('#vendor_review_rating_pagi_form #wcmp_review_rating_postperpage');
			var totalpage = $('#vendor_review_rating_pagi_form #wcmp_review_rating_totalpage');
			var totalreview = $('#vendor_review_rating_pagi_form #wcmp_review_rating_totalreview');
			var term_id = $('#vendor_review_rating_pagi_form #wcmp_review_rating_term_id');
			$('.wcmp_review_loader').show();
			var data = {
				action: 'wcmp_load_more_review_rating_vendor',
				pageno: pageno.val(),
				postperpage: postperpage.val(),
				totalpage: totalpage.val(),
				totalreview: totalreview.val(),
				term_id: term_id.val()
			}
			$.post(wcmp_review_rating_msg.ajax_url,data, function(response) {
				$('ol.vendor_comment_list').append(response);
				pageno.val(parseInt(pageno.val()) + parseInt('1'));
				if(parseInt(pageno.val()) >= parseInt(totalpage.val())) {
					$('input#wcmp_review_load_more').hide();
				}						
				$('.wcmp_review_loader').hide();						
			});				
		});

});