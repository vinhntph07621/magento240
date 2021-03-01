define([
    'jquery',
], function ($) {
    'use strict';

    $.widget('omnyfy_favourite.vendor', {
        options: {
            addFavouriteBtn: '.favourite-add-btn',
            removeFavouriteBtn: '.favourite-remove-btn'
        },

        /**
         * Widget initialization
         * @private
         */
        _create: function () {
            this._bind();
        },

        /**
         * Event binding, will monitor change, keyup and paste events.
         * @private
         */
        _bind: function () {
            let self = this;

            $(document).ready(function () {
                $(self.options.addFavouriteBtn).on('click', function(){
                    self._bindFavouriteVendor($(this));
                });

                $(self.options.removeFavouriteBtn).on('click', function(){
                    self._bindFavouriteVendor($(this));
                });

            });

        },

        _bindFavouriteVendor: function (element) {
            var vendorId = element.data('vendor-id'),
                clickBtn = element,
                favouriteAddBtn = $('.favourite-add-btn'),
                favouriteRemoveBtn = $('.favourite-remove-btn'),
                favouriteVendorBlock = $('.js-favourite-vendor-block-'+vendorId),
                actionType = element.data('action-type'),
                ajaxUrl = '';

            if (actionType === 'add') {
                ajaxUrl = '/rest/V1/vendors/add_favourite_vendor';
            }else {
                ajaxUrl = '/rest/V1/vendors/remove_favourite_vendor/';
            }
            $('body').trigger('processStart');
            $.ajax({
                method: 'POST',
                url: ajaxUrl,
                data: JSON.stringify(
                    {
                        vendorId: vendorId
                    }
                ),
                dataType: "json",
                contentType: 'application/json',
                processData: false
            })
                .done(function (data){
                    if (data.success) {
                        if (data.action === 'add'){
                            clickBtn.addClass('hide');
                            clickBtn.removeClass('active');
                            clickBtn.next().removeClass('hide');
                            clickBtn.next().addClass('active');
                        }else {
                            clickBtn.addClass('hide');
                            clickBtn.addClass('active');
                            clickBtn.prev().removeClass('hide');
                            favouriteVendorBlock.remove();
                            if ($('.saved-item-tile-wrapper').length == 0) {
                                $('#right-content-section').append("<div class='message info empty'><span>No Favourite vendors found.</span></div>");
                            }
                        }
                    }else {
                        alert(data.message);
                    }
                })
                .fail(function (data){
                    alert(data.message);
                })
                .always(function(){
                    $('body').trigger('processStop');
                });
            return false;
        }

    });

    return $.omnyfy_favourite.vendor;
});