<?php
session_start();

// Vérifier si l'utilisateur est connecté et il a un rôle 'admin' ou 'chef' ou je verifie les deux roles: role et role2
if (
    !isset($_SESSION['user']) ||
    (!in_array($_SESSION['user']['role'], ['admin', 'chef']) &&
        (!isset($_SESSION['user']['role2']) || !in_array($_SESSION['user']['role2'], ['admin', 'chef'])))
) {
    echo "Accès refusé.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Fichier contenant les recettes avec leurs informations 
    $recetteFile = 'donnees/recette.json';
    // Vérifie si le fichier existe, sinon on initialise un tableau vide
    $recettes = file_exists($recetteFile) ? json_decode(file_get_contents($recetteFile), true) : [];

    // Création de la nouvelle recette qu'on va rajouter
    $nouvelleRecette = [
        "id" => count($recettes) + 1,   // nouvelle id = nombre actuelle de recettes + 1
        "fr" => $_POST['titre_fr'] ?? "Sans titre", // titre en français ou utilise "Sans titre" si non défini
        "en" => $_POST['titre_en'] ?? "Untitled",
        "author" => $_SESSION['user']['email'],
        "ingredients" => [
            "fr" => explode("\n", str_replace("\r", "", $_POST['ingredients_fr'] ?? "")),  // les ingrédients en français et les sépare par une nouvelle ligne 
            "en" => explode("\n", str_replace("\r", "", $_POST['ingredients_en'] ?? ""))
        ],
        "etapes" => [
            "fr" => explode("\n", str_replace("\r", "", $_POST['etapes_fr'] ?? "")),    // les etapes en français et les sépare par une nouvelle ligne
            "en" => explode("\n", str_replace("\r", "", $_POST['etapes_en'] ?? ""))
        ],
        "gluten" => isset($_POST['gluten']) ? (bool) $_POST['gluten'] : false,  // verifie si si l'option "gluten" est cochée, sinon false
        "vegan" => isset($_POST['vegan']) ? (bool) $_POST['vegan'] : false,     // verifie si si l'option "vegan" est cochée, sinon false
        "status" => "publiée",
        "likes" => 0,
        "comments" => []
    ];

    // Ajout la nouvelle recette au tableau de recettes existantes et je sauvegardes dans recette.json
    $recettes[] = $nouvelleRecette;
    file_put_contents($recetteFile, json_encode($recettes, JSON_PRETTY_PRINT));

    echo "Recette ajoutée avec succès ! <a href='index.php'>Retour</a>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une recette</title>
    <link id="main-style" rel="stylesheet" href="css/main.css">
</head>

<body class="ajout_recette">
    <br><br><br><br><br><br><br><br><br><br>
    <h2 class="title-ajout" id="title-1-1-1-1-aj">Ajouter une recette</h2>
    <!-- Formulaire de soumission des données en méthode POST -->
    <form method="POST" class="ajout_recette_form">
        <!-- Champ pour le titre en français -->
        <label class="label_ajout_recette">Titre en français:</label>
        <input type="text" name="titre_fr" required class="text_inut"><br>

        <!-- Champ pour le titre en anglais -->
        <label class="label_ajout_recette">Titre en anglais:</label>
        <input type="text" name="titre_en" required class="text_inut"><br>

        <!-- Champ pour les ingrédients en français -->
        <label class="label_ajout_recette">Ingrédients (fr) :</label>
        <textarea name="ingredients_fr" required class="textarea_ajout"></textarea><br>

        <!-- Champ pour les ingrédients en anglais -->
        <label class="label_ajout_recette">Ingrédients (en) :</label>
        <textarea name="ingredients_en" required required class="textarea_ajout"></textarea><br>

        <!-- Champ pour les étapes de la recette en français -->
        <label class="label_ajout_recette">Étapes (fr) :</label>
        <textarea name="etapes_fr" required required class="textarea_ajout"></textarea><br>

        <!-- Champ pour les étapes de la recette en anglais -->
        <label class="label_ajout_recette">Étapes (en) :</label>
        <textarea name="etapes_en" required required class="textarea_ajout"></textarea><br>

        <!-- Case à cocher si la recette est sans gluten -->
        <label class="label_ajout_recette">Sans gluten ?</label>
        <input type="checkbox" name="gluten" value="1"><br>

        <!-- Case à cocher si la recette est vegan -->
        <label class="label_ajout_recette">Vegan ?</label>
        <input type="checkbox" name="vegan" value="1"><br>

        <!-- Bouton pour envoyer le formulaire -->
        <button type="submit">Ajouter</button>
    </form>
</body>

</html>