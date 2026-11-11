<?php 
require_once 'includes/functions.php';
$pageTitle = 'Actualités';

include 'includes/head.php';

$limit = 8;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

$actualites = getActualites($limit, $offset);

$totalActus = countActualites();
$totalPages = ceil($totalActus / $limit);
?>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <section class="section news-section">
            <div class="section-header">
                <h1 class="section-title">Actualités</h1>
                <p class="section-subtitle">Toutes les dernières nouvelles du monde du jeu vidéo</p>
            </div>
            <div class="news-grid">
                <?php
                foreach ($actualites as $actu):
                    if ($actu['masquer_actu'] != 1):
                ?>
                    <a href="/article.php?id=<?= $actu['id'] ?>" class="news-card">
                        <div class="news-image">
                            <img src="<?= getImagePath($actu['image'], 'news') ?>" alt="<?= htmlspecialchars($actu['titre']) ?>">
                        </div>
                        <div class="news-content">
                            <h3 class="news-title"><?= htmlspecialchars($actu['titre']) ?></h3>
                            <div class="news-meta">
                                <span class="news-date"><?= formatDateFr($actu['date']) ?></span>
                                <span class="news-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
            <div class="pagination">

                <!-- Bouton précédent -->
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="arrow">←</a>
                <?php endif; ?>

                <?php
                $range = 2; // nb de pages autour de la page actuelle

                for ($i = 1; $i <= $totalPages; $i++) {

                    // on affiche toujours la première et dernière page
                    if (
                        $i == 1 ||
                        $i == $totalPages ||
                        ($i >= $page - $range && $i <= $page + $range)
                    ) {
                ?>
                        <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                <?php
                    } 
                    // on ajoute "..." une seule fois
                    elseif (
                        $i == 2 && $page - $range > 3 ||
                        $i == $totalPages - 1 && $page + $range < $totalPages - 2
                    ) {
                        echo '<span class="dots">…</span>';
                    }
                }
                ?>

                <!-- Bouton suivant -->
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="arrow">→</a>
                <?php endif; ?>

            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
