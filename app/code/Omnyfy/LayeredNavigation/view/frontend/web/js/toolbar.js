define(
    [
        'jquery',
        'Omnyfy_LayeredNavigation/js/ajax',
        'Omnyfy_Core/js/helper/url',
    ],
    function($, ajax, urlHelper) {
        $.widget('omnyfy_layerednavigation.toolbar', {
            options: {
                modeControl: '[data-role="mode-switcher"]',
                directionControl: '[data-role="direction-switcher"]',
                orderControl: '[data-role="sorter"]',
                limitControl: '[data-role="limiter"]',
                mode: 'product_list_mode',
                direction: 'product_list_dir',
                order: 'product_list_order',
                limit: 'product_list_limit',
                modeDefault: 'grid',
                directionDefault: 'asc',
                orderDefault: 'position',
                limitDefault: '9',
                url: '',
                pager: '.pager',
                filterContainer: '#filter_container',
                resultContainer: '#result_container'
            },

            _create: function () {
                this._bind($(this.element).find(this.options.modeControl), this.options.mode, this.options.modeDefault);
                this._bind($(this.element).find(this.options.directionControl), this.options.direction, this.options.directionDefault);
                this._bind($(this.element).find(this.options.orderControl), this.options.order, this.options.orderDefault);
                this._bind($(this.element).find(this.options.limitControl), this.options.limit, this.options.limitDefault);
                this._bindPager($(this.element).find(this.options.pager));
            },

            _bind: function (element, paramName, defaultValue) {
                if (element.is("select")) {
                    element.on('change', {paramName: paramName, default: defaultValue}, $.proxy(this._processSelect, this));
                } else {
                    element.on('click', {paramName: paramName, default: defaultValue}, $.proxy(this._processLink, this));
                }
            },

            _bindPager: function(element) {
                element.find('a').on('click', function() {
                    ajax.load($(this).attr('href'), this.options.filterContainer, this.options.resultContainer);
                    return false;
                });
            },

            _processLink: function (event) {
                event.preventDefault();
                this.changeUrl(
                    event.data.paramName,
                    $(event.currentTarget).data('value'),
                    event.data.default
                );
            },

            _processSelect: function (event) {
                this.changeUrl(
                    event.data.paramName,
                    event.currentTarget.options[event.currentTarget.selectedIndex].value,
                    event.data.default
                );
            },

            changeUrl: function (paramName, paramValue, defaultValue) {
                var url = urlHelper.getUrlUpdatedParam(paramName, paramValue);
                ajax.load(url, this.options.filterContainer, this.options.resultContainer);
            }
        });

        return $.omnyfy_layerednavigation.toolbar;
    }
);