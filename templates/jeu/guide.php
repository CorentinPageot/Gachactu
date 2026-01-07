<?php
$guide = getGuide($jeuId, 'debutant');
?>

<div class="section-header">
    <h1 class="section-title">Guide débutant</h1>
    <p class="section-subtitle">Tout ce qu'il faut savoir pour bien débuter sur <?= htmlspecialchars($jeu['titre']) ?></p>
</div>

<?php if (!$guide): ?>
    <div class="empty-state">
        <i class="fas fa-book"></i>
        <p>Le guide débutant n'est pas encore disponible pour ce jeu.</p>
    </div>
<?php else: ?>
    <div class="guide-content">
        <?= $guide['contenu'] ?>
    </div>
    <?php if (!empty($guide['date_maj'])): ?>
    <div class="guide-meta">
        <small>Dernière mise à jour : <?= formatDateFr($guide['date_maj']) ?></small>
    </div>
    <?php endif; ?>
<?php endif; ?>
