<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260505230000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add association legal pages and cartage internal rules acceptance proof.';
    }

    public function up(Schema $schema): void
    {
        // Legacy SQL imports created these tables outside Doctrine. New installations
        // must create them before adding the internal rules columns.
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist(['cartage_qr_token'])) {
            $this->addSql("CREATE TABLE cartage_qr_token (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(120) NOT NULL, token VARCHAR(80) NOT NULL, amount DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, max_uses INT DEFAULT NULL, used_count INT NOT NULL, expires_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_CARTAGE_QR_TOKEN (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }
        if (!$schemaManager->tablesExist(['cartage_registration'])) {
            $this->addSql("CREATE TABLE cartage_registration (id INT AUTO_INCREMENT NOT NULL, qr_token_id INT DEFAULT NULL, first_name VARCHAR(120) NOT NULL, last_name VARCHAR(120) NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(40) NOT NULL, birth_date DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, city VARCHAR(120) DEFAULT NULL, shirt_size VARCHAR(30) DEFAULT NULL, polo_size VARCHAR(30) DEFAULT NULL, pull_size VARCHAR(30) DEFAULT NULL, sweat_size VARCHAR(30) DEFAULT NULL, jacket_size VARCHAR(30) DEFAULT NULL, short_size VARCHAR(30) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, payment_method VARCHAR(20) NOT NULL, status VARCHAR(30) NOT NULL, season VARCHAR(9) NOT NULL, checkout_reference VARCHAR(80) DEFAULT NULL, sumup_checkout_id VARCHAR(180) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', paid_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', validated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_CARTAGE_REGISTRATION_QR (qr_token_id), PRIMARY KEY(id), CONSTRAINT FK_CARTAGE_REGISTRATION_QR FOREIGN KEY (qr_token_id) REFERENCES cartage_qr_token (id) ON DELETE SET NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }
        $columns = array_change_key_case($schemaManager->listTableColumns('cartage_registration'), CASE_LOWER);
        if (!isset($columns['internal_rules_accepted_at'])) {
            $this->addSql("ALTER TABLE cartage_registration ADD internal_rules_accepted_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
        }
        if (!isset($columns['internal_rules_title'])) {
            $this->addSql('ALTER TABLE cartage_registration ADD internal_rules_title VARCHAR(180) DEFAULT NULL');
        }
        if (!isset($columns['internal_rules_content'])) {
            $this->addSql('ALTER TABLE cartage_registration ADD internal_rules_content LONGTEXT DEFAULT NULL');
        }

        $pages = [
            [
                'title' => 'Mentions legales',
                'slug' => 'mentions-legales',
                'content' => <<<'HTML'
<h2>Editeur du site</h2>
<p>Ce site est edite par l'association Ultras Lions Fleury, association regie par la loi du 1er juillet 1901. Renseignez ici, depuis EasyAdmin, l'adresse du siege social, le numero RNA, le responsable de publication et les coordonnees de contact de l'association.</p>
<h2>Hebergement</h2>
<p>Renseignez ici le nom, l'adresse et les coordonnees de l'hebergeur du site.</p>
<h2>Propriete intellectuelle</h2>
<p>Les textes, images, logos et contenus publies sur ce site appartiennent a l'association ou a leurs titulaires respectifs. Toute reutilisation doit etre autorisee au prealable.</p>
<h2>Contact</h2>
<p>Ajoutez ici l'email de contact officiel de l'association.</p>
HTML,
            ],
            [
                'title' => 'Politique de confidentialite',
                'slug' => 'politique-confidentialite',
                'content' => <<<'HTML'
<h2>Donnees collectees</h2>
<p>Le site peut collecter les informations necessaires a la gestion des comptes membres, du cartage, des commandes, des paiements, des inscriptions et des demandes envoyees via les formulaires.</p>
<h2>Finalites</h2>
<p>Ces donnees sont utilisees pour administrer l'association, gerer les adhesions, suivre les paiements, communiquer avec les membres et assurer la securite du site.</p>
<h2>Duree de conservation</h2>
<p>Les donnees sont conservees pendant la duree necessaire aux finalites indiquees, puis archivees ou supprimees selon les obligations applicables a l'association.</p>
<h2>Vos droits</h2>
<p>Conformement au RGPD et a la loi Informatique et Libertes, vous pouvez demander l'acces, la rectification, l'effacement, la limitation ou l'opposition au traitement de vos donnees. Ajoutez ici l'adresse email de contact. Vous pouvez egalement introduire une reclamation aupres de la CNIL.</p>
HTML,
            ],
            [
                'title' => 'Cookies',
                'slug' => 'cookies',
                'content' => <<<'HTML'
<h2>Cookies utilises</h2>
<p>Le site utilise des cookies strictement necessaires a son fonctionnement, notamment pour la session utilisateur, la securite, le panier et le parcours de paiement. Ces cookies ne necessitent pas de consentement prealable.</p>
<h2>Mesure d'audience et services tiers</h2>
<p>Si des outils de mesure d'audience, videos integrees, cartes ou contenus tiers sont ajoutes, completez cette page et mettez en place le recueil du consentement lorsque celui-ci est requis.</p>
HTML,
            ],
            [
                'title' => 'Reglement interieur',
                'slug' => 'reglement-interieur',
                'content' => <<<'HTML'
<h2>Reglement interieur de l'association</h2>
<p>Completez ici le reglement interieur depuis EasyAdmin. Ce texte doit preciser les regles applicables aux membres, les conditions de cartage, les engagements attendus, les modalites de paiement, les sanctions eventuelles et les contacts du bureau.</p>
<p>Avant de valider un cartage, chaque membre doit confirmer avoir lu et accepte ce reglement interieur.</p>
HTML,
            ],
        ];

        foreach ($pages as $page) {
            $this->addSql(
                'INSERT INTO page (title, slug, content, created_at, image_id)
                 SELECT :title, :slug, :content, NOW(), NULL
                 WHERE NOT EXISTS (SELECT 1 FROM page WHERE slug = :slug_check)',
                [
                    'title' => $page['title'],
                    'slug' => $page['slug'],
                    'content' => $page['content'],
                    'slug_check' => $page['slug'],
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cartage_registration DROP internal_rules_accepted_at, DROP internal_rules_title, DROP internal_rules_content');
        $this->addSql('DELETE FROM page WHERE slug IN (\'mentions-legales\', \'politique-confidentialite\', \'cookies\', \'reglement-interieur\')');
    }
}
