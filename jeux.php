<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeux - Gach'Actu</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="images/logo_gachactu.ico" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">Prochaines sorties</h2>
                <p class="section-subtitle">Les lancements à ne pas manquer</p>
            </div>
            <div class="games-grid">
            <?php
                $prochainsSorties = getProchainsSorties();
                foreach ($prochainsSorties as $jeu):
                    $plateformes = getPlateformesJeu($jeu['id']);
                    $categories  = getCategoriesJeu($jeu['id']);
                    $tagsSortie  = getTagsSortieJeu($jeu['date_sortie']);
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
            <?php endforeach; ?>
        </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
