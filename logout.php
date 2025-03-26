<?php
session_start();    // je commence la session pour pouvoir récupérer les informations de l'utilisateur
session_destroy();  // Détruit la session en déconnectant l'utilisateur
// fait un redirect vers la page index.php apres un logout
header("Location: ./index.php");
exit();
