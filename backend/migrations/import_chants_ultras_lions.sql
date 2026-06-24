START TRANSACTION;

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'Allez Fleury Allez',
'(entrée des joueurs)

Loooololololo
Loooololololo
Loooololololo
ALLEEEEEEZ FLEURY ALLEZ

Loooololololo
Loooololololo
Loooololololo
ALLEEEEEZ FLEURY ALLEZ',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'Allez Fleury Allez');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'LOLOLOLOLOLOLO FC FLEURY',
'Lololololololo FC FLEURY
Lololololololo FC FLEURY
Lololololololo FC FLEURY
Lololololololo FC FLEURY',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'LOLOLOLOLOLOLO FC FLEURY');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'Dans l''Essonne y''a une flamme',
'Dans l’Essonne, y''a une flamme,
Rouge et noir, c’est notre âme,
Sur le terrain, nos guerriers,
Font vibrer, Fleury FC

Lololololololoooooo
Lololololololoooooo
Lololololololoooooo
ALLEZ FLEURY FC

Dans l’Essonne, y''a une flamme,
Rouge et noir, c’est notre âme,
Sur le terrain, nos guerriers,
Font vibrer, Fleury FC',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'Dans l''Essonne y''a une flamme');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'Pour toi je chanterai',
'Allez Fleury FC
Pour toi je chanterai
Vêtu de rouge et noir
Je me casserai la voiiiix

Allez Fleury allez
Allez Fleury allez allez
Allez Fleury allez
Allez Fleury allez allez',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'Pour toi je chanterai');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'ULTRAS LIONESS',
'Lolololololololo
Lolololololololo
Lolololololololo-lolo
ULTRAS LIONESS

Lolololololololo
Lolololololololo
Lolololololololo-lolo
ULTRAS LIONESS',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'ULTRAS LIONESS');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'POOOLOPOPO LIO-NESS',
'Pooooooooolopopoooooo
Popolopopo
Popolopopo
popolopopo
LIO-NESS

Polopopoooooo
Popolopopo
Popolopopo
popolopopo
LIO-NESS',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'POOOLOPOPO LIO-NESS');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'F - C - F - FLEURY',
'F
C
F
FLEURY (∞)',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'F - C - F - FLEURY');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'NOUS SOMMES LES LIONESS',
'Nous sommes les Lioness
Et nous chantons en cœur
Nous sommes les Lioness
Fidèles à nos couleurs

Lolololooooo
Lolololooooo
Lolololooooo
Lolololooooo',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'NOUS SOMMES LES LIONESS');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'ALLEZ ALLEZ',
'Allez Fleury
Allez allez allez
Allez allez allez
Allez allez allez allez allezzz

Allez Allez
Allez Fleu-ry
Allez allez
Allez allez
Allez allez
Allez Fleu-ry allez allez',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'ALLEZ ALLEZ');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'Oh Ultras Lioness',
'Lolololololo
Oh Ultras Lioness
Lololololololo
Oh Ultras Lioness',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'Oh Ultras Lioness');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'Nous nous sommes les Lioness',
'NOUS NOUS SOMMES LES LIONESS
NOUS NOUS SOMMES LES LIONESS
ET CE SOIR ON CHANTERA
ET CE SOIR ON CHANTERA
TOUT LE STADE EXPLOSERA
TOUT LE STADE EXPLOSERA
LORSQUE FLEURY MARQUERA
LORSQUE FLEURY MARQUERA
ALLEZ LE FC FLEURY
ALLEZ LE FC FLEURY',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'Nous nous sommes les Lioness');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'Je n''arrive plus à m''arrêter',
'Fleury FC
Fleury FC
Je chante pour toi je n’arrive plus à m’arrêter
Fleury FC
Fleury FC
Et le Virage s’enflammera juste pour toi

Lalalalaaaaaa
Lalalaaaaaa
Lalalalalalalalalalalaaaaaa',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'Je n''arrive plus à m''arrêter');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'Toujours à tes côtés',
'Allez allez
Allez Fleury allez
Toujours à tes côtés
On chante avec fierté
Allez Fleury allez

Allez allez
Allez Fleury allez
Toujours à tes côtés
On chante avec fierté
Allez Fleury allez',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'Toujours à tes côtés');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'Allez Fleury allez oh',
'Allez Fleury allez oh OUH AH
Allez Fleury allez oh OUH AH

Allez Fleury allez LIO-NESS
Allez Fleury allez LIO-NESS

Allez Fleury allez oh
Allez Fleury allez oh

Allez Fleury allez
Allez Fleury allez',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'Allez Fleury allez oh');

INSERT INTO chant (title, lyrics, audio_path, updated_at)
SELECT 'Liberté pour les ultras',
'Liberté pour les ultras
Liberté pour les ultras
Liberté pour les ultras
Liberté pour les ultras

Oooooooh
Ooooooh
Oooooooooooooooooooh
Oooooh
Ooooooh',
NULL,
NOW()
WHERE NOT EXISTS (SELECT 1 FROM chant WHERE title = 'Liberté pour les ultras');

COMMIT;
