<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$pageTitle = 'Actualités';
$message = '';
$messageType = '';

// Suppression
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deleteActualite((int)$_GET['delete'])) {
        $message = 'Actualité supprimée avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'error';
    }
}

$actualites = getActualites();

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-<?= $messageType ?>">
    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="admin-page-header">
    <h2>Liste des actualités</h2>
    <a href="actualite-edit.php" class="admin-btn admin-btn-primary">
        <i class="fas fa-plus"></i> Nouvelle actualité
    </a>
</div>

<?php if (empty($actualites)): ?>
<div class="admin-empty">
    <i class="fas fa-newspaper"></i>
    <p>Aucune actualité pour le moment.</p>
    <a href="actualite-edit.php" class="admin-btn admin-btn-primary">Créer une actualité</a>
</div>
<?php else: ?>
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Titre</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($actualites as $actu): ?>
            <tr>
                <td>
                    <img src="../<?= getImagePath($actu['image'], 'news') ?>" alt="" class="admin-table-img">
                </td>
                <td><?= htmlspecialchars($actu['titre']) ?></td>
                <td><?= formatDateFr($actu['date']) ?></td>
                <td>
                    <?php if ($actu['masquer_actu'] == 1): ?>
                    <span class="admin-badge admin-badge-warning">Masquée</span>
                    <?php else: ?>
                    <span class="admin-badge admin-badge-success">Affichée</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="admin-actions">
                        <a href="actualite-edit.php?id=<?= $actu['id'] ?>" class="admin-btn admin-btn-primary" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="../article.php?id=<?= $actu['id'] ?>" target="_blank" class="admin-btn admin-btn-secondary" title="Voir sur le site">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="actualites.php?delete=<?= $actu['id'] ?>" class="admin-btn admin-btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'includes/admin-footer.php'; ?>
