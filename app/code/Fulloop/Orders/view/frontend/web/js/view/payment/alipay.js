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
                type: 'alipay',
                component: 'Fulloop_Orders/js/view/payment/method-renderer/alipay-method'
            }
        );
        return Component.extend({});
    }
);
