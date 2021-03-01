define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'jquery'
], function (_, uiRegistry, select, $) {
    'use strict';
    return select.extend({
        /**
         * Init
         */
        initialize: function () {
            this._super();
            this.resetVisibility();
            return this;
        },
        toggleVisibilityOnRender: function (visibility, time) {
            $('#vendor_bank_link').on('click', function () {
                window.location.href = window.vendorBankAccountUrl;
                return false;
            });

            $('.new_withdrawal .admin__additional-info .withdrawal-limit div strong span').html(window.vendorWithdrawalLimitCurrency);
            var withdrawal_amount = uiRegistry.get('index = withdrawal_amount');
            var vendor_id = uiRegistry.get('index = vendor_id');
            vendor_id.value(window.vendorId);
            if (withdrawal_amount !== undefined) {
                withdrawal_amount.validation = {'required-entry': true, 'validate-number': true, 'less-than-equals-to': window.vendorWithdrawalLimit, 'not-negative-amount': true}
            }
            var bankDiv = $('.new_withdrawal .admin__additional-info .account-detail');
            var bank_account_id = uiRegistry.get('index = bank_account_id');
            if (bank_account_id !== undefined) {
                if (visibility) {
                    if (bank_account_id) {
                        window.bankAccountDetailAjaxUrl = window.bankAccountDetailAjaxUrl + 'id/' + bank_account_id.value();
                        if (window.bankAccountDetailAjaxUrl) {
                            $.ajax({
                                url: window.bankAccountDetailAjaxUrl,
                                type: 'POST',
                                showLoader: true,
                                dataType: 'json',
                                data: {
                                    id: this.value()
                                },
                                success: function (response) {
                                    if (response) {
                                        var bankContent = '';
                                        if (response['account_name']) {
                                            bankContent += '<div class="row"><div class="half1">Account Name</div><div class="half2">' + response['account_name'] + '</div></div>';
                                        }
                                        if (response['bank_name']) {
                                            bankContent += '<div class="row"><div class="half1">Bank Name</div><div class="half2">' + response['bank_name'] + '</div></div>';
                                        }
                                        if (response['bsb']) {
                                            bankContent += '<div class="row"><div class="half1">BSB Number</div><div class="half2">' + response['bsb'] + '</div></div>';
                                        }
                                        if (response['account_number']) {
                                            bankContent += '<div class="row"><div class="half1">Account Number</div><div class="half2">' + response['account_number'] + '</div></div>';
                                        }
                                        bankDiv.html(bankContent);
                                        bankDiv.removeClass('hidden');
                                    }
                                },
                                fail: function () {
                                    bankDiv.html('Error');
                                    bankDiv.removeClass('hidden');
                                }
                            });
                        }
                    }
                }
                return;
            } else {
                var self = this;
                setTimeout(function () {
                    self.toggleVisibilityOnRender(visibility, time);
                }, time);
            }
        },
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            if (value) {
                this.showField();
            } else {
                this.hideField();
            }
            return this._super();
        },
        resetVisibility: function () {
            if (this.value()) {
                this.showField();
            } else {
                this.hideField();
            }
        },
        showField: function () {
            this.toggleVisibilityOnRender(true, 3000);
        },
        hideField: function () {
            this.toggleVisibilityOnRender(false, 3000);
        }
    });
});