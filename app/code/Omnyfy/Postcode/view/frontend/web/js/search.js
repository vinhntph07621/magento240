define(
    [
        'jquery'
    ],
    function ($) {
        var _fieldEl,
            _valueEl,
            _dropdownEl,
            _wrapEl,
            _selectedIndex = -1,
            _options = [],
            _lastSearch = null,
            _lastAjax = null;

        $.widget('omnyfy_postcode.postcodeSearch', {
            options: {
                apiUrl: '/rest//V1/postcode/list_by_keyword/',
                currentLocationApi: '/rest//V1/postcode/closest',
                wrapClass: null,
                emptyMessage: 'No results found',
                invalidMessage: 'Please select a valid value'
            },

            _create: function() {
                this._setup();
                this._observe();
                this.getLocation();
            },

            _setup: function() {
                _fieldEl = $(this.element).find('[data-role="field"]').addClass('postcode-search-field').get(0);
                _valueEl = $(this.element).find('[data-role="value"]').get(0);

                _wrapEl = $('<div class="postcode-search-wrap"/>').get(0);
                $(_fieldEl).before(_wrapEl);
                $(_wrapEl).append(_fieldEl);

                if (this.options.wrapClass) {
                    $(_wrapEl).addClass(this.options.wrapClass);
                }

                _dropdownEl = $('<ul class="postcode-search-dropdown"/>').get(0);
                $(_fieldEl).after(_dropdownEl);

                _lastSearch = $.trim($(_fieldEl).val());

                return this;
            },

            _observe: function() {
                var self = this;

                var timeout = null;
                $(_fieldEl).on('keypress', function(e) {
                    
                    if (timeout) {
                        clearTimeout(timeout);
                    }

                    if (e.which == 38) { // UP
                        self.selectionUp();
                        return cancelEvent(e);
                    }
                    if (e.which == 40) { // DOWN
                        self.selectionDown();
                        return cancelEvent(e);
                    }
                    if (e.which == 13) { // ENTER
                        self.select();
                        return cancelEvent(e);
                    }

                    
                    var search = $.trim($(_fieldEl).val());
                    
                    // Don't continue if nothing has changed
/*                    if (_lastSearch == search) {
                        return cancelEvent(e);
                    }*/


                    if ($(this).val().length > 2) {
                        // Timeout between keypresses
                        timeout = setTimeout(function() {
                            // Reset current selection
                            self._resetSelection();

                            // Do search
                            self.search();
                        }, 500);
                    }
                }).on('keydown', function(e) {
                    switch (e.which) {
                        case 38: // UP
                        case 40: // DOWN
                        case 27: // ESC
                        case 13: // ENTER
                            return cancelEvent(e);
                    }
                }).on('keyup', function(e){
                    
                    if (e.which == 8) {
                        if (timeout) {
                            clearTimeout(timeout);
                        }
                        var search = $.trim($(_fieldEl).val());

                        if ($(this).val().length > 2) {
                            // Timeout between keypresses
                            timeout = setTimeout(function() {
                                // Reset current selection
                                self._resetSelection();

                                // Do search
                                self.search();
                            }, 500);
                        }
                    }
                });

                return this;
            },

            search: function() {
                var self = this;

                if (_lastAjax) {
                    _lastAjax.abort();
                }

                // Reuse last search if searching the same value
                var search = $.trim($(_fieldEl).val());
                if (_lastSearch == search) {
                    return;
                }

                var wrap = $(_wrapEl);
                wrap.addClass('loading');
                _lastAjax = $.ajax({
                    url: self.options.apiUrl + '?' + $.param({keyword: search}),
                    type: 'GET',
                    global: true,
                    cache: true
                }).done(function(response) {
                    if (response) {
                        _lastSearch = search;
                        _options = response.items;
                        self._updateOptions();
                    }
                }).always(function() {
                    wrap.removeClass('loading');
                    _lastAjax = null;
                });

                return this;
            },

            _resetSelection: function() {
                _selectedIndex = -1;
                _options = [];

                $(_dropdownEl).removeAttr('style').html('');
                $(_valueEl).val('');
                $(_wrapEl).addClass('invalid').removeClass('valid');
                _fieldEl.setCustomValidity(this.options.invalidMessage);

                return this;
            },

            _updateOptions: function() {
                var self = this;
                var dropdown = $(_dropdownEl).html('');

                if ($.isArray(_options) && _options.length > 0) {
                    $.each(_options, function(i) {
                        var option = $('<li class="postcode-search-option"/>')
                            .html(_options[i].suburb + ' ' + _options[i].postcode)
                            .on('mousedown', function() {
                                self.select(i);
                            })
                        ;
                        dropdown.append(option);
                    });
                } else {
                    var option = $('<li class="postcode-search-option empty"/>').html(this.options.emptyMessage);
                    $(_dropdownEl).append(option);
                }

                return this;
            },

            selectionUp: function() {
                var length = _options.length;
                if (length > 0) {
                    _selectedIndex--;
                    if (_selectedIndex < 0) {
                        _selectedIndex = length - 1;
                    }

                    this._updateSelection();
                }

                return this;
            },

            selectionDown: function() {
                var length = _options.length;
                if (length > 0) {
                    _selectedIndex++;
                    if (_selectedIndex > _options.length - 1) {
                        _selectedIndex = 0;
                    }

                    this._updateSelection();
                }

                return this;
            },

            _updateSelection: function() {
                $(_dropdownEl).find('.postcode-search-option').removeClass('selected')
                    .eq(_selectedIndex).addClass('selected');

                return this;
            },

            getLocation: function(){
                
                var self = this;

                var wrap = $(_wrapEl);


                $(".location-btn").on("click", function (e) {

                    e.preventDefault();

                    if(navigator.geolocation) {

                        navigator.geolocation.getCurrentPosition(function(position) {

                            if (_lastAjax) {
                                _lastAjax.abort();
                            }

                            var current_position_lat = position.coords.latitude,
                                current_position_lon = position.coords.longitude;

                            wrap.addClass('loading');

                            _lastAjax = $.ajax({
                                url: self.options.currentLocationApi + '?' + 'lon=' + current_position_lon + '&lat=' + current_position_lat,
                                type: 'GET',
                                global: true,
                                cache: true
                            }).done(function(data) {
                                

                                _lastSearch = data.suburb + ' ' + data.postcode;
                                $(_valueEl).val(data.id);
                                $(_fieldEl).val(_lastSearch);
                                $(_wrapEl).addClass('valid').removeClass('invalid');
                                _fieldEl.setCustomValidity('');

                            }).always(function() {

                                wrap.removeClass('loading');
                                _lastAjax = null;

                            });

                        });
                    }
                })
            },

            select: function(index) {
                
                if (_options.length > 0) {
                    if (_options[index]) {
                        _selectedIndex = index;
                    }

                    if (_options[_selectedIndex]) {
                        _lastSearch = _options[_selectedIndex].suburb + ' ' + _options[_selectedIndex].postcode;
                        $(_valueEl).val(_options[_selectedIndex].id);
                        $(_fieldEl).val(_lastSearch);
                        $(_wrapEl).addClass('valid').removeClass('invalid');
                        _fieldEl.setCustomValidity('');
                        $(_dropdownEl).hide();
                    }
                }

                return this;
            }
        });

        return $.omnyfy_postcode.postcodeSearch;

        function cancelEvent(e) {
            e.preventDefault();
            return false;
        }
    }
);
