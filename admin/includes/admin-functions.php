<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// ============================================
// FONCTIONS ACTUALITÉS
// ============================================

/**
 * Récupère toutes les actualités triées par date décroissante
 */
function getActualitesDashboard($limit = null, $offset = null) {
    $pdo = getDatabase();

    $sql = "SELECT * FROM actualites ORDER BY date DESC";

    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// CRUD

function createActualite($titre, $texte, $date, $image, $masquerActu,
$lootbar_enabled, $lootbar_url, $lootbar_url_texte,
$ig_enabled, $ig_nom_jeu, $ig_categories, $ig_developpeur, $ig_date_sortie, $ig_plateformes, $ig_url, $ig_prix, $ig_image) {

    $pdo = getDatabase();

    $stmt = $pdo->prepare("
        INSERT INTO actualites 
        (titre, texte, date, image, masquer_actu,
        lootbar_enabled, lootbar_url, lootbar_url_texte,
        ig_enabled, ig_nom_jeu, ig_categories, ig_developpeur, ig_date_sortie, ig_plateformes, ig_url, ig_prix, ig_image)
        
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $titre, $texte, $date, $image, $masquerActu,
        $lootbar_enabled, $lootbar_url, $lootbar_url_texte,
        $ig_enabled, $ig_nom_jeu, $ig_categories, $ig_developpeur, $ig_date_sortie, $ig_plateformes, $ig_url, $ig_prix, $ig_image
    ]);
}

