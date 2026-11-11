<?php
$liens = getLiensUtilesJeu($jeuId);
?>

<div class="section-header">
    <h1 class="section-title">Liens utiles</h1>
    <p class="section-subtitle">Nos partenaires pour <?= htmlspecialchars($jeu['titre']) ?></p>
</div>

<?php if (empty($liens)): ?>
    <div class="empty-state">
        <i class="fas fa-link"></i>
        <p>Aucun lien utile disponible pour le moment.</p>
    </div>
<?php else: ?>
    <div class="liens-utiles-list">
        <?php foreach ($liens as $lien): ?>
        <div class="lien-utile-card">
            <?php if (!empty($lien['image'])): ?>
            <div class="lien-utile-logo">
                <img src="<?= getImagePath($lien['image'], 'partner') ?>" alt="<?= htmlspecialchars($lien['titre']) ?>">
            </div>
            <?php endif; ?>
            <div class="lien-utile-content">
                <h3 class="lien-utile-titre"><?= htmlspecialchars($lien['titre']) ?></h3>
                <?php if (!empty($lien['description'])): ?>
                <p class="lien-utile-description"><?= htmlspecialchars($lien['description']) ?></p>
                <?php endif; ?>

                <?php if (!empty($lien['code_promo'])): ?>
                <div class="lien-utile-code">
                    <span>Code :</span>
                    <code class="code-value"><?= htmlspecialchars($lien['code_promo']) ?></code>
                    <button class="btn-copy-code" onclick="copyLienCode(this, '<?= htmlspecialchars($lien['code_promo']) ?>')" title="Copier le code">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <?php endif; ?>

                <a href="<?= htmlspecialchars($lien['url']) ?>" target="_blank" rel="noopener nofollow sponsored" class="btn-lien-utile">
                    Découvrir <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
    function copyLienCode(button, code) {
        navigator.clipboard.writeText(code);
        const icon = button.querySelector("i");
        icon.classList.remove("fa-copy");
        icon.classList.add("fa-check");
        setTimeout(() => {
            icon.classList.remove("fa-check");
            icon.classList.add("fa-copy");
        }, 1500);
    }
    </script>
<?php endif; ?>