<?php
http_response_code(404);
$pageTitle = 'Page non trouvée';
include 'includes/head.php';
?>
<body>

    <?php include 'includes/header.php'; ?>

<main class="main-content">
    <div class="error-404">
        <section class="section page_404">
            <div class="section-header">
                <h2 class="section-title">Oups !</h2>
                <p class="section-subtitle">La page que vous recherchez semble introuvable.</p>
                <p class="section-subtitle">Code d'erreur : 404</p>
                <a href="index" class="btn-back"><i class="fas fa-home"></i> Retour à l'accueil</a>
            </div>
        </section>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