function updateActualite($id, $titre, $texte, $date, $image, $masquerActu,
$lootbar_enabled, $lootbar_url, $lootbar_url_texte,
$ig_enabled, $ig_nom_jeu, $ig_categories, $ig_developpeur, $ig_date_sortie, $ig_plateformes, $ig_url, $ig_prix, $ig_image) {

    $pdo = getDatabase();

    $stmt = $pdo->prepare("
        UPDATE actualites SET
        titre = ?,
        texte = ?,
        date = ?,
        image = ?,
        masquer_actu = ?,

        lootbar_enabled = ?,
        lootbar_url = ?,
        lootbar_url_texte = ?,

        ig_enabled = ?,
        ig_nom_jeu = ?,
        ig_categories = ?,
        ig_developpeur = ?,
        ig_date_sortie = ?,
        ig_plateformes = ?,
        ig_url = ?,
        ig_prix = ?,
        ig_image = ?

        WHERE id = ?
    ");

    return $stmt->execute([
        $titre, $texte, $date, $image, $masquerActu,
        $lootbar_enabled, $lootbar_url, $lootbar_url_texte,
        $ig_enabled, $ig_nom_jeu, $ig_categories, $ig_developpeur, $ig_date_sortie, $ig_plateformes, $ig_url, $ig_prix, $ig_image,
        $id
    ]);
}

function deleteActualite($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("DELETE FROM actualites WHERE id = ?");
    return $stmt->execute([$id]);
}

// ============================================
// FONCTIONS CRUD JEUX
// ============================================

function createJeu($titre, $dateSortie, $image, $estPopulaire, $masquerPage, $estTop10, $top10_position, $categories = [], $plateformes = [], $description = '', $developpeurId = null) {
    $pdo = getDatabase();
    $dateSortie = empty($dateSortie) ? null : $dateSortie;
    $stmt = $pdo->prepare("INSERT INTO jeux (titre, date_sortie, image, est_populaire, masquer_page, top10, top10_position, description, developpeur_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titre, $dateSortie, $image, $estPopulaire, $masquerPage, $estTop10, $top10_position, $description, $developpeurId]);
    $jeuId = $pdo->lastInsertId();

    // Ajouter les catégories et plateformes
    setJeuCategories($jeuId, $categories);
    setJeuPlateformes($jeuId, $plateformes);

    return $jeuId;
}

function updateJeu($id, $titre, $dateSortie, $image, $estPopulaire, $masquerPage, $estTop10, $top10_position, $categories = [], $plateformes = [], $description = '', $developpeurId = null) {
    $pdo = getDatabase();
    $dateSortie = empty($dateSortie) ? null : $dateSortie;
    $stmt = $pdo->prepare("UPDATE jeux SET titre = ?, date_sortie = ?, image = ?, est_populaire = ?, masquer_page = ?, top10 = ?, top10_position = ?, description = ?, developpeur_id = ? WHERE id = ?");
    $result = $stmt->execute([$titre, $dateSortie, $image, $estPopulaire, $masquerPage, $estTop10, $top10_position, $description, $developpeurId, $id]);

    // Mettre à jour les catégories et plateformes
    setJeuCategories($id, $categories);
    setJeuPlateformes($id, $plateformes);

    return $result;
}

function deleteJeu($id) {
    $pdo = getDatabase();
    // Supprimer les liaisons
    $pdo->prepare("DELETE FROM jeux_categories WHERE jeu = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM jeux_plateformes WHERE jeu = ?")->execute([$id]);
    // Supprimer le jeu
    $stmt = $pdo->prepare("DELETE FROM jeux WHERE id = ?");
    return $stmt->execute([$id]);
}

function setJeuCategories($jeuId, $categories) {
    $pdo = getDatabase();
    $pdo->prepare("DELETE FROM jeux_categories WHERE jeu = ?")->execute([$jeuId]);
    if (!empty($categories)) {
        $stmt = $pdo->prepare("INSERT INTO jeux_categories (jeu, categorie) VALUES (?, ?)");
        foreach ($categories as $catId) {
            $stmt->execute([$jeuId, $catId]);
        }
    }
}

function setJeuPlateformes($jeuId, $plateformes) {
    $pdo = getDatabase();
    $pdo->prepare("DELETE FROM jeux_plateformes WHERE jeu = ?")->execute([$jeuId]);
    if (!empty($plateformes)) {
        $stmt = $pdo->prepare("INSERT INTO jeux_plateformes (jeu, plateforme) VALUES (?, ?)");
        foreach ($plateformes as $platId) {
            $stmt->execute([$jeuId, $platId]);
        }
    }
}

// ============================================
// FONCTIONS CRUD PERSONNAGES
// ============================================

function createPersonnage($jeuId, $nom, $description, $rarete, $role, $image, $image_tierlist) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("INSERT INTO personnages (jeu_id, nom, description, rarete, role, image, image_tierlist) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$jeuId, $nom, $description, $rarete, $role, $image, $image_tierlist]);
    return $pdo->lastInsertId();
}

function updatePersonnage($id, $jeuId, $nom, $description, $rarete, $role, $image, $image_tierlist) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("UPDATE personnages SET jeu_id = ?, nom = ?, description = ?, rarete = ?, role = ?, image = ?, image_tierlist = ? WHERE id = ?");
    return $stmt->execute([$jeuId, $nom, $description, $rarete, $role, $image, $image_tierlist, $id]);
}

function deletePersonnage($id) {
    $pdo = getDatabase();
    // Supprimer les liaisons tier
    $pdo->prepare("DELETE FROM personnages_tiers WHERE personnage_id = ?")->execute([$id]);
    // Supprimer le guide
    $pdo->prepare("DELETE FROM guides_personnages WHERE personnage_id = ?")->execute([$id]);
    // Supprimer le personnage
    $stmt = $pdo->prepare("DELETE FROM personnages WHERE id = ?");
    return $stmt->execute([$id]);
}

// ============================================
// FONCTIONS CRUD GUIDES PERSONNAGES
// ============================================

function saveGuidePersonnage($personnageId, $presentation, $ordre_skills, $doublons) {
    $pdo = getDatabase();

    $stmt = $pdo->prepare("SELECT id FROM guides_personnages WHERE personnage_id = ?");
    $stmt->execute([$personnageId]);

    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE guides_personnages SET presentation = ?, ordre_skills = ?, doublons = ? WHERE personnage_id = ?");
        return $stmt->execute([$presentation, $ordre_skills, $doublons, $personnageId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO guides_personnages (personnage_id, presentation, ordre_skills, doublons) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$personnageId, $presentation, $ordre_skills, $doublons]);
    }
}

