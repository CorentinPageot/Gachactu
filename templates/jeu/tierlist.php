<?php
$tierLists = getTierListsJeu($jeuId);
$activeTierListId = isset($_GET['tier_list_id']) ? (int)$_GET['tier_list_id'] : (!empty($tierLists) ? $tierLists[0]['id'] : null);
$tierList = $activeTierListId ? getTierListComplete($activeTierListId) : [];

// Nom de la tier list active
$activeTierListNom = '';
foreach ($tierLists as $tl) {
    if ($tl['id'] == $activeTierListId) {
        $activeTierListNom = $tl['nom'];
        break;
    }
}

$personnages = getPersonnagesJeu($jeuId);

// Raretés uniques
$raretes = [];

foreach ($personnages as $perso) {
    if (!empty($perso['rarete'])) {
        $raretes[$perso['rarete']] = true;
    }
}

$raretes = array_keys($raretes);
?>

<div class="section-header">
    <h1 class="section-title">Tier List</h1>
    <p class="section-subtitle">Classement des personnages de <?= htmlspecialchars($jeu['titre']) ?></p>
</div>

<?php if (!empty($tierLists) && count($tierLists) > 1): ?>
<div class="tierlist-selector">
    <?php foreach ($tierLists as $tl): ?>
        <button type="button"
            class="tierlist-selector-btn<?= $tl['id'] == $activeTierListId ? ' active' : '' ?>"
            data-tier-list-id="<?= $tl['id'] ?>"
        >
            <?= htmlspecialchars($tl['nom']) ?>
        </button>
    <?php endforeach; ?>
</div>
<?php endif; ?>

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
                <?php foreach ($raretes as $rarete):
                    $rareteKey = rareteToKey($rarete);
                ?>
                    <button
                        type="button"
                        class="rarete-btn rarete-<?= $rareteKey ?> filtre-rarete"
                        data-rarete="<?= $rareteKey ?>"
                    >
                        <?= htmlspecialchars($rarete) ?>
                    </button>
                <?php endforeach; ?>
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
                <a href="/personnage.php?id=<?= $perso['id'] ?>" class="tier-character" data-rarete="<?= !empty($perso['rarete']) ? rareteToKey($perso['rarete']) : '' ?>" title="<?= htmlspecialchars($perso['nom']) ?>">
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

        const rareteButtons = document.querySelectorAll('.filtre-rarete');
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

        // Sélecteur de tier list
        document.querySelectorAll('.tierlist-selector-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tierListId = this.dataset.tierListId;
                loadSection('tierlist', 'tier_list_id=' + tierListId);
            });
        });
    })();
    </script>
<?php endif; ?>
