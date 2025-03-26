$(document).ready(function () {
    $("#resetEmailBtn").click(function () {
        let email = $("#resetEmail").val().trim();

        if (email === "") {
            $("#message").text("Veuillez entrer votre email!").css("color", "red");
            return;
        }

        $.ajax({
            type: "POST",
            url: "reset_pass.php",
            data: { email: email },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $("#message").text(response.message).css("color", "green");
                    window.location.href = "login.html";
                } else {
                    $("#message").text(response.message).css("color", "red");
                }
            },
            error: function () {
                $("#message").text("Erreur de connexion au serveur.").css("color", "red");
            }
        });
    });
});