<?php
require_once '../includes/functions.php';

$jeuId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$section = isset($_GET['section']) ? $_GET['section'] : 'tierlist';

$jeu = getJeuById($jeuId);
if (!$jeu) {
    echo '<div class="error">Jeu non trouvé.</div>';
    exit;
}

switch ($section) {
    case 'tierlist':
        include '../templates/jeu/tierlist.php';
        break;
    case 'reroll':
        include '../templates/jeu/reroll.php';
        break;
    case 'guide':
        include '../templates/jeu/guide.php';
        break;
    case 'codes':
        include '../templates/jeu/codes.php';
        break;
    case 'personnages':
        include '../templates/jeu/personnages.php';
        break;
    case 'tierlistmaker':
        include '../templates/jeu/tierlistmaker.php';
        break;
    default:
        echo '<div class="error">Section non trouvée.</div>';
}