/**
 * Sauvegarde des builds
 */
function savePersonnageBuildsRubrique($personnageId, $type, $buildsData, $buildsFiles = []) {
    $pdo = getDatabase();

    $stmt = $pdo->prepare("DELETE FROM personnage_builds WHERE personnage_id = ? AND type = ?");
    $stmt->execute([$personnageId, $type]);

    $position = 1;

    foreach ($buildsData as $buildIndex => $data) {
        $nom = trim($data['nom'] ?? '');
        $description = trim($data['description'] ?? '');
        $elements = $data['elements'] ?? [];
        $currentImages = $data['elements_current_image'] ?? [];
        $persos = array_filter($data['personnages'] ?? [], fn($p) => (int)$p > 0);

        $elementsFiltered = array_filter($elements, fn($e) => trim($e) !== '');

        if ($nom === '' && $description === '' && empty($elementsFiltered) && empty($persos)) {
            continue;
        }

        $stmt = $pdo->prepare("
            INSERT INTO personnage_builds (personnage_id, type, position, nom, description, date_maj)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$personnageId, $type, $position, $nom ?: null, $description ?: null]);

        $buildId = $pdo->lastInsertId();

        if ($type === 'equipe') {
            $ordre = 1;
            foreach ($persos as $persoId) {
                $stmt = $pdo->prepare("INSERT INTO personnage_build_personnages (build_id, personnage_id, ordre) VALUES (?, ?, ?)");
                $stmt->execute([$buildId, (int)$persoId, $ordre]);
                $ordre++;
            }
        } else {
            $ordre = 1;
            foreach ($elements as $elIndex => $elementNom) {
                $elementNom = trim($elementNom);
                if ($elementNom === '') continue;

                // Image actuelle par défaut (celle déjà en base, transmise en hidden)
                $imageFilename = $currentImages[$elIndex] ?? '';

                // Si un nouveau fichier a été uploadé pour cet élément, il remplace l'ancien
                $fileInfo = $buildsFiles[$buildIndex]['elements_images'][$elIndex] ?? null;
                if ($fileInfo && $fileInfo['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadImage($fileInfo, 'jeux');
                    if ($upload['success']) {
                        $imageFilename = $upload['filename'];
                    }
                }

                $stmt = $pdo->prepare("
                    INSERT INTO personnage_build_elements (build_id, nom, image, ordre)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$buildId, $elementNom, $imageFilename ?: null, $ordre]);
                $ordre++;
            }
        }

        $position++;
    }
}

// ============================================
// FONCTIONS CRUD CODES CADEAUX
// ============================================

function createCode($jeuId, $code, $recompense, $dateExpiration, $estActif) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("INSERT INTO codes_cadeaux (jeu_id, code, recompense, date_expiration, est_actif) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$jeuId, $code, $recompense, $dateExpiration ?: null, $estActif]);
}

function updateCode($id, $jeuId, $code, $recompense, $dateExpiration, $estActif) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("UPDATE codes_cadeaux SET jeu_id = ?, code = ?, recompense = ?, date_expiration = ?, est_actif = ? WHERE id = ?");
    return $stmt->execute([$jeuId, $code, $recompense, $dateExpiration ?: null, $estActif, $id]);
}

function deleteCode($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("DELETE FROM codes_cadeaux WHERE id = ?");
    return $stmt->execute([$id]);
}

