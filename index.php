<?php
// Commencer la session pour pouvoir récupérer les informations de l'utilisateur
session_start();

// fichier json avec les recettes
$recetteFile = 'donnees/recette.json';

// Vérification si le fichier $recetteFile existe et chargement des recettes sous forme de tableau associatif
$recettes = file_exists($recetteFile) ? json_decode(file_get_contents($recetteFile), true) : [];

// Vérifier si l'utilisateur est connecté (donc est ce qu'il est dans la session ou pas?)
$user = $_SESSION['user'] ?? null;

// Les rôles qui peuvent modifier ou ajouter une recette
$isAllowed = $user && (in_array($user['role'], ['admin', 'chef']) || in_array($user['role2'] ?? '', ['admin', 'chef']));
// Les rôles qui peuvent modifier ou traduire une recette
$isAllowedTraduc = $user && (in_array($user['role'], ['traducteur']) || in_array($user['role2'] ?? '', ['traducteur']));

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recette de cusine</title>
    <link rel="icon" href="img/icons/icon_site.png" type="image/png">
    <link id="main-style" rel="stylesheet" href="css/main.css">
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
                        <a href="./index.php" class="nav-list__link nav-list__link--active" data-lang-en="Cuisines"
                            data-lang-fr="Cuisines">
                            Cusines
                        </a>
                    </li>
                    <!-- On verifie si il y a un utilisateur en session, si oui alors il peut pas se connecter et la page de connexion est cache -->
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
                        <a href="./admin.php" class="nav-list__link" data-lang-en="Admin" data-lang-fr="Administrateur">
                            Administrateur
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php
    // Je regarde est ce qu'il y a un texte dans la barre de recherche? 
    $searchText = isset($_GET['search']) ? trim(strtolower($_GET['search'])) : '';
    ?>

    <header class="header">
        <div class="header__wrapper">
            <h1 class="header__title" data-lang-en="Discover, share and enjoy recipes from around the world!" data-lang-fr="Découvrez, partagez et savourez des recettes du monde entier !"></h1>
            <div class="header-text">
                <p>Rejoignez notre communauté de passionnés de cuisine et explorez des plats délicieux en français et en anglais.Ajoutez vos propres recettes, laissez des avis et participez à une aventure culinaire unique !
                    Que vous soyez chef expérimenté ou amateur curieux, trouvez l'inspiration et régalez-vous à chaque bouchée.
                </p>
            </div><br>
            <!-- Une barre de recherche pour trouver une recette par un mot cle qui redirige vers un fichier php  -->
            <div class="barreDeRech">
                <form method="GET" action="recette_trouve.php" onsubmit="return validateSearch()">
                    <input type="text" name="search" id="search" placeholder="Chercher ici..." value="<?php echo htmlspecialchars($searchText); ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>
            <script>
                // Une verification de ce que l'utilisateur a taper dans la barre de recherche, ca doit pas etre vide
                function validateSearch() {
                    var searchInput = document.getElementById('search').value.trim();
                    if (searchInput === '') {
                        alert('Veuillez entrer un texte pour la recherche.');
                        return false; // On pourra pas envoyer le texte de la recherche 
                    }
                    return true; // sinon on l'envoie 
                }
            </script>
        </div>
    </header>
    <!-- Une section main avec: 
            - tous les recetttes qui sont stockee dans le fichier recette.json 
            - un titre de chaque recette et son image qui est stockee dans img/cuisines
    -->
    <main class="section">
        <div class="container">
            <h2 class="title-1" id="title-1">Plats</h2>
            <ul class="cusines">
                <?php foreach ($recettes as $recette) : ?>
                    <li class="cusine">
                        <a href="recette.php?id=<?php echo $recette['id']; ?>">
                            <?php
                            $imagePath = 'img/cusines/img' . $recette['id'] . '.jpg';
                            if (!file_exists($imagePath)) {
                                // Default image si l'image avec un id n'existe pas
                                $imagePath = 'img/cusines/default_image.png';
                            }
                            ?>
                            <img src="<?php echo $imagePath; ?>" alt="Cusine img" class="cusine__img" loading="lazy">
                            <h3 class="cusine__title"><?php echo $recette['fr']; ?></h3>
                        </a>
                        <?php if ($isAllowed || $isAllowedTraduc) : ?>
                            <a href="modifier_recette.php?id=<?php echo $recette['id']; ?>" id="edit-button" data-lang-en="Modify" data-lang-fr="Modifier"></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                <!-- Ajouter une recette ssi c'est autorisé, si le role de l'utilisateur est == admin ou chef, donc on utilisie la var: $isAllowed -->
                <?php if ($isAllowed) : ?>
                    <li class="cusine">
                        <a href="ajouter_recette.php">
                            <img src="img/cusines/add-image.png" alt="Ajouter une recette" class="cusine__img" loading="lazy">
                            <h3 class="cusine__title" id="cusine-add">Ajouter une recette</h3>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </main>

    <!-- Un footer avec des informations concernant les contacts: 
            des liens qui redirigent vers les réseaux sociaux pour contacter l'admin le createur de se site    
    -->
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
                    <!-- &nbsp c'est de tabulation -->
                    <p><i class="fas fa-envelope"></i> btair04@gmail.com
                        &nbsp;&nbsp;&nbsp;&nbsp;tair.baurzhan@etu-upsaclay.fr</p>
                    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;©
                        2025 Tair Baurzhan
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Deux fichiers avec des script js qui gere la traduction de la page en anglais ou en francais -->
    <script src="js/lang.js"></script>
    <script src="js/main.js"></script>
</body>

</html>