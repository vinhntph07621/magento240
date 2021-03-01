define(["jquery", "Omnyfy_Vendor/js/modernizr/modernizr"], function($) {

	return {
		init: function() {
			
			var that = this;

			that.dockedHeaderInit();
			that.innerNavInit();
		},

		dockedHeaderInit: function() {

			if(!$(".vendor-navigation-wrapper").size()) return;

			var navContainer = $(".vendor-navigation-wrapper");

            var navAdjust = function () {
                var element_offset = navContainer.offset(),
					top_distance = element_offset.top,
					window_scroll_top = $(window).scrollTop(),
					element_height = navContainer.height();

                if (window_scroll_top > (top_distance - 1)) {
                    $("body").addClass("sub-header-docked");
                } else {
                    $("body").removeClass("sub-header-docked");
                }
            }

            navAdjust();

            $(window).scroll(function () {
                navAdjust();
            });
		},

		innerNavInit: function(){
			if(!$(".vendor-nav-list").size()) return;

			// add horizontal scroll to navigation bar
			if( Modernizr.mq('only screen and (min-width: 768px)') ) {
				if ($(".vendor-nav-list").hasClass('scroll')) {
					$(".vendor-nav-list").removeClass('scroll')
				}
			} else {
				if (!$(".vendor-nav-list").hasClass('scroll')) {
					$(".vendor-nav-list").addClass('scroll');
				}
			}

			// add page scroll functionality
			$(".vendor-nav-list").find("a").on("click", function(e){
				
				e.preventDefault();

				var $this = $(this),
					$parent = $this.parent(),
					target = $(this.hash);

				var header_height = $('.header.content').height();

				if(!!target.length) {
					$("html, body").animate({
						scrollTop: (target.offset().top - header_height)
					});
				}

				$(".vendor-nav-list li").removeClass('active');
				$parent.addClass('active');
			});
		}
	}
});