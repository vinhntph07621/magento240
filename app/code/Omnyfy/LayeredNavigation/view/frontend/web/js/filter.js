define(
    [
        'jquery',
        'Omnyfy_LayeredNavigation/js/ajax'
    ],
    function ($, ajax) {
        $.widget('omnyfy_layerednavigation.filter', {
            options: {
                filterInputs: 'input[type=checkbox], select',
                filterLinks: '.action.remove, .filter-clear',
                filterContainer: '#filter_container',
                resultContainer: '#result_container',
                mobileToggle: '#advanced_filters'
            },

            _create: function() {
                this._destroy();

                this.observe();
            },

            observe: function() {
                var self = this;
                $(self.options.filterContainer).find(self.options.filterInputs).on('change', function() {
                    var url = $(this).data('url');
                    if ($(this).get(0).nodeName == 'SELECT') {
                        url = $(this).find('option:selected').data('url');
                    }
                    ajax.load(url, self.options.filterContainer, self.options.resultContainer);
                    return false;
                });
                $(self.options.filterContainer).find(self.options.filterLinks).on('click', function() {
                    ajax.load($(this).attr('href'), self.options.filterContainer, self.options.resultContainer);
                    return false;
                });
                $(self.options.mobileToggle).on('click', function() {
                    $(this).toggleClass('active');
                    var filters = $(self.options.filterContainer);
                    if (filters.is(':visible')) {
                        filters.removeAttr('style');
                    } else {
                        filters.show();
                    }
                    return false;
                });
            },

            _destroy: function() {
                $(this.options.filterContainer).find(this.options.filterInputs).off('change');
                $(this.options.filterContainer).find(this.options.filterLinks).off('click');
                $(this.options.mobileToggle).off('click');
            }
        });

        return $.omnyfy_layerednavigation.filter;
    }
);
