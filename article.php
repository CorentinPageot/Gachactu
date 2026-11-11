<?php
require_once 'includes/functions.php';

// Récupérer l'ID de l'actualité
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$actualite = getActualiteById($id);
$developpeur = getDeveloppeurByIdFront($actualite['ig_developpeur']);
$plateformes = getPlateformesByIdsFront($actualite['ig_plateformes']);
$categories = getCategoriesByIdsFront($actualite['ig_categories']);

// Rediriger si l'actualité n'existe pas
if (!$actualite) {
    header('Location: /actualites');
    exit;
}

// SEO
$pageTitle = $actualite['titre'];
$pageDescription = isset($actualite['description']) ? substr(strip_tags($actualite['description']), 0, 155) : substr(strip_tags($actualite['contenu']), 0, 155);
$ogType = 'article';
$ogImage = isset($actualite['image']) ? 'https://gachactu.com/' . getImagePath($actualite['image'], 'article') : 'https://gachactu.com/images/logo_gachactu.png';
$canonicalUrl = '/article?id=' . $actualite['id'];
$structuredData = 'article'; // Active les données structurées Article

include 'includes/head.php';
?>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <article class="article-page">
            <!-- Retour aux actualités -->
            <a href="actualites" class="article-back">
                <i class="fas fa-arrow-left"></i>
                Retour aux actualités
            </a>

            <div class="article-hero_ig">

                <!-- Image principale -->
                <div class="article-hero">
                    <img src="<?= getImagePath($actualite['image'], 'news') ?>" alt="<?= htmlspecialchars($actualite['titre']) ?>">
                </div>

                <?php if ($actualite['ig_enabled'] == 1): ?>
                <!-- Bloc Instant Gaming (si activé) -->
                <aside class="bloc_ig">
                    <h2 class="legal-title"><?= htmlspecialchars($actualite['ig_nom_jeu']) ?></h2>
                    <div class="img_infos_ig">
                        <img src="<?= getImagePath($actualite['ig_image'], 'news') ?>" alt="<?= htmlspecialchars($actualite['ig_nom_jeu']) ?>">
                        <div class="infos_game_tags_ig">
                            <div>
                                <p>Développeur : <?= htmlspecialchars($developpeur['nom']) ?></p>
                                <p>Sortie : <?= getStatutSortie($actualite['ig_date_sortie']) ?></p>
                                <p>Plateforme(s) :
                                    <?php foreach ($plateformes as $plateforme): ?>
                                        <?= htmlspecialchars($plateforme['nom']) ?><?php if ($plateforme !== end($plateformes)): ?>, <?php endif; ?>
                                    <?php endforeach; ?>
                                </p>
                            </div>
                            <div class="game-tags">
                                <?php foreach ($categories as $categorie): ?>
                                    <span class="game-tag"><?= htmlspecialchars($categorie['nom']) ?><?php if ($categorie !== end($categories)): ?>, <?php endif; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="link_game_ig">
                        <a href="<?= htmlspecialchars($actualite['ig_url']) ?>" class="game-btn" target="_blank">Acheter <?= htmlspecialchars($actualite['ig_nom_jeu']) ?></a>
                        <span><?= htmlspecialchars($actualite['ig_prix']) ?>€</span>
                    </div>
                </aside>
                <?php endif; ?>

            </div>

            <?php if ($actualite['lootbar_enabled'] == 1): ?>
            <!-- Lien LOOTBAR (si activé) -->
            <div class="link_lootbar">
                <a href="<?= htmlspecialchars($actualite['lootbar_url']) ?>" class="game-btn" target="_blank"><?= htmlspecialchars($actualite['lootbar_url_texte']) ?></a>
            </div>
            <?php endif; ?>

            <!-- Contenu de l'article -->
            <div class="article-content">
                <span class="article-date"><?= formatDateFr($actualite['date']) ?></span>
                <h1 class="article-title"><?= htmlspecialchars($actualite['titre']) ?></h1>

                <div class="article-body">
                    <?= $actualite['texte'] ?>
                </div>

                <!-- Zone de partage -->
                <div class="article-share">
                    <span>Partager :</span>
                    <div class="share-buttons">
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode('https://gachactu.com/article/' . $actualite['id']) ?>&text=<?= urlencode($actualite['titre']) ?>" target="_blank" class="share-btn share-twitter" aria-label="Partager sur X">
                            <i class="fab fa-x-twitter"></i>
                        </a>
                        <button class="share-btn share-copy" aria-label="Copier le lien" onclick="copyLink(this)">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </article>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
    <script>
        function copyLink(button) {
            navigator.clipboard.writeText(window.location.href);

            const icon = button.querySelector("i");
            icon.classList.remove("fa-link");
            icon.classList.add("fa-check");

            setTimeout(() => {
                icon.classList.remove("fa-check");
                icon.classList.add("fa-link");
            }, 2000);
        }
    </script>
</body>
</html>
