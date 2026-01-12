<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$pageTitle = 'Gestion des Développeurs';
$message = '';
$messageType = '';

// Suppression d'un développeur
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deleteDeveloppeur((int)$_GET['delete'])) {
        $message = 'Développeur supprimé avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'error';
    }
}

// Ajout d'un développeur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $nom = trim($_POST['nom'] ?? '');
        if (!empty($nom)) {
            if (createDeveloppeur($nom)) {
                $message = 'Développeur ajouté avec succès.';
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
            if (updateDeveloppeur($id, $nom)) {
                $message = 'Développeur modifié avec succès.';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la modification.';
                $messageType = 'error';
            }
        }
    }
}

$developpeurs = getDeveloppeurs();

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-<?= $messageType ?>">
    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="admin-page-header">
    <h2>Développeurs (<?= count($developpeurs) ?>)</h2>
</div>

<div class="admin-two-columns">
    <div class="admin-column">
        <div class="admin-card">
            <h3><i class="fas fa-plus"></i> Ajouter un développeur</h3>
            <form method="POST" class="admin-inline-form">
                <input type="hidden" name="action" value="add">
                <input type="text" name="nom" placeholder="Nom du développeur" required>
                <button type="submit" class="admin-btn admin-btn-success">
                    <i class="fas fa-plus"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="admin-column">
        <div class="admin-card">
            <h3><i class="fas fa-list"></i> Liste des développeurs</h3>
            <?php if (empty($developpeurs)): ?>
            <p class="admin-text-muted">Aucun développeur.</p>
            <?php else: ?>
            <ul class="admin-simple-list">
                <?php foreach ($developpeurs as $dev): ?>
                <li>
                    <form method="POST" class="admin-edit-form">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $dev['id'] ?>">
                        <input type="text" name="nom" value="<?= htmlspecialchars($dev['nom']) ?>" required>
                        <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="developpeurs.php?delete=<?= $dev['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm" title="Supprimer" onclick="return confirm('Supprimer ce développeur ?');">
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
