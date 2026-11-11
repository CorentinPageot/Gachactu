<?php
require_once __DIR__ . '/config.php';

// ============================================
// FONCTIONS ACTUALITÉS
// ============================================

/**
 * Récupère toutes les actualités non masquées triées par date décroissante
 */
function getActualites($limit = null, $offset = null) {
    $pdo = getDatabase();

    $sql = "SELECT * FROM actualites WHERE masquer_actu != 1 ORDER BY date DESC";

    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère une actualité par son ID
 */
function getActualiteById($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM actualites WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Compte les actualités (pour la pagination)
 */
function countActualites() {
    $pdo = getDatabase();

    $stmt = $pdo->query("SELECT COUNT(*) FROM actualites WHERE masquer_actu != 1");
    return $stmt->fetchColumn();
}

// ============================================
// FONCTIONS JEUX
// ============================================

/**
 * Récupère tous les jeux
 */
function getJeux($limit = null, $search = null) {
    $pdo = getDatabase();

    $sql = "SELECT * FROM jeux";
    $params = [];

    if (!empty($search)) {
        $sql .= " WHERE titre LIKE :search";
        $params['search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY date_sortie DESC";

    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/**
 * Récupère tous les jeux qui n'ont pas la page masquée
 */
function getJeuxAvecGuide($limit = null) {
    $pdo = getDatabase();
    $sql = "SELECT * FROM jeux WHERE masquer_page = 0 ORDER BY date_sortie DESC ";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Récupère tous les jeux
 */
function getTop10Jeux() {
    $pdo = getDatabase();
    $sql = "SELECT * FROM jeux WHERE top10 = 1 AND top10_position IS NOT NULL AND top10_position <> 0 ORDER BY top10_position ASC LIMIT 10";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Récupère les positions du top 10 déjà prises
 */
function getTop10PositionsOccupees($excludeId = null) {
    $pdo = getDatabase();

    $sql = "SELECT top10_position FROM jeux WHERE top10 = 1 AND top10_position IS NOT NULL";

    if ($excludeId) {
        $sql .= " AND id != " . (int)$excludeId;
    }

    $stmt = $pdo->query($sql);
    return array_column($stmt->fetchAll(), 'top10_position');
}

/**
 * Récupère les jeux populaires
 */
function getJeuxPopulaires($limit = null) {
    $pdo = getDatabase();

    $sql = "SELECT * FROM jeux WHERE est_populaire = 1 ORDER BY date_sortie DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Récupère les prochaines sorties (jeux non sortis ou récents)
 */
function getProchainsSorties($limit = null, $onlyWithDate = false, $onlyWithoutDate = false) {
    $pdo = getDatabase();

    if ($onlyWithDate) {
        $sql = "SELECT * FROM jeux WHERE date_sortie >= CURDATE() AND date_sortie IS NOT NULL ORDER BY date_sortie ASC";
    } elseif ($onlyWithoutDate) {
        $sql = "SELECT * FROM jeux WHERE date_sortie IS NULL ORDER BY titre ASC";
    } else {
        $sql = "SELECT * FROM jeux WHERE date_sortie >= CURDATE() OR date_sortie IS NULL ORDER BY CASE WHEN date_sortie IS NULL THEN 1 ELSE 0 END, date_sortie ASC";
    }

    if ($limit !== null) {
        $sql .= " LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    } else {
        $stmt = $pdo->prepare($sql);
    }

    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Récupère un jeu par son ID
 */
function getJeuById($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM jeux WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Récupère les catégories d'un jeu
 */
function getCategoriesJeu($jeuId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("
        SELECT c.*
        FROM categories c
        INNER JOIN jeux_categories jc ON c.id = jc.categorie
        WHERE jc.jeu = ?
    ");
    $stmt->execute([$jeuId]);
    return $stmt->fetchAll();
}

/**
 * Récupère toutes les catégories
 */
function getCategories() {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY nom");
    return $stmt->fetchAll();
}

/**
 * Récupère une categorie par son ID
 */
function getCategoriesByIdsFront($ids) {
    $pdo = getDatabase();
    // on vérifie que $ids n'est pas vide et est une chaîne
    if (empty($ids)) {
        return [];
    }
    // on transforme la chaîne "1,2,3" en tableau [1, 2, 3]
    $idsArray = explode(',', $ids);
    // On prépare une requête avec des placeholders pour chaque ID
    $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id IN ($placeholders)");
    $stmt->execute($idsArray);
    return $stmt->fetchAll();
}

/**
 * Récupère toutes les plateformes
 */
function getPlateformes() {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT * FROM plateformes ORDER BY nom");
    return $stmt->fetchAll();
}

/**
 * Récupère une plateforme par son ID
 */
function getPlateformesByIdsFront($ids) {
    $pdo = getDatabase();
    // on vérifie que $ids n'est pas vide et est une chaîne
    if (empty($ids)) {
        return [];
    }
    // on transforme la chaîne "1,2,3" en tableau [1, 2, 3]
    $idsArray = explode(',', $ids);
    // On prépare une requête avec des placeholders pour chaque ID
    $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
    $stmt = $pdo->prepare("SELECT * FROM plateformes WHERE id IN ($placeholders)");
    $stmt->execute($idsArray);
    return $stmt->fetchAll();
}

/**
 * Récupère les plateformes d'un jeu
 */
function getPlateformesJeu($jeuId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("
        SELECT p.*
        FROM plateformes p
        INNER JOIN jeux_plateformes jp ON p.id = jp.plateforme
        WHERE jp.jeu = ?
    ");
    $stmt->execute([$jeuId]);
    return $stmt->fetchAll();
}

/**
 * Récupère tous les développeurs
 */
function getDeveloppeurs() {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT * FROM developpeurs ORDER BY nom");
    return $stmt->fetchAll();
}

/**
 * Récupère un developpeur par son ID
 */
function getDeveloppeurByIdFront($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM developpeurs WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Récupère le développeur d'un jeu
 */
function getDeveloppeurJeu($jeuId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("
        SELECT d.*
        FROM developpeurs d
        INNER JOIN jeux j ON j.developpeur_id = d.id
        WHERE j.id = ?
    ");
    $stmt->execute([$jeuId]);
    return $stmt->fetch();
}

/**
 * Récupère si un jeu va bientôt sortir ou est sorti récemment
 */
function getTagsSortieJeu(?string $dateSortie): array {
    $tags = [];

    $today = new DateTime('today');
    
    if($dateSortie === null){
        return $tags;
    }
    $releaseDate = new DateTime($dateSortie);
    $diffDays = (int)$today->diff($releaseDate)->format('%r%a');

    // Jeu bientôt disponible (sortie dans les 30 jours)
    if ($diffDays > 0 && $diffDays <= 30) {
        $tags[] = 'Bientôt disponible';
    }

    // Jeu nouveau (sorti il y a 30 jours ou moins)
    if ($diffDays <= 0 && $diffDays >= -30) {
        $tags[] = 'Nouveau';
    }

    return $tags;
}

/**
 * Fonction de normalisation de la rareté
 */
function rareteToKey(string $rarete): string {
    $rarete = strtolower($rarete);
    return str_replace('*', 'stars', $rarete);
}

// ============================================
// FONCTIONS PERSONNAGES
// ============================================

/**
 * Récupère tous les personnages d'un jeu
 */
function getPersonnagesJeu($jeuId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM personnages WHERE jeu_id = ? ORDER BY nom");
    $stmt->execute([$jeuId]);
    return $stmt->fetchAll();
}

/**
 * Récupère un personnage par son ID
 */
function getPersonnageById($id) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT p.*, j.titre as jeu_titre, j.id as jeu_id FROM personnages p LEFT JOIN jeux j ON p.jeu_id = j.id WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Récupère le guide/build d'un personnage
 */
function getGuidePersonnage($personnageId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM guides_personnages WHERE personnage_id = ?");
    $stmt->execute([$personnageId]);
    return $stmt->fetch();
}

/**
 * Récupère le build d'un personnage (pour les catégories armes reco, equipe reco et equipements)
 */
function getPersonnageBuilds($personnageId, $type) {
    $pdo = getDatabase();

    $stmt = $pdo->prepare("
        SELECT *
        FROM personnage_builds
        WHERE personnage_id = ? AND type = ?
        ORDER BY position ASC
    ");
    $stmt->execute([$personnageId, $type]);
    $builds = $stmt->fetchAll();

    foreach ($builds as &$build) {
        if ($type === 'equipe') {
            $stmt = $pdo->prepare("
                SELECT p.*
                FROM personnage_build_personnages bp
                JOIN personnages p ON p.id = bp.personnage_id
                WHERE bp.build_id = ?
                ORDER BY bp.ordre ASC
            ");
            $stmt->execute([$build['id']]);
            $build['personnages'] = $stmt->fetchAll();
        } else {
            $stmt = $pdo->prepare("
                SELECT *
                FROM personnage_build_elements
                WHERE build_id = ?
                ORDER BY ordre ASC
            ");
            $stmt->execute([$build['id']]);
            $build['elements'] = $stmt->fetchAll();
        }
    }

    return $builds;
}

/**
 * Récupère le tier d'un personnage (depuis la première tier list)
 */
function getTierPersonnage($personnageId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("
        SELECT t.nom, t.couleur
        FROM tiers t
        INNER JOIN personnages_tiers pt ON t.id = pt.tier_id
        INNER JOIN tier_lists tl ON t.tier_list_id = tl.id
        WHERE pt.personnage_id = ?
        ORDER BY tl.ordre ASC
        LIMIT 1
    ");
    $stmt->execute([$personnageId]);
    return $stmt->fetch();
}

// ============================================
// FONCTIONS TIER LIST
// ============================================

/**
 * Récupère toutes les tier lists d'un jeu
 */
function getTierListsJeu($jeuId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM tier_lists WHERE jeu_id = ? ORDER BY ordre");
    $stmt->execute([$jeuId]);
    return $stmt->fetchAll();
}

/**
 * Récupère les tiers d'une tier list
 */
function getTiersTierList($tierListId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM tiers WHERE tier_list_id = ? ORDER BY ordre");
    $stmt->execute([$tierListId]);
    return $stmt->fetchAll();
}

/**
 * Récupère une tier list complète (tiers avec personnages) par son ID
 */
function getTierListComplete($tierListId) {
    $pdo = getDatabase();
    $tiers = getTiersTierList($tierListId);

    foreach ($tiers as &$tier) {
        $stmt = $pdo->prepare("
            SELECT p.*
            FROM personnages p
            INNER JOIN personnages_tiers pt ON p.id = pt.personnage_id
            WHERE pt.tier_id = ?
            ORDER BY p.nom
        ");
        $stmt->execute([$tier['id']]);
        $tier['personnages'] = $stmt->fetchAll();
    }

    return $tiers;
}

/**
 * Récupère la tier list complète d'un jeu (première tier list par défaut, ou par ID)
 */
function getTierListJeu($jeuId, $tierListId = null) {
    $tierLists = getTierListsJeu($jeuId);
    if (empty($tierLists)) return [];

    if ($tierListId) {
        // Vérifier que cette tier list appartient bien au jeu
        foreach ($tierLists as $tl) {
            if ($tl['id'] == $tierListId) {
                return getTierListComplete($tl['id']);
            }
        }
        return [];
    }

    return getTierListComplete($tierLists[0]['id']);
}

/**
 * Renomme une tier list
 */
function renameTierList($tierListId, $nom) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("UPDATE tier_lists SET nom = ? WHERE id = ?");
    return $stmt->execute([$nom, $tierListId]);
}

/**
 * Crée une nouvelle tier list pour un jeu (max 3)
 */
function createTierList($jeuId, $nom = 'Nouvelle Tier List') {
    $pdo = getDatabase();

    // Vérifier le nombre de tier lists existantes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tier_lists WHERE jeu_id = ?");
    $stmt->execute([$jeuId]);
    $count = $stmt->fetchColumn();

    if ($count >= 3) return false;

    $ordre = $count + 1;
    $stmt = $pdo->prepare("INSERT INTO tier_lists (jeu_id, nom, ordre) VALUES (?, ?, ?)");
    $stmt->execute([$jeuId, $nom, $ordre]);
    return $pdo->lastInsertId();
}

/**
 * Supprime une tier list
 */
function deleteTierList($tierListId) {
    $pdo = getDatabase();
    $pdo->beginTransaction();

    // Supprimer les personnages_tiers liés
    $stmt = $pdo->prepare("SELECT id FROM tiers WHERE tier_list_id = ?");
    $stmt->execute([$tierListId]);
    $tierIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($tierIds)) {
        $placeholders = implode(',', array_fill(0, count($tierIds), '?'));
        $pdo->prepare("DELETE FROM personnages_tiers WHERE tier_id IN ($placeholders)")->execute($tierIds);
        $pdo->prepare("DELETE FROM tiers WHERE id IN ($placeholders)")->execute($tierIds);
    }

    $pdo->prepare("DELETE FROM tier_lists WHERE id = ?")->execute([$tierListId]);
    $pdo->commit();
    return true;
}

// ============================================
// FONCTIONS CODES CADEAUX
// ============================================

/**
 * Récupère les codes cadeaux actifs d'un jeu
 */
function getCodesJeu($jeuId, $actifsUniquement = true) {
    $pdo = getDatabase();
    $sql = "SELECT * FROM codes_cadeaux WHERE jeu_id = ?";
    if ($actifsUniquement) {
        $sql .= " AND est_actif = 1 AND (date_expiration IS NULL OR date_expiration >= CURDATE())";
    }
    $sql .= " ORDER BY date_expiration ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jeuId]);
    return $stmt->fetchAll();
}

// ============================================
// FONCTIONS GUIDES
// ============================================

/**
 * Récupère un guide par jeu et type
 */
function getGuide($jeuId, $type) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM guides WHERE jeu_id = ? AND type = ?");
    $stmt->execute([$jeuId, $type]);
    return $stmt->fetch();
}

/**
 * Récupère un guide teams par jeu
 */
function getTeams($jeuId) {

    $pdo = getDatabase();

    $stmt = $pdo->prepare("
        SELECT *
        FROM teams_guides
        WHERE jeu_id = ?
    ");
    $stmt->execute([$jeuId]);
    $guide = $stmt->fetch();

    if (!$guide) {
        return false;
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
            SELECT p.*
            FROM teams_guide_personnages tp
            JOIN personnages p ON p.id = tp.personnage_id
            WHERE tp.team_id = ?
            ORDER BY tp.position ASC
        ");
        $stmt->execute([$team['id']]);

        $team['personnages'] = $stmt->fetchAll();
    }

    $guide['teams'] = $teams;

    return $guide;
}

// ============================================
// FONCTIONS PARTENAIRES
// ============================================

/**
 * Récupère tous les partenaires
 */
function getPartenaires() {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT * FROM partenaires ORDER BY nom");
    return $stmt->fetchAll();
}

// ============================================
// FONCTIONS UTILITAIRES
// ============================================

/**
 * Formate une date en français
 */
function formatDateFr($date) {
    if ($date === null || $date === '0000-00-00' || empty($date)) {
        return 'À venir';
    }

    $mois = [
        1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
        5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
        9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'
    ];

    $timestamp = strtotime($date);
    $jour = date('j', $timestamp);
    $moisNum = (int)date('n', $timestamp);
    $annee = date('Y', $timestamp);

    return $jour . ' ' . $mois[$moisNum] . ' ' . $annee;
}

/**
 * Retourne le statut de sortie d'un jeu
 */
function getStatutSortie($dateSortie) {
    if ($dateSortie === null || $dateSortie === '0000-00-00' || empty($dateSortie)) {
        return 'À venir';
    }

    $today = date('Y-m-d');
    if ($dateSortie <= $today) {
        return 'Disponible';
    }

    return formatDateFr($dateSortie);
}

/**
 * Affiche une grille de cartes de jeux en utilisant le template game-card.php
 * 
 * @param array $jeux - Tableau de jeux à afficher
 */
function renderGameCards($jeux) {
    foreach ($jeux as $jeu):
        $plateformes = getPlateformesJeu($jeu['id']);
        $categories  = getCategoriesJeu($jeu['id']);
        $tagsSortie  = getTagsSortieJeu($jeu['date_sortie']);
        $developpeur = getDeveloppeurJeu($jeu['id']);
        include __DIR__ . '/../templates/game-card.php';
    endforeach;
}

/**
 * Génère le chemin de l'image avec une image par défaut
 */
function getImagePath($image, $type = 'game') {
    $folders = [
        'game' => 'images/jeux/',
        'news' => 'images/actualites/',
        'partner' => 'images/partenaires/'
    ];

    $defaults = [
        'game' => 'images/jeux/placeholder.jpg',
        'news' => 'images/actualites/placeholder.jpg',
        'partner' => 'images/partenaires/placeholder.png'
    ];

    if (empty($image)) {
        return $defaults[$type] ?? $defaults['game'];
    }

    $folder = $folders[$type] ?? 'images/';
    return $folder . $image;
}

// ============================================
// FONCTIONS RUBRIQUES PERSONNALISÉES
// ============================================

/**
 * Liste des rubriques de base avec leurs infos
 */
function getRubriquesDeBase() {
    return [
        'presentation' => ['nom' => 'Présentation', 'icone' => 'fas fa-info-circle', 'champ' => 'presentation'],
        'skills' => ['nom' => 'Ordre des skills', 'icone' => 'fas fa-bolt', 'champ' => 'ordre_skills'],
        'armes' => ['nom' => 'Armes recommandées', 'icone' => 'fas fa-fire', 'champ' => 'armes_recommandees'],
        'equipe' => ['nom' => 'Équipe recommandée', 'icone' => 'fas fa-users', 'champ' => 'equipe_recommandee'],
        'equipements' => ['nom' => 'Équipements', 'icone' => 'fas fa-gem', 'champ' => 'equipements'],
        'doublons' => ['nom' => 'Doublons', 'icone' => 'fas fa-layer-group', 'champ' => 'doublons'],
    ];
}

/**
 * Récupère la configuration des rubriques pour un jeu
 * @param int $jeuId ID du jeu
 * @param bool $includeInactive Inclure les rubriques inactives (pour l'admin)
 */
function getConfigRubriquesJeu($jeuId, $includeInactive = false) {
    $pdo = getDatabase();

    $rubriquesDeBase = getRubriquesDeBase();
    $config = [];

    // Vérifier si la table existe
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM config_rubriques_jeu WHERE jeu_id = ?");
        $stmt->execute([$jeuId]);
    } catch (PDOException $e) {
        // Si la table n'existe pas, retourner uniquement les rubriques de base
        $ordre = 1;
        foreach ($rubriquesDeBase as $code => $info) {
            $config[] = [
                'id' => null,
                'code_rubrique' => $code,
                'rubrique_perso_id' => null,
                'nom' => $info['nom'],
                'icone' => $info['icone'],
                'champ' => $info['champ'],
                'est_active' => 1,
                'ordre' => $ordre++,
                'est_personnalisee' => false
            ];
        }
        return $config;
    }

    // Récupérer la config existante
    $sql = "
        SELECT c.*, rp.nom as nom_perso, rp.icone as icone_perso
        FROM config_rubriques_jeu c
        LEFT JOIN rubriques_personnalisees rp ON c.rubrique_perso_id = rp.id
        WHERE c.jeu_id = ?
    ";
    if (!$includeInactive) {
        $sql .= " AND c.est_active = 1";
    }
    $sql .= " ORDER BY c.ordre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jeuId]);
    $rows = $stmt->fetchAll();

    $codesExistants = [];
    $maxOrdre = 0;

    // Construire la config existante
    foreach ($rows as $row) {

        if ($row['ordre'] > $maxOrdre) {
            $maxOrdre = $row['ordre'];
        }

        if (!empty($row['code_rubrique'])) {
            // Rubrique de base
            $baseInfo = $rubriquesDeBase[$row['code_rubrique']] ?? null;

            if ($baseInfo) {
                $codesExistants[] = $row['code_rubrique'];

                $config[] = [
                    'id' => $row['id'],
                    'code_rubrique' => $row['code_rubrique'],
                    'rubrique_perso_id' => null,
                    'nom' => $baseInfo['nom'],
                    'icone' => $baseInfo['icone'],
                    'champ' => $baseInfo['champ'],
                    'est_active' => $row['est_active'],
                    'ordre' => $row['ordre'],
                    'est_personnalisee' => false
                ];
            }

        } elseif (!empty($row['rubrique_perso_id'])) {
            // Rubrique personnalisée
            $config[] = [
                'id' => $row['id'],
                'code_rubrique' => null,
                'rubrique_perso_id' => $row['rubrique_perso_id'],
                'nom' => $row['nom_perso'],
                'icone' => $row['icone_perso'],
                'champ' => null,
                'est_active' => $row['est_active'],
                'ordre' => $row['ordre'],
                'est_personnalisee' => true
            ];
        }
    }

    // Ajout automatique des rubriques de base manquantes
    foreach ($rubriquesDeBase as $code => $info) {
        if (!in_array($code, $codesExistants)) {
            $maxOrdre++;

            $config[] = [
                'id' => null,
                'code_rubrique' => $code,
                'rubrique_perso_id' => null,
                'nom' => $info['nom'],
                'icone' => $info['icone'],
                'champ' => $info['champ'],
                'est_active' => 1,
                'ordre' => $maxOrdre,
                'est_personnalisee' => false
            ];
        }
    }

    // Réordonner proprement
    usort($config, function($a, $b) {
        return $a['ordre'] <=> $b['ordre'];
    });

    return $config;
}

/**
 * Récupère toutes les valeurs des rubriques personnalisées pour un personnage
 */
function getValeursRubriquesPersonnalisees($personnageId) {
    $pdo = getDatabase();

    try {
        $stmt = $pdo->prepare("
            SELECT vrp.*, rp.nom, rp.icone
            FROM valeurs_rubriques_personnalisees vrp
            JOIN rubriques_personnalisees rp ON vrp.rubrique_id = rp.id
            WHERE vrp.personnage_id = ?
        ");
        $stmt->execute([$personnageId]);

        $valeurs = [];
        foreach ($stmt->fetchAll() as $row) {
            $valeurs[$row['rubrique_id']] = $row;
        }
        return $valeurs;
    } catch (PDOException $e) {
        // Table n'existe pas encore
        return [];
    }
}

// ============================================
// FONCTIONS LIENS UTILES
// ============================================

/**
 * Récupère les liens utiles à afficher pour un jeu donné :
 * - les liens rattachés spécifiquement à ce jeu (via liens_utiles_jeux)
 * - + les liens marqués génériques (affichés sur tous les jeux)
 */
function getLiensUtilesJeu($jeuId) {
    $pdo = getDatabase();

    $stmt = $pdo->prepare("
        SELECT DISTINCT lu.*
        FROM liens_utiles lu
        INNER JOIN liens_utiles_jeux luj ON luj.lien_id = lu.id
        WHERE luj.jeu_id = ? AND lu.est_actif = 1
        ORDER BY lu.ordre ASC
    ");
    $stmt->execute([$jeuId]);
    $liensSpecifiques = $stmt->fetchAll();

    $stmt = $pdo->query("
        SELECT * FROM liens_utiles
        WHERE est_generique = 1 AND est_actif = 1
        ORDER BY ordre ASC
    ");
    $liensGeneriques = $stmt->fetchAll();

    // Cumul : spécifiques d'abord, puis génériques
    return array_merge($liensSpecifiques, $liensGeneriques);
}

/**
 * Indique si l'onglet Liens utiles est activé pour un jeu (actif par défaut).
 */
function isLiensUtilesActif($jeuId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT est_active FROM config_liens_utiles WHERE jeu_id = ?");
    $stmt->execute([$jeuId]);
    $row = $stmt->fetch();
    return $row ? (bool)$row['est_active'] : true;
}