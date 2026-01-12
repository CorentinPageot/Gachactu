<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$jeu = $id ? getJeuById($id) : null;
$pageTitle = $jeu ? 'Modifier le jeu' : 'Nouveau jeu';
$message = '';
$messageType = '';

// Récupérer toutes les catégories, plateformes et développeurs
$categories = getCategories();
$plateformes = getPlateformes();
$developpeurs = getDeveloppeurs();

// Récupérer les catégories, plateformes et développeur actuels du jeu
$jeuCategories = $id ? array_column(getCategoriesJeu($id), 'id') : [];
$jeuPlateformes = $id ? array_column(getPlateformesJeu($id), 'id') : [];
$jeuDeveloppeur = $id ? getDeveloppeurJeu($id) : null;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $dateSortie = $_POST['date_sortie'] ?? '';
    $estPopulaire = isset($_POST['est_populaire']) ? 1 : 0;
    $masquerPage = isset($_POST['masquer_page']) ? 1 : 0;
    $estTop10 = isset($_POST['est_top10']) ? 1 : 0;
    $top10_position = !empty($_POST['top10_position'])
    ? (int) $_POST['top10_position']
    : null;

    // Sécurité logique
    if ($estTop10 === 0) {
        $top10_position = null;
    }

    // Vérification unicité position Top 10
    if ($estTop10 && $top10_position) {
        $pdo = getDatabase();

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM jeux
            WHERE top10 = 1
            AND top10_position = ?
            AND id != ?
        ");

        $stmt->execute([
            $top10_position,
            $id ?? 0 // si création, $id est null
        ]);

        if ($stmt->fetchColumn() > 0) {
            $message = "Cette position du Top 10 est déjà utilisée.";
            $messageType = 'error';
        }
    }

    if ($estTop10 && empty($id)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM jeux WHERE top10 = 1");
        if ($stmt->fetchColumn() >= 10) {
            $message = "Le Top 10 contient déjà 10 jeux.";
            $messageType = 'error';
        }
    }

    $image = $jeu['image'] ?? '';
    $selectedCategories = $_POST['categories'] ?? [];
    $selectedPlateformes = $_POST['plateformes'] ?? [];
    $developpeurId = !empty($_POST['developpeur_id']) ? (int)$_POST['developpeur_id'] : null;

    // Upload de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['image'], 'jeux');
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
                if (updateJeu($id, $titre, $dateSortie, $image, $estPopulaire, $masquerPage, $estTop10, $top10_position, $selectedCategories, $selectedPlateformes, $description, $developpeurId)) {
                    $message = 'Jeu mis à jour avec succès.';
                    $messageType = 'success';
                    $jeu = getJeuById($id);
                    $jeuCategories = array_column(getCategoriesJeu($id), 'id');
                    $jeuPlateformes = array_column(getPlateformesJeu($id), 'id');
                    $jeuDeveloppeur = getDeveloppeurJeu($id);
                } else {
                    $message = 'Erreur lors de la mise à jour.';
                    $messageType = 'error';
                }
            } else {
                $newId = createJeu($titre, $dateSortie, $image, $estPopulaire, $masquerPage, $estTop10, $top10_position, $selectedCategories, $selectedPlateformes, $description, $developpeurId);
                if ($newId) {
                    header('Location: jeu-edit.php?id=' . $newId . '&created=1');
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
    $message = 'Jeu créé avec succès.';
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
    <a href="jeux.php" class="admin-btn admin-btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($jeu['titre'] ?? '') ?>" required>
        </div>
        <div class="admin-form-group">
            <label for="date_sortie">Date de sortie</label>
            <input type="date" id="date_sortie" name="date_sortie" value="<?= $jeu['date_sortie'] ?? '' ?>">
        </div>
    </div>

    <div class="admin-form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($jeu['description'] ?? '') ?></textarea>
    </div>

    <div class="admin-form-row">
        <div class="admin-form-group">
            <label>Catégories</label>
            <div class="admin-checkbox-group">
                <?php foreach ($categories as $cat): ?>
                <label class="admin-checkbox-label">
                    <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>"
                        <?= in_array($cat['id'], $jeuCategories) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($cat['nom']) ?>
                </label>
                <?php endforeach; ?>
            </div>
            <?php if (empty($categories)): ?>
            <p class="admin-hint">Aucune catégorie disponible. <a href="categories.php">Ajouter des catégories</a></p>
            <?php endif; ?>
        </div>
        <div class="admin-form-group">
            <label>Plateformes</label>
            <div class="admin-checkbox-group">
                <?php foreach ($plateformes as $plat): ?>
                <label class="admin-checkbox-label">
                    <input type="checkbox" name="plateformes[]" value="<?= $plat['id'] ?>"
                        <?= in_array($plat['id'], $jeuPlateformes) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($plat['nom']) ?>
                </label>
                <?php endforeach; ?>
            </div>
            <?php if (empty($plateformes)): ?>
            <p class="admin-hint">Aucune plateforme disponible. <a href="plateformes.php">Ajouter des plateformes</a></p>
            <?php endif; ?>
            <br/>
            <label>Options</label>
            <div class="admin-checkbox-group">
                <label class="admin-checkbox-label">
                    <input type="checkbox" name="est_populaire" value="1"
                        <?= (!empty($jeu['est_populaire'])) ? 'checked' : '' ?>>
                    Jeu populaire
                </label>
                <label class="admin-checkbox-label">
                    <input type="checkbox" name="masquer_page" value="1"
                        <?= (!empty($jeu['masquer_page'])) ? 'checked' : '' ?>>
                    Masquer la page du jeu
                </label>
                <label class="admin-checkbox-label">
                    <input type="checkbox" name="est_top10" value="1"
                        <?= (!empty($jeu['top10'])) ? 'checked' : '' ?>>
                    Top 10
                </label>
            </div>
        </div>
    </div>

    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="developpeur_id">Développeur</label>
            <select id="developpeur_id" name="developpeur_id">
                <option value="">— Aucun développeur —</option>
                <?php foreach ($developpeurs as $dev): ?>
                <option value="<?= $dev['id'] ?>" 
                    <?= (isset($jeuDeveloppeur) && $jeuDeveloppeur && $jeuDeveloppeur['id'] == $dev['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dev['nom']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php if (empty($developpeurs)): ?>
            <p class="admin-hint">Aucun développeur disponible. <a href="developpeurs.php">Ajouter des développeurs</a></p>
            <?php endif; ?>
        </div>
        <div class="admin-form-group">
            <label for="top10_position">Position dans le Top 10</label>
            <select id="top10_position" name="top10_position">
                <option value="">— Hors Top 10 —</option>
                <?php $positionsOccupees = getTop10PositionsOccupees($id ?? null); ?>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <?php
                        $positionActuelle = $jeu['top10_position'] ?? null;

                        // On affiche la position si :
                        // - elle est libre
                        // - ou c'est la position actuelle du jeu (édition)
                        if (
                            !in_array($i, $positionsOccupees)
                            || $positionActuelle == $i
                        ):
                    ?>
                        <option value="<?= $i ?>"
                            <?= ($positionActuelle == $i) ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                    <?php endif; ?>
                <?php endfor; ?>
            </select>
        </div>
    </div>
    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if (!empty($jeu['image'])): ?>
            <div class="admin-image-preview">
                <p>Image actuelle :</p>
                <img src="../<?= getImagePath($jeu['image'], 'game') ?>" alt="">
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-success">
            <i class="fas fa-save"></i> <?= $id ? 'Mettre à jour' : 'Créer' ?>
        </button>
        <a href="jeux.php" class="admin-btn admin-btn-secondary">Annuler</a>
        <?php if ($id): ?>
        <div class="admin-form-actions-right">
            <a href="personnages.php?jeu=<?= $id ?>" class="admin-btn admin-btn-primary">
                <i class="fas fa-users"></i> Gérer les personnages
            </a>
            <a href="codes.php?jeu=<?= $id ?>" class="admin-btn admin-btn-primary">
                <i class="fas fa-gift"></i> Gérer les codes
            </a>
        </div>
        <?php endif; ?>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>
