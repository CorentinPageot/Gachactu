<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$pageTitle = 'Gestion des Catégories';
$message = '';
$messageType = '';

// Suppression d'une catégorie
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deleteCategorie((int)$_GET['delete'])) {
        $message = 'Catégorie supprimée avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'error';
    }
}

// Ajout d'une catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $nom = trim($_POST['nom'] ?? '');
        if (!empty($nom)) {
            if (createCategorie($nom)) {
                $message = 'Catégorie ajoutée avec succès.';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de l\'ajout.';
                $messageType = 'error';
            }
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $nom = trim($_POST['nom'] ?? '');
        if ($id && !empty($nom)) {
            if (updateCategorie($id, $nom)) {
                $message = 'Catégorie modifiée avec succès.';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la modification.';
                $messageType = 'error';
            }
        }
    }
}

$categories = getCategories();

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-<?= $messageType ?>">
    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="admin-page-header">
    <h2>Catégories (<?= count($categories) ?>)</h2>
</div>

<div class="admin-two-columns">
    <div class="admin-column">
        <div class="admin-card">
            <h3><i class="fas fa-plus"></i> Ajouter une catégorie</h3>
            <form method="POST" class="admin-inline-form">
                <input type="hidden" name="action" value="add">
                <input type="text" name="nom" placeholder="Nom de la catégorie" required>
                <button type="submit" class="admin-btn admin-btn-success">
                    <i class="fas fa-plus"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="admin-column">
        <div class="admin-card">
            <h3><i class="fas fa-list"></i> Liste des catégories</h3>
            <?php if (empty($categories)): ?>
            <p class="admin-text-muted">Aucune catégorie.</p>
            <?php else: ?>
            <ul class="admin-simple-list">
                <?php foreach ($categories as $cat): ?>
                <li>
                    <form method="POST" class="admin-edit-form">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                        <input type="text" name="nom" value="<?= htmlspecialchars($cat['nom']) ?>" required>
                        <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="categories.php?delete=<?= $cat['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm" title="Supprimer" onclick="return confirm('Supprimer cette catégorie ?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
