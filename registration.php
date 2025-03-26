<?php
header('Content-Type: application/json');
ob_start(); // On fait un buffer pour supprimer tous ce qui est texte inattendu

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';
$code = $_POST['code'] ?? '';

if (!$name || !$email || !$password || !$role) {
    echo json_encode(["success" => false, "message" => "Tous les champs sont obligatoires"]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(["success" => false, "message" => "Le mot de passe doit contenir au moins 6 caractères."]);
    exit();
}

$fileUser = "donnees/users.json";
$users = file_exists($fileUser) ? json_decode(file_get_contents($fileUser), true) : [];

foreach ($users as $user) {
    if ($user['email'] == $email) {
        echo json_encode(["success" => false, "message" => "Cet email est déjà utilisé"]);
        exit;
    }
}

$codes = [];
for ($i = 0; $i < 100; $i++) {
    $codes[] = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
}
$verifCode = $codes[array_rand($codes)];

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'btair04@gmail.com';
    $mail->Password   = 'spmq ewhf ylzs yalz';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->setFrom('btair04@gmail.com', 'Code de Vérification');
    $mail->addAddress($email, $name);

    $mail->CharSet = 'UTF-8';
    $mail->Subject = "Code de vérification";
    $mail->Body = "Bonjour $name,\n\n"
        . "Votre code de vérification est : $verifCode \n\n"
        . "Veuillez saisir ce code dans l'application pour confirmer votre identité.\n\n"
        . "⚠️ Ne partagez jamais ce code avec quiconque. \n\n"
        . "Si vous n'êtes pas à l'origine de cette demande, veuillez nous en informer immédiatement en contactant l'administrateur : btair04@gmail.com.\n\n"
        . "Si cette demande provient bien de vous, vous pouvez simplement ignorer ce message.\n\n"
        . "Cordialement,\nL'équipe Recette de Cuisine";
    $mail->send();
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'envoi du code"]);
    exit;
}

$newUser = [
    "name" => $name,
    "email" => $email,
    "password" => password_hash($password, PASSWORD_DEFAULT),
    "role" => $role,
    "verified" => false,
    "code" => $verifCode
];

$users[] = $newUser;

if (file_put_contents($fileUser, json_encode($users, JSON_PRETTY_PRINT))) {
    ob_end_clean();
    echo json_encode([
        "success" => true,
        "message" => "Un code de vérification a été envoyé à votre email!",
        "require_verification" => true
    ]);
} else {
    ob_end_clean();
    echo json_encode(["success" => false, "message" => "Erreur lors de l'inscription"]);
}
