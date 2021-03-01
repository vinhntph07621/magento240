define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'mage/template'
], function(Column, $, mageTemplate){
        return Column.extend({
            defaults: {
                bodyTmpl: 'ui/grid/cells/html',
                fieldClass: {
                    'data-grid-html-cell': true
                }
            },
            preview: function (row) {
                console.log(row);
            },
            getFieldHandler: function (row) {
                return this.preview.bind(this, row);
            }
        });
});
