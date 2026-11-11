<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';
requireAuth();

$pageTitle = 'Liens utiles';

if (isset($_GET['delete'])) {
    deleteLienUtile((int)$_GET['delete']);
    header('Location: liens-utiles.php');
    exit;
}

$liens = getLiensUtilesAdmin();

include 'includes/admin-header.php';
?>

<div class="admin-page-header">
    <h2><?= htmlspecialchars($pageTitle) ?></h2>
    <a href="lien-utile-edit.php" class="admin-btn admin-btn-success">
        <i class="fas fa-plus"></i> Ajouter un lien
    </a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Ordre</th>
            <th>Logo</th>
            <th>Titre</th>
            <th>Code</th>
            <th>Jeux concernés</th>
            <th>Actif</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($liens as $lien): ?>
        <tr>
            <td><?= $lien['ordre'] ?></td>
            <td><?php if ($lien['image']): ?><img src="../<?= getImagePath($lien['image'], 'partner') ?>" style="height:32px;"><?php endif; ?></td>
            <td><?= htmlspecialchars($lien['titre']) ?></td>
            <td><?= $lien['code_promo'] ? htmlspecialchars($lien['code_promo']) : '—' ?></td>
            <td>
                <?php if ($lien['est_generique']): ?>
                    <span class="admin-badge admin-badge-info">Tous les jeux</span>
                <?php elseif (empty($lien['jeux'])): ?>
                    <span class="admin-badge admin-badge-warning">Aucun jeu sélectionné</span>
                <?php else: ?>
                    <?php foreach ($lien['jeux'] as $j): ?>
                        <span class="admin-badge"><?= htmlspecialchars($j['titre']) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </td>
            <td><?= $lien['est_actif'] ? '<i class="fas fa-check" style="color:green;"></i>' : '<i class="fas fa-times" style="color:red;"></i>' ?></td>
            <td>
                <a href="lien-utile-edit.php?id=<?= $lien['id'] ?>" class="admin-btn admin-btn-sm admin-btn-primary"><i class="fas fa-edit"></i></a>
                <a href="liens-utiles.php?delete=<?= $lien['id'] ?>"
                   onclick="return confirm('Supprimer ce lien ?')" class="admin-btn admin-btn-sm admin-btn-danger"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/admin-footer.php'; ?>