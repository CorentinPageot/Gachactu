<?php
/**
 * Template pour afficher une carte de jeu
 * 
 * Variables requises:
 * @var array $jeu - Les données du jeu
 * @var array $plateformes - Les plateformes du jeu
 * @var array $categories - Les catégories du jeu
 * @var array $tagsSortie - Les tags de sortie du jeu
 */
?>
<?php if ($jeu['masquer_page'] != 1): ?>
    <a href="/jeu.php?id=<?= $jeu['id'] ?>" class="link-game-card">
<?php endif; ?>
<div class="game-card">
    <!-- Badges sortie -->
    <?php if (!empty($tagsSortie)): ?>
        <?php foreach ($tagsSortie as $tag): ?>
            <span class="game-badge"><?= htmlspecialchars($tag) ?></span>
        <?php endforeach; ?>
    <?php endif; ?>

    <img src="<?= getImagePath($jeu['image'], 'game') ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>">

    <div class="game-card-content">
        <h3><?= htmlspecialchars($jeu['titre']) ?></h3>
        <?php if (!empty($jeu['description'])): ?>
            <p class="game-description"><?= htmlspecialchars($jeu['description']) ?></p>
        <?php endif; ?>

        <div class="game-tags">
            <?php foreach ($plateformes as $plat): ?>
                <span class="game-tag"><?= htmlspecialchars($plat['nom']) ?></span>
            <?php endforeach; ?>

            <?php foreach ($categories as $cat): ?>
                <span class="game-tag"><?= htmlspecialchars($cat['nom']) ?></span>
            <?php endforeach; ?>
        </div>
        
        <div class="game-card-footer">
            <span class="game-release"><?= getStatutSortie($jeu['date_sortie']) ?></span>
            <?php if ($jeu['masquer_page'] != 1): ?>
                <span class="game-btn">Voir le guide</span>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if ($jeu['masquer_page'] != 1): ?>
    </a>
<?php endif; ?>
