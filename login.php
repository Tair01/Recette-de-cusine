<?php
session_start();    // je commence la session pour pouvoir récupérer les informations de l'utilisateur
// mail et le mot de passe envoyés via la requête POST soit j'initialise une chaîne vide 
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// si les champs email et mot de passe sont remplis cad valides
if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Tous les cases sont obligatoires"]);
    exit;
}
// Fichier contenant les utilisateurs avec leurs informations
$userFile = "donnees/users.json";
// Vérifie si le fichier existe, sinon on initialise un tableau vide
$users = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];

// Parcourt des utilisateurs pour vérifier les identifiants mail et status verified?
foreach ($users as $user) {
    // Si le mail correspond à celui d'un utilisateur enregistré 
    if ($user['email'] == $email) {
        if (!isset($user['verified']) || !$user['verified']) {
            // Vérifie si l'email de l'utilisateur a été vérifié.  
            // Sans validation via un code envoyé par email, l'utilisateur ne peut pas se connecter.  
            echo json_encode(["success" => false, "message" => "Email non vérifié"]);
            exit;
        }
        // Si le mot de passe saisi == au mot de passe hashé stocké dans json
        if (password_verify($password, $user['password'])) {
            // Je stocke les informations de l'utilisateur en session après une connexion réussie!!!
            $_SESSION['user'] = [
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            echo json_encode(["success" => true, "message" => "Connexion good"]);
            exit;
        } else {
            // Mot de passe incorrect
            echo json_encode(["success" => false, "message" => "Mot de passe incorrect"]);
            exit;
        }
    }
}
echo json_encode(["success" => false, "message" => "Identifiants incorrects"]);
