define(
    [
        'jquery'
    ],
    function ($) {
        $.widget('omnyfy_core_form.selectToggle', {
            options: {
                showBlank: false
            },

            _create: function() {
                this._setup();
            },

            _setup: function() {
                var self = this;
                var container = $('<div class="btn-group"/>');
                var select = $(self.element).hide();

                select.find('option').each(function(i, el) {
                    var option = $(el);
                    if (self.showBlank || option.attr('value').length > 0) {
                        var button = $('<button type="button" class="btn"/>').text(option.text());
                        if (option.is(':selected')) {
                            button.addClass('active');
                        }

                        button.on('click', function(e) {
                            select.val(option.attr('value'));
                            container.find('.btn').removeClass('active');
                            button.addClass('active');
                            select.trigger("change");

                            return cancelEvent(e);
                        });

                        container.append(button);
                    }
                });

                select.before(container);
            }
        });

        return $.omnyfy_core_form.selectToggle;

        function cancelEvent(e) {
            e.preventDefault();
            return false;
        }
    }
);
