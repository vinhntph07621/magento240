define(["jquery"], function($) {

    return {
        init: function() {

            var that = this;

            that.favoriteVendorInit();
        },

        bindFavouriteVendor: function(element) {
            var brokerId = element.data('vendor-id'),
                clickBtn = element,
                favouriteAddBtn = $('.favourite-add-btn'),
                favouriteRemoveBtn = $('.favourite-remove-btn'),
                favouriteVendorBlock = $('.js-favourite-vendor-block-'+brokerId),
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
                        vendorId: brokerId
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
                            favouriteRemoveBtn.removeClass('hide');
                        }else {
                            clickBtn.addClass('hide');
                            favouriteAddBtn.removeClass('hide');
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
        },

        favoriteVendorInit() {
            var that = this;
            $('.favourite-add-btn').on("click", function(){
                that.bindFavouriteVendor($(this));
            });

            $('.favourite-remove-btn').on("click", function(){
                that.bindFavouriteVendor($(this));
            });
        }
    }
});