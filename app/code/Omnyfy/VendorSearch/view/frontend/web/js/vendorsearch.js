require([
    "jquery"
], function($){
    $(document).ready(function() {
        $('#distance').on('change', function(e){
            e.preventDefault();
            var href = new URL(window.location.href);
            href.searchParams.set('distance',this.value);

            window.location.href = href.toString();
        });

        $('#sorter').on('change', function (e){
            e.preventDefault();
            var href = new URL(window.location.href);
            href.searchParams.set('sort',this.value);

            window.location.href = href.toString();
        });

        $('a.filter-url-link').on('click', function(e){
            e.preventDefault();
            window.location.href= $(this).data('url');
        });

        $(".search-result-filter-container .filter-group").each(function() {
            var $this = $(this);

            $this.find(".group-actions .show-more").on("click", function() {
                $this.addClass("expanded");
            });

            $this.find(".group-actions .show-less").on("click", function() {
                $this.removeClass("expanded");
            });
        });

        $("#show-aside-filter").on("click", function(e) {
            e.preventDefault();

            $("body").addClass("aside-filter-active");
        });

        $(
            ".search-result-filter-wrapper .panel-header .panel-close-btn, .search-result-filter-cover"
        ).on("click", function(e) {
            e.preventDefault();

            $("body").removeClass("aside-filter-active");
        });


        var $container = $(".search-bar-row .search-type-container"),
            $dropdownPlaceholder = $container.find(".selected-search-type .name");

        $(".search-bar-row .selected-search-type").on("click", function() {
            if ($container.hasClass("active")) {
                $container.removeClass("active");
            } else {
                $container.addClass("active");
            }
        });

        $(document).on("click", function(event) {
            if (
                !$(event.target).closest(".search-bar-row .selected-search-type")
                    .length
            ) {
                $container.removeClass("active");
            }
        });

        $container
        .find(".search-type-dropdown .type-item")
        .on("click", function(e) {
            e.preventDefault();
            var $this = $(this),
                textValue = $this.find(".nav-name").text(),
                navIndex = $this.data("nav-target");

            if ($this.hasClass("active")) return;

            $container
                .find(".search-type-dropdown .type-item")
                .removeClass("active");

            $this.addClass("active");

            $dropdownPlaceholder.text(textValue);

            $(".search-bar-row .search-container").removeClass("active");
            $(".search-bar-row")
                .find("#search-container-" + navIndex)
                .addClass("active");
        });
    });
});
