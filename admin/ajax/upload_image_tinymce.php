<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/admin-functions.php';

header('Content-Type: application/json');

// Vérification de l'authentification
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Aucun fichier envoyé']);
    exit;
}

$upload = uploadImage($_FILES['file'], 'uploads');

if ($upload['success']) {
    // Utiliser un chemin absolu depuis la racine du site
    echo json_encode([
        'location' => '/images/uploads/' . $upload['filename']
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'error' => $upload['error']
    ]);
}
