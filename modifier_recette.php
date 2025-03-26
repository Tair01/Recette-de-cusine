<?php
session_start();    // je commence la session pour pouvoir récupérer les informations de l'utilisateur
// Fichier contenant les recettes avec leurs informations
$recetteFile = 'donnees/recette.json';
// Vérifie si le fichier existe, sinon on initialise un tableau vide
$recettes = file_exists($recetteFile) ? json_decode(file_get_contents($recetteFile), true) : [];

if ($recettes == null) {
    echo "Erreur: fichier JSON invalide.";
    exit();
}
// ID de la recette depuis l'URL
$recette_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$recette = null;

// Rechercher la recette par son ID
foreach ($recettes as &$r) {
    if ($r['id'] == $recette_id) {
        $recette = &$r;
        break;
    }
}

// Vérifier si la recette existe ? 
if (!$recette) {
    echo "Erreur: recette non trouvée.";
    exit();
}

// Vérification des permissions de l'utilisateur
// Seuls les auteurs, administrateurs, chefs et traducteurs peuvent modifier une recette
if (
    !isset($_SESSION['user']) ||
    ($_SESSION['user']['email'] != $recette['author'] &&
        !in_array($_SESSION['user']['role'], ['admin', 'chef', 'traducteur']) &&
        (!isset($_SESSION['user']['role2']) || !in_array($_SESSION['user']['role2'], ['admin', 'chef', 'traducteur'])))
) {
    echo "Accès refusé.";
    exit();
}

