<?php
require_once 'includes/functions.php';

// Récupérer l'ID du personnage
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$personnage = getPersonnageById($id);

// Rediriger si le personnage n'existe pas
if (!$personnage) {
    header('Location: /jeux');
    exit;
}

$guide = getGuidePersonnage($id);
$tier = getTierPersonnage($id);
$jeuId = $personnage['jeu_id'];

// Récupérer la configuration des rubriques pour ce jeu
$configRubriques = getConfigRubriquesJeu($jeuId);
$valeursPerso = getValeursRubriquesPersonnalisees($id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($personnage['nom']) ?> - Guide & Build - Gach'Actu</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="images/logo_gachactu.ico" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="main-content">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb">
                <a href="index">Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <a href="jeux">Jeux</a>
                <i class="fas fa-chevron-right"></i>
                <a href="jeu/<?= $personnage['jeu_id'] ?>"><?= htmlspecialchars($personnage['jeu_titre']) ?></a>
                <i class="fas fa-chevron-right"></i>
                <span><?= htmlspecialchars($personnage['nom']) ?></span>
            </nav>

            <!-- Header du personnage -->
            <div class="personnage-header">
                <div class="personnage-header-image">
                    <img src="<?= getImagePath($personnage['image'], 'game') ?>" alt="<?= htmlspecialchars($personnage['nom']) ?>">
                    <?php if ($tier): ?>
                    <span class="personnage-tier-badge" style="background-color: <?= htmlspecialchars($tier['couleur']) ?>">
                        Tier <?= htmlspecialchars($tier['nom']) ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="personnage-header-info">
                    <h1 class="personnage-header-name"><?= htmlspecialchars($personnage['nom']) ?></h1>
                    <p class="personnage-header-game">
                        <a href="/jeu/<?= $personnage['jeu_id'] ?>">
                            <i class="fas fa-gamepad"></i> <?= htmlspecialchars($personnage['jeu_titre']) ?>
                        </a>
                    </p>
                    <div class="personnage-header-tags">
                        <?php if (!empty($personnage['rarete'])): ?>
                        <span class="personnage-tag-big rarete-<?= strtolower($personnage['rarete']) ?>">
                            <?= htmlspecialchars($personnage['rarete']) ?>
                        </span>
                        <?php endif; ?>
                        <?php if (!empty($personnage['element'])): ?>
                        <span class="personnage-tag-big">
                            <i class="fas fa-fire"></i> <?= htmlspecialchars($personnage['element']) ?>
                        </span>
                        <?php endif; ?>
                        <?php if (!empty($personnage['role'])): ?>
                        <span class="personnage-tag-big">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($personnage['role']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($personnage['description'])): ?>
                    <p class="personnage-header-desc"><?= htmlspecialchars($personnage['description']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($guide): ?>
            <!-- Contenu du guide -->
            <div class="personnage-guide">
                <?php foreach ($configRubriques as $rubrique): ?>
                    <?php if (!$rubrique['est_active']) continue; ?>

                    <?php if (!$rubrique['est_personnalisee']): ?>
                        <!-- Rubrique de base -->
                        <?php
                        $champ = $rubrique['champ'];
                        $contenu = $guide[$champ] ?? '';
                        ?>
                        <?php if (!empty($contenu)): ?>
                        <section class="guide-section">
                            <h2><i class="<?= htmlspecialchars($rubrique['icone']) ?>"></i> <?= htmlspecialchars($rubrique['nom']) ?></h2>
                            <div class="guide-section-content">
                                <?= $contenu ?>
                            </div>
                        </section>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Rubrique personnalisée -->
                        <?php
                        $rubriqueId = $rubrique['rubrique_perso_id'];
                        $contenu = $valeursPerso[$rubriqueId]['contenu'] ?? '';
                        ?>
                        <?php if (!empty($contenu)): ?>
                        <section class="guide-section">
                            <h2><i class="<?= htmlspecialchars($rubrique['icone']) ?>"></i> <?= htmlspecialchars($rubrique['nom']) ?></h2>
                            <div class="guide-section-content">
                                <?= $contenu ?>
                            </div>
                        </section>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>

                <p class="guide-updated">
                    <i class="fas fa-clock"></i> Dernière mise à jour : <?= formatDateFr($guide['date_maj']) ?>
                </p>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <p>Le guide de ce personnage n'est pas encore disponible.</p>
                <p class="empty-state-sub">Revenez bientôt pour découvrir le build optimal !</p>
            </div>
            <?php endif; ?>

            <!-- Retour -->
            <div class="personnage-back">
                <a href="/jeu/<?= $personnage['jeu_id'] ?>&section=personnages" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Retour aux personnages
                </a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
