<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$actualite = $id ? getActualiteById($id) : null;
$pageTitle = $actualite ? 'Modifier l\'actualité' : 'Nouvelle actualité';
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = trim($_POST['titre'] ?? '');
    $texte = $_POST['texte'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');
    $image = $actualite['image'] ?? '';

    $masquerActu = isset($_POST['masquer_actu']) ? 1 : 0;

    // lootbar
    $lootbar_enabled = isset($_POST['lootbar_enabled']) ? 1 : 0;
    $lootbar_url = $_POST['lootbar_url'] ?? '';
    $lootbar_url_texte = $_POST['lootbar_url_texte'] ?? '';

    // Instant Gaming
    $ig_enabled = isset($_POST['ig_enabled']) ? 1 : 0;
    $ig_nom_jeu = $_POST['ig_nom_jeu'] ?? '';
    $ig_url = $_POST['ig_url'] ?? '';
    $ig_prix = $_POST['ig_prix'] ?? '';
    $ig_date_sortie = $_POST['ig_date_sortie'] ?? '';
    $ig_image = $actualite['ig_image'] ?? '';

    $ig_categories = isset($_POST['ig_categories']) ? implode(',', $_POST['ig_categories']) : '';
    $ig_plateformes = isset($_POST['ig_plateformes']) ? implode(',', $_POST['ig_plateformes']) : '';
    $ig_developpeur = $_POST['ig_developpeur'] ?? null;

    // Upload image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['image'], 'actualites');

        if ($upload['success']) {
            $image = $upload['filename'];
        } else {
            $message = $upload['error'];
            $messageType = 'error';
        }
    }

    // Upload image instant gaming
    if (isset($_FILES['ig_image']) && $_FILES['ig_image']['error'] === UPLOAD_ERR_OK) {
        $uploadIg = uploadImage($_FILES['ig_image'], 'actualites');

        if ($uploadIg['success']) {
            $ig_image = $uploadIg['filename'];
        } else {
            $message = $uploadIg['error'];
            $messageType = 'error';
        }
    }

    if (empty($message)) {

        if (empty($titre)) {
            $message = 'Le titre est obligatoire.';
            $messageType = 'error';
        } else {

            if ($id) {

                if (updateActualite(
                    $id,
                    $titre,
                    $texte,
                    $date,
                    $image,
                    $masquerActu,

                    $lootbar_enabled,
                    $lootbar_url,
                    $lootbar_url_texte,

                    $ig_enabled,
                    $ig_nom_jeu,
                    $ig_categories,
                    $ig_developpeur,
                    $ig_date_sortie,
                    $ig_plateformes,
                    $ig_url,
                    $ig_prix,
                    $ig_image
                )) {

                    $message = 'Actualité mise à jour avec succès.';
                    $messageType = 'success';
                    $actualite = getActualiteById($id);

                } else {

                    $message = 'Erreur lors de la mise à jour.';
                    $messageType = 'error';
                }

            } else {

                if (createActualite(
                    $titre,
                    $texte,
                    $date,
                    $image,
                    $masquerActu,

                    $lootbar_enabled,
                    $lootbar_url,
                    $lootbar_url_texte,

                    $ig_enabled,
                    $ig_nom_jeu,
                    $ig_categories,
                    $ig_developpeur,
                    $ig_date_sortie,
                    $ig_plateformes,
                    $ig_url,
                    $ig_prix,
                    $ig_image
                )) {

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

$categories = getCategories();
$developpeurs = getDeveloppeurs();
$plateformes = getPlateformes();

$selectedCategories = !empty($actualite['ig_categories']) ? explode(',', $actualite['ig_categories']) : [];
$selectedPlateformes = !empty($actualite['ig_plateformes']) ? explode(',', $actualite['ig_plateformes']) : [];

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
            <label class="admin-checkbox-label">
            <input type="checkbox" name="masquer_actu" value="1"
            <?= (!empty($actualite['masquer_actu'])) ? 'checked' : '' ?>>
            Masquer l'actualité
            </label>
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

    <h3 class="h3_edit">Intégrations</h3>

    <div class="admin-form-row">

        <div class="admin-form-group">
            <label class="admin-checkbox-label">
                <input type="checkbox" id="lootbar_enabled" name="lootbar_enabled"
                <?= (!empty($actualite['lootbar_enabled'])) ? 'checked' : '' ?>>
                Intégration LOOTBAR / Bluestacks
            </label>
        </div>

        <div class="admin-form-group">
            <label class="admin-checkbox-label">
                <input type="checkbox" id="ig_enabled" name="ig_enabled"
                <?= (!empty($actualite['ig_enabled'])) ? 'checked' : '' ?>>
                Intégration Instant Gaming
            </label>
        </div>

    </div>

    <div id="lootbar_fields" style="display:none">

        <div class="admin-form-group">
            <label>URL du lien LOOTBAR / Bluestacks</label>
            <input type="text" name="lootbar_url" value="<?= $actualite['lootbar_url'] ?? '' ?>">
        </div>

        <div class="admin-form-group">
            <label>Texte du lien LOOTBAR / Bluestacks</label>
            <input type="text" name="lootbar_url_texte" value="<?= $actualite['lootbar_url_texte'] ?? '' ?>">
        </div>

    </div>

    <div id="ig_fields" style="display:none">

        <div class="admin-form-row">

            <div class="admin-form-group">
                <label>Nom du jeu</label>
                <input type="text" name="ig_nom_jeu" value="<?= $actualite['ig_nom_jeu'] ?? '' ?>">
            </div>

            <div class="admin-form-group">
                <label>URL</label>
                <input type="text" name="ig_url" value="<?= $actualite['ig_url'] ?? '' ?>">
            </div>

        </div>


        <div class="admin-form-row">

            <div class="admin-form-group">
                <label>Prix</label>
                <input type="text" name="ig_prix" value="<?= $actualite['ig_prix'] ?? '' ?>">
            </div>

            <div class="admin-form-group">
                <label>Date de sortie</label>
                <input type="date" name="ig_date_sortie" value="<?= $actualite['ig_date_sortie'] ?? '' ?>">
            </div>

        </div>


        <!-- DEVELOPPEUR -->

        <div class="admin-form-group">

            <label>Développeur</label>

            <select name="ig_developpeur">

                <option value="">-- Sélectionner --</option>

                <?php foreach ($developpeurs as $dev): ?>

                    <option value="<?= $dev['id'] ?>"
                        <?= ($actualite['ig_developpeur'] ?? '') == $dev['id'] ? 'selected' : '' ?>>

                        <?= htmlspecialchars($dev['nom']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- CATEGORIES -->

        <div class="admin-form-group">

            <label>Catégories</label>

            <div class="admin-checkbox-group">

                <?php foreach ($categories as $cat): ?>

                    <label class="admin-checkbox-label">

                        <input type="checkbox"
                            name="ig_categories[]"
                            value="<?= $cat['id'] ?>"
                            <?= in_array($cat['id'], $selectedCategories) ? 'checked' : '' ?>>

                        <?= htmlspecialchars($cat['nom']) ?>

                    </label>

                <?php endforeach; ?>

            </div>

        </div>


        <!-- PLATEFORMES -->

        <div class="admin-form-group">

            <label>Plateformes</label>

            <div class="admin-checkbox-group">

                <?php foreach ($plateformes as $plat): ?>

                    <label class="admin-checkbox-label">

                        <input type="checkbox"
                            name="ig_plateformes[]"
                            value="<?= $plat['id'] ?>"
                            <?= in_array($plat['id'], $selectedPlateformes) ? 'checked' : '' ?>>

                        <?= htmlspecialchars($plat['nom']) ?>

                    </label>

                <?php endforeach; ?>

            </div>

        </div>

        <div class="admin-form-group">

            <label for="ig_image">Image</label>
            <input type="file" id="ig_image" name="ig_image" accept="image/*">

            <?php if (!empty($actualite['ig_image'])): ?>

            <div class="admin-image-preview">
                <p>Image actuelle :</p>
                <img src="../<?= getImagePath($actualite['ig_image'], 'news') ?>" alt="">
            </div>

            <?php endif; ?>

        </div>

    </div>

    <div class="admin-form-actions">

        <button type="submit" class="admin-btn admin-btn-success">
            <i class="fas fa-save"></i> <?= $id ? 'Mettre à jour' : 'Créer' ?>
        </button>

        <a href="actualites.php" class="admin-btn admin-btn-secondary">
        Annuler
        </a>

    </div>

</form>

<?php include 'includes/admin-footer.php'; ?>