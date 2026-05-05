-- MySQL / MariaDB (script relançable)
-- Catégories Merch + FK + seed + stocks par tailles + fix DateTimeImmutable

-- ----------------------------
-- 1) Catégories Merch
-- ----------------------------
CREATE TABLE IF NOT EXISTS merch_category (
  id INT AUTO_INCREMENT NOT NULL,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL,
  UNIQUE KEY UNIQ_MERCH_CATEGORY_SLUG (slug),
  PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB;

-- Colonne category_id si absente
SET @col := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'merch'
    AND COLUMN_NAME = 'category_id'
);
SET @sql := IF(@col = 0, 'ALTER TABLE merch ADD COLUMN category_id INT DEFAULT NULL', 'SELECT \"category_id déjà présent\"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Index si absent
SET @idx := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'merch'
    AND INDEX_NAME = 'IDX_MERCH_CATEGORY_ID'
);
SET @sql := IF(@idx = 0, 'CREATE INDEX IDX_MERCH_CATEGORY_ID ON merch (category_id)', 'SELECT \"index IDX_MERCH_CATEGORY_ID déjà présent\"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK si absente (nom fixé: FK_MERCH_CATEGORY_ID)
SET @fk := (
  SELECT COUNT(*)
  FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'merch'
    AND CONSTRAINT_NAME = 'FK_MERCH_CATEGORY_ID'
);
SET @sql := IF(
  @fk = 0,
  'ALTER TABLE merch ADD CONSTRAINT FK_MERCH_CATEGORY_ID FOREIGN KEY (category_id) REFERENCES merch_category(id) ON DELETE SET NULL',
  'SELECT \"FK_MERCH_CATEGORY_ID déjà présente\"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Seed catégories (idempotent)
INSERT INTO merch_category (name, slug) VALUES
  ('T-shirt', 't-shirt'),
  ('Polo', 'polo'),
  ('Chemise', 'chemise'),
  ('Pull / Sweat', 'pull-sweat'),
  ('Veste', 'veste'),
  ('Manteau', 'manteau'),
  ('Couvre-Chef (Bob Casquette Bonnet/Cache-Cou)', 'couvre-chef'),
  ('Cartage', 'cartage'),
  ('Echarpe', 'echarpe'),
  ('Gadget (Drapeau, Briquet, Sacoche, Calendrier, Affiche, Lunettes, Sac Banane, Pins, Porte Clé, Sac, DVD, Livre)', 'gadget'),
  ('Patch', 'patch'),
  ('Short', 'short'),
  ('Chaussure', 'chaussure'),
  ('Stickers', 'stickers')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ----------------------------
-- 2) Stocks par tailles
-- ----------------------------
CREATE TABLE IF NOT EXISTS merch_stock (
  id INT AUTO_INCREMENT NOT NULL,
  merch_id INT NOT NULL,
  size VARCHAR(30) NOT NULL,
  quantity INT NOT NULL,
  UNIQUE KEY UNIQ_MERCH_STOCK_MERCH_SIZE (merch_id, size),
  KEY IDX_MERCH_STOCK_MERCH (merch_id),
  PRIMARY KEY (id),
  CONSTRAINT FK_MERCH_STOCK_MERCH
    FOREIGN KEY (merch_id) REFERENCES merch(id)
    ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB;

-- ----------------------------
-- 3) Fix DateTimeImmutable (optionnel)
-- ----------------------------
ALTER TABLE media MODIFY updated_at DATETIME NULL;
ALTER TABLE invite_code MODIFY expires_at DATETIME NOT NULL;
ALTER TABLE visit MODIFY visited_at DATETIME NOT NULL;