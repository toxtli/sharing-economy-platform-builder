jQuery(window).load(function(){
    jQuery(document).ready(function ($) {

        $(document).on('click','[data-popup-target]',function () {
            $('html').addClass('wcmp-overlay');
            var activePopup = $(this).attr('data-popup-target');
            var timeOut = $(this).data('time_out');
            $(activePopup).addClass('visible');
            if(timeOut !== undefined){
                setTimeout(function (){
                    clearPopup();
                },timeOut*60);
            }
        });

        $(document).keyup(function (e) {
            if (e.keyCode == 27 && $('html').hasClass('wcmp-overlay')) {
                clearPopup();
            }
        });

        $('.popup-exit').click(function () {
            clearPopup();

        });

        $('.popup-overlay').click(function () {
            clearPopup();
        });

        function clearPopup() {
            $('.popup.visible').addClass('transitioning').removeClass('visible');
            $('html').removeClass('wcmp-overlay');

            setTimeout(function () {
                $('.popup').removeClass('transitioning');
            }, 200);
        }

    });
}); 
