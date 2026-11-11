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
                <h2 class="section-title">Nos jeux</h2>
                <p class="section-subtitle">Retrouvez ici tous les jeux pour lesquels un guide est rédigé.</p>
            </div>
            <div class="games-grid">
            <?php renderGameCards(getJeuxAvecGuide(null)); ?>
        </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
