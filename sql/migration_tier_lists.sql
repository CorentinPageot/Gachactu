-- Migration : Support de tier lists multiples par jeu (max 3)
-- A executer sur la base de donnees

-- 1. Creer la table tier_lists
CREATE TABLE IF NOT EXISTS tier_lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jeu_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL DEFAULT 'Tier List',
    ordre INT NOT NULL DEFAULT 1,
    FOREIGN KEY (jeu_id) REFERENCES jeux(id) ON DELETE CASCADE
);

-- 2. Ajouter la colonne tier_list_id a la table tiers
ALTER TABLE tiers ADD COLUMN tier_list_id INT NULL AFTER id;

-- 3. Migrer les donnees existantes : creer une tier_list par jeu existant
INSERT INTO tier_lists (jeu_id, nom, ordre)
SELECT DISTINCT jeu_id, 'Tier List', 1 FROM tiers;

-- 4. Mettre a jour les tiers existants avec le bon tier_list_id
UPDATE tiers t
INNER JOIN tier_lists tl ON tl.jeu_id = t.jeu_id
SET t.tier_list_id = tl.id;

-- 5. Rendre tier_list_id NOT NULL et ajouter la foreign key
ALTER TABLE tiers MODIFY COLUMN tier_list_id INT NOT NULL;
ALTER TABLE tiers ADD FOREIGN KEY (tier_list_id) REFERENCES tier_lists(id) ON DELETE CASCADE;

-- 6. Supprimer l'ancienne colonne jeu_id de tiers (devenue redondante)
-- NOTE : Le nom de la foreign key peut varier. Executez cette requete pour le trouver :
-- SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
-- WHERE TABLE_NAME = 'tiers' AND COLUMN_NAME = 'jeu_id' AND REFERENCED_TABLE_NAME = 'jeux';
-- Puis remplacez 'tiers_ibfk_1' par le nom trouve.
ALTER TABLE tiers DROP FOREIGN KEY tiers_ibfk_1;
ALTER TABLE tiers DROP COLUMN jeu_id;
