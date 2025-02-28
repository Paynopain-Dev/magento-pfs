define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/customer-data',
        'mage/translate'
    ],
    function (ko, $, Component, customer, customerData, $t) {
        'use strict';

        customerData.invalidate(['paynopain_payment_stored_cards']);
        customerData.reload(['paynopain_payment_stored_cards']);

        var controllerRedirect = window.checkoutConfig.payment.paynopain_payments.integrationTypeControllerUrl;
        var integrationType = window.checkoutConfig.payment.paynopain_payments.integrationType;
        var secure = window.checkoutConfig.payment.paynopain_payments.secure;
        var extraDataRedirect = '';
        var showStoredCards = ko.observable(customer.isLoggedIn());
        var saveCardCheckbox = ko.observable(false);

        return Component.extend({
            defaults: {
                template: 'Paynopain_Payments/payment/paynopain_payment',
                redirectAfterPlaceOrder: false,
            },
            showStoredCards: showStoredCards,
            saveCardCheckbox: saveCardCheckbox,

            getInstructions: function () {
                return window.checkoutConfig.payment.paynopain_payments[this.item.method].instructions;
            },
            getIcons: function () {
                return window.checkoutConfig.payment.paynopain_payments[this.item.method].icons;
            },
            afterPlaceOrder: function () {
                $.mage.redirect(controllerRedirect + extraDataRedirect);

                return false;
            },
            getStoredCards: function () {
                var localStorageStoredCards = customerData.get('paynopain_payment_stored_cards')();
                var storedCards = [];

                if (localStorageStoredCards.hasOwnProperty('stored_cards')) {
                    storedCards = customerData.get('paynopain_payment_stored_cards')().stored_cards[this.item.method];
                }

                return _.union(
                    _.map(storedCards, function (storedCard) {
                        var additional = (storedCard.additional) ? ' (' + storedCard.additional + ')' : '';

                        return {
                            'source_uuid': storedCard.source_uuid,
                            'desc': storedCard.bin + '-XX-XXX-XXXX-' + storedCard.last4 + ' | ' + storedCard.brand + additional
                        };
                    }),
                    [{ 'source_uuid': '', 'desc': $t('New card') }]
                );
            },
            showSaveCard: function () {
                if ($('#paynopain-stored-cards > option').length == 1) {
                    showStoredCards(false);
                }

                if ($('#paynopain-stored-cards option:selected').val() != '') {
                    extraDataRedirect = '?tokenized=1&suuid=' + $('#paynopain-stored-cards option:selected').val();
                    saveCardCheckbox(false);
                } else {
                    var saveCardValue = $('#paynopain_savecard').prop('checked') ? 1 : 0;
                    extraDataRedirect = '?saveCard=' + saveCardValue;
                    saveCardCheckbox(true);
                }
            },
            saveCardChange: function () {
                var saveCardValue = $('#paynopain_savecard').prop('checked') ? 1 : 0;
                extraDataRedirect = '?saveCard=' + saveCardValue;
            }
        });
    }
);
