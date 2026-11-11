<?php
$personnages = getPersonnagesJeu($jeuId);
$tierLists = getTierListsJeu($jeuId);
$activeTierListId = isset($_GET['tier_list_id']) ? (int)$_GET['tier_list_id'] : (!empty($tierLists) ? $tierLists[0]['id'] : null);
$tierListExistante = $activeTierListId ? getTierListComplete($activeTierListId) : [];

// Nom de la tier list active
$activeTierListNom = '';
foreach ($tierLists as $tl) {
    if ($tl['id'] == $activeTierListId) {
        $activeTierListNom = $tl['nom'];
        break;
    }
}

// Code d'accès au Tier List Maker
$codeAcces = $_ENV['TIERMAKER_CODE'] ?? null;

if ($codeAcces === null) {
    die('Code d\'accès non configuré');
}

// Vérifier si déjà déverrouillé via la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$alreadyUnlocked = !empty($_SESSION['tiermaker_unlocked']);

// Récupérer les personnages déjà placés dans la tier list
$personnagesPlaces = [];
foreach ($tierListExistante as $tier) {
    foreach ($tier['personnages'] as $perso) {
        $personnagesPlaces[$perso['id']] = $tier['nom'];
    }
}
?>

<div class="section-header">
    <h1 class="section-title">Tier List Maker</h1>
    <p class="section-subtitle">Créez votre propre tier list pour <?= htmlspecialchars($jeu['titre']) ?></p>
</div>

<?php if (empty($personnages)): ?>
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <p>Aucun personnage disponible pour créer une tier list.</p>
    </div>
