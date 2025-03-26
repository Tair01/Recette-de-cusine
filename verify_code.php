<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['succes' => false, 'message' => 'Methode non autorisee']);
    exit;
}

$email = $_POST['email'] ?? '';
$code = $_POST['code'] ?? '';

if (!$email || !$code) {
    echo json_encode(["success" => false, "message" => "Tous les cases sont obligatoires"]);
    exit;
}

$fileUser = "donnees/users.json";
$users = file_exists($fileUser) ? json_decode(file_get_contents($fileUser), true) : [];

$flag = false;
foreach ($users as &$user) {        // Pourquoi &? Parce que sans & ca ne modifie pas la variable dans json
    if ($user['email'] == $email) {
        if ($user['code'] == $code) {
            $user['verified'] = true;
            unset($user['code']);  // on enleve le 'code' dans le fichier json apres la verification
            $flag = true;
            break;
        } else {
            echo json_encode(['success' => false, 'message' => 'Code incorrect']);
            exit;
        }
    }
}

if ($flag) {
    file_put_contents($fileUser, json_encode($users, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true, 'message' => 'Email vérifié avec succès']);
} else {
    echo json_encode(['success' => false, 'message' => "Utilisateur non trouve"]);
}
