<?php
require_once 'includes/functions.php';

// Récupérer l'ID du jeu
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$jeu = getJeuById($id);

// Rediriger si le jeu n'existe pas
if (!$jeu) {
    header('Location: /jeux');
    exit;
}

$categories = getCategoriesJeu($jeu['id']);
$section = isset($_GET['section']) ? $_GET['section'] : 'tierlist';

$pageTitle = $jeu['titre'];
include 'includes/head.php';
?>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="game-layout">
            <!-- Menu latéral -->
            <aside class="game-sidebar">
                <div class="game-sidebar-header">
                    <img src="<?= getImagePath($jeu['image'], 'game') ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>" class="game-sidebar-image">
                    <h2 class="game-sidebar-title"><?= htmlspecialchars($jeu['titre']) ?></h2>
                </div>
                <nav class="game-sidebar-nav">
                    <a href="#" class="game-sidebar-link<?= $section === 'tierlist' ? ' active' : '' ?>" data-section="tierlist">
                        <i class="fas fa-ranking-star"></i> Tier List
                    </a>
                    <a href="#" class="game-sidebar-link<?= $section === 'reroll' ? ' active' : '' ?>" data-section="reroll">
                        <i class="fas fa-dice"></i> Reroll
                    </a>
                    <a href="#" class="game-sidebar-link<?= $section === 'guide' ? ' active' : '' ?>" data-section="guide">
                        <i class="fas fa-book"></i> Guide débutant
                    </a>
                    <a href="#" class="game-sidebar-link<?= $section === 'codes' ? ' active' : '' ?>" data-section="codes">
                        <i class="fas fa-gift"></i> Codes cadeaux
                    </a>
                    <a href="#" class="game-sidebar-link<?= $section === 'personnages' ? ' active' : '' ?>" data-section="personnages">
                        <i class="fas fa-users"></i> Personnages
                    </a>
                    <a href="#" class="game-sidebar-link<?= $section === 'tierlistmaker' ? ' active' : '' ?>" data-section="tierlistmaker">
                        <i class="fas fa-wand-magic-sparkles"></i> Tier List Maker
                    </a>
                </nav>
            </aside>

            <!-- Contenu principal -->
            <div class="game-content">
                <div id="game-section-content">
                    <!-- Contenu chargé dynamiquement -->
                    <div class="loading">Chargement...</div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
    <script>
        const jeuId = <?= $jeu['id'] ?>;
        let currentSection = '<?= $section ?>';

        // Charger une section
        function loadSection(section) {
            const content = document.getElementById('game-section-content');
            content.innerHTML = '<div class="loading">Chargement...</div>';

            fetch(`ajax/jeu-section.php?id=${jeuId}&section=${section}`)
                .then(response => response.text())
                .then(html => {
                    content.innerHTML = html;
                    currentSection = section;

                    // Exécuter les scripts injectés
                    content.querySelectorAll('script').forEach(oldScript => {
                        const newScript = document.createElement('script');
                        newScript.textContent = oldScript.textContent;
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });

                    // Mettre à jour l'URL sans recharger
                    history.pushState({section: section}, '', `jeu.php?id=${jeuId}&section=${section}`);

                    // Mettre à jour le menu actif
                    document.querySelectorAll('.game-sidebar-link').forEach(link => {
                        link.classList.remove('active');
                        if (link.dataset.section === section) {
                            link.classList.add('active');
                        }
                    });
                })
                .catch(error => {
                    content.innerHTML = '<div class="error">Erreur lors du chargement.</div>';
                });
        }

        // Gérer les clics sur le menu
        document.querySelectorAll('.game-sidebar-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                loadSection(this.dataset.section);
            });
        });

        // Gérer le bouton retour du navigateur
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.section) {
                loadSection(e.state.section);
            }
        });

        // Charger la section initiale
        loadSection(currentSection);
    </script>
</body>
</html>
