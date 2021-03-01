define(
    [],
    function() {
        return {
            populateTemplate: function(template, data) {
                var item;
                for (item in data) {
                    var pattern = new RegExp('{{' + item + '}}', 'g');
                    template = template.replace(pattern, data[item]);
                }
                template = template.replace(/\{\{[^}]+\}\}/g, '');

                return template;
            }
        }
    }
);
