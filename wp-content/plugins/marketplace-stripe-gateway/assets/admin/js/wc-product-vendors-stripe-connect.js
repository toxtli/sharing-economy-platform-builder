jQuery(document).ready(function ($) {
    $('#wcpv_vendor_settings_payment_gateway').change(function () {
        var clientIDLive = $('#wcpv_vendor_settings_paypal_masspay_client_id_live').parents('tr').eq(0),
                clientSecretLive = $('#wcpv_vendor_settings_paypal_masspay_client_secret_live').parents('tr').eq(0),
                clientIDSandbox = $('#wcpv_vendor_settings_paypal_masspay_client_id_sandbox').parents('tr').eq(0),
                clientSecretSandbox = $('#wcpv_vendor_settings_paypal_masspay_client_secret_sandbox').parents('tr').eq(0),
                paypalMasspayEvn = $('#wcpv_vendor_settings_paypal_masspay_environment').parents('tr').eq(0),
                stripeTestClientId = $('#wcpv_vendor_settings_stripe_test_client_id').parents('tr').eq(0),
                stripeLiveClientId = $('#wcpv_vendor_settings_stripe_live_client_id').parents('tr').eq(0);

        if ('paypal-masspay' === $(this).val()) {
            paypalMasspayEvn.show();
            clientIDLive.show();
            clientSecretLive.show();
            clientIDSandbox.show();
            clientSecretSandbox.show();
            stripeTestClientId.hide();
            stripeLiveClientId.hide();
        } else {
            paypalMasspayEvn.hide();
            clientIDLive.hide();
            clientSecretLive.hide();
            clientIDSandbox.hide();
            clientSecretSandbox.hide();
            stripeTestClientId.show();
            stripeLiveClientId.show();
        }
    }).change();
});