function getCodeById($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM codes_cadeaux WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getAllCodes($jeuId = null) {
    $pdo = getDatabase();
    if ($jeuId) {
        $stmt = $pdo->prepare("SELECT c.*, j.titre as jeu_titre FROM codes_cadeaux c LEFT JOIN jeux j ON c.jeu_id = j.id WHERE c.jeu_id = ? ORDER BY c.est_actif DESC, c.date_expiration ASC");
        $stmt->execute([$jeuId]);
    } else {
        $stmt = $pdo->query("SELECT c.*, j.titre as jeu_titre FROM codes_cadeaux c LEFT JOIN jeux j ON c.jeu_id = j.id ORDER BY j.titre, c.est_actif DESC, c.date_expiration ASC");
    }
    return $stmt->fetchAll();
}

// ============================================
// FONCTIONS CRUD GUIDES JEU
// ============================================

function saveGuide($jeuId, $type, $contenu) {
    $pdo = getDatabase();

    // Vérifier si un guide existe déjà
    $stmt = $pdo->prepare("SELECT id FROM guides WHERE jeu_id = ? AND type = ?");
    $stmt->execute([$jeuId, $type]);

    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE guides SET contenu = ? WHERE jeu_id = ? AND type = ?");
        return $stmt->execute([$contenu, $jeuId, $type]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO guides (jeu_id, type, contenu) VALUES (?, ?, ?)");
        return $stmt->execute([$jeuId, $type, $contenu]);
    }
}

function getAllGuides() {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT g.*, j.titre as jeu_titre FROM guides g LEFT JOIN jeux j ON g.jeu_id = j.id ORDER BY j.titre, g.type");
    return $stmt->fetchAll();
}

// ============================================
// FONCTIONS CRUD GUIDES TEAMS
// ============================================

function getTeamsGuideByJeu($jeuId) {

    $pdo = getDatabase();

    $stmt = $pdo->prepare("SELECT * FROM teams_guides WHERE jeu_id = ?");
    $stmt->execute([$jeuId]);
    $guide = $stmt->fetch();

    if (!$guide) {
        return null;
    }

    $stmt = $pdo->prepare("
        SELECT *
        FROM teams_guide_teams
        WHERE guide_id = ?
        ORDER BY position ASC
    ");
    $stmt->execute([$guide['id']]);
    $teams = $stmt->fetchAll();

    foreach ($teams as &$team) {

        $stmt = $pdo->prepare("
            SELECT *
            FROM teams_guide_personnages
            WHERE team_id = ?
            ORDER BY position ASC
        ");
        $stmt->execute([$team['id']]);

        $team['personnages'] = $stmt->fetchAll();
    }

    $guide['teams'] = $teams;

    return $guide;
}

function createTeamsGuide($jeuId) {

    $pdo = getDatabase();

    $stmt = $pdo->prepare("INSERT INTO teams_guides (jeu_id) VALUES (?)");
    $stmt->execute([$jeuId]);

    $guideId = $pdo->lastInsertId();

    // créer les 10 teams
    for ($i = 1; $i <= 10; $i++) {

        $stmt = $pdo->prepare("
            INSERT INTO teams_guide_teams (guide_id, position)
            VALUES (?, ?)
        ");

        $stmt->execute([$guideId, $i]);
    }

    return $guideId;
}

function updateGuideTeam($teamId, $nom, $description) {

    $pdo = getDatabase();

    $stmt = $pdo->prepare("
        UPDATE teams_guide_teams
        SET nom = ?, description = ?
        WHERE id = ?
    ");

    return $stmt->execute([$nom, $description, $teamId]);
}

function saveTeamPersonnages($teamId, $personnages) {

    $pdo = getDatabase();

    $stmt = $pdo->prepare("DELETE FROM teams_guide_personnages WHERE team_id = ?");
    $stmt->execute([$teamId]);

    $position = 1;

    foreach ($personnages as $persoId) {

        if (!$persoId) continue;

        $stmt = $pdo->prepare("
            INSERT INTO teams_guide_personnages (team_id, personnage_id, position)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([$teamId, $persoId, $position]);

        $position++;
    }
}

// ============================================
// FONCTIONS CRUD PARTENAIRES
// ============================================

function createPartenaire($nom, $url, $image) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("INSERT INTO partenaires (nom, url, image) VALUES (?, ?, ?)");
    return $stmt->execute([$nom, $url, $image]);
}

function updatePartenaire($id, $nom, $url, $image) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("UPDATE partenaires SET nom = ?, url = ?, image = ? WHERE id = ?");
    return $stmt->execute([$nom, $url, $image, $id]);
}

function deletePartenaire($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("DELETE FROM partenaires WHERE id = ?");
    return $stmt->execute([$id]);
}

function getPartenaireById($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM partenaires WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ============================================
// FONCTIONS CRUD CATÉGORIES
// ============================================

function createCategorie($nom) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
    return $stmt->execute([$nom]);
}

function updateCategorie($id, $nom) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("UPDATE categories SET nom = ? WHERE id = ?");
    return $stmt->execute([$nom, $id]);
}

function deleteCategorie($id) {
    $pdo = getDatabase();
    $pdo->prepare("DELETE FROM jeux_categories WHERE categorie = ?")->execute([$id]);
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$id]);
}

function getCategorieById($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ============================================
// FONCTIONS CRUD PLATEFORMES
// ============================================

function createPlateforme($nom) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("INSERT INTO plateformes (nom) VALUES (?)");
    return $stmt->execute([$nom]);
}

function updatePlateforme($id, $nom) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("UPDATE plateformes SET nom = ? WHERE id = ?");
    return $stmt->execute([$nom, $id]);
}

