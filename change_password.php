<?php
session_start();   // je commence la session pour pouvoir récupérer les informations de l'utilisateur
header('Content-Type: application/json');   // le format de la reponse sera du type Json 


// Si l'utilisateur est connecté ?? 
if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Vous devez être connecte pour changer votre mot de passe!"]);
    exit;
}

// le mail de l'utilisateur connecté
$email = $_SESSION['user']['email'];
// Fichier contenant les utilisateurs avec leurs informations
$fileUser = "donnees/users.json";
// Vérifie si le fichier existe, sinon on initialise un tableau vide
$users = file_exists($fileUser) ? json_decode(file_get_contents($fileUser), true) : [];

// Récupérer le nouveau mot de passe fourni par l'utilisateur via une requête POST
$new_password = $_POST['new_password'] ?? '';

// un recherche d'utilisateur avec son mail dans la liste des utilisateurs, array_search renvoie true or false
$index = array_search($email, array_column($users, 'email'));
if ($index === false) {
    echo json_encode(["success" => false, "message" => "Utilisateur non trouvé."]);
    exit();
}

// les informations de l'utilisateur qu'on a trouvé
$user = $users[$index];

// la longueur de nouveau mot de passe >= 6 caractères
if (strlen($new_password) < 6) {
    echo json_encode(["success" => false, "message" => "Le mot de passe doit contenir au moins 6 caractères."]);
    exit();
}

// Je haches le nouveau mot de passe avant de le sauvegarder dans le users.json
$users[$index]['password'] = password_hash($new_password, PASSWORD_DEFAULT);
// mise a jour de users.json
file_put_contents($fileUser, json_encode($users, JSON_PRETTY_PRINT));

echo json_encode(["success" => true, "message" => "Mot de passe modifié avec succès!"]);
exit();
