<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['jeu_id']) || !isset($input['tiers'])) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

$jeuId = (int)$input['jeu_id'];
$tiersData = $input['tiers'];

try {
    $pdo = getDatabase();
    $pdo->beginTransaction();

    // Définition des tiers avec leurs couleurs et ordre
    $tiersConfig = [
        'APEX' => ['couleur' => '#ff7f7f', 'ordre' => 1],
        'T1'   => ['couleur' => '#ffbf7f', 'ordre' => 2],
        'T2'   => ['couleur' => '#ffdf7f', 'ordre' => 3],
        'T3'   => ['couleur' => '#ffff7f', 'ordre' => 4],
        'T4'   => ['couleur' => '#bfff7f', 'ordre' => 5],
        'T5'   => ['couleur' => '#7fbfff', 'ordre' => 6],
    ];

    // Supprimer les anciens tiers et associations pour ce jeu
    $stmt = $pdo->prepare("SELECT id FROM tiers WHERE jeu_id = ?");
    $stmt->execute([$jeuId]);
    $oldTierIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($oldTierIds)) {
        $placeholders = implode(',', array_fill(0, count($oldTierIds), '?'));
        $pdo->prepare("DELETE FROM personnages_tiers WHERE tier_id IN ($placeholders)")->execute($oldTierIds);
        $pdo->prepare("DELETE FROM tiers WHERE id IN ($placeholders)")->execute($oldTierIds);
    }

    // Créer les nouveaux tiers et associations
    foreach ($tiersConfig as $tierNom => $config) {
        // Créer le tier
        $stmt = $pdo->prepare("INSERT INTO tiers (jeu_id, nom, couleur, ordre) VALUES (?, ?, ?, ?)");
        $stmt->execute([$jeuId, $tierNom, $config['couleur'], $config['ordre']]);
        $tierId = $pdo->lastInsertId();

        // Ajouter les personnages à ce tier
        if (isset($tiersData[$tierNom]) && !empty($tiersData[$tierNom])) {
            $stmtPerso = $pdo->prepare("INSERT INTO personnages_tiers (personnage_id, tier_id) VALUES (?, ?)");
            foreach ($tiersData[$tierNom] as $personnageId) {
                $stmtPerso->execute([(int)$personnageId, $tierId]);
            }
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
