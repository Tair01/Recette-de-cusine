<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// Verification si l'utilisateur a un role admin dans role ou role2 
if (
    !isset($_SESSION['user']) ||
    ($_SESSION['user']['role'] != 'admin' &&
        (!isset($_SESSION['user']['role2']) || $_SESSION['user']['role2'] != 'admin'))
) {
    echo json_encode(["success" => false, "message" => "Access denied"]);
    exit();
}


$userFile = 'donnees/users.json';
$users = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];

$email = strtolower(trim($_POST['email'] ?? ''));
$name = trim($_POST['name'] ?? '');
$role = trim($_POST['role'] ?? '');

if (!$email || !$name || !$role) {
    echo json_encode(["success" => false, "message" => "Tous les champs sont obligatoires"]);
    exit();
}

$index = array_search($email, array_column($users, 'email'));
if ($index === false) {
    echo json_encode(["success" => false, "message" => "L'utilisateur n'a pas été trouvé"]);
    exit();
}

$users[$index]['name'] = $name;
// $users[$index]['role'] = $role;
$users[$index]['role2'] = $role;

// Si l'utilisateur a demandé un nouveau rôle et que l'administrateur l'a confirmé,alors on enleve requested_role dans le fichier users.json
if (isset($users[$index]['requested_role']) && $users[$index]['requested_role'] === $role) {
    unset($users[$index]['requested_role']);
}

if (file_put_contents($userFile, json_encode($users, JSON_PRETTY_PRINT))) {
    echo json_encode(["success" => true, "message" => "User updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour"]);
}