// Bloquer un chef qui n'est pas l'auteur de modifier la recette
if ((($_SESSION['user']['role'] == 'chef' || (isset($_SESSION['user']['role2']) && $_SESSION['user']['role2'] == 'chef'))
    && $_SESSION['user']['email'] != $recette['author'])) {
    header("Location: index.php");  // redirect vers la page principale 
    exit();
}
// formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si l'utilisateur est un traducteur, il ne peut modifier que les textes et non les autres propriétés
    if ($_SESSION['user']['role'] == 'traducteur' || (isset($_SESSION['user']['role2']) && $_SESSION['user']['role2'] == 'traducteur')) {
        $recette['fr'] = $_POST['titre_fr'];
        $recette['en'] = $_POST['titre_en'];
        // explode: divise une chaîne en un tableau.
        $recette['ingredients']['fr'] = explode("\n", $_POST['ingredients_fr']);
        $recette['ingredients']['en'] = explode("\n", $_POST['ingredients_en']);
        $recette['etapes']['fr'] = explode("\n", $_POST['etapes_fr']);
        $recette['etapes']['en'] = explode("\n", $_POST['etapes_en']);

        // Sauvegarde des modifications dans json
        file_put_contents($recetteFile, json_encode($recettes, JSON_PRETTY_PRINT));
        echo "Traduction mise à jour avec succès ! <a href='index.php'>Retour</a>";
        exit();
    }

    // Si l'utilisateur est un chef ou un admin, il peut modifier toutes les informations
    $recette['fr'] = $_POST['titre_fr'];
    $recette['en'] = $_POST['titre_en'];
    $recette['ingredients']['fr'] = explode("\n", $_POST['ingredients_fr']);
    $recette['ingredients']['en'] = explode("\n", $_POST['ingredients_en']);
    $recette['etapes']['fr'] = explode("\n", $_POST['etapes_fr']);
    $recette['etapes']['en'] = explode("\n", $_POST['etapes_en']);
    $recette['gluten'] = !empty($_POST['gluten']);
    $recette['vegan'] = !empty($_POST['vegan']);

    // Seul l'administrateur peut changer le statut de la recette (terminée ou publiée)
    if ((isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'admin') ||
        (isset($_SESSION['user']['role2']) && $_SESSION['user']['role2'] == 'admin')
    ) {
        $recette['status'] = isset($_POST['status_terminee']) ? 'terminee' : 'publiée';
    }

    // Sauvegarde des modifications dans json
    file_put_contents($recetteFile, json_encode($recettes, JSON_PRETTY_PRINT));
    echo "Recette modifiée avec succès ! <a href='index.php'>Retour</a>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une recette</title>
    <link id="main-style" rel="stylesheet" href="css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body class="ajout_recette">
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <h2 class="title-ajout" id="modifTitleRct">Modifier la recette</h2>
    <form method="POST" class="ajout_recette_form">
        <label class="label_ajout_recette" id='ajoutRctTitreFr'>Titre en français:</label>
        <input type="text" name="titre_fr" value="<?= htmlspecialchars($recette['fr']) ?>" required class="text_inut"><br>

        <label class="label_ajout_recette" id='ajoutRctTitreEn'>Titre en anglais:</label>
        <input type="text" name="titre_en" value="<?= htmlspecialchars($recette['en']) ?>" required class="text_inut"><br>

        <label class="label_ajout_recette" id='ajoutRctIngrFr'>Ingrédients (fr):</label>
        <!-- La fonction implode: renvoie des chaines de caracteres d'un array donnee en param et un separateur dans mon cas c'est retour a la ligne  -->
        <!-- La fonction explode: divise des chaines de caracteres en un array en prenant une chaine de carat en param et un separateur  -->
        <textarea name="ingredients_fr" class="textarea_ajout"><?= htmlspecialchars(implode("\n", $recette['ingredients']['fr'])) ?></textarea><br>

        <label class="label_ajout_recette" id='ajoutRctIngrEn'>Ingrédients (en):</label>
        <textarea name="ingredients_en" class="textarea_ajout"><?= htmlspecialchars(implode("\n", $recette['ingredients']['en'])) ?></textarea><br>

        <label class="label_ajout_recette" id='ajoutRctEtapFr'>Étapes (fr):</label>
        <textarea name="etapes_fr" required class="textarea_ajout"><?= htmlspecialchars(implode("\n", $recette['etapes']['fr'])) ?></textarea><br>

        <label class="label_ajout_recette" id='ajoutRctEtapEn'>Étapes (en):</label>
        <textarea name="etapes_en" required class="textarea_ajout"><?= htmlspecialchars(implode("\n", $recette['etapes']['en'])) ?></textarea>
        <?php if ($_SESSION['user']['role'] !== 'traducteur') : ?>
            <label class="label_ajout_recette" id='ajoutRctGlut'>Sans gluten?</label>
            <input type="checkbox" name="gluten" value="1" <?= $recette['gluten'] ? 'checked' : '' ?>>
            <label class="label_ajout_recette">Vegan?</label>
            <input type="checkbox" name="vegan" value="1" <?= $recette['vegan'] ? 'checked' : '' ?>>
        <?php endif; ?>

        <?php if ($_SESSION['user']['role'] === 'admin') : ?>
            <label class="label_ajout_recette" id='ajoutRctStatus'>Marquer comme terminée:</label>
            <input type="checkbox" name="status_terminee" value="1" <?= $recette['status'] === 'terminee' ? 'checked' : '' ?>>
        <?php endif; ?>

        <button type="submit" id="modifierRct">Modifier</button>

        <!--  Si l'utilisateur est un admin alors il voit le button supprimer ou en clicant il peut completement supprimer la recette  -->
        <?php if ($_SESSION['user']['role'] === 'admin') : ?>
            <button type="button" onclick="deleteRecette(<?= $recette_id ?>)" id="delete-btn-rct">
                Supprimer
            </button>
        <?php endif; ?>
    </form>
    <script>
        // Un ajax pour supprimer la recette par l'admin 
        window.deleteRecette = function(recetteId) {
            // Une confirmation de la suppression de la recette s 
            if (!confirm('Vous etes sûr de vouloir supprimer cette recette ?')) return;

            $.ajax({
                url: './supprimer_recette.php',
                type: 'POST',
                data: {
                    id: recetteId
                },
                dataType: 'json',
                success: function(response) {
                    console.log("La reponse de cote serveur:", response);
                    if (response.success) {
                        alert(response.message);
                        // D'apres la suppression on se situe dans la main page
                        window.location.href = 'index.php';
                    } else {
                        alert('Erreur: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erreur lors de la suppression de la recette: ' + error);
                }
            });
        };
    </script>
    <script src="js/lang.js"></script>
    <script src="js/main.js"></script>
</body>

</html>