jQuery(document).ready(function ($) {
    $('.img_tip').each(function () {
        $(this).qtip({
            content: $(this).attr('data-desc'),
            position: {
                my: 'top center',
                at: 'bottom center',
                viewport: $(window)
            },
            show: {
                event: 'mouseover',
                solo: true,
            },
            hide: {
                inactive: 6000,
                fixed: true
            },
            style: {
                classes: 'qtip-dark qtip-shadow qtip-rounded qtip-dc-css'
            }
        });
    });

    $('.dc_datepicker').each(function () {
        $(this).datepicker({
            dateFormat: $(this).data('date_format'),
            changeMonth: true,
            changeYear: true
        });
    });

    $('.multi_input_holder').each(function () {
        var multi_input_holder = $(this);
        if (multi_input_holder.find('.multi_input_block').length == 1)
            multi_input_holder.find('.remove_multi_input_block').css('display', 'none');
        multi_input_holder.find('.multi_input_block').each(function () {
            if ($(this)[0] != multi_input_holder.find('.multi_input_block:last')[0]) {
                $(this).find('.add_multi_input_block').remove();
            }
        });

        multi_input_holder.find('.add_multi_input_block').click(function () {
            var holder_id = multi_input_holder.attr('id');
            var holder_name = multi_input_holder.data('name');
            var multi_input_blockCount = multi_input_holder.data('length');
            multi_input_blockCount++;
            var multi_input_blockEle = multi_input_holder.find('.multi_input_block:first').clone(true);

            multi_input_blockEle.find('textarea,input:not(input[type=button],input[type=submit])').val('');
            multi_input_blockEle.find('.multi_input_block_element').each(function () {
                var ele = $(this);
                var ele_name = ele.data('name');
                ele.attr('name', holder_name + '[' + multi_input_blockCount + '][' + ele_name + ']');
                ele.attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount);
                if (ele.parent().hasClass('dc-wp-fields-uploader')) {
                    var uploadEle = ele.parent();
                    uploadEle.find('img').attr('src', '').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_display').addClass('placeHolder');
                    uploadEle.find('.upload_button').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_button').show();
                    uploadEle.find('.remove_button').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_remove_button').hide();
                }

                if (ele.hasClass('dc_datepicker')) {
                    ele.removeClass('hasDatepicker').datepicker({
                        dateFormat: ele.data('date_format'),
                        changeMonth: true,
                        changeYear: true
                    });
                }

            });

            multi_input_blockEle.find('.add_multi_input_block').remove();
            multi_input_holder.append(multi_input_blockEle);
            multi_input_holder.find('.multi_input_block:last').append($(this));
            if (multi_input_holder.find('.multi_input_block').length > 1)
                multi_input_holder.find('.remove_multi_input_block').css('display', 'block');
            multi_input_holder.data('length', multi_input_blockCount);
        });

        multi_input_holder.find('.remove_multi_input_block').click(function () {
            var addEle = multi_input_holder.find('.add_multi_input_block').clone(true);
            $(this).parent().remove();
            multi_input_holder.find('.add_multi_input_block').remove();
            multi_input_holder.find('.multi_input_block:last').append(addEle);
            if (multi_input_holder.find('.multi_input_block').length == 1)
                multi_input_holder.find('.remove_multi_input_block').css('display', 'none');
        });
    });

    if ($('#commission_typee').val() == 'fixed_with_percentage') {
        $('#default_commissionn').closest("tr").css("display", "none");
        $('#fixed_with_percentage_qty').closest("tr").css("display", "none");
    } else if ($('#commission_typee').val() == 'fixed_with_percentage_qty') {
        $('#default_commissionn').closest("tr").css("display", "none");
        $('#fixed_with_percentage').closest("tr").css("display", "none");
    } else {
        $('#default_percentage').closest("tr").css("display", "none");
        $('#fixed_with_percentage').closest("tr").css("display", "none");
        $('#fixed_with_percentage_qty').closest("tr").css("display", "none");
    }

    $('#commission_typee').change(function () {
        var commission_type = $(this).val();
        if (commission_type == 'fixed_with_percentage') {
            $('#default_commissionn').closest("tr").css("display", "none");
            $('#default_percentage').val('');
            $('#fixed_with_percentage').val('');
            $('#default_percentage').closest("tr").show();
            $('#fixed_with_percentage').closest("tr").show();
            $('#fixed_with_percentage_qty').closest("tr").hide();
        } else if (commission_type == 'fixed_with_percentage_qty') {
            $('#default_commissionn').closest("tr").css("display", "none");
            $('#default_percentage').closest("tr").show();
            $('#fixed_with_percentage_qty').closest("tr").show();
            $('#fixed_with_percentage').closest("tr").hide();
            $('#default_percentage').val('');
            $('#fixed_with_percentage_qty').val('');
        } else {
            $('#default_commissionn').closest("tr").show();
            $('#default_percentage').closest("tr").css("display", "none");
            $('#fixed_with_percentage').closest("tr").css("display", "none");
            $('#fixed_with_percentage_qty').closest("tr").css("display", "none");
        }
    });

    if ($('#wcmp_disbursal_mode_admin').is(':checked')) {
        $('#payment_schedule').closest("tr").show();
    } else {
        $('#payment_schedule').closest("tr").css("display", "none");
    }

    if ($('#wcmp_disbursal_mode_vendor').is(':checked')) {
        $('#commission_transfer').closest("tr").show();
        $('#no_of_orders').closest("tr").show();
    } else {
        $('#commission_transfer').closest("tr").css("display", "none");
        $('#no_of_orders').closest("tr").css("display", "none");
    }

    $('#wcmp_disbursal_mode_admin').change(function () {
        if ($(this).is(':checked')) {
            $('#payment_schedule').closest("tr").show();
        } else {
            $('#payment_schedule').closest("tr").css("display", "none");
        }
    });

    $('#wcmp_disbursal_mode_vendor').change(function () {
        if ($(this).is(':checked')) {
            $('#commission_transfer').closest("tr").show();
            $('#no_of_orders').closest("tr").show();
        } else {
            $('#commission_transfer').closest("tr").css("display", "none");
            $('#no_of_orders').closest("tr").css("display", "none");
        }
    });
    // toggle check uncheck event on gatewar charge

    $('#payment_gateway_charge').change(function () {
        if ($(this).prop('checked')) {
            $('.payment_gateway_charge').show();
        } else {
            $('.payment_gateway_charge').hide();
        }
    });

    $('.automatic_payment_method').change(function () {
        var id = $(this).attr('id');
        if (id !== undefined) {
            var terget_id = 'gateway_charge' + id.split('payment_method')[1];
            if ($(this).is(':checked') && $('#payment_gateway_charge').prop('checked')) {
                $('#' + terget_id).closest('tr').show();
            } else {
                $('#' + terget_id).closest('tr').hide();
            }
        }
    }).change();
    // end
});