function deletePlateforme($id) {
    $pdo = getDatabase();
    $pdo->prepare("DELETE FROM jeux_plateformes WHERE plateforme = ?")->execute([$id]);
    $stmt = $pdo->prepare("DELETE FROM plateformes WHERE id = ?");
    return $stmt->execute([$id]);
}

function getPlateformeById($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM plateformes WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ============================================
// FONCTIONS CRUD DÉVELOPPEURS
// ============================================

function createDeveloppeur($nom) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("INSERT INTO developpeurs (nom) VALUES (?)");
    return $stmt->execute([$nom]);
}

function updateDeveloppeur($id, $nom) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("UPDATE developpeurs SET nom = ? WHERE id = ?");
    return $stmt->execute([$nom, $id]);
}

function deleteDeveloppeur($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("DELETE FROM developpeurs WHERE id = ?");
    return $stmt->execute([$id]);
}

function getDeveloppeurById($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM developpeurs WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ============================================
// FONCTIONS RUBRIQUES PERSONNALISÉES (CRUD Admin)
// Les fonctions de lecture sont dans functions.php
// ============================================

/**
 * Récupère les rubriques personnalisées d'un jeu
 */
function getRubriquesPersonnaliseesJeu($jeuId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM rubriques_personnalisees WHERE jeu_id = ? ORDER BY id");
    $stmt->execute([$jeuId]);
    return $stmt->fetchAll();
}

/**
 * Crée une rubrique personnalisée et l'ajoute à la config du jeu
 */
function createRubriquePersonnalisee($jeuId, $nom, $icone = 'fas fa-star') {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("INSERT INTO rubriques_personnalisees (jeu_id, nom, icone) VALUES (?, ?, ?)");
    $stmt->execute([$jeuId, $nom, $icone]);
    $rubriqueId = $pdo->lastInsertId();

    // Ajouter automatiquement à la config du jeu (à la fin, active par défaut)
    $stmt = $pdo->prepare("SELECT COALESCE(MAX(ordre), 0) + 1 as next_ordre FROM config_rubriques_jeu WHERE jeu_id = ?");
    $stmt->execute([$jeuId]);
    $nextOrdre = $stmt->fetch()['next_ordre'];

    $stmt = $pdo->prepare("INSERT INTO config_rubriques_jeu (jeu_id, code_rubrique, rubrique_perso_id, est_active, ordre) VALUES (?, NULL, ?, 1, ?)");
    $stmt->execute([$jeuId, $rubriqueId, $nextOrdre]);

    return $rubriqueId;
}

/**
 * Met à jour une rubrique personnalisée
 */
function updateRubriquePersonnalisee($id, $nom, $icone) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("UPDATE rubriques_personnalisees SET nom = ?, icone = ? WHERE id = ?");
    return $stmt->execute([$nom, $icone, $id]);
}

/**
 * Supprime une rubrique personnalisée
 */
