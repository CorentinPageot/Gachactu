<?php
$personnages = getPersonnagesJeu($jeuId);
?>

<div class="section-header">
    <h1 class="section-title">Personnages</h1>
    <p class="section-subtitle">Tous les personnages de <?= htmlspecialchars($jeu['titre']) ?></p>
</div>

<?php if (empty($personnages)): ?>
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <p>La liste des personnages n'est pas encore disponible pour ce jeu.</p>
    </div>
<?php else: ?>
    <div class="personnages-search">
        <div class="search-filtres">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="searchPersonnage" class="search-personnage-input" placeholder="Rechercher un personnage...">
            </div>

            <div class="div-filtres-rarete">
                <span><i class="fas fa-filter"></i> Filtrer par rareté</span>
                <div class="btns-rarete">
                    <button type="button" class="rarete-btn rarete-ssr filtre-rarete" data-rarete="ssr">SSR</button>
                    <button type="button" class="rarete-btn rarete-sr filtre-rarete" data-rarete="sr">SR</button>
                </div>
            </div>
        </div>

        <p class="search-results-count">
            <span id="personnagesCount"><?= count($personnages) ?></span> personnage(s)
        </p>
    </div>

    <div class="personnages-grid">
        <?php foreach ($personnages as $perso): ?>
        <a href="/personnage.php?id=<?= $perso['id'] ?>" class="personnage-card" data-rarete="<?= strtolower($perso['rarete'] ?? '') ?>">
            <div class="personnage-image">
                <img src="<?= getImagePath($perso['image'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>">
                <?php if (!empty($perso['rarete'])): ?>
                <span class="personnage-rarete rarete-<?= strtolower($perso['rarete']) ?>"><?= htmlspecialchars($perso['rarete']) ?></span>
                <?php endif; ?>
            </div>
            <div class="personnage-info">
                <h3 class="personnage-nom"><?= htmlspecialchars($perso['nom']) ?></h3>
                <?php if (!empty($perso['role'])): ?>
                <div class="personnage-tags">
                    <?php if (!empty($perso['role'])): ?>
                    <span class="personnage-tag"><?= htmlspecialchars($perso['role']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($perso['description'])): ?>
                <p class="personnage-description"><?= htmlspecialchars($perso['description']) ?></p>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <script>
    (function() {
        let activeRarete = null;

        const searchInput = document.getElementById('searchPersonnage');
        const cards = document.querySelectorAll('.personnage-card');
        const countEl = document.getElementById('personnagesCount');
        const rareteButtons = document.querySelectorAll('.filtre-rarete');

        function filterPersonnages() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            cards.forEach(card => {
                const nom = card.querySelector('.personnage-nom').textContent.toLowerCase();
                const tags = card.querySelectorAll('.personnage-tag');
                const rarete = card.dataset.rarete;

                let tagText = '';
                tags.forEach(tag => tagText += ' ' + tag.textContent.toLowerCase());

                const matchSearch =
                    nom.includes(searchTerm) || tagText.includes(searchTerm);

                const matchRarete =
                    !activeRarete || rarete === activeRarete;

                if (matchSearch && matchRarete) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            countEl.textContent = visibleCount;
        }

        // Recherche texte
        searchInput.addEventListener('input', filterPersonnages);

        // Boutons rareté
        rareteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const rarete = button.dataset.rarete;

                // Toggle
                if (activeRarete === rarete) {
                    activeRarete = null;
                    button.classList.remove('active');
                } else {
                    activeRarete = rarete;
                    rareteButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                }

                filterPersonnages();
            });
        });
    })();
    </script>

<?php endif; ?>
