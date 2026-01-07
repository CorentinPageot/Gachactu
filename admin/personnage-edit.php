<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$personnage = $id ? getPersonnageById($id) : null;
$pageTitle = $personnage ? 'Modifier le personnage' : 'Nouveau personnage';
$message = '';
$messageType = '';

// Jeu pré-sélectionné
$jeuIdPreselect = isset($_GET['jeu']) ? (int)$_GET['jeu'] : ($personnage['jeu_id'] ?? 0);

// Récupérer tous les jeux
$jeux = getJeux();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jeuId = (int)($_POST['jeu_id'] ?? 0);
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $rarete = trim($_POST['rarete'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $image = $personnage['image'] ?? '';
    $image_tierlist = $personnage['image_tierlist'] ?? '';

    // Upload de l'image principale
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['image'], 'jeux');
        if ($upload['success']) {
            $image = $upload['filename'];
        } else {
            $message = $upload['error'];
            $messageType = 'error';
        }
    }

    // Upload de l'image pour la tierlist
    if (isset($_FILES['image_tierlist']) && $_FILES['image_tierlist']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['image_tierlist'], 'jeux');
        if ($upload['success']) {
            $image_tierlist = $upload['filename'];
        } else {
            $message = $upload['error'];
            $messageType = 'error';
        }
    }

    if (empty($message)) {
        if (empty($nom)) {
            $message = 'Le nom est obligatoire.';
            $messageType = 'error';
        } elseif (empty($jeuId)) {
            $message = 'Veuillez sélectionner un jeu.';
            $messageType = 'error';
        } else {
            if ($id) {
                if (updatePersonnage($id, $jeuId, $nom, $description, $rarete, $role, $image, $image_tierlist)) {
                    $message = 'Personnage mis à jour avec succès.';
                    $messageType = 'success';
                    $personnage = getPersonnageById($id);
                } else {
                    $message = 'Erreur lors de la mise à jour.';
                    $messageType = 'error';
                }
            } else {
                $newId = createPersonnage($jeuId, $nom, $description, $rarete, $role, $image, $image_tierlist);
                if ($newId) {
                    header('Location: personnage-edit.php?id=' . $newId . '&created=1');
                    exit;
                } else {
                    $message = 'Erreur lors de la création.';
                    $messageType = 'error';
                }
            }
        }
    }
}

// Message après création
if (isset($_GET['created'])) {
    $message = 'Personnage créé avec succès.';
    $messageType = 'success';
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
    <a href="personnages.php<?= $jeuIdPreselect ? '?jeu=' . $jeuIdPreselect : '' ?>" class="admin-btn admin-btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($personnage['nom'] ?? '') ?>" required>
        </div>
        <div class="admin-form-group">
            <label for="jeu_id">Jeu *</label>
            <select id="jeu_id" name="jeu_id" required>
                <option value="">Sélectionner un jeu</option>
                <?php foreach ($jeux as $jeu): ?>
                <option value="<?= $jeu['id'] ?>" <?= $jeuIdPreselect == $jeu['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($jeu['titre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="rarete">Rareté</label>
            <select id="rarete" name="rarete">
                <option value="">Aucune</option>
                <option value="SSR" <?= ($personnage['rarete'] ?? '') === 'SSR' ? 'selected' : '' ?>>SSR</option>
                <option value="SR" <?= ($personnage['rarete'] ?? '') === 'SR' ? 'selected' : '' ?>>SR</option>
                <option value="R" <?= ($personnage['rarete'] ?? '') === 'R' ? 'selected' : '' ?>>R</option>
                <option value="5*" <?= ($personnage['rarete'] ?? '') === '5*' ? 'selected' : '' ?>>5 étoiles</option>
                <option value="4*" <?= ($personnage['rarete'] ?? '') === '4*' ? 'selected' : '' ?>>4 étoiles</option>
                <option value="3*" <?= ($personnage['rarete'] ?? '') === '3*' ? 'selected' : '' ?>>3 étoiles</option>
            </select>
        </div>
        <div class="admin-form-group">
            <label for="role">Rôle</label>
            <input type="text" id="role" name="role" value="<?= htmlspecialchars($personnage['role'] ?? '') ?>" placeholder="Ex: DPS, Support, Tank...">
        </div>
    </div>

    <div class="admin-form-group full-width">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($personnage['description'] ?? '') ?></textarea>
    </div>

    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="image">Image principale</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if (!empty($personnage['image'])): ?>
            <div class="admin-image-preview">
                <p>Image actuelle :</p>
                <img src="../<?= getImagePath($personnage['image'], 'game') ?>" alt="">
            </div>
            <?php endif; ?>
        </div>
        <div class="admin-form-group">
            <label for="image_tierlist">Image pour la tier list</label>
            <input type="file" id="image_tierlist" name="image_tierlist" accept="image/*">
            <?php if (!empty($personnage['image_tierlist'])): ?>
            <div class="admin-image-preview">
                <p>Image actuelle :</p>
                <img src="../<?= getImagePath($personnage['image_tierlist'], 'game') ?>" alt="">
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-success">
            <i class="fas fa-save"></i> <?= $id ? 'Mettre à jour' : 'Créer' ?>
        </button>
        <a href="personnages.php<?= $jeuIdPreselect ? '?jeu=' . $jeuIdPreselect : '' ?>" class="admin-btn admin-btn-secondary">Annuler</a>
        <?php if ($id): ?>
        <div class="admin-form-actions-right">
            <a href="guide-personnage-edit.php?id=<?= $id ?>" class="admin-btn admin-btn-primary">
                <i class="fas fa-book"></i> Modifier le guide
            </a>
        </div>
        <?php endif; ?>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>
