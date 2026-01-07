<?php
$codes = getCodesJeu($jeuId);
?>

<div class="section-header">
    <h1 class="section-title">Codes cadeaux</h1>
    <p class="section-subtitle">Liste des codes actifs pour <?= htmlspecialchars($jeu['titre']) ?></p>
</div>

<?php if (empty($codes)): ?>
    <div class="empty-state">
        <i class="fas fa-gift"></i>
        <p>Aucun code cadeau disponible pour le moment.</p>
    </div>
<?php else: ?>
    <div class="codes-list">
        <?php foreach ($codes as $code): ?>
        <div class="code-card">
            <div class="code-main">
                <code class="code-value"><?= htmlspecialchars($code['code']) ?></code>
                <button class="btn-copy-code" onclick="copyCode('<?= htmlspecialchars($code['code']) ?>')" title="Copier le code">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
            <?php if (!empty($code['recompense'])): ?>
            <div class="code-reward">
                <i class="fas fa-gift"></i> <?= htmlspecialchars($code['recompense']) ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($code['date_expiration'])): ?>
            <div class="code-expiration">
                <i class="fas fa-clock"></i> Expire le <?= formatDateFr($code['date_expiration']) ?>
            </div>
            <?php else: ?>
            <div class="code-expiration code-permanent">
                <i class="fas fa-infinity"></i> Permanent
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
    function copyCode(code) {
        navigator.clipboard.writeText(code);
        alert('Code copié : ' + code);
    }
    </script>
<?php endif; ?>
