<?php
session_start();  // je commence la session pour pouvoir récupérer les informations de l'utilisateur

// Verification si l'utilisateur est connecté et possède un role admin dans 'role' ou 'role2' de json
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] != 'admin' && (!isset($_SESSION['user']['role2']) || $_SESSION['user']['role2'] != 'admin'))) {
    echo json_encode(["success" => false, "message" => "Access denied"]);
    exit();
}

// Fichier contenant les utilisateurs avec leurs informations
$userFile = 'donnees/users.json';
// Vérifie si le fichier existe, sinon on initialise un tableau vide
$users = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];

// l'email de l'utilisateur à supprimer depuis la requête POST, trim enleve des caracteres whitespace et d'autres caracts predefinies
$emailDelete = strtolower(trim($_POST['email'] ?? '')); // strlower convertit un string vers un string miniscule 

// L'email est valide ou pas ? 
if (empty($emailDelete)) {
    echo json_encode(["success" => false, "message" => "Invalid email"]);
    exit();
}
// un recherche d'utilisateur avec son mail dans la liste des utilisateurs, array_search renvoie true or false
$index = array_search($emailDelete, array_column($users, 'email'));
if ($index === false) {
    echo json_encode(["success" => false, "message" => "L'utilisateur n'a pas été trouvé"]);
    exit();
}

// On fait un delete de l'utilisateur à l'index trouvé dans le tableau des utilisateurs, array_splice: removes selected elements from an array
array_splice($users, $index, 1);

// mis à jour dans le fichier JSON
if (file_put_contents($userFile, json_encode($users, JSON_PRETTY_PRINT))) {
    echo json_encode(["success" => true, "message" => "User deleted successfully"]);    // un petit message de confirmation
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de la suppression"]);
}
