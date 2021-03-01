/*global define*/
define([
    'jquery',
    'uiComponent',
    'uiLayout',
    'mageUtils'
], function ($, Component, layout, utils) {
    return Component.extend({
        defaults: {
            dataUrl: '/faq/index/rating',
            voteUrl: '/faq/index/vote',
            itemsTemplate: 'Amasty_Faq/rating/yesno',
            items: []
        },
        initialize: function (config) {
            this._super().getRatings();
        },
        initObservable: function () {
            this._super();

            return this;
        },
        getRatings: function () {
            var self = this;
            $.ajax({
                url: this.dataUrl,
                data: {items: this.items, isAjax: true},
                method: 'post',
                global: false,
                dataType: 'json',
                success: function (responce) {
                    self.createItems(responce);
                }
            });
        },
        createItems: function (items) {
            for (var item in items) {
                if (items.hasOwnProperty(item)) {
                    layout([
                        this.createComponent(items[item])
                    ]);
                }
            }
        },
        createComponent: function (item) {
            var rendererTemplate,
                rendererComponent,
                templateData;

            templateData = {
                parentName: this.name,
                name: 'faq-rating-item-' + item.id
            };
            rendererTemplate = {
                template: this.itemsTemplate,
                parent: '${ $.$data.parentName }',
                name: '${ $.$data.name }',
                displayArea: 'frontend',
                component: 'Amasty_Faq/js/rating/item',
                voteUrl: this.voteUrl
            };
            rendererComponent = utils.template(rendererTemplate, templateData);
            utils.extend(rendererComponent, {
                id: item.id,
                positiveRating: parseInt(item.positiveRating),
                negativeRating: parseInt(item.negativeRating),
                isVoted: item.isVoted,
                isPositiveVoted: item.isPositiveVoted
            });

            return rendererComponent;
        }
    });
});
