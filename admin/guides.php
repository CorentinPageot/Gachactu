<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$pageTitle = 'Gestion des Guides';
$jeux = getJeux();

include 'includes/admin-header.php';
?>

<div class="admin-page-header">
    <h2>Guides par jeu</h2>
</div>

<?php if (empty($jeux)): ?>
<div class="admin-empty">
    <i class="fas fa-book"></i>
    <p>Aucun jeu disponible. Créez d'abord un jeu pour y ajouter des guides.</p>
    <a href="jeu-edit.php" class="admin-btn admin-btn-primary">Ajouter un jeu</a>
</div>
<?php else: ?>
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Jeu</th>
                <th>Guide Débutant</th>
                <th>Guide Reroll</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jeux as $jeu): ?>
            <?php
                $guideDebutant = getGuide($jeu['id'], 'debutant');
                $guideReroll = getGuide($jeu['id'], 'reroll');
            ?>
            <tr>
                <td>
                    <img src="../<?= getImagePath($jeu['image'], 'game') ?>" alt="" class="admin-table-img">
                </td>
                <td><strong><?= htmlspecialchars($jeu['titre']) ?></strong></td>
                <td>
                    <?php if ($guideDebutant && !empty($guideDebutant['contenu'])): ?>
                    <span class="admin-badge admin-badge-success">Rédigé</span>
                    <?php else: ?>
                    <span class="admin-badge admin-badge-secondary">Vide</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($guideReroll && !empty($guideReroll['contenu'])): ?>
                    <span class="admin-badge admin-badge-success">Rédigé</span>
                    <?php else: ?>
                    <span class="admin-badge admin-badge-secondary">Vide</span>
                    <?php endif; ?>
                </td>
                <td class="admin-actions">
                    <a href="guide-edit.php?jeu=<?= $jeu['id'] ?>&type=debutant" class="admin-btn admin-btn-primary" title="Guide Débutant">
                        <i class="fas fa-graduation-cap"></i> Débutant
                    </a>
                    <a href="guide-edit.php?jeu=<?= $jeu['id'] ?>&type=reroll" class="admin-btn admin-btn-success" title="Guide Reroll">
                        <i class="fas fa-sync"></i> Reroll
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'includes/admin-footer.php'; ?>
