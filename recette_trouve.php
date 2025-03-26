<?php
// Pour afficher les erreurs de cote serveur
error_reporting(E_ALL);
ini_set('display_errors', 1);

// chargement de Json
$recetteFile = 'donnees/recette.json';
$recettes = file_exists($recetteFile) ? json_decode(file_get_contents($recetteFile), true) : [];

if ($recettes == null) {
    echo "Erreur: fichier JSON invalide.";
    exit();
}

// on prends le mot de recherche et on le met en minuscule
$searchText = isset($_GET['search']) ? trim(strtolower($_GET['search'])) : '';
if (empty($searchText)) {
    echo "Le texte de la recherche ne doit pas etre vide";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recette de cusine</title>
    <link rel="icon" href="img/icons/recette.png" type="image/png">
    <link id="main-style" rel="stylesheet" href="css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
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
                        <a href="./admin.php" class="nav-list__link" data-lang-en="Admin" data-lang-fr="Administrateur">
                            Administrateur
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="section">
        <div class="container">
            <div class="cusine-details">
                <h1 class="title-1" id="title-1-recette">
                    <?php
                    $recette = null;
                    foreach ($recettes as $r) {
                        // le titre de la recette
                        if (stripos(strtolower($r['fr']), $searchText) !== false) {
                            $recette = $r;
                        }


                        // les ingredients de la recette
                        foreach ($r['ingredients']['fr'] as $ingredient) {
                            if (stripos(strtolower($ingredient), $searchText) !== false) {
                                $recette = $r;
                                break;
                            }
                        }

                        // les etapes de la recette
                        foreach ($r['etapes']['fr'] as $etape) {
                            if (stripos(strtolower($etape), $searchText) !== false) {
                                $recette = $r;
                                break;
                            }
                        }
                    }
                    if ($recette) {
                        echo $recette['fr'];
                    } else {
                        echo "Recette non trouvée";
                    }
                    ?>
                </h1>
                <?php
                // Si un jour il y aura un probleme avec l'image, cad on va pas le trouver on le changera avec l'image par defaut
                if ($recette) {
                    $imagePath = "img/cusines/img{$recette['id']}b.jpg";
                    if (!file_exists($imagePath)) {
                        $imagePath = "img/cusines/default_image.png"; // image par defaut
                    }
                } else {
                    $imagePath = "img/cusines/default_image.png"; // image par défaut
                }
                ?>
                <img src="<?php echo $imagePath; ?>" alt="Image de la recette" class="cusine-details_img">
            </div>

            <div class="cusine-details-desk">
                <?php
                if ($recette) {
                    echo "<h2>Ingrédients</h2>";
                    foreach ($recette['ingredients']['fr'] as $ingredient) {
                        echo "<li>{$ingredient}</li>";
                    }
                    echo "<h2>Étapes</h2>";
                    echo "<ol>";
                    foreach ($recette['etapes']['fr'] as $etape) {
                        echo "<li>" . $etape . "</li>";
                    }
                    echo "</ol>";
                    if ($recette['gluten']) {
                        echo "<h2>Gluten:Oui</h2>";
                    } else {
                        echo "<h2>Gluten:Non</h2>";
                    }
                    if ($recette['vegan']) {
                        echo "<h2>Vegan:Oui</h2>";
                    } else {
                        echo "<h2>Vegan:Non</h2>";
                    }
                    echo "<h3>Status:{$recette['status']}</h3>";
                } else {
                    echo "<p>Recette non trouvée.</p>";
                }

                ?>
            </div>
        </div>
    </main>

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

    <script src="js/lang.js"></script>
    <script src="js/main.js"></script>
</body>

</html>