function deleteRubriquePersonnalisee($id) {
    $pdo = getDatabase();
    // Les valeurs et la config seront supprimées en cascade
    $stmt = $pdo->prepare("DELETE FROM rubriques_personnalisees WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Sauvegarde la configuration des rubriques pour un jeu
 */
function saveConfigRubriquesJeu($jeuId, $rubriques) {
    $pdo = getDatabase();

    // Supprimer l'ancienne config
    $stmt = $pdo->prepare("DELETE FROM config_rubriques_jeu WHERE jeu_id = ?");
    $stmt->execute([$jeuId]);

    // Insérer la nouvelle config
    $stmt = $pdo->prepare("INSERT INTO config_rubriques_jeu (jeu_id, code_rubrique, rubrique_perso_id, est_active, ordre) VALUES (?, ?, ?, ?, ?)");

    foreach ($rubriques as $ordre => $rubrique) {
        $codeRubrique = $rubrique['code_rubrique'] ?? null;
        $rubriquePersoId = $rubrique['rubrique_perso_id'] ?? null;
        $estActive = $rubrique['est_active'] ?? 1;

        $stmt->execute([$jeuId, $codeRubrique, $rubriquePersoId, $estActive, $ordre + 1]);
    }

    return true;
}

/**
 * Récupère la valeur d'une rubrique personnalisée pour un personnage
 */
function getValeurRubriquePersonnalisee($rubriqueId, $personnageId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT contenu FROM valeurs_rubriques_personnalisees WHERE rubrique_id = ? AND personnage_id = ?");
    $stmt->execute([$rubriqueId, $personnageId]);
    $result = $stmt->fetch();
    return $result ? $result['contenu'] : '';
}

/**
 * Sauvegarde la valeur d'une rubrique personnalisée pour un personnage
 */
function saveValeurRubriquePersonnalisee($rubriqueId, $personnageId, $contenu) {
    $pdo = getDatabase();

    // Utiliser INSERT ... ON DUPLICATE KEY UPDATE
    $stmt = $pdo->prepare("
        INSERT INTO valeurs_rubriques_personnalisees (rubrique_id, personnage_id, contenu)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE contenu = VALUES(contenu)
    ");
    return $stmt->execute([$rubriqueId, $personnageId, $contenu]);
}

// ============================================
// FONCTIONS STATISTIQUES
// ============================================

function getStats() {
    $pdo = getDatabase();
    return [
        'jeux' => $pdo->query("SELECT COUNT(*) FROM jeux")->fetchColumn(),
        'actualites' => $pdo->query("SELECT COUNT(*) FROM actualites")->fetchColumn(),
        'personnages' => $pdo->query("SELECT COUNT(*) FROM personnages")->fetchColumn(),
        'codes' => $pdo->query("SELECT COUNT(*) FROM codes_cadeaux WHERE est_actif = 1")->fetchColumn(),
        'partenaires' => $pdo->query("SELECT COUNT(*) FROM partenaires")->fetchColumn(),
    ];
}

// ============================================
// FONCTIONS UPLOAD IMAGE
// ============================================

function uploadImage($file, $folder) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Erreur lors de l\'upload'];
    }

    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'error' => 'Type de fichier non autorisé'];
    }

    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'Fichier trop volumineux (max 5MB)'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $destination = __DIR__ . '/../../images/' . $folder . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }

    return ['success' => false, 'error' => 'Erreur lors de l\'enregistrement'];
}

// ============================================
// Appelé sur $_FILES['builds'], ça donne une structure qui reflète exactement $_POST['builds'] : 
// $files['armes'][0]['elements_images'][2] = ['name'=>..., 'tmp_name'=>..., 'error'=>..., 'size'=>...] 
// — directement compatible avec uploadImage().
// ============================================

function reorganizeFiles(array $files) {
    $result = [];
    foreach ($files as $attr => $value) {
        reorganizeFilesRecursive($result, $value, $attr);
    }
    return $result;
}

