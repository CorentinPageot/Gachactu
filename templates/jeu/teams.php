<?php
$teams = getTeams($jeuId);
?>

<div class="section-header">
    <h1 class="section-title">Guide Teams</h1>
    <p class="section-subtitle">Les meilleures teams de <?= htmlspecialchars($jeu['titre']) ?></p>
</div>

<?php if (!$teams): ?>
    <div class="empty-state">
        <i class="fas fa-sitemap"></i>
        <p>Le guide teams n'est pas encore disponible pour ce jeu.</p>
    </div>
<?php else: ?>
    <div class="teams-grid">

        <?php foreach ($teams['teams'] as $team): ?>

        <div class="team-card">

            <h3><?= htmlspecialchars($team['nom'] ?: 'Top ' . $team['position']) ?></h3>

            <div class="team-personnages">
                <?php foreach ($team['personnages'] as $perso): ?>

                <div class="team-personnage">
                    <a href="/personnage.php?id=<?= $perso['id'] ?>">
                        <img src="<?= getImagePath($perso['image'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>">
                        <h4 class="personnage-nom"><?= htmlspecialchars($perso['nom']) ?></h4>
                    </a>
                </div>

                <?php endforeach; ?>
            </div>

            <?php if ($team['description']): ?>
            <p class="team-description">
                <?= nl2br(htmlspecialchars($team['description'])) ?>
            </p>
            <?php endif; ?>

        </div>

        <?php endforeach; ?>

    </div>
    <?php if (!empty($teams['date_maj'])): ?>
    <div class="guide-meta">
        <small>Dernière mise à jour : <?= formatDateFr($teams['date_maj']) ?></small>
    </div>
    <?php endif; ?>
<?php endif; ?>
