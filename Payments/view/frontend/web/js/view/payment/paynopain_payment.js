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
                type: 'paynopain_payment',
                component: 'Paynopain_Payments/js/view/payment/method-renderer/paynopain_payment-method'
            }
        );
        return Component.extend({});
    }
);