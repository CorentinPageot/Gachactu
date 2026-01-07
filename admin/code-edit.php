<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$code = $id ? getCodeById($id) : null;
$pageTitle = $code ? 'Modifier le code' : 'Nouveau code';
$message = '';
$messageType = '';

// Jeu pré-sélectionné
$jeuIdPreselect = isset($_GET['jeu']) ? (int)$_GET['jeu'] : ($code['jeu_id'] ?? 0);

// Récupérer tous les jeux
$jeux = getJeux();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jeuId = (int)($_POST['jeu_id'] ?? 0);
    $codeValue = trim($_POST['code'] ?? '');
    $recompense = trim($_POST['recompense'] ?? '');
    $dateExpiration = $_POST['date_expiration'] ?? '';
    $estActif = isset($_POST['est_actif']) ? 1 : 0;

    if (empty($codeValue)) {
        $message = 'Le code est obligatoire.';
        $messageType = 'error';
    } elseif (empty($jeuId)) {
        $message = 'Veuillez sélectionner un jeu.';
        $messageType = 'error';
    } else {
        if ($id) {
            if (updateCode($id, $jeuId, $codeValue, $recompense, $dateExpiration, $estActif)) {
                $message = 'Code mis à jour avec succès.';
                $messageType = 'success';
                $code = getCodeById($id);
            } else {
                $message = 'Erreur lors de la mise à jour.';
                $messageType = 'error';
            }
        } else {
            if (createCode($jeuId, $codeValue, $recompense, $dateExpiration, $estActif)) {
                header('Location: codes.php?jeu=' . $jeuId);
                exit;
            } else {
                $message = 'Erreur lors de la création.';
                $messageType = 'error';
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
    <a href="codes.php<?= $jeuIdPreselect ? '?jeu=' . $jeuIdPreselect : '' ?>" class="admin-btn admin-btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<form method="POST" class="admin-form">
    <div class="admin-form-row">
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
        <div class="admin-form-group">
            <label for="code">Code *</label>
            <input type="text" id="code" name="code" value="<?= htmlspecialchars($code['code'] ?? '') ?>" required placeholder="Ex: WELCOME2024">
        </div>
    </div>

    <div class="admin-form-row">
        <div class="admin-form-group">
            <label for="recompense">Récompense</label>
            <input type="text" id="recompense" name="recompense" value="<?= htmlspecialchars($code['recompense'] ?? '') ?>" placeholder="Ex: 100 Primogems + 5 Hero's Wit">
        </div>
        <div class="admin-form-group">
            <label for="date_expiration">Date d'expiration</label>
            <input type="date" id="date_expiration" name="date_expiration" value="<?= $code['date_expiration'] ?? '' ?>">
            <p class="admin-hint">Laissez vide pour un code permanent.</p>
        </div>
    </div>

    <div class="admin-form-group">
        <label>Statut</label>
        <div class="admin-checkbox-group">
            <label class="admin-checkbox-label">
                <input type="checkbox" name="est_actif" value="1"
                    <?= (!isset($code) || $code['est_actif']) ? 'checked' : '' ?>>
                Code actif
            </label>
        </div>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-success">
            <i class="fas fa-save"></i> <?= $id ? 'Mettre à jour' : 'Créer' ?>
        </button>
        <a href="codes.php<?= $jeuIdPreselect ? '?jeu=' . $jeuIdPreselect : '' ?>" class="admin-btn admin-btn-secondary">Annuler</a>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>
