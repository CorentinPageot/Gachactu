<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$personnage = $id ? getPersonnageById($id) : null;

if (!$personnage) {
    header('Location: personnages.php');
    exit;
}

$guide = getGuidePersonnage($id);
$jeuId = $personnage['jeu_id'];
$pageTitle = 'Guide de ' . $personnage['nom'];
$message = '';
$messageType = '';

// Récupérer la configuration des rubriques pour ce jeu
$configRubriques = getConfigRubriquesJeu($jeuId);
$rubriquesPerso = getRubriquesPersonnaliseesJeu($jeuId);
$valeursPerso = getValeursRubriquesPersonnalisees($id);

// Ajouter les rubriques perso qui ne sont pas encore dans la config
$rubriquePersoIds = array_column(array_filter($configRubriques, fn($r) => $r['est_personnalisee']), 'rubrique_perso_id');
foreach ($rubriquesPerso as $rp) {
    if (!in_array($rp['id'], $rubriquePersoIds)) {
        $configRubriques[] = [
            'id' => null,
            'code_rubrique' => null,
            'rubrique_perso_id' => $rp['id'],
            'nom' => $rp['nom'],
            'icone' => $rp['icone'],
            'champ' => null,
            'est_active' => 1,
            'ordre' => count($configRubriques) + 1,
            'est_personnalisee' => true
        ];
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rubriques de base
    $presentation = $_POST['presentation'] ?? '';
    $skills = $_POST['skills'] ?? '';
    $armes = $_POST['armes'] ?? '';
    $artefacts = $_POST['artefacts'] ?? '';
    $equipe = $_POST['equipe'] ?? '';
    $bonds = $_POST['bonds'] ?? '';

    // Sauvegarder le guide de base
    if (saveGuidePersonnage($id, $presentation, $skills, $armes, $artefacts, $equipe, $bonds)) {
        // Sauvegarder les rubriques personnalisées
        foreach ($rubriquesPerso as $rp) {
            $fieldName = 'rubrique_perso_' . $rp['id'];
            $contenu = $_POST[$fieldName] ?? '';
            saveValeurRubriquePersonnalisee($rp['id'], $id, $contenu);
        }

        $message = 'Guide enregistré avec succès.';
        $messageType = 'success';
        $guide = getGuidePersonnage($id);
        $valeursPerso = getValeursRubriquesPersonnalisees($id);
    } else {
        $message = 'Erreur lors de l\'enregistrement.';
        $messageType = 'error';
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
    <h2>
        <img src="../<?= getImagePath($personnage['image'], 'game') ?>" alt="" style="width: 40px; height: 40px; border-radius: 8px; vertical-align: middle; margin-right: 10px;">
        <?= $pageTitle ?>
    </h2>
    <div class="admin-header-actions">
        <a href="personnage-edit.php?id=<?= $id ?>" class="admin-btn admin-btn-secondary">
            <i class="fas fa-user"></i> Fiche personnage
        </a>
        <a href="rubriques-personnages.php?jeu=<?= $jeuId ?>" class="admin-btn admin-btn-secondary">
            <i class="fas fa-cog"></i> Gérer les rubriques
        </a>
        <a href="personnages.php?jeu=<?= $personnage['jeu_id'] ?>" class="admin-btn admin-btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<form method="POST" class="admin-form">
    <?php foreach ($configRubriques as $rubrique): ?>
        <?php if (!$rubrique['est_active']) continue; ?>

        <?php if (!$rubrique['est_personnalisee']): ?>
            <!-- Rubrique de base -->
            <?php
            $code = $rubrique['code_rubrique'];
            $fieldName = match($code) {
                'presentation' => 'presentation',
                'skills' => 'skills',
                'armes' => 'armes',
                'equipe' => 'equipe',
                'bonds' => 'bonds',
                'set_stamp' => 'artefacts',
                default => $code
            };
            $champ = $rubrique['champ'];
            $value = $guide[$champ] ?? '';
            ?>
            <div class="admin-form-group full-width">
                <label for="<?= $fieldName ?>">
                    <i class="<?= htmlspecialchars($rubrique['icone']) ?>"></i>
                    <?= htmlspecialchars($rubrique['nom']) ?>
                </label>
                <textarea id="<?= $fieldName ?>" name="<?= $fieldName ?>" class="tinymce-editor"><?= htmlspecialchars($value) ?></textarea>
            </div>
        <?php else: ?>
            <!-- Rubrique personnalisée -->
            <?php
            $rubriqueId = $rubrique['rubrique_perso_id'];
            $fieldName = 'rubrique_perso_' . $rubriqueId;
            $value = $valeursPerso[$rubriqueId]['contenu'] ?? '';
            ?>
            <div class="admin-form-group full-width">
                <label for="<?= $fieldName ?>">
                    <i class="<?= htmlspecialchars($rubrique['icone']) ?>"></i>
                    <?= htmlspecialchars($rubrique['nom']) ?>
                    <span class="label-badge">Personnalisée</span>
                </label>
                <textarea id="<?= $fieldName ?>" name="<?= $fieldName ?>" class="tinymce-editor"><?= htmlspecialchars($value) ?></textarea>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-success">
            <i class="fas fa-save"></i> Enregistrer le guide
        </button>
        <a href="personnages.php?jeu=<?= $personnage['jeu_id'] ?>" class="admin-btn admin-btn-secondary">Annuler</a>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>
