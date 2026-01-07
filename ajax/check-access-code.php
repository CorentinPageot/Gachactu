<?php
require_once __DIR__ . '/../includes/config.php';

$data = json_decode(file_get_contents('php://input'), true);
$inputCode = $data['code'] ?? '';

if (!$inputCode) {
    echo json_encode(['success' => false]);
    exit;
}

if (hash_equals($_ENV['TIERMAKER_CODE'], $inputCode)) {
    session_start();
    $_SESSION['tiermaker_unlocked'] = true;

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}