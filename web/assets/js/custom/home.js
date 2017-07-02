$(document).ready(function () {

    var ias = $.ias({
        container: "#timeline .box-content",
        item: ".publication-item",
        pagination: "#timeline .pagination",
        next: "#timeline .pagination .next_link",
        triggerPageThreshold: 5
    });

    ias.extension(new IASTriggerExtension({
        text: "Ver más publicaciones",
        offset: 3
    }));

    ias.extension(new IASSpinnerExtension({
        src: URL + "/assets/images/ajax-loader.gif"
    }));

    ias.extension(new IASNoneLeftExtension({
        text: "No hay más publicaciones"
    }));

    ias.on("ready", function (event) {
        Buttons();
    });

    ias.on("rendered", function (event) {
        Buttons();
    })

});


function Buttons() {
    $(".btn-img").unbind("click").on("click", function () {
        $(this).parent().find(".pub-image").fadeToggle();
    });

    $(".btn-delete-pub").unbind("click").on("click", function () {
        var button = $(this);
        button.parent().parent().addClass("hidden");
        $.ajax({
            url: URL + "/publication/remove/" + button.data("id"),
            type: "GET",
            success: function (response) {
                console.log(response);
            }
        });
    });
}