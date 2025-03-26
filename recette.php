<?php
// La langue en fonction du paramètre GET, par défaut 'fr'
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'fr';
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
                    // Récupération de l'ID de la recette à partir de l'URL
                    $recette_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;
                    $json_f = file_get_contents("donnees/recette.json");
                    $recettes = json_decode($json_f, true);
                    $recette = null;

                    // Recherche de la recette correspondante par son id 
                    foreach ($recettes as $r) {
                        if ($r['id'] == $recette_id) {
                            $recette = $r;
                            break;
                        }
                    }
                    // Affichage du nom de la recette ou un message d'erreur
                    if ($recette) {
                        echo $recette[$lang];
                    } else {
                        echo "Recette non trouvée";
                    }
                    ?>
                </h1>
                <?php
                // Si un jour il y aura un probleme avec l'image, cad on va pas le retrouver on le changera avec l'image par defaut
                $imagePath = "img/cusines/img{$recette_id}b.jpg";
                if (!file_exists($imagePath)) {
                    $imagePath = "img/cusines/default_image.png"; // image par defaut
                }
                ?>
                <img src="<?php echo $imagePath; ?>" alt="Image de la recette" class="cusine-details_img">
            </div>

            <div class="cusine-details-desk">
                <?php
                if ($recette) {
                    // Les informations de la recette trouvee avant par son id
                    echo "<h2 data-lang-en='Ingredients' data-lang-fr='Ingrédients'></h2>";
                    foreach ($recette['ingredients'][$lang] as $ingredient) {
                        echo "<li>{$ingredient}</li>";
                    }
                    echo "<h2 data-lang-en='Steps' data-lang-fr='Étapes'></h2>";
                    echo "<ol>";
                    foreach ($recette['etapes'][$lang] as $etape) {
                        echo "<li>" . $etape . "</li>";
                    }
                    echo "</ol>";
                    // Indication de la présence de gluten
                    if ($recette['gluten']) {
                        echo "<h2>Gluten:Oui</h2>";
                    } else {
                        echo "<h2>Gluten:Non</h2>";
                    }
                    // Indication si la recette est vegan
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
            <h4>Likes: <span id="like-count"><?php echo $recette['likes']; ?></span></h4>
            <button class="likeBtn" id="like-button" data-id="<?php echo $recette_id; ?>">❤️</button><br><br>


            <h3>Commentaires:</h3>
            <ul id="comment-list">
                <?php foreach ($recette['comments'] as $comment) {
                    // Les commentaires de recette qui sont sauvegarder dans recette.json 
                    echo "<li><strong>{$comment['user']}</strong>: {$comment['text']}</li>";
                } ?>
            </ul><br>
            <form class="ajout-comment" id="comment-form" data-recette-id="<?php echo $recette_id; ?>">
                <textarea id="comment-text" placeholder="Votre commentaire" required></textarea>
                <button type="submit" data-lang-en="Comment" data-lang-fr="Commenter"></button>
            </form>
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

    <!-- Un fichier js avec l'ajax pour liker et commentter une recette  -->
    <script src="js/like_comment.js"></script>
    <!-- Deux fichiers avec des script js qui gere la traduction de la page en anglais ou en francais -->
    <script src="js/lang.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Un changement de la langue 
        document.querySelector(".change-lang").addEventListener("change", function() {
            let lang = this.value;
            let url = new URL(window.location.href);
            url.searchParams.set("lang", lang);
            window.location.href = url.toString();
        });
    </script>
</body>

</html>