<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

$action = $input['action'];

try {
    switch ($action) {
        case 'create':
            if (!isset($input['jeu_id'])) {
                echo json_encode(['success' => false, 'error' => 'jeu_id requis']);
                exit;
            }
            $nom = isset($input['nom']) ? trim($input['nom']) : 'Nouvelle Tier List';
            if (empty($nom)) $nom = 'Nouvelle Tier List';

            $id = createTierList((int)$input['jeu_id'], $nom);
            if ($id === false) {
                echo json_encode(['success' => false, 'error' => 'Maximum 3 tier lists par jeu']);
            } else {
                echo json_encode(['success' => true, 'tier_list_id' => $id]);
            }
            break;

        case 'rename':
            if (!isset($input['tier_list_id']) || !isset($input['nom'])) {
                echo json_encode(['success' => false, 'error' => 'tier_list_id et nom requis']);
                exit;
            }
            $nom = trim($input['nom']);
            if (empty($nom)) {
                echo json_encode(['success' => false, 'error' => 'Le nom ne peut pas être vide']);
                exit;
            }
            renameTierList((int)$input['tier_list_id'], $nom);
            echo json_encode(['success' => true]);
            break;

        case 'delete':
            if (!isset($input['tier_list_id'])) {
                echo json_encode(['success' => false, 'error' => 'tier_list_id requis']);
                exit;
            }
            deleteTierList((int)$input['tier_list_id']);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Action inconnue']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
