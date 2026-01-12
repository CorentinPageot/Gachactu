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
                $prochainsSorties = getProchainsSorties(null, true);
                foreach ($prochainsSorties as $jeu):
                    $plateformes = getPlateformesJeu($jeu['id']);
                    $categories  = getCategoriesJeu($jeu['id']);
                    $tagsSortie  = getTagsSortieJeu($jeu['date_sortie']);
                    include 'templates/game-card.php';
                endforeach;
            ?>
        </div>
        </section>
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">Jeux annoncés</h2>
                <p class="section-subtitle">Pas de date annoncé mais à suivre...</p>
            </div>
            <div class="games-grid">
                <?php
                $prochainsSorties = getProchainsSorties(null, false, true);
                foreach ($prochainsSorties as $jeu):
                    $plateformes = getPlateformesJeu($jeu['id']);
                    $categories  = getCategoriesJeu($jeu['id']);
                    $tagsSortie  = getTagsSortieJeu($jeu['date_sortie']);
                    include 'templates/game-card.php';
                endforeach;
                ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
