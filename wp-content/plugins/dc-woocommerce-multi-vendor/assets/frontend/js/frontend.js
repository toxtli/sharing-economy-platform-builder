/* global front_end_param */

jQuery(document).ready(function ($) {
    $('#report_abuse').click(function (e) {
        e.preventDefault();
        $('#report_abuse_form').simplePopup();
    });

    $('.submit-report-abuse').on('click', function (e) {
        var inpObjName = document.getElementById("report_abuse_name");
        if (inpObjName.checkValidity() === false) {
            $('#report_abuse_name').next('span').html(inpObjName.validationMessage);
        } else {
            $('#report_abuse_name').next('span').html('');
        }
        var inpObjEmail = document.getElementById("report_abuse_email");
        if (inpObjEmail.checkValidity() === false) {
            $('#report_abuse_email').next('span').html(inpObjEmail.validationMessage);
        } else {
            $('#report_abuse_email').next('span').html('');
        }
        var inpObjMessage = document.getElementById("report_abuse_msg");
        if (inpObjMessage.checkValidity() === false) {
            $('#report_abuse_msg').next('span').html(inpObjMessage.validationMessage);
        } else {
            $('#report_abuse_msg').next('span').html('');
        }
        e.preventDefault();
        var data = {
            action: 'send_report_abuse',
            product_id: $('.report_abuse_product_id').val(),
            name: $('.report_abuse_name').val(),
            email: $('.report_abuse_email').val(),
            msg: $('.report_abuse_msg').val(),
        };
        if (inpObjName.checkValidity() && inpObjEmail.checkValidity() && inpObjMessage.checkValidity()) {
            $.post(woocommerce_params.ajax_url, data, function (responsee) {
                $('.simplePopupClose').click();
                $('#report_abuse').css({'box-shadow': 'none', 'cursor': 'default', 'color': '#686868'});
                $('#report_abuse').attr('href', 'javascript:void(0)');
                $('#report_abuse').off('click');
                $('#report_abuse').text(front_end_param.report_abuse_msg);
            });
        }
    });

    $('#wcmp_widget_vendor_search .search_keyword').on('input', function () {

        var vendor_search_data = {
            action: 'vendor_list_by_search_keyword',
            s: $(this).val(),
            vendor_search_nonce: $('#wcmp_vendor_search_nonce').val()
        }

        $.post(woocommerce_params.ajax_url, vendor_search_data, function (response) {
            $('#wcmp_widget_vendor_list').html('');
            $('#wcmp_widget_vendor_list').html(response);

        });

    });

    $('#vendor_sort_type').change(function () {
        if ($(this).val() == 'category') {
            $('#vendor_sort_category').show();
        } else {
            $('#vendor_sort_category').hide();
        }
    }).change();
});