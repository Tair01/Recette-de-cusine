<?php
// je commence la session pour pouvoir récupérer les informations de l'utilisateur
session_start();

// Vérifie si l'utilisateur est connecté == dans la session et son role est: admin parce que c'est la page que pour l'admin, 
//    sinon redirige vers la page de de profile
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role2'] !== 'admin')) {
    header("Location: profile.php");
    exit();
}


// Fichier contenant les utilisateurs avec leurs informations 
$userFile = 'donnees/users.json';

// Vérifie si le fichier existe, sinon on initialise un tableau vide
$users = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="icon" href="img/icons/icon_admin.png" type="image/png">
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
                <!-- Sélecteur de langue de la page -->
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
                        <a href="./profile.php" class="nav-list__link" data-lang-en="Profile" data-lang-fr="Profil">
                            Profil
                        </a>
                    </li>
                    <li class="nav-list__item" id="nav4">
                        <a href="./admin.php" class="nav-list__link nav-list__link--active" data-lang-en="Admin" data-lang-fr="Administrateur">
                            Administrateur
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="title-1" id="title-1-admin">Panneau d'administration</h2>
        <table border="1">
            <tr>
                <th id="prenom-admin">Prénom</th>
                <th id="email-admin">Email</th>
                <th id="role-admin">Rôle</th>
                <th id="actions-admin">Actions</th>
            </tr>
            <?php foreach ($users as $user):
                // Nettoyer l'email pour l'utiliser comme identifiant sûr
                $safeEmail = str_replace(["@", "."], "_", $user['email']);
                $requestedRole = $user['requested_role'] ?? null;
            ?>
                <tr>
                    <!-- 
                        Si dans le users.json il y un champ requested_role le nom d'tuilisateur qui 
                     a demande un nouveau role est en rouge et le role qui la demander est aussi en rouge.
                     Des que l'admin a confirmer et changer son role le nom et la role choisi de cette personne revient 
                     en couleur noir et dans json on fait un clear de champ requested role!
                    -->
                    <td><input type="text"
                            value="<?= htmlspecialchars($user['name']) ?>"
                            id="name-<?= $safeEmail ?>"
                            class="<?= isset($user['requested_role']) ? 'highlight' : '' ?>"></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <select id="role-<?= $safeEmail ?>">
                            <option value="cuisinier" <?= $user['role'] === 'cuisinier' ? 'selected' : '' ?>
                                class="<?= $requestedRole === 'cuisinier' ? 'highlight' : '' ?>">
                                Cuisinier
                            </option>
                            <option value="chef" <?= $user['role'] === 'chef' ? 'selected' : '' ?>
                                class="<?= $requestedRole === 'chef' ? 'highlight' : '' ?>">
                                Chef
                            </option>
                            <option value="traducteur" <?= $user['role'] === 'traducteur' ? 'selected' : '' ?>
                                class="<?= $requestedRole === 'traducteur' ? 'highlight' : '' ?>">
                                Traducteur
                            </option>
                        </select>
                    </td>
                    <td>
                        <!-- Boutons pour sauvegarder ou supprimer l'utilisateur -->
                        <button onclick="updateUser('<?= htmlspecialchars($user['email']) ?>')" id="save-btn" data-lang-en="Save" data-lang-fr="Sauvegarder"></button><br><br>
                        <button onclick="deleteUser('<?= htmlspecialchars($user['email']) ?>')" id="delete-btn" data-lang-en="Delete" data-lang-fr="Supprimer"></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table><br>
    </div>

    <!-- Un footer avec des informations concernant les contacts: 
            des liens qui redirigent vers les réseaux sociaux pour contacter l'admin le createur de se site    
    -->
    <footer class="footer">
        <div class="container">
            <div class="footer__wrapper">
                <ul class="social">
                    <li class="social__item"><a href="https://www.instagram.com/tbaurzh" target="_blank"><img src="./img/icons/instagram.svg" alt="Link"></a></li>
                    <li class="social__item"><a href="https://wa.me/+77475324696" target="_blank"><img src="./img/icons/whatsapp.svg" alt="Link"></a></li>
                    <li class="social__item"><a href="https://t.me/tairr1" target="_blank"><img src="./img/icons/telegram.svg" alt="Link"></a></li>
                    <li class="social__item"><a href="https://discordapp.com/users/848159576511086602/" target="_blank"><img src="./img/icons/discord.svg" alt="Link"></a></li>
                    <li class="social__item"><a href="https://github.com/Tair01" target="_blank"><img src="./img/icons/gitHub.svg" alt="Link"></a></li>
                    <li class="social__item"><a href="https://www.linkedin.com/in/tair-baurzhan-11013a30a/" target="_blank"><img src="./img/icons/linkedIn.svg" alt="Link"></a></li>
                </ul>
                <div class="copyright">
                    <p><i class="fas fa-phone-alt"></i> +33672971711 <img src="https://upload.wikimedia.org/wikipedia/commons/c/c3/Flag_of_France.svg" alt="French Flag" style="width: 15px; height: 15px;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; +77475324696 <img src="https://upload.wikimedia.org/wikipedia/commons/d/d3/Flag_of_Kazakhstan.svg" alt="Kazakh Flag" style="width: 15px; height: 15px;"></p>
                    <p><i class="fas fa-envelope"></i> btair04@gmail.com &nbsp;&nbsp;&nbsp;&nbsp;tair.baurzhan@etu-upsaclay.fr</p>
                    <p>© 2025 Tair Baurzhan</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Un fichier js avec des ajax: pour supprimer ou modifier les infos d'un ou plusierus utilisateurs -->
    <script src="js/update_info.js"></script>
    <!-- Deux fichiers avec des script js qui gere la traduction de la page en anglais ou en francais -->
    <script src="js/lang.js"></script>
    <script src="js/main.js"></script>
</body>

</html>