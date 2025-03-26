<?php
session_start();  // je commence la session pour pouvoir récupérer les informations de l'utilisateur

// Si l'utilisateur est connecté ??
if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Vous devez être connecté pour faire laisser un commentaire."]);
    exit();
}

// Fichier contenant les recettes avec leurs informations
$recetteFile = 'donnees/recette.json';
// Vérifie si le fichier existe, sinon on initialise un tableau vide
$recettes = file_exists($recetteFile) ? json_decode(file_get_contents($recetteFile), true) : [];

// ID de la recette et le texte du commentaire depuis la requête POST
$recette_id = isset($_POST['recette_id']) ? (int) $_POST['recette_id'] : 0;
// le commentaire de l'utilisateur, trim enleve des caracteres whitespace et autres caracts predefinie
$user_comment = isset($_POST['comment_text']) ? trim($_POST['comment_text']) : "";

// si l'id de la recette et le commentaire sont valides
if (!$recette_id || empty($user_comment)) {
    echo json_encode(["success" => false, "message" => "Données invalides"]);
    exit();
}

// Trouver la bonne recette par id et on ajoute le commentaire dans json
foreach ($recettes as &$recette) {
    if ($recette['id'] == $recette_id) {
        // nouveau commentaire 
        $nouveau_commentaire = [
            "user" => $_SESSION['user']['name'],
            "text" => $user_comment
        ];
        // on l'ajoute à la liste des commentaires de la recette
        $recette['comments'][] = $nouveau_commentaire;

        // sauvegarde les nouveaux donnees dans json
        file_put_contents($recetteFile, json_encode($recettes, JSON_PRETTY_PRINT));

        echo json_encode(["success" => true, "comment" => $nouveau_commentaire]);
        exit();
    }
}

echo json_encode(["success" => false, "message" => "Recette non trouvée"]);
