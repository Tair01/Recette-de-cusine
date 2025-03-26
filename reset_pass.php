<?php
header('Content-Type: application/json; charset=UTF-8');
ob_start(); // On fait un buffer pour supprimer tous ce qui est texte inattendu

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$fileUser = "donnees/users.json";
$users = file_exists($fileUser) ? json_decode(file_get_contents($fileUser), true) : [];

$email = trim($_POST['email'] ?? '');

if (!$email) {
    echo json_encode(["success" => false, "message" => "Vous devez remplir le champ Email!"]);
    exit;
}

$index = array_search($email, array_column($users, 'email'));
if ($index === false) {
    echo json_encode(["success" => false, "message" => "Utilisateur non trouvé"]);
    exit();
}

$name = $users[$index]['name'] ?? 'Utilisateur inconnu'; // Pour savoir le nom d'utilisateur d'apres la verification d'email qui vient de saisir

function gen_password($length = 12)
{
    if ($length < 4) {
        throw new InvalidArgumentException('Le mot de passe doit comporter au moins 4 caractères');
    }

    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $digits = '0123456789';
    $specials = '!@#$%^&*()-_=+[]{}|;:,.<>?';

    $all_chars = $lowercase . $uppercase . $digits . $specials;

    // Il doit avoir au moins un symbole de chaque groupe
    $password = [
        $lowercase[random_int(0, strlen($lowercase) - 1)],
        $uppercase[random_int(0, strlen($uppercase) - 1)],
        $digits[random_int(0, strlen($digits) - 1)],
        $specials[random_int(0, strlen($specials) - 1)]
    ];

    // Je remplis les symboles restants avec des symboles aléatoires parmi tous les symboles possibles
    for ($i = 4; $i < $length; $i++) {
        $password[] = $all_chars[random_int(0, strlen($all_chars) - 1)];
    }

    // Je mélanges les caractères pour que l'ordre ne soit pas prévisible
    shuffle($password);

    return implode('', $password);
}

$newPass = gen_password(12);
$users[$index]['password'] = password_hash($newPass, PASSWORD_DEFAULT);
file_put_contents($fileUser, json_encode($users, JSON_PRETTY_PRINT));

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
    $mail->setFrom('btair04@gmail.com', 'Recette de Cuisine');
    $mail->addAddress($email, $name);

    $mail->CharSet = 'UTF-8';
    $mail->Subject = "Nouveau mot de passe";
    $mail->Body = "Bonjour $name,\n\n"
        . "Vous avez demandé la réinitialisation de votre mot de passe.\n\n"
        . "Votre nouveau mot de passe temporaire est : $newPass \n\n"
        . "Pour des raisons de sécurité, nous vous recommandons de le modifier dès que possible depuis les paramètres de votre compte.\n\n"
        . "⚠️ Ne partagez jamais votre mot de passe avec quiconque.\n\n"
        . "Si vous n'êtes pas à l'origine de cette demande, veuillez nous en informer immédiatement en contactant l'administrateur : btair04@gmail.com.\n\n"
        . "Si cette demande provient bien de vous, vous pouvez simplement ignorer ce message.\n\n"
        . "Cordialement,\nL'équipe Recette de Cuisine";


    $mail->send();
    echo json_encode(["success" => true, "message" => "Un email a été envoyé avec votre nouveau mot de passe."]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'envoi du nouveau mot de passe"]);
    exit;
}
