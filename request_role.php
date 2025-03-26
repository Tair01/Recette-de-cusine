<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "L'utilisateur est pas inscrit"]);
    exit();
}

$userFile = 'donnees/users.json';
$users = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];

$email = $_SESSION['user']['email'];
$newRole = trim($_POST['role'] ?? '');

if (empty($newRole)) {
    echo json_encode(["success" => false, "message" => "Choisissez un role"]);
    exit();
}

$index = array_search($email, array_column($users, 'email'));
if ($index === false) {
    echo json_encode(["success" => false, "message" => "L'utilisateur n'a pas ete trouvee"]);
    exit();
}

// Vérifier si le rôle demandé est déjà attribué dans le role de l'utilisateur 
if ($users[$index]['role'] === $newRole || (isset($users[$index]['role2']) && $users[$index]['role2'] === $newRole)) {
    echo json_encode(["success" => false, "message" => "Vous avez déjà ce rôle"]);
    exit();
}

$users[$index]['requested_role'] = $newRole;

if (file_put_contents($userFile, json_encode($users, JSON_PRETTY_PRINT))) {
    echo json_encode(["success" => true, "message" => "La demande a ete envoyee"]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de la demande"]);
}
