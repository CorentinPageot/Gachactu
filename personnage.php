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

$pageTitle = $personnage['nom'] . ' - Guide & Build';
include 'includes/head.php';
?>
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
                <a href="jeu.php?id=<?= $personnage['jeu_id'] ?>"><?= htmlspecialchars($personnage['jeu_titre']) ?></a>
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
                        <a href="/jeu.php?id=<?= $personnage['jeu_id'] ?>">
                            <i class="fas fa-gamepad"></i> <?= htmlspecialchars($personnage['jeu_titre']) ?>
                        </a>
                    </p>
                    <div class="personnage-header-tags">
                        <?php if (!empty($personnage['rarete'])): 
                            $rarete_class = strtolower($personnage['rarete']);
                            $rarete_class = str_replace('*', 'stars', $rarete_class);
                        ?>
                            <span class="rarete-<?= $rarete_class ?> personnage-tag-big">
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

                    <?php if (!$rubrique['est_personnalisee']):
                        $code = $rubrique['code_rubrique'];
                        $buildTypesMap = ['armes' => 'armes', 'equipements' => 'artefacts', 'equipe' => 'equipe'];
                    ?>
                        <?php if (isset($buildTypesMap[$code])): ?>
                            <?php
                            $buildType = $buildTypesMap[$code];
                            $builds = getPersonnageBuilds($id, $buildType);
                            ?>
                            <?php if (!empty($builds)): ?>
                            <section class="guide-section">
                                <h2><i class="<?= htmlspecialchars($rubrique['icone']) ?>"></i> <?= htmlspecialchars($rubrique['nom']) ?></h2>
                                <div class="teams-grid">
                                    <?php foreach ($builds as $i => $build): ?>
                                    <div class="team-card">
                                        <h3><?= htmlspecialchars($build['nom'] ?: ($buildType === 'equipe' ? 'Team ' : 'Build ') . ($i + 1)) ?></h3>

                                        <?php if ($buildType === 'equipe'): ?>
                                        <div class="team-personnages">
                                            <?php foreach ($build['personnages'] as $perso): ?>
                                            <div class="team-personnage">
                                                <a href="/personnage.php?id=<?= $perso['id'] ?>">
                                                    <img src="<?= getImagePath($perso['image'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>">
                                                    <h4 class="personnage-nom"><?= htmlspecialchars($perso['nom']) ?></h4>
                                                </a>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php else: ?>
                                        <div class="team-personnages">
                                            <?php foreach ($build['elements'] as $el): ?>
                                            <div class="team-personnage">
                                                <?php if (!empty($el['image'])): ?>
                                                <img src="<?= getImagePath($el['image'], 'game') ?>" alt="<?= htmlspecialchars($el['nom']) ?>">
                                                <?php endif; ?>
                                                <h4 class="personnage-nom"><?= htmlspecialchars($el['nom']) ?></h4>
                                            </div>
                                            <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($build['description']): ?>
                                        <p class="team-description"><?= nl2br(htmlspecialchars($build['description'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Rubrique de base classique (presentation, skills, doublons) -->
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
                <a href="/jeu.php?id=<?= $personnage['jeu_id'] ?>&section=personnages" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Retour aux personnages
                </a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
