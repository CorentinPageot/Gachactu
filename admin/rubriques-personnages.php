<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

$pageTitle = 'Rubriques personnages';
$message = '';
$messageType = '';

// Récupérer le jeu sélectionné
$jeuId = isset($_GET['jeu']) ? (int)$_GET['jeu'] : 0;
$jeux = getJeux();

// Si un jeu est sélectionné
$jeu = $jeuId ? getJeuById($jeuId) : null;
$configRubriques = $jeu ? getConfigRubriquesJeu($jeuId, true) : [];
$rubriquesPerso = $jeu ? getRubriquesPersonnaliseesJeu($jeuId) : [];

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $jeuId) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_rubrique') {
        // Ajouter une rubrique personnalisée
        $nom = trim($_POST['nom'] ?? '');
        $icone = trim($_POST['icone'] ?? 'fas fa-star');

        if (!empty($nom)) {
            $newId = createRubriquePersonnalisee($jeuId, $nom, $icone);
            if ($newId) {
                $message = 'Rubrique créée avec succès.';
                $messageType = 'success';
                $rubriquesPerso = getRubriquesPersonnaliseesJeu($jeuId);
            } else {
                $message = 'Erreur lors de la création.';
                $messageType = 'error';
            }
        }
    } elseif ($action === 'update_rubrique') {
        // Modifier une rubrique personnalisée
        $rubriqueId = (int)($_POST['rubrique_id'] ?? 0);
        $nom = trim($_POST['nom'] ?? '');
        $icone = trim($_POST['icone'] ?? 'fas fa-star');

        if ($rubriqueId && !empty($nom)) {
            if (updateRubriquePersonnalisee($rubriqueId, $nom, $icone)) {
                $message = 'Rubrique modifiée avec succès.';
                $messageType = 'success';
                $rubriquesPerso = getRubriquesPersonnaliseesJeu($jeuId);
            } else {
                $message = 'Erreur lors de la modification.';
                $messageType = 'error';
            }
        }
    } elseif ($action === 'delete_rubrique') {
        // Supprimer une rubrique personnalisée
        $rubriqueId = (int)($_POST['rubrique_id'] ?? 0);

        if ($rubriqueId) {
            if (deleteRubriquePersonnalisee($rubriqueId)) {
                $message = 'Rubrique supprimée.';
                $messageType = 'success';
                $rubriquesPerso = getRubriquesPersonnaliseesJeu($jeuId);
            } else {
                $message = 'Erreur lors de la suppression.';
                $messageType = 'error';
            }
        }
    } elseif ($action === 'save_config') {
        // Sauvegarder la configuration des rubriques (ordre et activation)
        $rubriquesData = json_decode($_POST['rubriques_data'] ?? '[]', true);

        if (!empty($rubriquesData)) {
            if (saveConfigRubriquesJeu($jeuId, $rubriquesData)) {
                $message = 'Configuration sauvegardée.';
                $messageType = 'success';
                $configRubriques = getConfigRubriquesJeu($jeuId, true);
            } else {
                $message = 'Erreur lors de la sauvegarde.';
                $messageType = 'error';
            }
        }
    }
}

// Reconstruire la config avec les rubriques perso
if ($jeu) {
    $configRubriques = getConfigRubriquesJeu($jeuId, true);

    // Ajouter les rubriques perso qui ne sont pas encore dans la config
    $rubriquePersoIds = array_column(array_filter($configRubriques, fn($r) => $r['est_personnalisee']), 'rubrique_perso_id');
    foreach ($rubriquesPerso as $rp) {
        if (!in_array($rp['id'], $rubriquePersoIds)) {
            $configRubriques[] = [
                'id' => null,
                'code_rubrique' => null,
                'rubrique_perso_id' => $rp['id'],
                'nom' => $rp['nom'],
                'icone' => $rp['icone'],
                'champ' => null,
                'est_active' => 1,
                'ordre' => count($configRubriques) + 1,
                'est_personnalisee' => true
            ];
        }
    }
}

include 'includes/admin-header.php';
?>

<?php if ($message): ?>
<div class="admin-alert admin-alert-<?= $messageType ?>">
    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="admin-page-header">
    <h2>Configuration des rubriques</h2>
</div>