function reorganizeFilesRecursive(&$result, $value, $attr, $path = []) {
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            reorganizeFilesRecursive($result, $v, $attr, array_merge($path, [$k]));
        }
    } else {
        $ref = &$result;
        foreach ($path as $p) {
            if (!isset($ref[$p])) $ref[$p] = [];
            $ref = &$ref[$p];
        }
        $ref[$attr] = $value;
    }
}

/**
 * Liste tous les liens utiles (admin), avec les jeux associés en plus
 */
function getLiensUtilesAdmin() {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT * FROM liens_utiles ORDER BY ordre ASC");
    $liens = $stmt->fetchAll();

    foreach ($liens as &$lien) {
        $lien['jeux'] = getJeuxDuLienUtile($lien['id']);
    }
    return $liens;
}

/**
 * Récupère les jeux associés à un lien utile spécifique
 */
function getJeuxDuLienUtile($lienId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("
        SELECT j.id, j.titre
        FROM jeux j
        INNER JOIN liens_utiles_jeux luj ON luj.jeu_id = j.id
        WHERE luj.lien_id = ?
        ORDER BY j.titre ASC
    ");
    $stmt->execute([$lienId]);
    return $stmt->fetchAll();
}

function getLienUtileById($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM liens_utiles WHERE id = ?");
    $stmt->execute([$id]);
    $lien = $stmt->fetch();
    if ($lien) {
        $lien['jeux_ids'] = array_column(getJeuxDuLienUtile($id), 'id');
    }
    return $lien;
}

/**
 * Crée un lien utile.
 * $jeuxIds : tableau d'IDs de jeux (ignoré si $estGenerique = true)
 */
function createLienUtile($titre, $description, $url, $codePromo, $image, $ordre, $estActif, $estGenerique, $jeuxIds = []) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("
        INSERT INTO liens_utiles (titre, description, url, code_promo, image, ordre, est_actif, est_generique)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$titre, $description, $url, $codePromo, $image, $ordre, $estActif, $estGenerique ? 1 : 0]);
    $id = $pdo->lastInsertId();

    if (!$estGenerique) {
        associerJeuxLienUtile($id, $jeuxIds);
    }

    return $id;
}

function updateLienUtile($id, $titre, $description, $url, $codePromo, $image, $ordre, $estActif, $estGenerique, $jeuxIds = []) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("
        UPDATE liens_utiles
        SET titre = ?, description = ?, url = ?, code_promo = ?, image = ?, ordre = ?, est_actif = ?, est_generique = ?
        WHERE id = ?
    ");
    $stmt->execute([$titre, $description, $url, $codePromo, $image, $ordre, $estActif, $estGenerique ? 1 : 0, $id]);

    // On réinitialise toujours les associations : soit vide (générique), soit la nouvelle sélection
    $pdo->prepare("DELETE FROM liens_utiles_jeux WHERE lien_id = ?")->execute([$id]);
    if (!$estGenerique) {
        associerJeuxLienUtile($id, $jeuxIds);
    }

    return true;
}

/**
 * Associe un lien utile à une liste de jeux (insertion en masse dans la table de liaison)
 */
function associerJeuxLienUtile($lienId, $jeuxIds) {
    if (empty($jeuxIds)) return;

    $pdo = getDatabase();
    $stmt = $pdo->prepare("INSERT IGNORE INTO liens_utiles_jeux (lien_id, jeu_id) VALUES (?, ?)");
    foreach ($jeuxIds as $jeuId) {
        $stmt->execute([$lienId, (int)$jeuId]);
    }
}

function deleteLienUtile($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("DELETE FROM liens_utiles WHERE id = ?");
    return $stmt->execute([$id]); // liens_utiles_jeux supprimé automatiquement (ON DELETE CASCADE)
}

/**
 * Active/désactive l'onglet Liens utiles pour un jeu (upsert)
 */
function setLiensUtilesActifJeu($jeuId, $actif) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("
        INSERT INTO config_liens_utiles (jeu_id, est_active)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE est_active = VALUES(est_active)
    ");
    return $stmt->execute([$jeuId, $actif ? 1 : 0]);
}