<?php else: ?>
    <!-- Écran de verrouillage -->
    <div id="tiermaker-lock" class="tiermaker-lock"<?php if ($alreadyUnlocked): ?> style="display: none;"<?php endif; ?>>
        <div class="lock-container">
            <div class="lock-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h3>Accès protégé</h3>
            <p>Entrez le code d'accès pour modifier la tier list.</p>
            <div class="lock-input-group">
                <div class="lock-input-wrapper">
                    <input type="password" id="accessCode" class="lock-input" placeholder="Code d'accès">
                    <button type="button" id="togglePassword" class="toggle-password">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                <button type="button" id="unlockBtn" class="btn-unlock">
                    <i class="fas fa-unlock"></i> Débloquer
                </button>
            </div>
            <p id="lockError" class="lock-error" style="display: none;">
                <i class="fas fa-exclamation-circle"></i> Code incorrect
            </p>
        </div>
    </div>

    <!-- Contenu du Tier List Maker (caché par défaut) -->
    <div id="tiermaker-content" class="tiermaker-container" style="display: <?= $alreadyUnlocked ? 'flex' : 'none' ?>;">

        <!-- Sélecteur de tier list -->
        <div class="tierlist-manager">
            <div class="tierlist-selector">
                <?php foreach ($tierLists as $tl): ?>
                    <button type="button"
                        class="tierlist-selector-btn<?= $tl['id'] == $activeTierListId ? ' active' : '' ?>"
                        data-tier-list-id="<?= $tl['id'] ?>"
                        onclick="switchTierListMaker(<?= $tl['id'] ?>)"
                    >
                        <?= htmlspecialchars($tl['nom']) ?>
                    </button>
                <?php endforeach; ?>
                <?php if (count($tierLists) < 3): ?>
                    <button type="button" class="tierlist-selector-btn tierlist-add-btn" onclick="createNewTierList()">
                        <i class="fas fa-plus"></i>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Renommer / Supprimer la tier list active -->
            <?php if ($activeTierListId): ?>
            <div class="tierlist-edit-name">
                <input type="text" id="tierListName" class="tierlist-name-input" value="<?= htmlspecialchars($activeTierListNom) ?>" placeholder="Nom de la tier list">
                <div class="btn-actions-tierlist">
                    <button type="button" class="btn-tiermaker btn-rename" onclick="renameTierListAction()">
                        <i class="fas fa-pen"></i>
                    </button>
                    <?php if (count($tierLists) > 1): ?>
                    <button type="button" class="btn-tiermaker btn-delete-tierlist" onclick="deleteTierListAction()">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Zone de la Tier List -->
        <div class="tiermaker-board">
            <div class="tier-row-maker" data-tier="APEX">
                <div class="tier-label-maker" style="background-color: #ff7f7f;">APEX</div>
                <div class="tier-drop-zone" data-tier="APEX">
                    <?php foreach ($tierListExistante as $tier): ?>
                        <?php if ($tier['nom'] === 'APEX'): ?>
                            <?php foreach ($tier['personnages'] as $perso): ?>
                            <div class="tier-character-draggable" draggable="true" data-id="<?= $perso['id'] ?>">
                                <img src="<?= getImagePath($perso['image_tierlist'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>" title="<?= htmlspecialchars($perso['nom']) ?>">
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tier-row-maker" data-tier="T1">
                <div class="tier-label-maker" style="background-color: #ffbf7f;">T1</div>
                <div class="tier-drop-zone" data-tier="T1">
                    <?php foreach ($tierListExistante as $tier): ?>
                        <?php if ($tier['nom'] === 'T1'): ?>
                            <?php foreach ($tier['personnages'] as $perso): ?>
                            <div class="tier-character-draggable" draggable="true" data-id="<?= $perso['id'] ?>">
                                <img src="<?= getImagePath($perso['image_tierlist'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>" title="<?= htmlspecialchars($perso['nom']) ?>">
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tier-row-maker" data-tier="T2">
                <div class="tier-label-maker" style="background-color: #ffdf7f;">T2</div>
                <div class="tier-drop-zone" data-tier="T2">
                    <?php foreach ($tierListExistante as $tier): ?>
                        <?php if ($tier['nom'] === 'T2'): ?>
                            <?php foreach ($tier['personnages'] as $perso): ?>
                            <div class="tier-character-draggable" draggable="true" data-id="<?= $perso['id'] ?>">
                                <img src="<?= getImagePath($perso['image_tierlist'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>" title="<?= htmlspecialchars($perso['nom']) ?>">
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tier-row-maker" data-tier="T3">
                <div class="tier-label-maker" style="background-color: #ffff7f;">T3</div>
                <div class="tier-drop-zone" data-tier="T3">
                    <?php foreach ($tierListExistante as $tier): ?>
                        <?php if ($tier['nom'] === 'T3'): ?>
                            <?php foreach ($tier['personnages'] as $perso): ?>
                            <div class="tier-character-draggable" draggable="true" data-id="<?= $perso['id'] ?>">
                                <img src="<?= getImagePath($perso['image_tierlist'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>" title="<?= htmlspecialchars($perso['nom']) ?>">
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tier-row-maker" data-tier="T4">
                <div class="tier-label-maker" style="background-color: #bfff7f;">T4</div>
                <div class="tier-drop-zone" data-tier="T4">
                    <?php foreach ($tierListExistante as $tier): ?>
                        <?php if ($tier['nom'] === 'T4'): ?>
                            <?php foreach ($tier['personnages'] as $perso): ?>
                            <div class="tier-character-draggable" draggable="true" data-id="<?= $perso['id'] ?>">
                                <img src="<?= getImagePath($perso['image_tierlist'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>" title="<?= htmlspecialchars($perso['nom']) ?>">
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tier-row-maker" data-tier="T5">
                <div class="tier-label-maker" style="background-color: #7fbfff;">T5</div>
                <div class="tier-drop-zone" data-tier="T5">
                    <?php foreach ($tierListExistante as $tier): ?>
                        <?php if ($tier['nom'] === 'T5'): ?>
                            <?php foreach ($tier['personnages'] as $perso): ?>
                            <div class="tier-character-draggable" draggable="true" data-id="<?= $perso['id'] ?>">
                                <img src="<?= getImagePath($perso['image_tierlist'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>" title="<?= htmlspecialchars($perso['nom']) ?>">
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="tiermaker-actions">
            <button class="btn-tiermaker btn-save" onclick="saveTierList()">
                <i class="fas fa-save"></i> Sauvegarder
            </button>
            <button class="btn-tiermaker btn-reset" onclick="resetTierList()">
                <i class="fas fa-undo"></i> Réinitialiser
            </button>
        </div>

        <!-- Pool de personnages non classés -->
        <div class="tiermaker-pool">
            <h3 class="tiermaker-pool-title">Personnages disponibles</h3>
            <div class="tiermaker-search">
                <i class="fas fa-search"></i>
                <input type="text" id="searchTiermaker" placeholder="Rechercher un personnage...">
            </div>
            <div class="tier-drop-zone pool-zone" data-tier="pool">
                <?php foreach ($personnages as $perso): ?>
                    <?php if (!isset($personnagesPlaces[$perso['id']])): ?>
                    <div class="tier-character-draggable" draggable="true" data-id="<?= $perso['id'] ?>">
                        <img src="<?= getImagePath($perso['image_tierlist'], 'game') ?>" alt="<?= htmlspecialchars($perso['nom']) ?>" title="<?= htmlspecialchars($perso['nom']) ?>">
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const jeuId = <?= $jeuId ?>;
        const currentTierListId = <?= $activeTierListId ? $activeTierListId : 'null' ?>;
        let isUnlocked = <?= $alreadyUnlocked ? 'true' : 'false' ?>;

        // Vérification du code
        function checkAccessCode() {
            const inputCode = document.getElementById('accessCode').value;
            const errorMsg = document.getElementById('lockError');

            fetch('ajax/check-access-code.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ code: inputCode })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    isUnlocked = true;
                    document.getElementById('tiermaker-lock').style.display = 'none';
                    document.getElementById('tiermaker-content').style.display = 'flex';
                    initDragAndDrop();
                } else {
                    errorMsg.style.display = 'block';
                    document.getElementById('accessCode').value = '';
                    document.getElementById('accessCode').focus();
                }
            })
            .catch(() => {
                errorMsg.style.display = 'block';
            });
        }

        // Si déjà déverrouillé, initialiser le drag & drop immédiatement
        if (isUnlocked) {
            initDragAndDrop();
        }

        // Event listeners pour le déverrouillage
        document.getElementById('unlockBtn').addEventListener('click', checkAccessCode);
        document.getElementById('accessCode').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                checkAccessCode();
            }
        });

        // Cacher l'erreur quand on tape
        document.getElementById('accessCode').addEventListener('input', function() {
            document.getElementById('lockError').style.display = 'none';
        });

        // Toggle affichage mot de passe
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('accessCode');
            const icon = document.getElementById('eyeIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        let draggedElement = null;

        // Initialiser le drag & drop
        function initDragAndDrop() {
            const draggables = document.querySelectorAll('.tier-character-draggable');
            const dropZones = document.querySelectorAll('.tier-drop-zone');

            draggables.forEach(draggable => {
                draggable.addEventListener('dragstart', handleDragStart);
                draggable.addEventListener('dragend', handleDragEnd);
            });

            dropZones.forEach(zone => {
                zone.addEventListener('dragover', handleDragOver);
                zone.addEventListener('dragenter', handleDragEnter);
                zone.addEventListener('dragleave', handleDragLeave);
                zone.addEventListener('drop', handleDrop);
            });

            // Initialiser la recherche
            document.getElementById('searchTiermaker').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const poolCharacters = document.querySelectorAll('.pool-zone .tier-character-draggable');

                poolCharacters.forEach(character => {
                    const name = character.querySelector('img').alt.toLowerCase();
                    character.style.display = name.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        function handleDragStart(e) {
            draggedElement = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.dataset.id);
        }

        function handleDragEnd(e) {
            this.classList.remove('dragging');
            document.querySelectorAll('.tier-drop-zone').forEach(zone => {
                zone.classList.remove('drag-over');
            });
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        }

        function handleDragEnter(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        }

        function handleDragLeave(e) {
            this.classList.remove('drag-over');
        }

        function handleDrop(e) {
            e.preventDefault();
            this.classList.remove('drag-over');

            if (draggedElement) {
                this.appendChild(draggedElement);
            }
        }

        // Sauvegarder la tier list
        window.saveTierList = function() {
            if (!isUnlocked) return;

            const tierData = {};
            const tiers = ['APEX', 'T1', 'T2', 'T3', 'T4', 'T5'];

            tiers.forEach(tier => {
                const zone = document.querySelector(`.tier-drop-zone[data-tier="${tier}"]`);
                const characters = zone.querySelectorAll('.tier-character-draggable');
                tierData[tier] = Array.from(characters).map(c => c.dataset.id);
            });

            fetch('ajax/save-tierlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    jeu_id: jeuId,
                    tier_list_id: currentTierListId,
                    tiers: tierData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Tier list sauvegardée avec succès !', 'success');
                } else {
                    showNotification('Erreur lors de la sauvegarde.', 'error');
                }
            })
            .catch(error => {
                showNotification('Erreur lors de la sauvegarde.', 'error');
            });
        };

        // Réinitialiser la tier list
        window.resetTierList = function() {
            if (!isUnlocked) return;
            if (!confirm('Êtes-vous sûr de vouloir réinitialiser la tier list ?')) return;

            const poolZone = document.querySelector('.pool-zone');
            const allCharacters = document.querySelectorAll('.tier-drop-zone:not(.pool-zone) .tier-character-draggable');

            allCharacters.forEach(character => {
                poolZone.appendChild(character);
            });
        };

        // Changer de tier list
        window.switchTierListMaker = function(tierListId) {
            loadSection('tierlistmaker', 'tier_list_id=' + tierListId);
        };

        // Créer une nouvelle tier list
        window.createNewTierList = function() {
            const nom = prompt('Nom de la nouvelle tier list :', 'Nouvelle Tier List');
            if (!nom) return;

            fetch('ajax/manage-tierlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'create', jeu_id: jeuId, nom: nom })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    switchTierListMaker(data.tier_list_id);
                } else {
                    showNotification(data.error || 'Erreur lors de la création.', 'error');
                }
            });
        };

        // Renommer la tier list active
        window.renameTierListAction = function() {
            if (!currentTierListId) return;
            const nom = document.getElementById('tierListName').value.trim();
            if (!nom) {
                showNotification('Le nom ne peut pas être vide.', 'error');
                return;
            }

            fetch('ajax/manage-tierlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'rename', tier_list_id: currentTierListId, nom: nom })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification('Tier list renommée avec succès !', 'success');
                    // Mettre à jour le bouton correspondant
                    const btn = document.querySelector(`.tierlist-selector-btn[data-tier-list-id="${currentTierListId}"]`);
                    if (btn) btn.textContent = nom;
                } else {
                    showNotification(data.error || 'Erreur lors du renommage.', 'error');
                }
            });
        };

        // Supprimer la tier list active
        window.deleteTierListAction = function() {
            if (!currentTierListId) return;
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette tier list ? Cette action est irréversible.')) return;

            fetch('ajax/manage-tierlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', tier_list_id: currentTierListId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Recharger la section pour afficher la première tier list restante
                    loadSection('tierlistmaker');
                } else {
                    showNotification(data.error || 'Erreur lors de la suppression.', 'error');
                }
            });
        };

        // Notification
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `tiermaker-notification ${type}`;
            notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    })();
    </script>
<?php endif; ?>
