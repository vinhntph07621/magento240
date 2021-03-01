define(
    [
        'jquery'
    ],
    function($) {
        return {
            load: function(url, filterContainer, resultContainer) {
                $('body').trigger('processStart');

                if (typeof window.history.pushState === 'function') {
                    window.history.pushState({url: url}, '', url);
                }

                var _filterContainer = $(filterContainer);
                var _resultContainer = $(resultContainer);

                this.get(url).done(function(response) {
                    if (response.error) {
                        alert(response.error);
                        return;
                    }
                    if (response.backUrl) {
                        window.location = response.backUrl;
                        return;
                    }
                    if (response.filters) {
                        _filterContainer.html(response.filters);
                        _filterContainer.trigger('contentUpdated');
                    }
                    if (response.results) {
                        _resultContainer.html(response.results);
                        _resultContainer.trigger('contentUpdated');
                    }
                }).fail(function() {
                    window.location.reload();
                }).always(function() {
                    $('body').trigger('processStop')
                });
            },

            /**
             * Ajax get
             * - Copy of mage/storage::get
             *   except adds cache parameter to ajax call
             */
            get: function (url, global, contentType) {
                global = global === undefined ? true : global;
                contentType = contentType || 'application/json';

                return $.ajax({
                    url: url,
                    type: 'GET',
                    global: global,
                    contentType: contentType,
                    cache: true
                });
            }
        }
    }
);
