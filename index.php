<?php 
require_once 'includes/functions.php';

// SEO
$pageTitle = 'Accueil';
$pageDescription = 'Gach\'Actu - Votre source d\'actualités, guides, tier lists et codes promo pour tous vos jeux gacha préférés. Découvrez les meilleurs personnages et stratégies.';
$canonicalUrl = '/';

include 'includes/head.php';
?>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Bloc Jeux Populaires -->
        <section class="section popular-games">
            <div class="section-header">
                <h2 class="section-title">Nos jeux du moment</h2>
                <p class="section-subtitle">Guides, astuces et conseils sur les jeux que nous couvrons actuellement.</p>
            </div>
            <div class="games-grid">
            <?php renderGameCards(getJeuxPopulaires(4)); ?>
            </div>
        </section>

        <!-- Bloc Top 10 -->
        <section class="section upcoming-releases">
            <div class="section-header">
                <h2 class="section-title">Top 10</h2>
                <p class="section-subtitle">Notre top 10 des meilleurs jeux Gacha</p>
            </div>
            <div class="games-grid">
            <?php renderGameCards(getTop10Jeux()); ?>
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
