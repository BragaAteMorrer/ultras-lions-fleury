<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260425090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Import Ultras Lions chants from the legacy Wix site.';
    }

    public function up(Schema $schema): void
    {
        $chants = [
            [
                'title' => 'Allez Fleury Allez',
                'lyrics' => <<<'LYRICS'
(entrée des joueurs)

Loooololololo
Loooololololo
Loooololololo
ALLEEEEEEZ FLEURY ALLEZ

Loooololololo
Loooololololo
Loooololololo
ALLEEEEEZ FLEURY ALLEZ
LYRICS,
            ],
            [
                'title' => 'LOLOLOLOLOLOLO FC FLEURY',
                'lyrics' => <<<'LYRICS'
Lololololololo FC FLEURY
Lololololololo FC FLEURY
Lololololololo FC FLEURY
Lololololololo FC FLEURY
LYRICS,
            ],
            [
                'title' => "Dans l'Essonne y'a une flamme",
                'lyrics' => <<<'LYRICS'
Dans l’Essonne, y'a une flamme,
Rouge et noir, c’est notre âme,
Sur le terrain, nos guerriers,
Font vibrer, Fleury FC

Lololololololoooooo
Lololololololoooooo
Lololololololoooooo
ALLEZ FLEURY FC

Dans l’Essonne, y'a une flamme,
Rouge et noir, c’est notre âme,
Sur le terrain, nos guerriers,
Font vibrer, Fleury FC
LYRICS,
            ],
            [
                'title' => 'Pour toi je chanterai',
                'lyrics' => <<<'LYRICS'
Allez Fleury FC
Pour toi je chanterai
Vêtu de rouge et noir
Je me casserai la voiiiix

Allez Fleury allez
Allez Fleury allez allez
Allez Fleury allez
Allez Fleury allez allez
LYRICS,
            ],
            [
                'title' => 'ULTRAS LIONESS',
                'lyrics' => <<<'LYRICS'
Lolololololololo
Lolololololololo
Lolololololololo-lolo
ULTRAS LIONESS

Lolololololololo
Lolololololololo
Lolololololololo-lolo
ULTRAS LIONESS
LYRICS,
            ],
            [
                'title' => 'POOOLOPOPO LIO-NESS',
                'lyrics' => <<<'LYRICS'
Pooooooooolopopoooooo
Popolopopo
Popolopopo
popolopopo
LIO-NESS

Polopopoooooo
Popolopopo
Popolopopo
popolopopo
LIO-NESS
LYRICS,
            ],
            [
                'title' => 'F - C - F - FLEURY',
                'lyrics' => <<<'LYRICS'
F
C
F
FLEURY (∞)
LYRICS,
            ],
            [
                'title' => 'NOUS SOMMES LES LIONESS',
                'lyrics' => <<<'LYRICS'
Nous sommes les Lioness
Et nous chantons en cœur
Nous sommes les Lioness
Fidèles à nos couleurs

Lolololooooo
Lolololooooo
Lolololooooo
Lolololooooo
LYRICS,
            ],
            [
                'title' => 'ALLEZ ALLEZ',
                'lyrics' => <<<'LYRICS'
Allez Fleury
Allez allez allez
Allez allez allez
Allez allez allez allez allezzz

Allez Allez
Allez Fleu-ry
Allez allez
Allez allez
Allez allez
Allez Fleu-ry allez allez
LYRICS,
            ],
            [
                'title' => 'Oh Ultras Lioness',
                'lyrics' => <<<'LYRICS'
Lolololololo
Oh Ultras Lioness
Lololololololo
Oh Ultras Lioness
LYRICS,
            ],
            [
                'title' => 'Nous nous sommes les Lioness',
                'lyrics' => <<<'LYRICS'
NOUS NOUS SOMMES LES LIONESS
NOUS NOUS SOMMES LES LIONESS
ET CE SOIR ON CHANTERA
ET CE SOIR ON CHANTERA
TOUT LE STADE EXPLOSERA
TOUT LE STADE EXPLOSERA
LORSQUE FLEURY MARQUERA
LORSQUE FLEURY MARQUERA
ALLEZ LE FC FLEURY
ALLEZ LE FC FLEURY
LYRICS,
            ],
            [
                'title' => "Je n'arrive plus à m'arrêter",
                'lyrics' => <<<'LYRICS'
Fleury FC
Fleury FC
Je chante pour toi je n’arrive plus à m’arrêter
Fleury FC
Fleury FC
Et le Virage s’enflammera juste pour toi

Lalalalaaaaaa
Lalalaaaaaa
Lalalalalalalalalalalaaaaaa
LYRICS,
            ],
            [
                'title' => 'Toujours à tes côtés',
                'lyrics' => <<<'LYRICS'
Allez allez
Allez Fleury allez
Toujours à tes côtés
On chante avec fierté
Allez Fleury allez

Allez allez
Allez Fleury allez
Toujours à tes côtés
On chante avec fierté
Allez Fleury allez
LYRICS,
            ],
            [
                'title' => 'Allez Fleury allez oh',
                'lyrics' => <<<'LYRICS'
Allez Fleury allez oh OUH AH
Allez Fleury allez oh OUH AH

Allez Fleury allez LIO-NESS
Allez Fleury allez LIO-NESS

Allez Fleury allez oh
Allez Fleury allez oh

Allez Fleury allez
Allez Fleury allez
LYRICS,
            ],
            [
                'title' => 'Liberté pour les ultras',
                'lyrics' => <<<'LYRICS'
Liberté pour les ultras
Liberté pour les ultras
Liberté pour les ultras
Liberté pour les ultras

Oooooooh
Ooooooh
Oooooooooooooooooooh
Oooooh
Ooooooh
LYRICS,
            ],
        ];

        foreach ($chants as $chant) {
            $this->addSql(
                'INSERT INTO chant (title, lyrics, audio_path, updated_at)
                 SELECT :title, :lyrics, NULL, NOW()
                 WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = :title_check)',
                [
                    'title' => $chant['title'],
                    'lyrics' => $chant['lyrics'],
                    'title_check' => $chant['title'],
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $titles = [
            'Allez Fleury Allez',
            'LOLOLOLOLOLOLO FC FLEURY',
            "Dans l'Essonne y'a une flamme",
            'Pour toi je chanterai',
            'ULTRAS LIONESS',
            'POOOLOPOPO LIO-NESS',
            'F - C - F - FLEURY',
            'NOUS SOMMES LES LIONESS',
            'ALLEZ ALLEZ',
            'Oh Ultras Lioness',
            'Nous nous sommes les Lioness',
            "Je n'arrive plus à m'arrêter",
            'Toujours à tes côtés',
            'Allez Fleury allez oh',
            'Liberté pour les ultras',
        ];

        foreach ($titles as $title) {
            $this->addSql('DELETE FROM chant WHERE title = :title', ['title' => $title]);
        }
    }
}
