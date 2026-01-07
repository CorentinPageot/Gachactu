<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$partenaire = $id ? getPartenaireById($id) : null;
$pageTitle = $partenaire ? 'Modifier le partenaire' : 'Nouveau partenaire';
$message = '';
$messageType = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $image = $partenaire['image'] ?? '';

    // Upload de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['image'], 'partenaires');
        if ($upload['success']) {
            $image = $upload['filename'];
        } else {
            $message = $upload['error'];
            $messageType = 'error';
        }
    }

    if (empty($message)) {
        if (empty($nom)) {
            $message = 'Le nom est obligatoire.';
            $messageType = 'error';
        } else {
            if ($id) {
                if (updatePartenaire($id, $nom, $url, $image)) {
                    $message = 'Partenaire mis à jour avec succès.';
                    $messageType = 'success';
                    $partenaire = getPartenaireById($id);
                } else {
                    $message = 'Erreur lors de la mise à jour.';
                    $messageType = 'error';
                }
            } else {
                if (createPartenaire($nom, $url, $image)) {
                    header('Location: partenaires.php');
                    exit;
                } else {
                    $message = 'Erreur lors de la création.';
                    $messageType = 'error';
                }
            }
        }
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
    <h2><?= $pageTitle ?></h2>
    <a href="partenaires.php" class="admin-btn admin-btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($partenaire['nom'] ?? '') ?>" required>
        </div>
        <div class="admin-form-group">
            <label for="url">URL</label>
            <input type="url" id="url" name="url" value="<?= htmlspecialchars($partenaire['url'] ?? '') ?>" placeholder="https://...">
        </div>
    </div>

    <div class="admin-form-group">
        <label for="image">Logo</label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (!empty($partenaire['image'])): ?>
        <div class="admin-image-preview">
            <p>Logo actuel :</p>
            <img src="../<?= getImagePath($partenaire['image'], 'partner') ?>" alt="">
        </div>
        <?php endif; ?>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-success">
            <i class="fas fa-save"></i> <?= $id ? 'Mettre à jour' : 'Créer' ?>
        </button>
        <a href="partenaires.php" class="admin-btn admin-btn-secondary">Annuler</a>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>
