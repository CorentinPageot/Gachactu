<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$pageTitle = 'Gestion des Plateformes';
$message = '';
$messageType = '';

// Suppression d'une plateforme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deletePlateforme((int)$_GET['delete'])) {
        $message = 'Plateforme supprimée avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'error';
    }
}

// Ajout d'une plateforme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $nom = trim($_POST['nom'] ?? '');
        if (!empty($nom)) {
            if (createPlateforme($nom)) {
                $message = 'Plateforme ajoutée avec succès.';
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
            if (updatePlateforme($id, $nom)) {
                $message = 'Plateforme modifiée avec succès.';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la modification.';
                $messageType = 'error';
            }
        }
    }
}

$plateformes = getPlateformes();

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-<?= $messageType ?>">
    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="admin-page-header">
    <h2>Plateformes (<?= count($plateformes) ?>)</h2>
</div>

<div class="admin-two-columns">
    <div class="admin-column">
        <div class="admin-card">
            <h3><i class="fas fa-plus"></i> Ajouter une plateforme</h3>
            <form method="POST" class="admin-inline-form">
                <input type="hidden" name="action" value="add">
                <input type="text" name="nom" placeholder="Nom de la plateforme" required>
                <button type="submit" class="admin-btn admin-btn-success">
                    <i class="fas fa-plus"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="admin-column">
        <div class="admin-card">
            <h3><i class="fas fa-list"></i> Liste des plateformes</h3>
            <?php if (empty($plateformes)): ?>
            <p class="admin-text-muted">Aucune plateforme.</p>
            <?php else: ?>
            <ul class="admin-simple-list">
                <?php foreach ($plateformes as $plat): ?>
                <li>
                    <form method="POST" class="admin-edit-form">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $plat['id'] ?>">
                        <input type="text" name="nom" value="<?= htmlspecialchars($plat['nom']) ?>" required>
                        <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="plateformes.php?delete=<?= $plat['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm" title="Supprimer" onclick="return confirm('Supprimer cette plateforme ?');">
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
