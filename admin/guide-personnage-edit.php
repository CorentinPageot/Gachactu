<?php
require_once 'includes/auth.php';
require_once 'includes/admin-functions.php';

requireAuth();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$personnage = $id ? getPersonnageById($id) : null;

if (!$personnage) {
    header('Location: personnages.php');
    exit;
}

$guide = getGuidePersonnage($id);
$jeuId = $personnage['jeu_id'];
$pageTitle = 'Guide de ' . $personnage['nom'];
$message = '';
$messageType = '';

// Récupérer la configuration des rubriques pour ce jeu
$configRubriques = getConfigRubriquesJeu($jeuId);
$rubriquesPerso = getRubriquesPersonnaliseesJeu($jeuId);
$valeursPerso = getValeursRubriquesPersonnalisees($id);
$personnages = getPersonnagesJeu($jeuId);

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

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $presentation = $_POST['presentation'] ?? '';
    $skills = $_POST['skills'] ?? '';
    $doublons = $_POST['doublons'] ?? '';

    if (saveGuidePersonnage($id, $presentation, $skills, $doublons)) {
        foreach ($rubriquesPerso as $rp) {
            $fieldName = 'rubrique_perso_' . $rp['id'];
            $contenu = $_POST[$fieldName] ?? '';
            saveValeurRubriquePersonnalisee($rp['id'], $id, $contenu);
        }

        $normalizedFiles = isset($_FILES['builds']) ? reorganizeFiles($_FILES['builds']) : [];

        foreach (['armes', 'artefacts', 'equipe'] as $buildType) {
            $buildsData = $_POST['builds'][$buildType] ?? [];
            $buildsFiles = $normalizedFiles[$buildType] ?? [];
            savePersonnageBuildsRubrique($id, $buildType, $buildsData, $buildsFiles);
        }

        $message = 'Guide enregistré avec succès.';
        $messageType = 'success';
        $guide = getGuidePersonnage($id);
        $valeursPerso = getValeursRubriquesPersonnalisees($id);
    } else {
        $message = 'Erreur lors de l\'enregistrement.';
        $messageType = 'error';
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
    <h2>
        <img src="../<?= getImagePath($personnage['image'], 'game') ?>" alt="" style="width: 40px; height: 40px; border-radius: 8px; vertical-align: middle; margin-right: 10px;">
        <?= $pageTitle ?>
    </h2>
    <div class="admin-header-actions">
        <a href="personnage-edit.php?id=<?= $id ?>" class="admin-btn admin-btn-secondary">
            <i class="fas fa-user"></i> Fiche personnage
        </a>
        <a href="rubriques-personnages.php?jeu=<?= $jeuId ?>" class="admin-btn admin-btn-secondary">
            <i class="fas fa-cog"></i> Gérer les rubriques
        </a>
        <a href="personnages.php?jeu=<?= $personnage['jeu_id'] ?>" class="admin-btn admin-btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<form method="POST" class="admin-form" enctype="multipart/form-data">
    <?php foreach ($configRubriques as $rubrique): ?>
        <?php if (!$rubrique['est_active']) continue; ?>

        <?php if (!$rubrique['est_personnalisee']): ?>
            <?php
            $code = $rubrique['code_rubrique'];
            $buildTypesMap = ['armes' => 'armes', 'equipements' => 'artefacts', 'equipe' => 'equipe'];
            ?>

            <?php if (isset($buildTypesMap[$code])): ?>
                <?php
                $buildType = $buildTypesMap[$code];
                $builds = getPersonnageBuilds($id, $buildType);
                ?>
                <div class="admin-form-group full-width">
                    <label>
                        <i class="<?= htmlspecialchars($rubrique['icone']) ?>"></i>
                        <?= htmlspecialchars($rubrique['nom']) ?>
                    </label>

                    <div class="builds-container" data-type="<?= $buildType ?>">
                        <?php foreach ($builds as $i => $build): ?>
                        <div class="build-card">
                            <div class="admin-form-row">
                                <div class="admin-form-group">
                                    <label>Titre (optionnel)</label>
                                    <input type="text"
                                        name="builds[<?= $buildType ?>][<?= $i ?>][nom]"
                                        value="<?= htmlspecialchars($build['nom'] ?? '') ?>"
                                        placeholder="<?= $buildType === 'equipe' ? 'Team' : 'Build' ?> <?= $i + 1 ?>">
                                </div>
                                <button type="button" class="admin-btn admin-btn-danger btn-remove-build">x</button>
                            </div>

                            <div class="admin-form-group">
                                <label>Description</label>
                                <textarea name="builds[<?= $buildType ?>][<?= $i ?>][description]" rows="2"><?= htmlspecialchars($build['description'] ?? '') ?></textarea>
                            </div>

                            <?php if ($buildType === 'equipe'): ?>
                            <div class="admin-form-group">
                                <label>Personnages</label>
                                <div class="build-personnages-list">
                                    <?php
                                    $selectedPersos = $build['personnages'] ?? [];
                                    $slots = max(6, count($selectedPersos));
                                    for ($s = 0; $s < $slots; $s++):
                                        $selectedId = $selectedPersos[$s]['id'] ?? 0;
                                    ?>
                                    <select name="builds[<?= $buildType ?>][<?= $i ?>][personnages][]">
                                        <option value="">-- Personnage --</option>
                                        <?php foreach ($personnages as $perso): ?>
                                        <option value="<?= $perso['id'] ?>" <?= $perso['id'] == $selectedId ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($perso['nom']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="admin-form-group">
                                <label>Éléments</label>
                                <div class="build-elements-list">
                                    <?php
                                    $elements = !empty($build['elements']) ? $build['elements'] : [['nom' => '', 'image' => '']];
                                    foreach ($elements as $elIndex => $el):
                                    ?>
                                    <div class="build-element-row">
                                        <div class="build-element-image">
                                            <?php if (!empty($el['image'])): ?>
                                            <img src="../<?= getImagePath($el['image'], 'game') ?>" alt="" class="build-element-image-preview">
                                            <?php endif; ?>
                                            <input type="file"
                                                name="builds[<?= $buildType ?>][<?= $i ?>][elements_images][]"
                                                accept="image/*">
                                            <input type="hidden"
                                                name="builds[<?= $buildType ?>][<?= $i ?>][elements_current_image][]"
                                                value="<?= htmlspecialchars($el['image'] ?? '') ?>">
                                        </div>
                                        <input type="text"
                                            name="builds[<?= $buildType ?>][<?= $i ?>][elements][]"
                                            value="<?= htmlspecialchars($el['nom'] ?? '') ?>"
                                            placeholder="Ex: Arc du Dragon">
                                        <button type="button" class="admin-btn admin-btn-danger btn-remove-element">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="admin-btn admin-btn-secondary btn-add-element">
                                    + Ajouter un élément
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="admin-btn admin-btn-primary btn-add-build">
                        <i class="fas fa-plus"></i> Ajouter <?= $buildType === 'equipe' ? 'une équipe' : 'un build' ?>
                    </button>
                </div>

            <?php else: ?>
                <!-- Rubrique de base classique (presentation, skills, doublons) -->
                <?php
                $fieldName = match($code) {
                    'presentation' => 'presentation',
                    'skills' => 'skills',
                    'doublons' => 'doublons',
                    default => $code
                };
                $champ = $rubrique['champ'];
                $value = $guide[$champ] ?? '';
                ?>
                <div class="admin-form-group full-width">
                    <label for="<?= $fieldName ?>">
                        <i class="<?= htmlspecialchars($rubrique['icone']) ?>"></i>
                        <?= htmlspecialchars($rubrique['nom']) ?>
                    </label>
                    <textarea id="<?= $fieldName ?>" name="<?= $fieldName ?>" class="tinymce-editor"><?= htmlspecialchars($value) ?></textarea>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Rubrique personnalisée -->
            <?php
            $rubriqueId = $rubrique['rubrique_perso_id'];
            $fieldName = 'rubrique_perso_' . $rubriqueId;
            $value = $valeursPerso[$rubriqueId]['contenu'] ?? '';
            ?>
            <div class="admin-form-group full-width">
                <label for="<?= $fieldName ?>">
                    <i class="<?= htmlspecialchars($rubrique['icone']) ?>"></i>
                    <?= htmlspecialchars($rubrique['nom']) ?>
                    <span class="label-badge">Personnalisée</span>
                </label>
                <textarea id="<?= $fieldName ?>" name="<?= $fieldName ?>" class="tinymce-editor"><?= htmlspecialchars($value) ?></textarea>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-success">
            <i class="fas fa-save"></i> Enregistrer le guide
        </button>
        <a href="personnages.php?jeu=<?= $personnage['jeu_id'] ?>" class="admin-btn admin-btn-secondary">Annuler</a>
    </div>

    <!-- A REFACTO ? -->
    <template id="template-armes">
        <div class="build-card">
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Titre (optionnel)</label>
                    <input type="text" name="builds[armes][__INDEX__][nom]" placeholder="Build __LABEL__">
                </div>
                <button type="button" class="admin-btn admin-btn-danger btn-remove-build">x</button>
            </div>
            <div class="admin-form-group">
                <label>Description</label>
                <textarea name="builds[armes][__INDEX__][description]" rows="2"></textarea>
            </div>
            <div class="admin-form-group">
                <label>Éléments</label>
                <div class="build-elements-list">
                    <div class="build-element-row">
                        <div class="build-element-image">
                            <input type="file" name="builds[armes][__INDEX__][elements_images][]" accept="image/*">
                            <input type="hidden" name="builds[armes][__INDEX__][elements_current_image][]" value="">
                        </div>
                        <input type="text" name="builds[armes][__INDEX__][elements][]" placeholder="Ex: Arc du Dragon">
                        <button type="button" class="admin-btn admin-btn-danger btn-remove-element">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="admin-btn admin-btn-secondary btn-add-element">+ Ajouter un élément</button>
            </div>
        </div>
    </template>
    
    <template id="template-artefacts">
        <div class="build-card">
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Titre (optionnel)</label>
                    <input type="text" name="builds[artefacts][__INDEX__][nom]" placeholder="Build __LABEL__">
                </div>
                <button type="button" class="admin-btn admin-btn-danger btn-remove-build">x</button>
            </div>
            <div class="admin-form-group">
                <label>Description</label>
                <textarea name="builds[artefacts][__INDEX__][description]" rows="2"></textarea>
            </div>
            <div class="admin-form-group">
                <label>Éléments</label>
                <div class="build-elements-list">
                    <div class="build-element-row">
                        <div class="build-element-image">
                            <input type="file" name="builds[artefacts][__INDEX__][elements_images][]" accept="image/*">
                            <input type="hidden" name="builds[artefacts][__INDEX__][elements_current_image][]" value="">
                        </div>
                        <input type="text" name="builds[artefacts][__INDEX__][elements][]" placeholder="Ex: Stamp de Feu">
                        <button type="button" class="admin-btn admin-btn-danger btn-remove-element">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="admin-btn admin-btn-secondary btn-add-element">+ Ajouter un élément</button>
            </div>
        </div>
    </template>

    <template id="template-equipe">
        <div class="build-card">
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Titre (optionnel)</label>
                    <input type="text" name="builds[equipe][__INDEX__][nom]" placeholder="Team __LABEL__">
                </div>
                <button type="button" class="admin-btn admin-btn-danger btn-remove-build">x</button>
            </div>
            <div class="admin-form-group">
                <label>Description</label>
                <textarea name="builds[equipe][__INDEX__][description]" rows="2"></textarea>
            </div>
            <div class="admin-form-group">
                <label>Personnages</label>
                <div class="build-personnages-list">
                    <?php for ($s = 0; $s < 6; $s++): ?>
                    <select name="builds[equipe][__INDEX__][personnages][]">
                        <option value="">-- Personnage --</option>
                        <?php foreach ($personnages as $perso): ?>
                        <option value="<?= $perso['id'] ?>"><?= htmlspecialchars($perso['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </template>

    <script>
    document.querySelectorAll('.builds-container').forEach(container => {
        const type = container.dataset.type;
        let index = container.querySelectorAll('.build-card').length;

        const addBuildBtn = container.parentElement.querySelector('.btn-add-build');
        addBuildBtn.addEventListener('click', () => {
            const template = document.getElementById('template-' + type);
            const html = template.innerHTML
                .replaceAll('__INDEX__', index)
                .replaceAll('__LABEL__', index + 1);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            container.appendChild(wrapper.firstElementChild);
            index++;
        });

        container.addEventListener('click', e => {
            if (e.target.closest('.btn-remove-build')) {
                e.target.closest('.build-card').remove();
            }
            if (e.target.closest('.btn-add-element')) {
                const list = e.target.closest('.admin-form-group').querySelector('.build-elements-list');
                const row = list.querySelector('.build-element-row').cloneNode(true);
                row.querySelector('input[type="text"]').value = '';
                const hiddenInput = row.querySelector('input[type="hidden"]');
                if (hiddenInput) hiddenInput.value = '';
                const preview = row.querySelector('.build-element-image-preview');
                if (preview) preview.remove();
                list.appendChild(row);
            }
            if (e.target.closest('.btn-remove-element')) {
                const list = e.target.closest('.build-elements-list');
                if (list.querySelectorAll('.build-element-row').length > 1) {
                    e.target.closest('.build-element-row').remove();
                }
            }
        });
    });
    </script>
    <!-- FIN REFACTO -->
</form>

<?php include 'includes/admin-footer.php'; ?>
