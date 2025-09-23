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
                type: 'wechat',
                component: 'Fulloop_Orders/js/view/payment/method-renderer/wechat-method'
            }
        );
        return Component.extend({});
    }
);
