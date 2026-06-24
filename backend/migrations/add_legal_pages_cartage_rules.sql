ALTER TABLE `cartage_registration`
  ADD COLUMN IF NOT EXISTS `internal_rules_accepted_at` DATETIME DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `internal_rules_title` VARCHAR(180) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `internal_rules_content` LONGTEXT DEFAULT NULL;

INSERT INTO `page` (`title`, `slug`, `content`, `created_at`, `image_id`)
SELECT
  'Mentions legales',
  'mentions-legales',
  '<h2>Editeur du site</h2>
<p>Ce site est edite par l''association Ultras Lions Fleury, association regie par la loi du 1er juillet 1901. Renseignez ici, depuis EasyAdmin, l''adresse du siege social, le numero RNA, le responsable de publication et les coordonnees de contact de l''association.</p>
<h2>Hebergement</h2>
<p>Renseignez ici le nom, l''adresse et les coordonnees de l''hebergeur du site.</p>
<h2>Propriete intellectuelle</h2>
<p>Les textes, images, logos et contenus publies sur ce site appartiennent a l''association ou a leurs titulaires respectifs. Toute reutilisation doit etre autorisee au prealable.</p>
<h2>Contact</h2>
<p>Ajoutez ici l''email de contact officiel de l''association.</p>',
  NOW(),
  NULL
WHERE NOT EXISTS (
  SELECT 1 FROM `page` WHERE `slug` = 'mentions-legales'
);

INSERT INTO `page` (`title`, `slug`, `content`, `created_at`, `image_id`)
SELECT
  'Politique de confidentialite',
  'politique-confidentialite',
  '<h2>Donnees collectees</h2>
<p>Le site peut collecter les informations necessaires a la gestion des comptes membres, du cartage, des commandes, des paiements, des inscriptions et des demandes envoyees via les formulaires.</p>
<h2>Finalites</h2>
<p>Ces donnees sont utilisees pour administrer l''association, gerer les adhesions, suivre les paiements, communiquer avec les membres et assurer la securite du site.</p>
<h2>Duree de conservation</h2>
<p>Les donnees sont conservees pendant la duree necessaire aux finalites indiquees, puis archivees ou supprimees selon les obligations applicables a l''association.</p>
<h2>Vos droits</h2>
<p>Conformement au RGPD et a la loi Informatique et Libertes, vous pouvez demander l''acces, la rectification, l''effacement, la limitation ou l''opposition au traitement de vos donnees. Ajoutez ici l''adresse email de contact. Vous pouvez egalement introduire une reclamation aupres de la CNIL.</p>',
  NOW(),
  NULL
WHERE NOT EXISTS (
  SELECT 1 FROM `page` WHERE `slug` = 'politique-confidentialite'
);

INSERT INTO `page` (`title`, `slug`, `content`, `created_at`, `image_id`)
SELECT
  'Cookies',
  'cookies',
  '<h2>Cookies utilises</h2>
<p>Le site utilise des cookies strictement necessaires a son fonctionnement, notamment pour la session utilisateur, la securite, le panier et le parcours de paiement. Ces cookies ne necessitent pas de consentement prealable.</p>
<h2>Mesure d''audience et services tiers</h2>
<p>Si des outils de mesure d''audience, videos integrees, cartes ou contenus tiers sont ajoutes, completez cette page et mettez en place le recueil du consentement lorsque celui-ci est requis.</p>',
  NOW(),
  NULL
WHERE NOT EXISTS (
  SELECT 1 FROM `page` WHERE `slug` = 'cookies'
);

INSERT INTO `page` (`title`, `slug`, `content`, `created_at`, `image_id`)
SELECT
  'Reglement interieur',
  'reglement-interieur',
  '<h2>Reglement interieur de l''association</h2>
<p>Completez ici le reglement interieur depuis EasyAdmin. Ce texte doit preciser les regles applicables aux membres, les conditions de cartage, les engagements attendus, les modalites de paiement, les sanctions eventuelles et les contacts du bureau.</p>
<p>Avant de valider un cartage, chaque membre doit confirmer avoir lu et accepte ce reglement interieur.</p>',
  NOW(),
  NULL
WHERE NOT EXISTS (
  SELECT 1 FROM `page` WHERE `slug` = 'reglement-interieur'
);
