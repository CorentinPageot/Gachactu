<?php
require_once __DIR__ . '/config.php';

// ============================================
// FONCTIONS ACTUALITÉS
// ============================================

/**
 * Récupère toutes les actualités triées par date décroissante
 */
function getActualites($limit = null) {
    $pdo = getDatabase();
    $sql = "SELECT * FROM actualites ORDER BY date DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
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

// ============================================
// FONCTIONS JEUX
// ============================================

/**
 * Récupère tous les jeux
 */
function getJeux($limit = null) {
    $pdo = getDatabase();
    $sql = "SELECT * FROM jeux ORDER BY date_sortie DESC";
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
function getJeuxPopulaires($limit = 4) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM jeux WHERE est_populaire = 1 ORDER BY date_sortie DESC LIMIT ?");
    $stmt->execute([$limit]);
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
 * Récupère toutes les plateformes
 */
function getPlateformes() {
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT * FROM plateformes ORDER BY nom");
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
 * Récupère le tier d'un personnage
 */
function getTierPersonnage($personnageId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("
        SELECT t.nom, t.couleur
        FROM tiers t
        INNER JOIN personnages_tiers pt ON t.id = pt.tier_id
        WHERE pt.personnage_id = ?
    ");
    $stmt->execute([$personnageId]);
    return $stmt->fetch();
}

// ============================================
// FONCTIONS TIER LIST
// ============================================

/**
 * Récupère les tiers d'un jeu
 */
function getTiersJeu($jeuId) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("SELECT * FROM tiers WHERE jeu_id = ? ORDER BY ordre");
    $stmt->execute([$jeuId]);
    return $stmt->fetchAll();
}

/**
 * Récupère la tier list complète d'un jeu (tiers avec personnages)
 */
function getTierListJeu($jeuId) {
    $pdo = getDatabase();
    $tiers = getTiersJeu($jeuId);

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
        'bonds' => ['nom' => 'Bonds', 'icone' => 'fas fa-handshake', 'champ' => 'bonds'],
        'set_stamp' => ['nom' => 'Set stamp', 'icone' => 'fas fa-gem', 'champ' => 'set_stamp'],
    ];
}

/**
 * Récupère la configuration des rubriques pour un jeu
 * @param int $jeuId ID du jeu
 * @param bool $includeInactive Inclure les rubriques inactives (pour l'admin)
 */
function getConfigRubriquesJeu($jeuId, $includeInactive = false) {
    $pdo = getDatabase();

    // Vérifier si la table existe (pour éviter les erreurs si le SQL n'a pas été exécuté)
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM config_rubriques_jeu WHERE jeu_id = ?");
        $stmt->execute([$jeuId]);
    } catch (PDOException $e) {
        // Table n'existe pas, retourner les rubriques de base par défaut
        $rubriquesDeBase = getRubriquesDeBase();
        $config = [];
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

    if ($stmt->fetchColumn() == 0) {
        // Pas de config, retourner les rubriques de base par défaut
        $rubriquesDeBase = getRubriquesDeBase();
        $config = [];
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

    $rubriquesDeBase = getRubriquesDeBase();
    $config = [];

    foreach ($rows as $row) {
        if ($row['code_rubrique']) {
            // Rubrique de base
            $baseInfo = $rubriquesDeBase[$row['code_rubrique']] ?? null;
            if ($baseInfo) {
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
        } else if ($row['rubrique_perso_id']) {
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
