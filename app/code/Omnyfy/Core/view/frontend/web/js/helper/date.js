define(
    [
        'Omnyfy_Core/js/helper/string'
    ],
    function(strHelper) {
        return {
            addDays: function(date, days) {
                date = new Date(date);
                return date.setDate(date.getDate() + days);
            },

            date2Iso: function(date) {
                date = new Date(date);
                var d = strHelper.strpad(date.getDate(), 2, '0');
                var m = strHelper.strpad(date.getMonth() + 1, 2, '0');
                var y = date.getFullYear();
                return y + '-' + m + '-' + d;
            }
        }
    }
);
