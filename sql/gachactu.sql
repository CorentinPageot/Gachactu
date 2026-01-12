-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 28 déc. 2025 à 18:28
-- Version du serveur : 8.0.31
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gachactu`
--

-- --------------------------------------------------------

--
-- Structure de la table `actualites`
--

DROP TABLE IF EXISTS `actualites`;
CREATE TABLE IF NOT EXISTS `actualites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `texte` text NOT NULL,
  `date` date NOT NULL,
  `image` varchar(200) NOT NULL,
  `masquer_actu` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(3, 'admin', '$2y$10$BQokPeH6snTPnT.H.iFMtewBqv2RlsPB3hIhKGYlCcoswuoP1WeuS', '2025-12-19 10:14:29');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(1, 'Action RPG'),
(2, 'Deckbuilder'),
(3, 'Idle RPG'),
(4, 'Cartes / Stratégie'),
(6, 'Global'),
(7, 'Open World RPG');

-- --------------------------------------------------------

--
-- Structure de la table `codes_cadeaux`
--

DROP TABLE IF EXISTS `codes_cadeaux`;
CREATE TABLE IF NOT EXISTS `codes_cadeaux` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jeu_id` int NOT NULL,
  `code` varchar(100) NOT NULL,
  `recompense` varchar(255) DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `est_actif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `jeu_id` (`jeu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `config_rubriques_jeu`
--

DROP TABLE IF EXISTS `config_rubriques_jeu`;
CREATE TABLE IF NOT EXISTS `config_rubriques_jeu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jeu_id` int NOT NULL,
  `code_rubrique` varchar(50) DEFAULT NULL,
  `rubrique_perso_id` int DEFAULT NULL,
  `est_active` tinyint(1) DEFAULT '1',
  `ordre` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_config` (`jeu_id`,`code_rubrique`,`rubrique_perso_id`),
  KEY `idx_config_jeu` (`jeu_id`),
  KEY `fk_config_rubrique_perso` (`rubrique_perso_id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `config_rubriques_jeu`
--

INSERT INTO `config_rubriques_jeu` (`id`, `jeu_id`, `code_rubrique`, `rubrique_perso_id`, `est_active`, `ordre`) VALUES
(73, 1, 'presentation', NULL, 1, 1),
(74, 1, 'skills', NULL, 1, 2),
(75, 1, 'armes', NULL, 1, 3),
(76, 1, 'equipe', NULL, 1, 4),
(77, 1, 'bonds', NULL, 1, 5),
(78, 1, 'set_stamp', NULL, 1, 6);

-- --------------------------------------------------------

--
-- Structure de la table `guides`
--

DROP TABLE IF EXISTS `guides`;
CREATE TABLE IF NOT EXISTS `guides` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jeu_id` int NOT NULL,
  `type` enum('debutant','reroll') NOT NULL,
  `contenu` text NOT NULL,
  `date_maj` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jeu_type` (`jeu_id`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `guides`
--

INSERT INTO `guides` (`id`, `jeu_id`, `type`, `contenu`, `date_maj`) VALUES
(1, 1, 'debutant', '', '2025-12-28 18:17:11'),
(2, 1, 'reroll', '', '2025-12-28 18:17:19');

-- --------------------------------------------------------

--
-- Structure de la table `guides_personnages`
--

DROP TABLE IF EXISTS `guides_personnages`;
CREATE TABLE IF NOT EXISTS `guides_personnages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `personnage_id` int NOT NULL,
  `presentation` text,
  `ordre_skills` text,
  `armes_recommandees` text,
  `set_stamp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `equipe_recommandee` text,
  `bonds` text,
  `date_maj` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personnage_id` (`personnage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `guides_personnages`
--

INSERT INTO `guides_personnages` (`id`, `personnage_id`, `presentation`, `ordre_skills`, `armes_recommandees`, `set_stamp`, `equipe_recommandee`, `bonds`, `date_maj`) VALUES
(1, 1, '<p>Ichigo Kurosaki est le protagoniste principal de Bleach. En tant que Shinigami rempla&ccedil;ant, il poss&egrave;de une puissance spirituelle exceptionnelle qui en fait l\'un des meilleurs DPS du jeu.</p>\r\n<p>Son kit est orient&eacute; vers les d&eacute;g&acirc;ts massifs en zone, ce qui le rend excellent pour le contenu PvE comme PvP.</p>', '<ul>\r\n<li><strong>Zangetsu</strong>- Meilleure arme pour maximiser les d&eacute;g&acirc;ts</li>\r\n<li><strong>Lame&nbsp;</strong>- Alternative viable avec bonus de critique</li>\r\n</ul>', '<ul>\r\n<li><strong>Zangetsu Ultime</strong> - Meilleure arme pour maximiser les d&eacute;g&acirc;ts</li>\r\n<li><strong>Lame du Hollow</strong> - Alternative viable avec bonus de critique</li>\r\n</ul>', '<ul>\r\n<li><strong>Set Flammes &Eacute;ternelles (4 pi&egrave;ces)</strong> - +40% d&eacute;g&acirc;ts Feu</li>\r\n<li><strong>Stats principales:</strong> ATK% / D&eacute;g&acirc;ts Feu / Taux Critique</li>\r\n</ul>', '<ul>\r\n<li><strong>Rukia Kuchiki</strong> - Support qui amplifie les d&eacute;g&acirc;ts</li>\r\n<li><strong>Orihime Inoue</strong> - Heal et boucliers</li>\r\n<li><strong>Yoruichi</strong> - Buff de vitesse pour l\'&eacute;quipe</li>\r\n</ul>', '<ul>\r\n<li><strong>Set Flammes</strong>&nbsp;- +40% d&eacute;g&acirc;ts Feu</li>\r\n<li><strong>Stats :</strong> ATK% / D&eacute;g&acirc;ts Feu / Taux Critique</li>\r\n</ul>', '2025-12-21 11:17:12'),
(2, 2, '<p>Rukia Kuchiki est une Shinigami noble de la famille Kuchiki. Spécialisée dans les techniques de glace, elle excelle en tant que support capable de contrôler le terrain.</p>', NULL, '<ul>\r\n<li><strong>Sode no Shirayuki</strong> - Augmente l\'efficacité des gels</li>\r\n<li><strong>Lame Ancestrale</strong> - Boost les soins d\'équipe</li>\r\n</ul>', '<ul>\r\n<li><strong>Set Glace Éternelle (4 pièces)</strong> - +30% durée des gels</li>\r\n<li><strong>Stats principales:</strong> PV% / DEF% / Efficacité</li>\r\n</ul>', '<ul>\r\n<li><strong>Ichigo Kurosaki</strong> - DPS principal</li>\r\n<li><strong>Toshiro Hitsugaya</strong> - Combo Glace dévastateur</li>\r\n<li><strong>Byakuya Kuchiki</strong> - Double DPS pour burst</li>\r\n</ul>', NULL, '2025-12-17 19:29:11');

-- --------------------------------------------------------

--
-- Structure de la table `jeux`
--

DROP TABLE IF EXISTS `jeux`;
CREATE TABLE IF NOT EXISTS `jeux` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `date_sortie` date DEFAULT NULL,
  `est_populaire` tinyint(1) NOT NULL,
  `image` varchar(200) NOT NULL,
  `masquer_page` tinyint(1) NOT NULL,
  `top10` tinyint(1) NOT NULL DEFAULT '0',
  `top10_position` tinyint DEFAULT NULL,
  `description` text DEFAULT NULL,
  `developpeur_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `jeux`
--

INSERT INTO `jeux` (`id`, `titre`, `date_sortie`, `est_populaire`, `image`, `masquer_page`, `top10`, `top10_position`) VALUES
(1, 'Bleach: Soul Resonance', '2025-11-21', 1, 'bleach.jpg', 0, 1, 1),
(2, 'The Seven Deadly Sins: Origin', '2026-01-28', 1, '7ds_origin.jpg', 0, 1, 2),
(9, 'Etheria: Restart', '2025-01-22', 0, 'etheria_restart.jpg', 0, 1, 5),
(4, 'Chaos Zero Nightmare', '2025-10-22', 0, 'chaos_zero_nightmare.webp', 1, 1, 4),
(10, 'Jujutsu Kaisen Phantom Parade', '2024-11-07', 0, 'jjk_phantom_parade.jpg', 0, 1, 3),
(7, 'Wuthering Waves', '2024-05-22', 0, 'wuthering_waves.jpg', 0, 1, 6),
(8, 'Arknights: Endfield', '2026-01-22', 0, 'arknights_endfield.jpg', 0, 1, 7),
(11, 'Honkai: Star Rail', '2023-04-26', 0, 'honkai_star_rail.png', 0, 1, 8),
(12, 'Genshin Impact', '2020-09-28', 0, 'genshin_impact.png', 0, 1, 9),
(13, 'Solo Leveling:Arise', '2024-05-08', 0, 'solo_leveling_arise.png', 0, 1, 10);

-- --------------------------------------------------------

--
-- Structure de la table `jeux_categories`
--

DROP TABLE IF EXISTS `jeux_categories`;
CREATE TABLE IF NOT EXISTS `jeux_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jeu` int NOT NULL DEFAULT '0',
  `categorie` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_categorie` (`categorie`),
  KEY `jeu` (`jeu`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `jeux_categories`
--

INSERT INTO `jeux_categories` (`id`, `jeu`, `categorie`) VALUES
(6, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `jeux_plateformes`
--

DROP TABLE IF EXISTS `jeux_plateformes`;
CREATE TABLE IF NOT EXISTS `jeux_plateformes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jeu` int NOT NULL DEFAULT '0',
  `plateforme` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `jeu` (`jeu`),
  KEY `fk_plateforme` (`plateforme`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `jeux_plateformes`
--

INSERT INTO `jeux_plateformes` (`id`, `jeu`, `plateforme`) VALUES
(5, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `partenaires`
--

DROP TABLE IF EXISTS `partenaires`;
CREATE TABLE IF NOT EXISTS `partenaires` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) NOT NULL,
  `url` varchar(200) NOT NULL,
  `image` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `partenaires`
--

INSERT INTO `partenaires` (`id`, `nom`, `url`, `image`) VALUES
(1, 'Instant Gaming', 'https://www.instant-gaming.com/?igr=LimuluTV', 'instant_gaming.png'),
(2, 'LDPlayer 9', 'https://leap.ldplayer.gg/T4EhONXHW', 'ldplayer9.png');

-- --------------------------------------------------------

--
-- Structure de la table `personnages`
--

DROP TABLE IF EXISTS `personnages`;
CREATE TABLE IF NOT EXISTS `personnages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jeu_id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `rarete` varchar(20) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `image_tierlist` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jeu_id` (`jeu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `personnages`
--

INSERT INTO `personnages` (`id`, `jeu_id`, `nom`, `description`, `rarete`, `role`, `image`, `image_tierlist`) VALUES
(1, 1, 'Ichigo Kurosaki (Initial)', 'Élève de première année au lycée Karakura, Ichigo est souvent pris pour un délinquant en raison de ses cheveux naturellement orange et de son expression solennelle. Malgré cette première impression, il a une âme douce. Bien qu\'il ait des sens spirituels aiguisés, il mène la vie d\'un lycéen ordinaire. Cependant, après sa rencontre avec Rukia Kuchiki, il devient un Shinigami suppléant et combat les Hollows.', 'SR', 'DPS', 'ichigo_initial.png', '694b0793eafb6.png'),
(2, 1, 'Rukia Kuchiki', 'Membre de la Brigade 13 de la Soul Society, elle a conféré à Ichigo les pouvoirs d\'un Shinigami afin de le protéger, lui et sa famille. Cependant, transférer de tels pouvoirs à des humains constitue un grave délit. À son retour à la Soul Society, elle a été condamnée à mort, mais Ichigo l\'a sauvée.', 'SR', 'Control / Support (Spiritual)', 'rukia.png', '694b08ef1dff9.png'),
(3, 1, 'Byakuya Kuchiki', 'Capitaine de la 6e division et 28e chef du clan Kuchiki, l\'un des quatre clans nobles de la Soul Society. Il est également le beau-frère de Rukia Kuchiki. En tant que noble, il se considère comme un modèle pour tous les Shinigamis, ce qui se traduit par un comportement apparemment impitoyable. Cependant, derrière cette façade se cache une âme compatissante.', 'SSR', 'Support / Sub-DPS (Slash)', 'byakuya.png', '694b087b4e03f.png'),
(4, 1, 'Nemu Kurotsuchi', '', 'SR', 'Support (Strike)', 'nemu.png', '694b096fe8aa3.png'),
(6, 1, 'Orihime Inoue', 'Camarade de classe d\'Ichigo, incroyablement gentille et un peu tête en l\'air. Elle possède le pouvoir unique Shunshunrikka, qui lui permet de rejeter divers phénomènes.', 'SR', 'Healer / Support', 'inoue.png', '694b08d9a2c0c.png'),
(7, 1, 'Renji Abarai', 'Lieutenant de la 6e escouade, Renji contraste fortement avec le capitaine Kuchiki par sa nature passionnée. Renji et Rukia étaient amis d\'enfance. Cependant, lorsque Rukia est entrée à l\'Académie des Shinigamis et a été adoptée par le clan Kuchiki, il a soutenu son adoption à contrecœur. Depuis lors, il s\'est consacré à dépasser le capitaine Kuchiki grâce à un entraînement acharné. Renji a pour passe-temps de collectionner des lunettes.', 'SR', 'DPS / Mid-Range (Slash)', 'renji.png', '694b08e4b2cb6.png'),
(8, 1, 'Uryū Ishida', 'Rare survivant de la lignée Quincy, lui et sa famille combattent les Hollows depuis des générations. Au départ, il nourrissait une intense hostilité envers les Shinigamis, allant même jusqu\'à s\'opposer à Ichigo, mais il finit par devenir son allié. Bien que très doué pour la couture et assez habile de ses mains, son sens de la mode laisse fortement à désirer.', 'SR', 'Ranged DPS / Utility (Thrust)', 'uryu.png', '694b0909990be.png'),
(9, 1, 'Yasutora Sado', 'Ami proche d\'Ichigo depuis le collège, il est connu pour son caractère réservé et son manque d\'expressions faciales. Sous cette apparence, il cache une nature douce, prête à défendre les plus faibles et étonnamment attirée par tout ce qui est mignon.', 'SR', 'DPS / Bruiser (Strike)', 'sado.png', '694b09283bca5.png'),
(10, 1, 'Kisuke Urahara', 'Se faisant passer pour le propriétaire de la confiserie Urahara\'s Shop, il vend divers outils aux Shinigamis et aide Rukia dans ses missions dans le monde des vivants. Ancien capitaine de la 12e division et premier directeur du département Recherche et Développement, il est à l\'origine de nombreuses inventions révolutionnaires, notamment le Hogyoku, mais a finalement été banni de la Soul Society.', 'SSR', 'Support (Global Buff)', 'urahara.png', '694b08b741822.png'),
(11, 1, 'Kaname Tōsen', 'Ancien capitaine de l\'escouade 9, ce guerrier aveugle qui rejette les conflits lutte avec ardeur pour la paix et l\'amour. Animé par son sens inébranlable de la justice, il ne montre aucune pitié, même envers ses propres camarades s\'ils sont reconnus coupables.', 'SSR', 'DPS / Leader (Thrust)', 'tosen.png', '694b08a7c3dca.png'),
(12, 1, 'Tsumugiya Ururu', 'La vendeuse de la boutique d\'Urahara. Malgré son apparence ordinaire, elle possède des talents de combattante exceptionnels.', 'SR', '', 'ururu.png', '694b0936a8fb1.png'),
(13, 1, 'Kusajishi Yachiru', 'Elle est lieutenant de la 11e division aux côtés de Kenpachi depuis avant même qu\'ils ne rejoignent les treize divisions de la Garde impériale. Malgré son apparence et son comportement enfantins, elle possède des capacités extraordinaires. Yachiru a souvent du mal à se souvenir des noms, alors elle invente des surnoms amusants pour ceux qui l\'entourent.', 'SR', 'Support (Strike)', 'yachiru.png', '694b08c1a5f89.png'),
(14, 1, 'Sajin Komamura', 'Le capitaine de l\'escouade 7, un géant imposant à tête de loup. Profondément loyal et reconnaissant envers le capitaine Yamamoto qui l\'a accepté, il est toujours prêt à se battre à tout prix pour rembourser la gentillesse qui lui a été témoignée.', 'SSR', 'Tank / Sub-DPS (Strike)', 'komamura.png', '694b08fc60a14.png'),
(15, 1, 'Madarame Ikkaku', 'Le 3e siège de la 11e division, connu pour ses compétences de combat exceptionnelles et sa passion pour la bataille. Il excelle dans les techniques Bankai, rivalisant avec les prouesses des lieutenants et même des capitaines d\'autres divisions. Par profond respect pour Kenpachi, il choisit de rester dans la 11e division en tant que 3e siège.', 'SSR', 'DPS (Thrust)', 'ikkaku.png', '694b08cc0f667.png'),
(16, 1, 'Yoruichi Shihouin', 'Chef du clan Shihoin, l\'un des quatre clans nobles de la Soul Society, et ancienne commandante en chef de la Force furtive. Connue sous le nom de Flash Master Yoruichi, elle est passée maître dans l\'art du Flash Step. Dans le monde des vivants, elle apparaît souvent sous la forme d\'un chat.', 'SSR', 'DPS (Slash)', 'yoruichi.png', '694b0919cb26e.png'),
(17, 1, 'Ichigo Kurosaki (Shikai)', 'Élève de première année au lycée Karakura, Ichigo est souvent pris pour un délinquant en raison de ses cheveux naturellement orange et de son expression solennelle. Malgré cette première impression, il a une âme douce. Bien qu\'il ait des sens spirituels aiguisés, il mène la vie d\'un lycéen ordinaire. Cependant, après sa rencontre avec Rukia Kuchiki, il devient un Shinigami suppléant et combat les Hollows.', 'SR', 'DPS (Slash)', 'ichigo_shikai.png', '694b089a994c0.png'),
(18, 1, 'Ichigo Kurosaki (Bankai)', 'Élève de première année au lycée Karakura, Ichigo est souvent pris pour un délinquant en raison de ses cheveux naturellement orange et de son expression solennelle. Malgré cette première impression, il a une âme douce. Bien qu\'il ait des sens spirituels aiguisés, il mène la vie d\'un lycéen ordinaire. Cependant, après sa rencontre avec Rukia Kuchiki, il devient un Shinigami suppléant et combat les Hollows.', 'SSR', 'DPS (Slash)', 'ichigo_bankai.png', '694b08865ec39.png');

-- --------------------------------------------------------

--
-- Structure de la table `personnages_tiers`
--

DROP TABLE IF EXISTS `personnages_tiers`;
CREATE TABLE IF NOT EXISTS `personnages_tiers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `personnage_id` int NOT NULL,
  `tier_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `personnage_id` (`personnage_id`),
  KEY `tier_id` (`tier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `personnages_tiers`
--

INSERT INTO `personnages_tiers` (`id`, `personnage_id`, `tier_id`) VALUES
(94, 18, 67),
(95, 3, 68),
(96, 11, 69),
(97, 6, 70),
(98, 9, 71);

-- --------------------------------------------------------

--
-- Structure de la table `plateformes`
--

DROP TABLE IF EXISTS `plateformes`;
CREATE TABLE IF NOT EXISTS `plateformes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `plateformes`
--

INSERT INTO `plateformes` (`id`, `nom`) VALUES
(1, 'Android'),
(2, 'IOS'),
(3, 'PC'),
(4, 'PS5');

-- --------------------------------------------------------

--
-- Structure de la table `developpeurs`
--

DROP TABLE IF EXISTS `developpeurs`;
CREATE TABLE IF NOT EXISTS `developpeurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rubriques_personnalisees`
--

DROP TABLE IF EXISTS `rubriques_personnalisees`;
CREATE TABLE IF NOT EXISTS `rubriques_personnalisees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jeu_id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `icone` varchar(50) DEFAULT 'fas fa-star',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_jeu_nom` (`jeu_id`,`nom`),
  KEY `idx_rubriques_jeu` (`jeu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tiers`
--

DROP TABLE IF EXISTS `tiers`;
CREATE TABLE IF NOT EXISTS `tiers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jeu_id` int NOT NULL,
  `nom` varchar(10) NOT NULL,
  `ordre` int NOT NULL DEFAULT '0',
  `couleur` varchar(7) DEFAULT '#CCCCCC',
  PRIMARY KEY (`id`),
  KEY `jeu_id` (`jeu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `tiers`
--

INSERT INTO `tiers` (`id`, `jeu_id`, `nom`, `ordre`, `couleur`) VALUES
(67, 1, 'APEX', 1, '#ff7f7f'),
(68, 1, 'T1', 2, '#ffbf7f'),
(69, 1, 'T2', 3, '#ffdf7f'),
(70, 1, 'T3', 4, '#ffff7f'),
(71, 1, 'T4', 5, '#bfff7f'),
(72, 1, 'T5', 6, '#7fbfff');

-- --------------------------------------------------------

--
-- Structure de la table `valeurs_rubriques_personnalisees`
--

DROP TABLE IF EXISTS `valeurs_rubriques_personnalisees`;
CREATE TABLE IF NOT EXISTS `valeurs_rubriques_personnalisees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rubrique_id` int NOT NULL,
  `personnage_id` int NOT NULL,
  `contenu` text,
  `date_maj` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_valeur` (`rubrique_id`,`personnage_id`),
  KEY `idx_valeurs_personnage` (`personnage_id`),
  KEY `idx_valeurs_rubrique` (`rubrique_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `config_rubriques_jeu`
--
ALTER TABLE `config_rubriques_jeu`
  ADD CONSTRAINT `fk_config_rubrique_perso` FOREIGN KEY (`rubrique_perso_id`) REFERENCES `rubriques_personnalisees` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `guides_personnages`
--
ALTER TABLE `guides_personnages`
  ADD CONSTRAINT `fk_guide_personnage` FOREIGN KEY (`personnage_id`) REFERENCES `personnages` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
