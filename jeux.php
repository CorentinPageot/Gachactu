<?php 
require_once 'includes/functions.php';
$pageTitle = 'Jeux';
include 'includes/head.php';
?>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">Planning des Sorties</h2>
                <p class="section-subtitle">Retrouvez tous les jeux dont la date de lancement est officiellement confirmée. </p>
            </div>
            <div class="games-grid">
            <?php renderGameCards(getProchainsSorties(null, true)); ?>
        </div>
        </section>
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">À venir</h2>
                <p class="section-subtitle">Retrouvez tous les jeux annoncés dont la date de sortie n'est pas encore fixée.</p>
            </div>
            <div class="games-grid">
                <?php renderGameCards(getProchainsSorties(null, false, true)); ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
