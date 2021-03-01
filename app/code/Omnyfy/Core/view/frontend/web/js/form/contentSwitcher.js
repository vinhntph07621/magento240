define(
    [
        'jquery'
    ],
    function ($) {
        $.widget('omnyfy_core_form.contentSwitcher', {
            options: {
                containerRole: 'switchcontent'
            },

            _create: function() {
                var self = this;
                var form = $(this.element);

                form.find('[required]').data('required', true);

                $(this.element).find('[data-role="' + self.options.containerRole + '"]').each(function() {
                    var container = $(this);
                    var field = form.find(container.data('field'));

                    field.on('change', function() {
                        container.find('> [data-switchvalue]').each(function(i, el) {
                            if ($(el).data('switchvalue') == field.val()) {
                                $(el).show();
                                self._updateRequiredFields(container, true);
                            } else {
                                $(el).hide();
                                self._updateRequiredFields(container, false);
                            }
                        });
                    });
                    field.trigger("change");
                });
            },

            _updateRequiredFields: function(container, required) {
                container.find('input, select, textarea').each(function() {
                    if ($(this).data('required') && this.required != required) {
                        this.required = required;
                    }
                });
            }
        });

        return $.omnyfy_core_form.contentSwitcher;

        function cancelEvent(e) {
            e.preventDefault();
            return false;
        }
    }
);
