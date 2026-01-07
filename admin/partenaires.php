<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$pageTitle = 'Gestion des Partenaires';
$message = '';
$messageType = '';

// Suppression d'un partenaire
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deletePartenaire((int)$_GET['delete'])) {
        $message = 'Partenaire supprimé avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'error';
    }
}

$partenaires = getPartenaires();

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-<?= $messageType ?>">
    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="admin-page-header">
    <h2>Partenaires (<?= count($partenaires) ?>)</h2>
    <a href="partenaire-edit.php" class="admin-btn admin-btn-primary">
        <i class="fas fa-plus"></i> Nouveau partenaire
    </a>
</div>

<?php if (empty($partenaires)): ?>
<div class="admin-empty">
    <i class="fas fa-handshake"></i>
    <p>Aucun partenaire pour le moment.</p>
    <a href="partenaire-edit.php" class="admin-btn admin-btn-primary">Ajouter un partenaire</a>
</div>
<?php else: ?>
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Logo</th>
                <th>Nom</th>
                <th>URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($partenaires as $partenaire): ?>
            <tr>
                <td>
                    <img src="../<?= getImagePath($partenaire['image'], 'partner') ?>" alt="" class="admin-table-img">
                </td>
                <td><strong><?= htmlspecialchars($partenaire['nom']) ?></strong></td>
                <td>
                    <?php if (!empty($partenaire['url'])): ?>
                    <a href="<?= htmlspecialchars($partenaire['url']) ?>" target="_blank" class="admin-link">
                        <?= htmlspecialchars($partenaire['url']) ?>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    <?php else: ?>
                    <span class="admin-text-muted">Aucune URL</span>
                    <?php endif; ?>
                </td>
                <td class="admin-actions">
                    <a href="partenaire-edit.php?id=<?= $partenaire['id'] ?>" class="admin-btn admin-btn-primary" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="partenaires.php?delete=<?= $partenaire['id'] ?>" class="admin-btn admin-btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce partenaire ?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'includes/admin-footer.php'; ?>
