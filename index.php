<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Gach'Actu</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="images/logo_gachactu.ico" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Bloc Jeux Populaires -->
        <section class="section popular-games">
            <div class="section-header">
                <h2 class="section-title">Jeux populaires</h2>
                <p class="section-subtitle">Les titres les plus joués et les plus recherchés en ce moment</p>
            </div>
            <div class="games-grid">
            <?php
                $jeuxPopulaires = getJeuxPopulaires(4);
                foreach ($jeuxPopulaires as $jeu):
                    $plateformes = getPlateformesJeu($jeu['id']);
                    $categories  = getCategoriesJeu($jeu['id']);
                    $tagsSortie  = getTagsSortieJeu($jeu['date_sortie']);
                ?>
                <?php if ($jeu['masquer_page'] != 1): ?>
                    <a href="/jeu.php?id=<?= $jeu['id'] ?>" class="link-game-card">
                <?php endif; ?>
                <div class="game-card">

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

                        <?php if ($jeu['masquer_page'] != 1): ?>
                            <span class="game-link">Voir le guide</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($jeu['masquer_page'] != 1): ?>
                    </a>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Bloc Top 10 -->
        <section class="section upcoming-releases">
            <div class="section-header">
                <h2 class="section-title">Top 10</h2>
                <p class="section-subtitle">Notre top 10 des meilleurs jeux Gacha</p>
            </div>
            <div class="games-grid">
            <?php
                $jeux = getTop10Jeux();
                foreach ($jeux as $jeu):
                    $plateformes = getPlateformesJeu($jeu['id']);
                    $categories  = getCategoriesJeu($jeu['id']);
                    $tagsSortie  = getTagsSortieJeu($jeu['date_sortie']);
                ?>
                <?php if ($jeu['masquer_page'] != 1): ?>
                    <a href="/jeu.php?id=<?= $jeu['id'] ?>" class="link-game-card">
                <?php endif; ?>
                <div class="game-card">

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
                
                        <?php if ($jeu['masquer_page'] != 1): ?>
                            <span class="game-link">Voir le guide</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($jeu['masquer_page'] != 1): ?>
                    </a>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Bloc Partenaires -->
        <section class="section partners">
            <div class="section-header">
                <h2 class="section-title">Partenaires</h2>
            </div>
            <div class="partners-grid">
                <?php
                $partenaires = getPartenaires();
                foreach ($partenaires as $partenaire):
                ?>
                <a href="<?= htmlspecialchars($partenaire['url']) ?>" target="_blank" class="partner-card">
                    <img src="<?= getImagePath($partenaire['image'], 'partner') ?>" alt="<?= htmlspecialchars($partenaire['nom']) ?>">
                    <span><?= htmlspecialchars($partenaire['nom']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
