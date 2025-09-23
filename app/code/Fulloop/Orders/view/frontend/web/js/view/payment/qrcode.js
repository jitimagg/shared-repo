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
                type: 'qrcode',
                component: 'Fulloop_Orders/js/view/payment/method-renderer/qrcode-method'
            }
        );
        return Component.extend({});
    }
);
