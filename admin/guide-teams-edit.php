<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$jeuId = (int)($_GET['jeu'] ?? 0);

$guide = getTeamsGuideByJeu($jeuId);

if (!$guide) {

    $guideId = createTeamsGuide($jeuId);
    $guide = getTeamsGuideByJeu($jeuId);
}

$personnages = getPersonnagesJeu($jeuId);

$jeu = getJeuById($jeuId);

if (!$jeuId) {
    header('Location: guides.php');
    exit;
}

$pageTitle = 'Guide Teams - ' . $jeu['titre'];
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($_POST['teams'] as $teamId => $data) {

        updateGuideTeam(
            $teamId,
            trim($data['nom']),
            trim($data['description'])
        );

        saveTeamPersonnages(
            $teamId,
            $data['personnages'] ?? []
        );
    }

    $guide = getTeamsGuideByJeu($jeuId);

    $message = "Guide teams mis à jour.";
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
    <h2>
        <img src="../<?= getImagePath($jeu['image'], 'game') ?>" alt="" style="width: 40px; height: 40px; border-radius: 8px; vertical-align: middle; margin-right: 10px;">
        Guide Teams - <?= htmlspecialchars($jeu['titre']) ?>
    </h2>
    <a href="guides.php" class="admin-btn admin-btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<form method="POST" class="admin-form">
    <?php foreach ($guide['teams'] as $team): ?>

    <div class="admin-card">

        <h3>Team <?= $team['position'] ?></h3>

        <div class="admin-form-group">
            <label>Nom personnalisé</label>
            <input type="text"
            name="teams[<?= $team['id'] ?>][nom]"
            value="<?= htmlspecialchars($team['nom'] ?? '') ?>">
        </div>

        <div class="admin-form-group">
            <label>Description</label>
            <textarea name="teams[<?= $team['id'] ?>][description]" rows="3"><?= htmlspecialchars($team['description'] ?? '') ?></textarea>
        </div>

        <div class="admin-form-row">

            <?php for ($i=0;$i<6;$i++): 
            $selectedId = $team['personnages'][$i]['personnage_id'] ?? 0;
            ?>

            <select name="teams[<?= $team['id'] ?>][personnages][]">

                <option value="">-- Personnage --</option>

                <?php foreach ($personnages as $perso): ?>

                <option value="<?= $perso['id'] ?>" <?= $perso['id'] == $selectedId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($perso['nom']) ?>
                </option>

                <?php endforeach; ?>

            </select>

            <?php endfor; ?>

        </div>

    </div>

    <?php endforeach; ?>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-success">
            <i class="fas fa-save"></i> Enregistrer
        </button>
        <a href="guides.php" class="admin-btn admin-btn-secondary">Annuler</a>
        <a href="guide-edit.php?jeu=<?= $jeuId ?>&type=reroll" class="admin-btn admin-btn-primary">
            <i class="fas fa-sync"></i> Voir le guide Reroll
        </a>
        <a href="guide-edit.php?jeu=<?= $jeuId ?>&type=debutant" class="admin-btn admin-btn-teams">
            <i class="fas fa-graduation-cap"></i> Voir le guide Débutant
        </a>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>
