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

    $( "body" ).delegate( ".btn-taskChild-list .btn-box", "click", function() {
        $(this).parent(".btn-taskChild-list").next().toggleClass("active");
        $(this).parent(".btn-taskChild-list").toggleClass("active");
    });

    $( "body" ).delegate( ".createTask", "click", function() {
        $("#createTaskBox").addClass("is-open");
        var idStatus = $(this).attr("data-status");
        $("input#status").val(idStatus);
    });

    $( "body" ).delegate( ".close-createTask, #createTaskBox .mask", "click", function() {
        $("#createTaskBox").removeClass("is-open");
        $('#formCreateTask')[0].reset();
    });

    $( "body" ).delegate( ".task-item a", "click", function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        var dest = url.split('#');
        var target = dest[1];
        $('#' + target).addClass("is-open");
    });

    $( "body" ).delegate( ".taskContent-popup .close-popup", "click", function(event) {
        $(".taskContent-popup").removeClass("is-open");
    });

});

