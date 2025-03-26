<?php

session_start();
header('Content-Type: application/json');

if (
    !isset($_SESSION['user']) ||
    ($_SESSION['user']['role'] !== 'admin' &&
        (!isset($_SESSION['user']['role2']) || $_SESSION['user']['role2'] !== 'admin'))
) {
    echo json_encode(["success" => false, "message" => "Accès refusé."]);
    exit();
}


$recettesFile = 'donnees/recette.json';
$recettes = file_exists($recettesFile) ? json_decode(file_get_contents($recettesFile), true) : [];

$recette_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$index = array_search($recette_id, array_column($recettes, 'id'));

if ($index === false) {
    echo json_encode(["success" => false, "message" => "Recette non trouvée."]);
    exit();
}

array_splice($recettes, $index, 1);
file_put_contents($recettesFile, json_encode($recettes, JSON_PRETTY_PRINT));

echo json_encode(["success" => true, "message" => "Recette supprimée avec succès."]);
exit();
