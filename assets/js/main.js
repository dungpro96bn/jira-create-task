jQuery(function ($) {

    $(".form-createTask .user-active").click(function () {
        $(".form-createTask .user-list").toggleClass("is-open");
        $(this).toggleClass("is-active");
    });

    $(".form-createTask .user-item").click(function () {
        var dataIdUser = $(this).attr("data-id"),
            avtUser = $(this).html();
        $(".form-createTask .user-item-active").html(avtUser);
        $("#assignee").val(dataIdUser);
        $(".form-createTask .user-active").toggleClass("is-active");
        $(".form-createTask .user-list").toggleClass("is-open");
    });

});

