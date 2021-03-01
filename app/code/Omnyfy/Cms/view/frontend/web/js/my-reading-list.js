require(
["jquery"],
function ($) {
    $(document).ready(function () {
        $('.article').on("click", ".mark-btn", function () {
            var id = $(this).data('articleid');
            var bookMarkButton = $(this);
            $.ajax({
                url: "/readinglist/add/add",
                type: 'POST',
                showLoader: true,
                dataType: 'json',
                data: {articleid: id},
                success: function (response) {
                    bookMarkButton.prev('div.message-readinglist').show().html(response['message']).delay(5000).fadeOut("slow");
 
                    if (response['type'] == "added") {
                        bookMarkButton.find(".bookmark-message").html("Added to reading list");
                        bookMarkButton.addClass("marked");
                    }
 
                    if (response['type'] == "removed") {
                        bookMarkButton.find(".bookmark-message").html("Add to reading list");
                        bookMarkButton.removeClass("marked");
                    }
                },
                fail: function () {
                    $(".message-readinglist").show().html('Error');
                    $(".message-readinglist").delay(5000).fadeOut("slow");
                },
                always: function () {}
            });
        });
    });
});