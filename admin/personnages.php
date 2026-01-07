<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$pageTitle = 'Gestion des Personnages';
$message = '';
$messageType = '';

// Filtre par jeu
$jeuId = isset($_GET['jeu']) ? (int)$_GET['jeu'] : 0;
$jeux = getJeux();
$jeuActuel = $jeuId ? getJeuById($jeuId) : null;

// Suppression d'un personnage
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deletePersonnage((int)$_GET['delete'])) {
        $message = 'Personnage supprimé avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'error';
    }
}

// Récupérer les personnages (tous ou filtrés par jeu)
if ($jeuId) {
    $personnages = getPersonnagesJeu($jeuId);
} else {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT p.*, j.titre as jeu_titre FROM personnages p LEFT JOIN jeux j ON p.jeu_id = j.id ORDER BY j.titre, p.nom");
    $personnages = $stmt->fetchAll();
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
        Personnages
        <?php if ($jeuActuel): ?>
        - <?= htmlspecialchars($jeuActuel['titre']) ?>
        <?php endif; ?>
        (<?= count($personnages) ?>)
    </h2>
    <div class="admin-header-actions">
        <?php if ($jeuId): ?>
        <a href="personnages.php" class="admin-btn admin-btn-secondary">
            <i class="fas fa-list"></i> Tous les personnages
        </a>
        <?php endif; ?>
        <a href="personnage-edit.php<?= $jeuId ? '?jeu=' . $jeuId : '' ?>" class="admin-btn admin-btn-primary">
            <i class="fas fa-plus"></i> Nouveau personnage
        </a>
    </div>
</div>

<div class="admin-filter-bar">
    <select id="filterJeu" onchange="window.location.href = this.value ? 'personnages.php?jeu=' + this.value : 'personnages.php'">
        <option value="">Tous les jeux</option>
        <?php foreach ($jeux as $jeu): ?>
        <option value="<?= $jeu['id'] ?>" <?= $jeuId == $jeu['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($jeu['titre']) ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>

<?php if (empty($personnages)): ?>
<div class="admin-empty">
    <i class="fas fa-users"></i>
    <p>Aucun personnage pour le moment.</p>
    <a href="personnage-edit.php<?= $jeuId ? '?jeu=' . $jeuId : '' ?>" class="admin-btn admin-btn-primary">Ajouter un personnage</a>
</div>
<?php else: ?>
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image principale</th>
                <th>Nom</th>
                <?php if (!$jeuId): ?><th>Jeu</th><?php endif; ?>
                <th>Rareté</th>
                <th>Rôle</th>
                <th>Guide</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($personnages as $perso): ?>
            <?php $guide = getGuidePersonnage($perso['id']); ?>
            <tr>
                <td>
                    <img src="../<?= getImagePath($perso['image'], 'game') ?>" alt="" class="admin-table-img">
                </td>
                <td><strong><?= htmlspecialchars($perso['nom']) ?></strong></td>
                <?php if (!$jeuId): ?>
                <td><?= htmlspecialchars($perso['jeu_titre'] ?? 'N/A') ?></td>
                <?php endif; ?>
                <td>
                    <?php if (!empty($perso['rarete'])): ?>
                    <span class="admin-badge rarete-<?= strtolower($perso['rarete']) ?>"><?= htmlspecialchars($perso['rarete']) ?></span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($perso['role'] ?? '') ?></td>
                <td>
                    <?php if ($guide): ?>
                    <span class="admin-badge admin-badge-success">Oui</span>
                    <?php else: ?>
                    <span class="admin-badge admin-badge-secondary">Non</span>
                    <?php endif; ?>
                </td>
                <td class="admin-actions">
                    <a href="personnage-edit.php?id=<?= $perso['id'] ?>" class="admin-btn admin-btn-primary" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="guide-personnage-edit.php?id=<?= $perso['id'] ?>" class="admin-btn admin-btn-success" title="Guide">
                        <i class="fas fa-book"></i>
                    </a>
                    <a href="personnages.php?<?= $jeuId ? 'jeu=' . $jeuId . '&' : '' ?>delete=<?= $perso['id'] ?>" class="admin-btn admin-btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce personnage ?');">
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
