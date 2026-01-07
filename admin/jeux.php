<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$pageTitle = 'Gestion des Jeux';
$message = '';
$messageType = '';

// Suppression d'un jeu
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deleteJeu((int)$_GET['delete'])) {
        $message = 'Jeu supprimé avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'error';
    }
}

$jeux = getJeux();

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-<?= $messageType ?>">
    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="admin-page-header">
    <h2>Jeux (<?= count($jeux) ?>)</h2>
    <a href="jeu-edit.php" class="admin-btn admin-btn-primary">
        <i class="fas fa-plus"></i> Nouveau jeu
    </a>
</div>

<?php if (empty($jeux)): ?>
<div class="admin-empty">
    <i class="fas fa-gamepad"></i>
    <p>Aucun jeu pour le moment.</p>
    <a href="jeu-edit.php" class="admin-btn admin-btn-primary">Ajouter un jeu</a>
</div>
<?php else: ?>
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Titre</th>
                <th>Date de sortie</th>
                <th>Catégories</th>
                <th>Plateformes</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jeux as $jeu): ?>
            <?php
                $categories = getCategoriesJeu($jeu['id']);
                $plateformes = getPlateformesJeu($jeu['id']);
            ?>
            <tr>
                <td>
                    <img src="../<?= getImagePath($jeu['image'], 'game') ?>" alt="" class="admin-table-img">
                </td>
                <td>
                    <strong><?= htmlspecialchars($jeu['titre']) ?></strong>
                </td>
                <td><?= formatDateFr($jeu['date_sortie']) ?></td>
                <td>
                    <?php foreach ($categories as $cat): ?>
                    <span class="admin-badge admin-badge-info"><?= htmlspecialchars($cat['nom']) ?></span>
                    <?php endforeach; ?>
                </td>
                <td>
                    <?php foreach ($plateformes as $plat): ?>
                    <span class="admin-badge admin-badge-secondary"><?= htmlspecialchars($plat['nom']) ?></span>
                    <?php endforeach; ?>
                </td>
                <td>
                    <?php if ($jeu['est_populaire']): ?>
                    <span class="admin-badge admin-badge-success">Populaire</span>
                    <?php endif; ?>
                    <?php if (!empty($jeu['masquer_page']) && $jeu['masquer_page']): ?>
                    <span class="admin-badge admin-badge-warning">Masqué</span>
                    <?php endif; ?>
                    <?php if ($jeu['top10'] && $jeu['top10_position']): ?>
                    <span class="admin-badge admin-badge-info">Top 10 (n°<?= htmlspecialchars($jeu['top10_position']) ?>)</span>
                    <?php endif; ?>
                </td>
                <td class="admin-actions">
                    <a href="jeu-edit.php?id=<?= $jeu['id'] ?>" class="admin-btn admin-btn-primary" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="personnages.php?jeu=<?= $jeu['id'] ?>" class="admin-btn admin-btn-secondary" title="Personnages">
                        <i class="fas fa-users"></i>
                    </a>
                    <a href="codes.php?jeu=<?= $jeu['id'] ?>" class="admin-btn admin-btn-secondary" title="Codes">
                        <i class="fas fa-gift"></i>
                    </a>
                    <a href="jeux.php?delete=<?= $jeu['id'] ?>" class="admin-btn admin-btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce jeu ?');">
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
