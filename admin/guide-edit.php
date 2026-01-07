<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$jeuId = isset($_GET['jeu']) ? (int)$_GET['jeu'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Validation
if (!$jeuId || !in_array($type, ['debutant', 'reroll'])) {
    header('Location: guides.php');
    exit;
}

$jeu = getJeuById($jeuId);
if (!$jeu) {
    header('Location: guides.php');
    exit;
}

$guide = getGuide($jeuId, $type);
$typeName = $type === 'debutant' ? 'Débutant' : 'Reroll';
$pageTitle = 'Guide ' . $typeName . ' - ' . $jeu['titre'];
$message = '';
$messageType = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = $_POST['contenu'] ?? '';

    if (saveGuide($jeuId, $type, $contenu)) {
        $message = 'Guide enregistré avec succès.';
        $messageType = 'success';
        $guide = getGuide($jeuId, $type);
    } else {
        $message = 'Erreur lors de l\'enregistrement.';
        $messageType = 'error';
    }
}

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-<?= $messageType ?>">
    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="admin-page-header">
    <h2>
        <img src="../<?= getImagePath($jeu['image'], 'game') ?>" alt="" style="width: 40px; height: 40px; border-radius: 8px; vertical-align: middle; margin-right: 10px;">
        Guide <?= $typeName ?> - <?= htmlspecialchars($jeu['titre']) ?>
    </h2>
    <a href="guides.php" class="admin-btn admin-btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<form method="POST" class="admin-form">
    <div class="admin-form-group full-width">
        <label for="contenu">Contenu du guide</label>
        <textarea id="contenu" name="contenu" class="tinymce-editor"><?= htmlspecialchars($guide['contenu'] ?? '') ?></textarea>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-success">
            <i class="fas fa-save"></i> Enregistrer
        </button>
        <a href="guides.php" class="admin-btn admin-btn-secondary">Annuler</a>
        <?php if ($type === 'debutant'): ?>
        <a href="guide-edit.php?jeu=<?= $jeuId ?>&type=reroll" class="admin-btn admin-btn-primary">
            <i class="fas fa-sync"></i> Voir le guide Reroll
        </a>
        <?php else: ?>
        <a href="guide-edit.php?jeu=<?= $jeuId ?>&type=debutant" class="admin-btn admin-btn-primary">
            <i class="fas fa-graduation-cap"></i> Voir le guide Débutant
        </a>
        <?php endif; ?>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>
