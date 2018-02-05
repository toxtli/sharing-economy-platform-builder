(function($) {

    $(document).ready(function() {

        /* ==== COMPARE PLANS TOOLTIP =======*/

        $('.compare-wrapper ul li span.dashicons-editor-help').on('click', function(){

            var $tooltip = $(this).closest('li').find('.tooltip');

            if($tooltip.hasClass('hide')){
                $('.compare-wrapper .tooltip').addClass('hide');
                $tooltip.removeClass('hide');
            }
            else {
                $tooltip.addClass('hide');
            }

        });

        $('html').bind('click', function(e) {

            if($(e.target).closest('.compare-wrapper ul li span.dashicons-editor-help').length == 0 && $(e.target).closest('.tooltip').length == 0){
                $('.compare-wrapper .tooltip').addClass('hide');
            }

        });

    });

})(jQuery);