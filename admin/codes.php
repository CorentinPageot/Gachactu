<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$pageTitle = 'Gestion des Codes Cadeaux';
$message = '';
$messageType = '';

// Filtre par jeu
$jeuId = isset($_GET['jeu']) ? (int)$_GET['jeu'] : 0;
$jeux = getJeux();
$jeuActuel = $jeuId ? getJeuById($jeuId) : null;

// Suppression d'un code
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deleteCode((int)$_GET['delete'])) {
        $message = 'Code supprimé avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'error';
    }
}

// Récupérer les codes
$codes = getAllCodes($jeuId ?: null);

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
        Codes Cadeaux
        <?php if ($jeuActuel): ?>
        - <?= htmlspecialchars($jeuActuel['titre']) ?>
        <?php endif; ?>
        (<?= count($codes) ?>)
    </h2>
    <div class="admin-header-actions">
        <?php if ($jeuId): ?>
        <a href="codes.php" class="admin-btn admin-btn-secondary">
            <i class="fas fa-list"></i> Tous les codes
        </a>
        <?php endif; ?>
        <a href="code-edit.php<?= $jeuId ? '?jeu=' . $jeuId : '' ?>" class="admin-btn admin-btn-primary">
            <i class="fas fa-plus"></i> Nouveau code
        </a>
    </div>
</div>

<div class="admin-filter-bar">
    <select id="filterJeu" onchange="window.location.href = this.value ? 'codes.php?jeu=' + this.value : 'codes.php'">
        <option value="">Tous les jeux</option>
        <?php foreach ($jeux as $jeu): ?>
        <option value="<?= $jeu['id'] ?>" <?= $jeuId == $jeu['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($jeu['titre']) ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>

<?php if (empty($codes)): ?>
<div class="admin-empty">
    <i class="fas fa-gift"></i>
    <p>Aucun code cadeau pour le moment.</p>
    <a href="code-edit.php<?= $jeuId ? '?jeu=' . $jeuId : '' ?>" class="admin-btn admin-btn-primary">Ajouter un code</a>
</div>
<?php else: ?>
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <?php if (!$jeuId): ?><th>Jeu</th><?php endif; ?>
                <th>Code</th>
                <th>Récompense</th>
                <th>Expiration</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($codes as $code): ?>
            <?php
                $isExpired = !empty($code['date_expiration']) && $code['date_expiration'] < date('Y-m-d');
            ?>
            <tr>
                <?php if (!$jeuId): ?>
                <td><?= htmlspecialchars($code['jeu_titre'] ?? 'N/A') ?></td>
                <?php endif; ?>
                <td>
                    <code class="admin-code"><?= htmlspecialchars($code['code']) ?></code>
                </td>
                <td><?= htmlspecialchars($code['recompense']) ?></td>
                <td>
                    <?php if (empty($code['date_expiration'])): ?>
                        <span class="admin-badge admin-badge-info">Permanent</span>
                    <?php elseif ($isExpired): ?>
                        <span class="admin-badge admin-badge-danger"><?= formatDateFr($code['date_expiration']) ?></span>
                    <?php else: ?>
                        <?= formatDateFr($code['date_expiration']) ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($code['est_actif'] && !$isExpired): ?>
                    <span class="admin-badge admin-badge-success">Actif</span>
                    <?php else: ?>
                    <span class="admin-badge admin-badge-danger">Inactif</span>
                    <?php endif; ?>
                </td>
                <td class="admin-actions">
                    <a href="code-edit.php?id=<?= $code['id'] ?>" class="admin-btn admin-btn-primary" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="codes.php?<?= $jeuId ? 'jeu=' . $jeuId . '&' : '' ?>delete=<?= $code['id'] ?>" class="admin-btn admin-btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce code ?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'includes/admin-footer.php'; ?>
