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
                type: 'creditcard',
                component: 'Fulloop_Orders/js/view/payment/method-renderer/creditcard-method'
            }
        );
        return Component.extend({});
    }
);