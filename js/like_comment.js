// Button de like
$(document).ready(function () {
    $("#like-button").click(function () {
        let recetteId = $(this).data("id");

        $.ajax({
            url: "like_recette.php",
            type: "POST",
            data: { recette_id: recetteId },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $("#like-count").text(response.likes);
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert("Erreur lors de l'envoi de la requête.");
            }
        });
    });
});

// Des commentaires
$(document).ready(function () {
    $("#comment-form").submit(function (e) {
        e.preventDefault(); // pour ne pas avoir un reload de la page

        let recetteId = $(this).data("recette-id");
        let commentText = $("#comment-text").val().trim();

        if (commentText === "") {
            alert("Veuillez écrire un commentaire.");
            return;
        }

        $.ajax({
            url: "comment_recette.php",
            type: "POST",
            data: { recette_id: recetteId, comment_text: commentText },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $("#comment-list").append(`<li><strong>${response.comment.user}</strong>: ${response.comment.text}</li>`);
                    $("#comment-text").val(""); // Réinitialiser le champ a vide!
                } else {
                    alert(response.message); // l'erreur envoyée de cote serveur Php
                }
            },
            error: function () {
                alert("Erreur lors de l'ajout du commentaire.");
            }
        });
    });
});