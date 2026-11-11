<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';
requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$lien = $id ? getLienUtileById($id) : null;
$pageTitle = $lien ? 'Modifier le lien' : 'Nouveau lien';
$message = '';
$messageType = '';

$tousLesJeux = getJeux(); // liste complète pour les checkboxes
$jeuxSelectionnes = $lien['jeux_ids'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $codePromo = trim($_POST['code_promo'] ?? '') ?: null;
    $ordre = (int)($_POST['ordre'] ?? 0);
    $estActif = isset($_POST['est_actif']) ? 1 : 0;
    $estGenerique = isset($_POST['est_generique']) ? 1 : 0;
    $jeuxIds = $_POST['jeux'] ?? [];
    $image = $lien['image'] ?? '';

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
        if (empty($titre) || empty($url)) {
            $message = 'Le titre et l\'URL sont obligatoires.';
            $messageType = 'error';
        } elseif (!$estGenerique && empty($jeuxIds)) {
            $message = 'Sélectionnez au moins un jeu, ou cochez "Lien générique".';
            $messageType = 'error';
        } else {
            if ($id) {
                updateLienUtile($id, $titre, $description, $url, $codePromo, $image, $ordre, $estActif, $estGenerique, $jeuxIds);
            } else {
                $id = createLienUtile($titre, $description, $url, $codePromo, $image, $ordre, $estActif, $estGenerique, $jeuxIds);
            }
            header('Location: liens-utiles.php?saved=1');
            exit;
        }
    }
}

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="admin-page-header">
    <h2><?= $pageTitle ?></h2>
    <a href="liens-utiles.php" class="admin-btn admin-btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="admin-form-group">
        <label for="titre">Titre / Partenaire *</label>
        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($lien['titre'] ?? '') ?>" required>
    </div>

    <div class="admin-form-group">
        <label for="description">Description courte</label>
        <textarea id="description" name="description" rows="3"><?= htmlspecialchars($lien['description'] ?? '') ?></textarea>
    </div>

    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="url">Lien d'affiliation (URL) *</label>
            <input type="url" id="url" name="url" value="<?= htmlspecialchars($lien['url'] ?? '') ?>" required>
        </div>
        <div class="admin-form-group">
            <label for="code_promo">Code promo / créateur</label>
            <input type="text" id="code_promo" name="code_promo" value="<?= htmlspecialchars($lien['code_promo'] ?? '') ?>">
        </div>
    </div>

    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="ordre">Ordre d'affichage</label>
            <input type="number" id="ordre" name="ordre" value="<?= $lien['ordre'] ?? 0 ?>">
        </div>
        <div class="admin-form-group actif_checkbox">
            <label class="admin-checkbox-label">
                <input type="checkbox" name="est_actif" value="1" <?= (!isset($lien) || !empty($lien['est_actif'])) ? 'checked' : '' ?>>
                Actif
            </label>
        </div>
    </div>

    <div class="admin-form-group">
        <label class="admin-checkbox-label">
            <input type="checkbox" id="est_generique" name="est_generique" value="1"
                <?= !empty($lien['est_generique']) ? 'checked' : '' ?>
                onchange="document.getElementById('jeux-selector').style.display = this.checked ? 'none' : 'block';">
            Lien générique (affiché sur tous les jeux)
        </label>
    </div>

    <div class="admin-form-group" id="jeux-selector" style="<?= !empty($lien['est_generique']) ? 'display:none;' : '' ?>">
        <label>Jeux concernés par ce lien</label>
        <div class="admin-checkbox-group">
            <?php foreach ($tousLesJeux as $j): ?>
            <label class="admin-checkbox-label">
                <input type="checkbox" name="jeux[]" value="<?= $j['id'] ?>"
                    <?= in_array($j['id'], $jeuxSelectionnes) ? 'checked' : '' ?>>
                <?= htmlspecialchars($j['titre']) ?>
            </label>
            <?php endforeach; ?>
        </div>
        <p class="admin-hint">Ce lien s'affichera uniquement sur les jeux cochés, en plus des liens génériques éventuels.</p>
    </div>

    <div class="admin-form-group">
        <label for="image">Logo / Icône</label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (!empty($lien['image'])): ?>
        <div class="admin-image-preview">
            <p>Logo actuel :</p>
            <img src="../<?= getImagePath($lien['image'], 'partner') ?>" alt="">
        </div>
        <?php endif; ?>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-success"><i class="fas fa-save"></i> <?= $id ? 'Mettre à jour' : 'Créer' ?></button>
        <a href="liens-utiles.php" class="admin-btn admin-btn-secondary">Annuler</a>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>