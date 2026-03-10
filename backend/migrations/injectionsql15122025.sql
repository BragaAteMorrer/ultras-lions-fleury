CREATE TABLE merch_category (
  id INT AUTO_INCREMENT NOT NULL,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL,
  UNIQUE INDEX UNIQ_MERCH_CATEGORY_SLUG (slug),
  PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

ALTER TABLE merch
  ADD COLUMN category_id INT DEFAULT NULL,
  ADD INDEX IDX_MERCH_CATEGORY_ID (category_id),
  ADD CONSTRAINT FK_MERCH_CATEGORY_ID
    FOREIGN KEY (category_id) REFERENCES merch_category (id)
    ON DELETE SET NULL;

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