<?php
session_start();   // je commence la session pour pouvoir récupérer les informations de l'utilisateur

// Si l'utilisateur est connecté ou pas ?? 
if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Vous devez être connecté pour faire un like."]);
    exit();
}

// Fichier contenant les recettes avec leurs informations
$recetteFile = 'donnees/recette.json';
// Vérifie si le fichier existe, sinon on initialise un tableau vide
$recettes = file_exists($recetteFile) ? json_decode(file_get_contents($recetteFile), true) : [];

// ID de la recette 
$recette_id = isset($_POST['recette_id']) ? (int) $_POST['recette_id'] : 0;
if (!$recette_id) {
    echo json_encode(["success" => false, "message" => "Recette non trouvée."]);
    exit();
}
// Si la recette possède déjà un tableau 'liked_by' pour enregistrer les utilisateurs ayant liké cette recette
if (!isset($recette['liked_by'])) {
    $recette['liked_by'] = [];
}

$user_email = $_SESSION['user']['email']; // mail de l'utilisateur connecté

// un recherche de la recette depuis son ID
foreach ($recettes as &$recette) {
    if ($recette['id'] == $recette_id) {
        // Si un tableau 'liked_by' existe pour la recette?  sinon l'initialiser
        if (!isset($recette['liked_by'])) {
            $recette['liked_by'] = [];
        }

        // Est ce que l'utilisateur a deja liké 
        if (in_array($user_email, $recette['liked_by'])) {
            // Si oui, on fait -1, cad on retire son like
            $recette['liked_by'] = array_diff($recette['liked_by'], [$user_email]);
            $recette['likes']--;
        } else {
            // Sinon +1 like, cad on ajoute son like
            $recette['liked_by'][] = $user_email;
            $recette['likes']++;
        }

        // On sauvegarde les modifications dans json
        file_put_contents($recetteFile, json_encode($recettes, JSON_PRETTY_PRINT));
        echo json_encode(["success" => true, "likes" => $recette['likes']]);
        exit();
    }
}

echo json_encode(["success" => false, "message" => "Erreur lors d'un like"]);
