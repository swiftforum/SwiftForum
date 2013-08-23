$(function() {
    $(".roster-user").click(function(e) {
        $("#user-dropdown").css("top", e.pageY).css("left", e.pageX).show();
        return false;
    });

    $(document).click(function() {
        $("#user-dropdown").hide();
    })
})
