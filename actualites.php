<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités - Gach'Actu</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="images/logo_gachactu.ico" type="image/x-icon">
</head>
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
                $actualites = getActualites();
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
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
