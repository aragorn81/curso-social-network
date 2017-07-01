$(document).ready(function () {


    var $nickInput = $(".nick-input");
    $nickInput.on("blur", function () {
        var nick = this.value;
        console.log(nick);

        $.ajax({
            url: URL + "/nick-test",
            data: { nick: nick },
            type: "POST",
            success: function (response) {
                console.log(response);
                if (response == "used") {
                    $nickInput.css("border", "1px solid red");
                } else {
                    $nickInput.css("border", "1px solid green");
                }
            }
        }); // Ajax request
    }); // on-blur nick-input

});







