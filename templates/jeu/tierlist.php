<?php
$tierList = getTierListJeu($jeuId);
?>

<div class="section-header">
    <h1 class="section-title">Tier List</h1>
    <p class="section-subtitle">Classement des personnages de <?= htmlspecialchars($jeu['titre']) ?></p>
</div>

<?php if (empty($tierList)): ?>
    <div class="empty-state">
        <i class="fas fa-ranking-star"></i>
        <p>La tier list n'est pas encore disponible pour ce jeu.</p>
    </div>
<?php else: ?>
    <div class="tierlist-filtres">
        <div class="div-filtres-rarete">
            <span><i class="fas fa-filter"></i> Filtrer par rareté</span>
            <div class="btns-rarete">
                <button type="button" class="rarete-btn rarete-ssr filtre-rarete-tierlist" data-rarete="ssr">SSR</button>
                <button type="button" class="rarete-btn rarete-sr filtre-rarete-tierlist" data-rarete="sr">SR</button>
            </div>
        </div>
    </div>

    <div class="tier-list">
        <?php foreach ($tierList as $tier): ?>
        <div class="tier-row">
            <div class="tier-label" style="background-color: <?= htmlspecialchars($tier['couleur']) ?>">
                <?= htmlspecialchars($tier['nom']) ?>
            </div>
            <div class="tier-characters">
                
                <?php foreach ($tier['personnages'] as $perso): ?>
                <a href="/personnage/<?= $perso['id'] ?>" class="tier-character" data-rarete="<?= strtolower($perso['rarete'] ?? '') ?>" title="<?= htmlspecialchars($perso['nom']) ?>">
                    <img src="<?= getImagePath($perso['image_tierlist'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>">
                    <span class="tier-character-name"><?= htmlspecialchars($perso['nom']) ?></span>
                </a>
                <?php endforeach; ?>
                
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
    (function() {
        let activeRarete = null;

        const rareteButtons = document.querySelectorAll('.filtre-rarete-tierlist');
        const tierCharacters = document.querySelectorAll('.tier-character');

        function filterTierList() {
            tierCharacters.forEach(character => {
                const rarete = character.dataset.rarete;
                const matchRarete = !activeRarete || rarete === activeRarete;

                if (matchRarete) {
                    character.style.display = '';
                } else {
                    character.style.display = 'none';
                }
            });
        }

        rareteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const rarete = button.dataset.rarete;

                if (activeRarete === rarete) {
                    activeRarete = null;
                    button.classList.remove('active');
                } else {
                    activeRarete = rarete;
                    rareteButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                }

                filterTierList();
            });
        });
    })();
    </script>
<?php endif; ?>
