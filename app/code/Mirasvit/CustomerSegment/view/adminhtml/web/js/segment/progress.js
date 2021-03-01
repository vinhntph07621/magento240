define([
    'jquery',
    'jquery/ui',
    'uiComponent',
    'ko',
    'underscore'
], function ($, ui, Component, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mirasvit_CustomerSegment/segment/progress',
            refresher: null
        },

        listener: false,

        initialize: function () {
            this._super();

            _.bindAll(this, 'afterRender');

            this.state = ko.observable({});
        },

        afterRender: function (element) {
            var self = this;

            this.$element = $(element);

            this.$element.dialog({
                modal: true,
                autoOpen: false,
                resizable: false,
                title: $('h1.page-title').html(),

                open: function () {
                    $(this).closest('.ui-dialog').addClass('ui-dialog-active').addClass('segment__dialog-progress');
                },

                close: function () {
                    $(this).closest('.ui-dialog').removeClass('ui-dialog-active');
                    if (self.refresher) {
                        self.refresher.stop();
                    }
                }
            });
        },

        show: function () {
            if (!this.$element.dialog('isOpen')) {
                this.$element.dialog('open');
            }
        },

        hide: function () {
            if (this.$element.dialog('isOpen')) {
                this.$element.dialog('close');
            }
        },

        setProgress: function (progress) {
            this.state(progress);
        },

        setRefresher: function (refresher) {
            this.refresher = refresher;
        }
    });
});

