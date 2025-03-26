<?php
// Commencer la session pour pouvoir récupérer les informations de l'utilisateur
session_start();
// L'tuilisateur dans la session
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="icon" href="img/icons/icon_profile.png" type="image/png">
    <link id="main-style" rel="stylesheet" href="css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <!--
         Une barre de navigation qui contient: 
            - un logo
            - une page Cuisines qui est une page principale
            - une page Se connecter ou l'utilisateur peut se connecter ou s'inscrire si il n'a pas de compte 
            - une page de Profile ou il peut voir son nom, email, role, peut changer son mot de passe et demander un role
            - une page Admin ou l'admin peut changer les informations des utilisateurs, de donner un nouveau role, et meme de les supprimer 
    -->
    <nav class="nav">
        <div class="container">
            <div class="nav-row">
                <a href="./index.php" class="logo"><strong>Recette</strong> Cusine</a>
                <select class="change-lang">
                    <option value="en">EN</option>
                    <option value="fr">FR</option>
                </select>
                <ul class="nav-list">
                    <li class="nav-list__item" id="nav1">
                        <a href="./index.php" class="nav-list__link" data-lang-en="Cuisines"
                            data-lang-fr="Cuisines">
                            Cusines
                        </a>
                    </li>
                    <!-- On verifie si il y a un utilisateur en session, alors il peut pas se connecter -->
                    <?php session_start(); ?>
                    <li class="nav-list__item" id="nav2">
                        <?php if (!isset($_SESSION['user'])) : ?>
                            <a href="./login.html" class="nav-list__link" data-lang-en="Login" data-lang-fr="Se connecter">
                                Se connecter
                            </a>
                        <?php endif; ?>
                    </li>
                    <li class="nav-list__item" id="nav3">
                        <a href="./profile.php" class="nav-list__link nav-list__link--active" data-lang-en="Profile" data-lang-fr="Profil">
                            Profil
                        </a>
                    </li>
                    <li class="nav-list__item" id="nav4">
                        <a href="./admin.php" class="nav-list__link" data-lang-en="Admin" data-lang-fr="Administrateur">
                            Administrateur
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="title-1" id="title-1-profile">Profil utilisateur</h2>
        <!-- Affichage des informations de l'utilisateur -->
        <p><strong id="profileP">Prénom:</strong> <?= isset($user['name']) ? htmlspecialchars($user['name']) : 'Unknown' ?></p><br>
        <p><strong id="profileE">Email:</strong> <?= isset($user['email']) ? htmlspecialchars($user['email']) : 'Unknown' ?></p><br>
        <p><strong id="profileR">Rôle:</strong> <?= isset($user['role']) ? htmlspecialchars($user['role']) : 'Unknown' ?></p><br>
        <p><strong id="profileR2">Rôle 2:</strong> <?= isset($user['role2']) ? htmlspecialchars($user['role2']) : 'Unknown' ?></p><br>
        <!-- Formulaire pour demander un changement de rôle -->
        <?php if ($user): ?>
            <label for="new-role"><strong id="changeRP">Changer le rôle:</strong></label>
            <select id="new-role">
                <option value="cuisinier">Cuisinier</option>
                <option value="chef">Chef</option>
                <option value="traducteur">Traducteur</option>
            </select><br><br>
            <button id="request-role-btn">Demander</button>
            <p id="request-message"></p>
        <?php endif; ?>
        <br>
        <!-- Formulaire pour changer le mot de passe -->
        <form id="changePassForm">
            <p><strong id="new-pass">Nouveau mot de passe:</strong> <input type="password" id="new-password" name="new_password" required></p>
            <button type="submit" id="newPassBtn">Modifier le mot de passe</button><br><br>
        </form>
        <p id="passwordMessage"></p>
        <form action="logout.php" method="post">
            <button type="submit" id="logout-btn">Déconnexion</button>
        </form><br>
    </div>

    <script>
        $(document).ready(function() {
            // Gérer la demande de changement de rôle
            $('#request-role-btn').click(function() {
                let newRole = $('#new-role').val();

                $.post("request_role.php", {
                    role: newRole
                }, function(response) {
                    $('#request-message').text(response.message).css("color", response.success ? "green" : "red");
                }, "json");
            });
        });
        // Gérer le changement de mot de passe
        $(document).ready(function() {
            $("#changePassForm").submit(function(event) {
                event.preventDefault(); // pas de reload de la page
                let newPassword = $("#new-password").val();

                $.ajax({
                    type: "POST",
                    url: "change_password.php",
                    data: {
                        new_password: newPassword
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#passwordMessage").text(response.message);
                        $("#passwordMessage").css("color", response.success ? "green" : "red");
                        if (response.success) {
                            $("#changePassForm")[0].reset(); // On vide la form si le mot de passe a ete bien changee
                        }
                    },
                    error: function() {
                        $("#passwordMessage").text("Une erreur s'est produite. Veuillez réessayer.");
                        $("#passwordMessage").css("color", "red");
                    }
                });
            });
        });
    </script>

    <script src="js/lang.js"></script>
    <script src="js/main.js"></script>

    <footer class="footer">
        <div class="container">
            <div class="footer__wrapper">
                <ul class="social">
                    <li class="social__item"><a
                            href="https://www.instagram.com/tbaurzh?igsh=MXMwMjc5bmJyNmhnYQ%3D%3D&utm_source=qr"
                            target="_blank"><img src="./img/icons/instagram.svg" alt="Link"></a></li>
                    <li class="social__item"><a href="https://wa.me/+77475324696" target="_blank"><img
                                src="./img/icons/whatsapp.svg" alt="Link"></a></li>
                    <li class="social__item"><a href="https://t.me/tairr1" target="_blank"><img
                                src="./img/icons/telegram.svg" alt="Link"></a></li>
                    <li class="social__item"><a href="https://discordapp.com/users/848159576511086602/"
                            target="_blank"><img src="./img/icons/discord.svg" alt="Link"></a></li>
                    <li class="social__item"><a href="https://github.com/Tair01" target="_blank"><img
                                src="./img/icons/gitHub.svg" alt="Link"></a></li>
                    <li class="social__item"><a href="https://www.linkedin.com/in/tair-baurzhan-11013a30a/"
                            target="_blank"><img src="./img/icons/linkedIn.svg" alt="Link"></a></li>
                </ul>
                <div class="copyright">
                    <p><i class="fas fa-phone-alt"></i> +33672971711<img
                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Flag_of_France.svg/800px-Flag_of_France.svg.png"
                            alt="French Flag"
                            style="width: 15px; height: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        +77475324696
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d3/Flag_of_Kazakhstan.svg/800px-Flag_of_Kazakhstan.svg.png"
                            alt="Kazakh Flag" style="width: 15px; height: 15px;">
                    </p>
                    <p><i class="fas fa-envelope"></i> btair04@gmail.com
                        &nbsp;&nbsp;&nbsp;&nbsp;tair.baurzhan@etu-upsaclay.fr</p>
                    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;©
                        2025 Tair Baurzhan
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>