<!-- Sélection du jeu -->
<div class="admin-filter-bar">
    <form method="GET" class="admin-filter-form">
        <label for="jeu">Sélectionner un jeu :</label>
        <select name="jeu" id="jeu" onchange="this.form.submit()">
            <option value="">-- Choisir un jeu --</option>
            <?php foreach ($jeux as $j): ?>
            <option value="<?= $j['id'] ?>" <?= $jeuId == $j['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($j['titre']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if ($jeu): ?>
<div class="rubriques-container">
    <!-- Section des rubriques personnalisées -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><i class="fas fa-plus-circle"></i> Rubriques personnalisées</h3>
        </div>
        <div class="admin-card-body">
            <p class="admin-help-text">Créez des rubriques spécifiques à ce jeu. Ces rubriques s'ajouteront aux rubriques de base.</p>

            <!-- Formulaire d'ajout -->
            <form method="POST" class="rubriques-add-form">
                <input type="hidden" name="action" value="add_rubrique">
                <div class="rubriques-add-row">
                    <div class="form-group">
                        <label>Nom de la rubrique</label>
                        <input type="text" name="nom" required placeholder="Écrire le nom de la rubrique">
                    </div>
                    <div class="form-group">
                        <label>Icône <a href="https://fontawesome.com/" class="link" target="_blank">FontAwesome</a></label>
                        <input type="text" name="icone" placeholder="Ex: fas fa-star">
                    </div>
                    <button type="submit" class="admin-btn admin-btn-success">
                        <i class="fas fa-plus"></i> Ajouter
                    </button>
                </div>
            </form>

            <!-- Liste des rubriques personnalisées -->
            <?php if (!empty($rubriquesPerso)): ?>
            <div class="rubriques-perso-list">
                <?php foreach ($rubriquesPerso as $rp): ?>
                <div class="rubrique-perso-item">
                    <div class="rubrique-perso-info">
                        <i class="<?= htmlspecialchars($rp['icone']) ?>"></i>
                        <span><?= htmlspecialchars($rp['nom']) ?></span>
                    </div>
                    <div class="rubrique-perso-actions">
                        <button type="button" class="admin-btn admin-btn-sm admin-btn-secondary"
                                onclick="editRubrique(<?= $rp['id'] ?>, '<?= htmlspecialchars(addslashes($rp['nom'])) ?>', '<?= htmlspecialchars($rp['icone']) ?>')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cette rubrique ?');">
                            <input type="hidden" name="action" value="delete_rubrique">
                            <input type="hidden" name="rubrique_id" value="<?= $rp['id'] ?>">
                            <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="admin-empty-text">Aucune rubrique personnalisée pour ce jeu.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section configuration de l'ordre -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><i class="fas fa-sort"></i> Ordre et activation des rubriques</h3>
        </div>
        <div class="admin-card-body">
            <p class="admin-help-text">Glissez-déposez les rubriques pour modifier leur ordre d'affichage. Décochez pour masquer une rubrique.</p>

            <form method="POST" id="configForm">
                <input type="hidden" name="action" value="save_config">
                <input type="hidden" name="rubriques_data" id="rubriquesData">

                <ul class="rubriques-sortable" id="rubriquesList">
                    <?php foreach ($configRubriques as $rubrique): ?>
                    <li class="rubrique-item"
                        data-code="<?= htmlspecialchars($rubrique['code_rubrique'] ?? '') ?>"
                        data-perso-id="<?= $rubrique['rubrique_perso_id'] ?? '' ?>">
                        <div class="rubrique-handle">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div class="rubrique-checkbox">
                            <input type="checkbox" <?= $rubrique['est_active'] ? 'checked' : '' ?>>
                        </div>
                        <div class="rubrique-icon">
                            <i class="<?= htmlspecialchars($rubrique['icone']) ?>"></i>
                        </div>
                        <div class="rubrique-name">
                            <?= htmlspecialchars($rubrique['nom']) ?>
                            <?php if ($rubrique['est_personnalisee']): ?>
                            <span class="rubrique-badge">Personnalisée</span>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <div class="admin-form-actions">
                    <button type="submit" class="admin-btn admin-btn-success">
                        <i class="fas fa-save"></i> Sauvegarder la configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'édition -->
<div class="modal-overlay" id="editModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Modifier la rubrique</h3>
            <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update_rubrique">
            <input type="hidden" name="rubrique_id" id="editRubriqueId">
            <div class="admin-form-group">
                <label for="editNom">Nom</label>
                <input type="text" name="nom" id="editNom" required>
            </div>
            <div class="admin-form-group">
                <label for="editIcone">Icône</label>
                <input type="text" name="icone" id="editIcone">
            </div>
            <div class="modal-actions">
                <button type="submit" class="admin-btn admin-btn-success">Enregistrer</button>
                <button type="button" class="admin-btn admin-btn-secondary" onclick="closeEditModal()">Annuler</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Initialiser le drag & drop
const sortable = new Sortable(document.getElementById('rubriquesList'), {
    handle: '.rubrique-handle',
    animation: 150,
    ghostClass: 'rubrique-ghost'
});

// Sauvegarder la configuration
document.getElementById('configForm').addEventListener('submit', function(e) {
    const items = document.querySelectorAll('#rubriquesList .rubrique-item');
    const data = [];

    items.forEach((item, index) => {
        const code = item.dataset.code || null;
        const persoId = item.dataset.persoId || null;
        const isActive = item.querySelector('input[type="checkbox"]').checked ? 1 : 0;

        data.push({
            code_rubrique: code || null,
            rubrique_perso_id: persoId ? parseInt(persoId) : null,
            est_active: isActive
        });
    });

    document.getElementById('rubriquesData').value = JSON.stringify(data);
});

// Modal d'édition
function editRubrique(id, nom, icone) {
    document.getElementById('editRubriqueId').value = id;
    document.getElementById('editNom').value = nom;
    document.getElementById('editIcone').value = icone;
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Fermer la modal en cliquant en dehors
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

<?php else: ?>
<div class="admin-empty-state">
    <i class="fas fa-gamepad"></i>
    <p>Sélectionnez un jeu pour configurer ses rubriques.</p>
</div>
<?php endif; ?>

<?php include 'includes/admin-footer.php'; ?>
