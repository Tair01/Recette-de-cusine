$(document).ready(function () {
    $("#showRegisterForm").click(function () {
        $("#loginForm").hide();
        $("#registerForm").fadeIn();
    });

    $("#showLoginForm").click(function () {
        $("#registerForm").hide();
        $("#loginForm").fadeIn();
    });

    $("#registerForm").submit(function (e) {
        e.preventDefault();
        console.log("Hello1.1");

        let name = $("#name").val();
        let email = $("#registerEmail").val();
        let password = $("#registerPassword").val();
        let role = $("#registerRole").val();

        $.ajax({
            type: "POST",
            url: "./registration.php",
            data: { name: name, email: email, password: password, role: role },
            dataType: "json",
            success: function (response) {
                $("#message").text(response.message);
                console.log(response);

                if (response.success) {
                    console.log("Inscription réussie ! Afficher le champ de code.");
                    $("#registerForm").hide();
                    $("#verification").fadeIn();
                } else {
                    console.log("Erreur lors de l'inscription :", response.message);
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX Error:", status, error);
                console.log("Response Text:", xhr.responseText);
            }
        });
    });

    $("#verifyBtn").click(function () {
        let code = $("#verificationCode").val();
        let email = $("#registerEmail").val();

        $.ajax({
            type: "POST",
            url: "./verify_code.php",
            data: { email: email, code: code },
            dataType: "json",
            success: function (response) {
                $("#message").text(response.message);
                if (response.success) {
                    console.log("Le code est bon,on retourne sur la page de login.");
                    setTimeout(() => {
                        $("#verification").hide();
                        $("#loginForm").fadeIn();
                    }, 2000);
                } else {
                    console.log("Error de verification:", response.message);
                }
            }
        });
    });

    $("#loginForm").submit(function (e) {
        e.preventDefault();
        let email = $("#loginEmail").val();
        let password = $("#loginPassword").val();

        $.ajax({
            type: "POST",
            url: "./login.php",
            data: { email: email, password: password },
            dataType: "json",
            success: function (response) {
                $("#message").text(response.message);
                if (response.success) {
                    window.location.href = "index.php";
                }
            }
        });
    });
});
