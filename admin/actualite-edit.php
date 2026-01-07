<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$actualite = $id ? getActualiteById($id) : null;
$pageTitle = $actualite ? 'Modifier l\'actualité' : 'Nouvelle actualité';
$message = '';
$messageType = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $texte = $_POST['texte'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');
    $image = $actualite['image'] ?? '';
    $masquerActu = isset($_POST['masquer_actu']) ? 1 : 0;

    // Upload de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['image'], 'actualites');
        if ($upload['success']) {
            $image = $upload['filename'];
        } else {
            $message = $upload['error'];
            $messageType = 'error';
        }
    }

    if (empty($message)) {
        if (empty($titre)) {
            $message = 'Le titre est obligatoire.';
            $messageType = 'error';
        } else {
            if ($id) {
                if (updateActualite($id, $titre, $texte, $date, $image, $masquerActu)) {
                    $message = 'Actualité mise à jour avec succès.';
                    $messageType = 'success';
                    $actualite = getActualiteById($id);
                } else {
                    $message = 'Erreur lors de la mise à jour.';
                    $messageType = 'error';
                }
            } else {
                if (createActualite($titre, $texte, $date, $image, $masquerActu)) {
                    header('Location: actualites.php');
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
    <a href="actualites.php" class="admin-btn admin-btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($actualite['titre'] ?? '') ?>" required>
        </div>
        <div class="admin-form-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?= $actualite['date'] ?? date('Y-m-d') ?>">
        </div>
    </div>

    <div class="admin-form-group full-width">
        <label for="texte">Contenu</label>
        <textarea id="texte" name="texte" class="tinymce-editor"><?= htmlspecialchars($actualite['texte'] ?? '') ?></textarea>
    </div>

    <div class="admin-form-row">
        <div class="admin-form-group">
            <div class="admin-checkbox-group">
                <label class="admin-checkbox-label">
                    <input type="checkbox" name="masquer_actu" value="1"
                        <?= (!empty($actualite['masquer_actu'])) ? 'checked' : '' ?>>
                    Masquer l'actualité
                </label>
            </div>
        </div>
    </div>

    <div class="admin-form-group">
        <label for="image">Image</label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (!empty($actualite['image'])): ?>
        <div class="admin-image-preview">
            <p>Image actuelle :</p>
            <img src="../<?= getImagePath($actualite['image'], 'news') ?>" alt="">
        </div>
        <?php endif; ?>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-success">
            <i class="fas fa-save"></i> <?= $id ? 'Mettre à jour' : 'Créer' ?>
        </button>
        <a href="actualites.php" class="admin-btn admin-btn-secondary">Annuler</a>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>
