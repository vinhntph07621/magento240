define(
    [
        'jquery'
    ],
    function($){

        $.widget('vendor.popup', {
            options: {
                url: '/shop/store/edit',
                modal: '#vendor-modal'
            },

            _create: function(){
                var self=this;


                $(document).on('click', '#my_vendor_link', function(e){
                    self._ajax(self.options.url+ '?' + $.param({
                        id: $(this).data('vendor_id')
                    }));
                    return cancelEvent(e);
                });

                $(document).ready(function () {
                    var vendorId = window.vendor_id;
                    var isLoggedin = window.is_loggedin;
                    if (vendorId == 0 && isLoggedin == 1){
                        self._ajax(self.options.url+ '?' + $.param({
                            id: 0
                        }));
                    }
                });
            },

            _ajax: function(url, dontReopenModal) {
                var self=this;
                var modalEl = $(self.options.modal);

                $('body').trigger('processStart');
                $.ajax({
                    url: url,
                    type: 'GET',
                    global: true,
                    cache: false
                }).done(function(response){
                    modalEl.html(response).trigger('contentUpdated');
                    if (!dontReopenModal) {
                        $bootstrap.modal.call(modalEl, 'show');
                    }
                }).fail(function(data){
                    if (data.status == 406) {
                        alert(data.responseText);
                        $bootstrap.modal.call(modalEl, 'hide');
                    }
                }).always(function(){
                    $('body').trigger('processStop');
                });
            }
        });

        return $.vendor.popup;

        function cancelEvent(e){
            e.preventDefault();
            return false;
        }
    }
);
