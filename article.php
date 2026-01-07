<?php
require_once 'includes/functions.php';

// Récupérer l'ID de l'actualité
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$actualite = getActualiteById($id);

// Rediriger si l'actualité n'existe pas
if (!$actualite) {
    header('Location: /actualites');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($actualite['titre']) ?> - Gach'Actu</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="images/logo_gachactu.ico" type="image/x-icon">
</head>
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

            <!-- Image principale -->
            <div class="article-hero">
                <img src="<?= getImagePath($actualite['image'], 'news') ?>" alt="<?= htmlspecialchars($actualite['titre']) ?>">
            </div>

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
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode('https://gachactu.fr/article/' . $actualite['id']) ?>&text=<?= urlencode($actualite['titre']) ?>" target="_blank" class="share-btn share-twitter" aria-label="Partager sur X">
                            <i class="fab fa-x-twitter"></i>
                        </a>
                        <button class="share-btn share-copy" aria-label="Copier le lien" onclick="copyLink()">
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
        function copyLink() {
            navigator.clipboard.writeText(window.location.href);
            alert('Lien copié !');
        }
    </script>
</body>
</html>
