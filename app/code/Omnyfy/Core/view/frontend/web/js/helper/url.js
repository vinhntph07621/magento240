define(
    [
        'jquery'
    ],
    function($) {
        return {
            getUrlParams: function() {
                var decode = window.decodeURIComponent;
                var urlParams = window.location.search.replace(/^\?/, '').split('&');
                var paramData = {};
                var parameters;

                for (var i = 0; i < urlParams.length; i++) {
                    parameters = urlParams[i].split('=');
                    paramData[decode(parameters[0])] = parameters[1] !== undefined
                        ? decode(parameters[1].replace(/\+/g, '%20'))
                        : '';
                }

                return paramData;
            },

            getUrlUpdatedParam: function(paramName, paramValue) {
                var baseUrl = window.location.origin + window.location.pathname;

                var paramData = this.getUrlParams();

                if (typeof paramValue != 'undefined') {
                    var _paramName = {};
                    _paramName[paramName] = paramValue;
                    paramName = _paramName;
                }

                $.each(paramName, function(k, v) {
                    paramData[k] = v;
                })
                paramData = $.param(paramData);

                return baseUrl + (paramData.length ? '?' + paramData : '');
            }
        }
    }
);
