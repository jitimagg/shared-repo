define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'creditcard_installment',
                component: 'Fulloop_Orders/js/view/payment/method-renderer/creditcard_installment-method'
            }
        );
        return Component.extend({});
    }
);

function saveInstallmentPlan(e) {
    jQuery('#submit-installment').hide();

    var val = 3;
    if (jQuery(e).val()) {
        val = jQuery(e).val();
    }

    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");
    var raw = JSON.stringify({"installment_period": val});

    var requestOptions = {
        method: 'POST',
        headers: myHeaders,
        body: raw,
        redirect: 'follow'
    };

    fetch('/rest/V1/fulloop-orders/set-quote', requestOptions)
        .then(
            response => {
                response.text();
                jQuery('#submit-installment').show();
            }
        )
        .then(
            result => {
                jQuery('#submit-installment').show();
            }
        )
        .catch(
            error => {
                jQuery('#submit-installment').show();
            }
        );
}
