-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mar. 05 mai 2026 à 21:28
-- Version du serveur : 10.11.16-MariaDB-deb12
-- Version de PHP : 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ultra2785554`
--

-- --------------------------------------------------------

--
-- Structure de la table `billetweb_lead`
--

CREATE TABLE `billetweb_lead` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(120) NOT NULL,
  `last_name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `billetweb_lead`
--

INSERT INTO `billetweb_lead` (`id`, `ticket_id`, `user_id`, `first_name`, `last_name`, `email`, `created_at`) VALUES
(3, 2, NULL, 'Hicham', 'Jshaha', 'krng4166@gmail.com', '2026-04-25 12:23:18'),
(4, 2, NULL, 'Andrian\'Isa', 'RAYMOND', 'nisa.raymond@gmail.com', '2026-04-25 16:03:41'),
(6, 3, NULL, 'Jeremie', 'Pujol', 'jeremiepujol@hotmail.fr', '2026-04-27 22:56:31'),
(7, 3, 3, 'Nelson', 'Almeida', 'nene.almeida78@gmail.com', '2026-04-27 22:57:02'),
(10, 3, NULL, 'Maxime', 'Maxime', 'max031601@gmail.com', '2026-04-27 23:05:19'),
(11, 3, 5, 'Hicham', 'Habib', 'krng4166@gmail.com', '2026-04-28 00:04:20'),
(12, 3, NULL, 'Ultras', 'Lions', 'ultraslionsfleury@gmail.com', '2026-04-28 08:28:11'),
(13, 3, NULL, 'Hugo', 'DE CAMPOS', 'hugodecampos91@gmail.com', '2026-04-28 08:34:09'),
(14, 3, NULL, 'Arthur', 'Yor', 'junioryoro91125@icloud.com', '2026-04-28 13:29:43'),
(15, 3, 7, 'Sarah', 'Berbiche - - Piacentino', 'sarahbp1999@hotmail.fr', '2026-04-28 14:03:15'),
(16, 3, NULL, 'Aymeric', 'Capron', 'capron.aymeric@gmail.com', '2026-04-28 16:15:37'),
(17, 3, NULL, 'Arthur', 'Yoro', 'junioryoro91125@icloud.com', '2026-04-28 22:55:40'),
(18, 3, NULL, 'Clément', 'Secher', 'clemsec91@gmail.com', '2026-04-28 23:31:34'),
(19, 3, NULL, 'Loric', 'Carle', 'loric_carle@yahoo.fr', '2026-04-29 08:58:01'),
(20, 3, NULL, 'tilio', 'manojlovic', 'tilio.manojlovic11@gmail.com', '2026-04-29 13:39:34'),
(21, 3, NULL, 'Noah', 'Charpentier', 'noah08738@gmail.com', '2026-04-29 17:58:51'),
(22, 3, 6, 'Arthur', 'Yoro', 'junioryoro91125@icloud.com', '2026-04-29 18:49:54'),
(23, 3, NULL, 'Lana', 'Pradin', 'noah08738@gmail.com', '2026-04-29 19:20:40'),
(24, 3, NULL, 'tilio', 'manojlovic', 'tilio.manojlovic11@gmail.com', '2026-04-29 19:31:03'),
(25, 3, NULL, 'Clément', 'Auzet', 'clement.auzet@gmail.com', '2026-04-29 22:58:52'),
(26, 3, NULL, 'Amine', 'wy', 'jeanfk.u00@gmail.com', '2026-04-30 02:16:26'),
(27, 3, NULL, 'Arthur', 'Yoro', 'junioryoro91125@icloud.com', '2026-04-30 09:02:38'),
(28, 3, 3, 'Nelson', 'Almeida', 'nene.almeida78@gmail.com', '2026-04-30 20:55:33'),
(29, 3, 10, 'Mickael', 'Darbonnel', 'mickael.darbonnel@gmail.com', '2026-04-30 21:04:44'),
(30, 3, NULL, 'Stephane', 'Lanty', 'lantyste@hotmail.fr', '2026-04-30 23:59:08'),
(31, 3, NULL, 'Ousmane', 'Toure', 'otoure94500@gmail.com', '2026-05-01 12:15:28'),
(32, 3, NULL, 'Ahzhah', 'Shshshdh', 'krng4166@gmail.com', '2026-05-01 14:10:05'),
(33, 3, NULL, 'Sheh', 'Sbbsbe', 'krng4166@gmail.com', '2026-05-01 14:58:52'),
(34, 3, NULL, 'Hhhh', 'Hhbv', 'krng4166@gmail.com', '2026-05-01 14:59:36'),
(35, 3, NULL, 'Stephane', 'Lanty', 'lantyste@hotmail.fr', '2026-05-01 15:03:18'),
(36, 3, NULL, 'ULtras', 'Lions', 'ultraslionsfleury@gmail.com', '2026-05-01 15:03:47'),
(37, 3, 3, 'Nelson', 'Almeida', 'nene.almeida78@gmail.com', '2026-05-01 18:42:52');

-- --------------------------------------------------------

--
-- Structure de la table `cartage_qr_token`
--

CREATE TABLE `cartage_qr_token` (
  `id` int(11) NOT NULL,
  `label` varchar(120) NOT NULL,
  `token` varchar(80) NOT NULL,
  `amount` double NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `max_uses` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL,
  `expires_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cartage_qr_token`
--

INSERT INTO `cartage_qr_token` (`id`, `label`, `token`, `amount`, `enabled`, `max_uses`, `used_count`, `expires_at`, `created_at`) VALUES
(1, 'QR cartage principal', 'b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', 20, 1, NULL, 11, NULL, '2026-04-30 11:13:37');

-- --------------------------------------------------------

--
-- Structure de la table `cartage_registration`
--

CREATE TABLE `cartage_registration` (
  `id` int(11) NOT NULL,
  `qr_token_id` int(11) DEFAULT NULL,
  `first_name` varchar(120) NOT NULL,
  `last_name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `birth_date` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
  `address` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `shirt_size` varchar(30) DEFAULT NULL,
  `amount` double NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `status` varchar(30) NOT NULL,
  `checkout_reference` varchar(80) DEFAULT NULL,
  `sumup_checkout_id` varchar(180) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `paid_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `validated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `sweat_size` varchar(30) DEFAULT NULL,
  `jacket_size` varchar(30) DEFAULT NULL,
  `short_size` varchar(30) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cartage_registration`
--

INSERT INTO `cartage_registration` (`id`, `qr_token_id`, `first_name`, `last_name`, `email`, `phone`, `birth_date`, `address`, `postal_code`, `city`, `shirt_size`, `amount`, `payment_method`, `status`, `checkout_reference`, `sumup_checkout_id`, `created_at`, `paid_at`, `validated_at`, `sweat_size`, `jacket_size`, `short_size`, `password`) VALUES
(23, 1, 'Clément', 'Secher', 'clemsec91@gmail.com', '0635554233', NULL, '15 Rue Georges Charpak', '91220', 'Brétigny-sur-Orge', 'M', 20, 'online', 'paid_online', 'CARTAGE-D1D48CD7C4', '2f33f2e9-2acf-4c4e-8e24-a6ed4ef4261c', '2026-05-01 17:55:27', '2026-05-01 17:56:04', NULL, '', 'M', 'M', '$2y$13$vnjzjP2ibMb4RqaUr6I8M.zx1BeqEJGrW6Iimpx2SydaK.jHJNWbS'),
(24, 1, 'Andrian\'isa', 'Raymond', 'nisa.raymond@gmail.com', '0767207486', NULL, '89 Rue Rosa Parks', '91700', 'Fleury-Merogis', 'M', 20, 'cash', 'validated_cash', NULL, NULL, '2026-05-01 18:08:21', NULL, '2026-05-01 18:09:16', 'L', 'L', 'M', '$2y$13$5nED9SNLMmOnrGR0sai9euoz/7Imo0/r2rLSDZQIm1UW7rexP6IM6');

-- --------------------------------------------------------

--
-- Structure de la table `chant`
--

CREATE TABLE `chant` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `lyrics` longtext DEFAULT NULL,
  `audio_path` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `chant`
--

INSERT INTO `chant` (`id`, `title`, `lyrics`, `audio_path`, `updated_at`) VALUES
(1, 'Allez Fleury Allez (entrée des joueurs)', 'Loooololololo\r\nLoooololololo\r\nLoooololololo\r\nALLEEEEEEZ FLEURY ALLEZ\r\n\r\nLoooololololo\r\nLoooololololo\r\nLoooololololo\r\nALLEEEEEZ FLEURY ALLEZ', 'ae686e-ca945989d6bb4062b80f1ebee562b795-320-69ebef16ec8fc191513767.mp3', '2026-04-25 00:30:46'),
(2, 'LOLOLOLOLOLOLO FC FLEURY', 'Lololololololo FC FLEURY\r\nLololololololo FC FLEURY\r\nLololololololo FC FLEURY\r\nLololololololo FC FLEURY', 'ae686e-9bbf307b3f9d49d494c3edcd6f50a9f6-320-69ebef2fc2238635996702.mp3', '2026-04-25 00:31:11'),
(3, 'Dans l\'Essonne y\'a une flamme', 'Dans l’Essonne, y\'a une flamme,\r\nRouge et noir, c’est notre âme,\r\nSur le terrain, nos guerriers,\r\nFont vibrer, Fleury FC\r\n\r\nLololololololoooooo\r\nLololololololoooooo\r\nLololololololoooooo\r\nALLEZ FLEURY FC\r\n\r\nDans l’Essonne, y\'a une flamme,\r\nRouge et noir, c’est notre âme,\r\nSur le terrain, nos guerriers,\r\nFont vibrer, Fleury FC', 'ae686e-d4208d7f6a554ebfbb0433b5551657d5-320-69ebef478634c618471488.mp3', '2026-04-25 00:31:35'),
(4, 'Pour toi je chanterai', 'Allez Fleury FC\r\nPour toi je chanterai\r\nVêtu de rouge et noir\r\nJe me casserai la voiiiix\r\n\r\nAllez Fleury allez\r\nAllez Fleury allez allez\r\nAllez Fleury allez\r\nAllez Fleury allez allez', 'ae686e-fdbf39c625884e56b511a7fc28b62111-320-69ebef60990fc610322577.mp3', '2026-04-25 00:32:00'),
(5, 'ULTRAS LIONESS', 'Lolololololololo\r\nLolololololololo\r\nLolololololololo-lolo\r\nULTRAS LIONESS\r\n\r\nLolololololololo\r\nLolololololololo\r\nLolololololololo-lolo\r\nULTRAS LIONESS', 'ae686e-22aa025bd1ec42a9a146b0eec91fc02f-320-69ebef7d10119662271179.mp3', '2026-04-25 00:32:29'),
(6, 'POOOLOPOPO LIO-NESS', 'Pooooooooolopopoooooo\r\nPopolopopo\r\nPopolopopo\r\npopolopopo\r\nLIO-NESS\r\n\r\nPolopopoooooo\r\nPopolopopo\r\nPopolopopo\r\npopolopopo\r\nLIO-NESS', 'ae686e-1b510a632d714677905231870b5d1014-320-69ebef92d37b7076934259.mp3', '2026-04-25 00:32:50'),
(7, 'F - C - F - FLEURY', 'F\r\nC\r\nF\r\nFLEURY (∞)', 'ae686e-cfed89cf7d7e4125b272f541d8fb7030-320-69ebefa8e1a92471712018.mp3', '2026-04-25 00:33:12'),
(8, 'NOUS SOMMES LES LIONESS', 'Nous sommes les Lioness\r\nEt nous chantons en cœur\r\nNous sommes les Lioness\r\nFidèles à nos couleurs\r\n\r\nLolololooooo\r\nLolololooooo\r\nLolololooooo\r\nLolololooooo', 'ae686e-8d9dc1a88afd476b81271d82b059ddea-320-69ebefc0e9f5a379082583.mp3', '2026-04-25 00:33:36'),
(9, 'ALLEZ ALLEZ', 'Allez Fleury\r\nAllez allez allez\r\nAllez allez allez\r\nAllez allez allez allez allezzz\r\n\r\nAllez Allez\r\nAllez Fleu-ry\r\nAllez allez\r\nAllez allez\r\nAllez allez\r\nAllez Fleu-ry allez allez', 'ae686e-590729e21a15461b934216cc2b0c928f-320-69ebefd504422935893445.mp3', '2026-04-25 00:33:57'),
(10, 'Oh Ultras Lioness', 'Lolololololo\r\nOh Ultras Lioness\r\nLololololololo\r\nOh Ultras Lioness', 'ae686e-232c09683e9c4047aa09f8a155639d63-320-69ebefe9e3622666530888.mp3', '2026-04-25 00:34:17'),
(11, 'Nous nous sommes les Lioness', 'NOUS NOUS SOMMES LES LIONESS\nNOUS NOUS SOMMES LES LIONESS\nET CE SOIR ON CHANTERA\nET CE SOIR ON CHANTERA\nTOUT LE STADE EXPLOSERA\nTOUT LE STADE EXPLOSERA\nLORSQUE FLEURY MARQUERA\nLORSQUE FLEURY MARQUERA\nALLEZ LE FC FLEURY\nALLEZ LE FC FLEURY', NULL, '2026-04-25 00:28:42'),
(12, 'Je n\'arrive plus à m\'arrêter', 'Fleury FC\r\nFleury FC\r\nJe chante pour toi je n’arrive plus à m’arrêter\r\nFleury FC\r\nFleury FC\r\nEt le Virage s’enflammera juste pour toi\r\n\r\nLalalalaaaaaa\r\nLalalaaaaaa\r\nLalalalalalalalalalalaaaaaa', 'ae686e-767ad6a3e421456daddb41f68c8fa7c0-69ebf01dc826c802810175.mp3', '2026-04-25 00:35:09'),
(13, 'Toujours à tes côtés', 'Allez allez\r\nAllez Fleury allez\r\nToujours à tes côtés\r\nOn chante avec fierté\r\nAllez Fleury allez\r\n\r\nAllez allez\r\nAllez Fleury allez\r\nToujours à tes côtés\r\nOn chante avec fierté\r\nAllez Fleury allez', 'ae686e-3e3e7335cee54b7c9873cd4555d45282-320-69ebf0360203f815864626.mp3', '2026-04-25 00:35:34'),
(14, 'Allez Fleury allez oh', 'Allez Fleury allez oh OUH AH\nAllez Fleury allez oh OUH AH\n\nAllez Fleury allez LIO-NESS\nAllez Fleury allez LIO-NESS\n\nAllez Fleury allez oh\nAllez Fleury allez oh\n\nAllez Fleury allez\nAllez Fleury allez', NULL, '2026-04-25 00:28:42'),
(15, 'Liberté pour les ultras', 'Liberté pour les ultras\r\nLiberté pour les ultras\r\nLiberté pour les ultras\r\nLiberté pour les ultras\r\n\r\nOooooooh\r\nOoooooh\r\nOooooooooooooooooooh\r\nOooooh\r\nOoooooh', 'ae686e-2d248fd8a5054cf7bbdf1d67b1c2542d-320-69ebf056eeab0135086390.mp3', '2026-04-25 00:36:06');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250104192616', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20251201212207', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20251202215857', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20251202220409', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20251206013450', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20251215120000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20251215124500', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20251216120000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260213104945', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260311120000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260311123000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260316120000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260408120000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260408123000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260411120000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260424120000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260424123000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260424130000', '2026-04-24 09:03:15', 0),
('DoctrineMigrations\\Version20260424131000', '2026-04-24 09:03:15', 0);

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `date` datetime NOT NULL,
  `description` longtext DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `opponent` varchar(180) DEFAULT NULL,
  `match_location` varchar(12) DEFAULT NULL,
  `season` varchar(9) DEFAULT NULL,
  `journee` int(11) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event`
--

INSERT INTO `event` (`id`, `title`, `date`, `description`, `category_id`, `opponent`, `match_location`, `season`, `journee`, `image_id`) VALUES
(1, 'SM Caen - FC Fleury 91 - 06/02/2026', '2026-02-06 19:30:00', NULL, 2, 'SM Caen', 'exterieur', '2025-2026', 20, 7),
(2, 'FC Fleury 91 - Valenciennes FC - 17/04/2026', '2026-04-17 19:30:00', NULL, 2, 'Valenciennes FC', 'domicile', '2025-2026', 30, 13),
(3, 'FC Versailles - FC Fleury 91 - 20/03/2026', '2026-03-20 19:30:00', NULL, 2, 'FC Versailles', 'exterieur', '2025-2026', 26, 17);

-- --------------------------------------------------------

--
-- Structure de la table `event_category`
--

CREATE TABLE `event_category` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `section` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event_category`
--

INSERT INTO `event_category` (`id`, `name`, `slug`, `section`) VALUES
(1, 'Articles', 'articles', 'articles'),
(2, 'Photos de match', 'photos-de-match', 'photos_de_match'),
(3, 'Evenements', 'evenements', 'evenements'),
(4, 'Medias', 'medias', 'medias');

-- --------------------------------------------------------

--
-- Structure de la table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `description` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `group_page`
--

CREATE TABLE `group_page` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` longtext DEFAULT NULL,
  `histoire_text` longtext DEFAULT NULL,
  `mentalite_text` longtext DEFAULT NULL,
  `fonctionnement_text` longtext DEFAULT NULL,
  `rejoindre_text` longtext DEFAULT NULL,
  `se_carter_text` longtext DEFAULT NULL,
  `logo_id` int(11) DEFAULT NULL,
  `banner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `group_page`
--

INSERT INTO `group_page` (`id`, `name`, `description`, `histoire_text`, `mentalite_text`, `fonctionnement_text`, `rejoindre_text`, `se_carter_text`, `logo_id`, `banner_id`) VALUES
(1, 'Le Groupe', '<div>Qui est cet bande de cinglés dans le 91 squattant les gradins de Robert Bobin ?</div>', '<div>L’identité des ULTRAS LIONS, elle s’est construite assez naturellement.<br><br>Déjà, le choix du nom, il vient directement du club.<br>Les lions, c’est l’emblème du FC Fleury 91 depuis sa création dans les années 1970.<br>Du coup, ça s’est imposé assez vite. On voulait quelque chose de cohérent, de logique, qui parle immédiatement aux gens.<br><br>Mais l’identité du groupe ne s’arrête pas juste au nom.<br><br>On a aussi une influence qui vient clairement de la culture ultra, avec un regard tourné vers l’Italie, vers des groupes qui ont une vraie identité visuelle, parfois marquée, parfois dure dans les symboles.<br><br>Dans cette logique-là, il y a aussi une inspiration plus personnelle, notamment avec le film The Warriors (Les Guerriers de la nuit).<br>Dedans, il y a un gang qui s’appelle les Baseball Furies, avec une esthétique très forte.<br>C’est quelque chose qui nous a parlé, et l\'on s’en est inspiré pour construire l’emblème du groupe.<br><br>Et aujourd’hui, cet emblème fait partie intégrante de notre identité.<br><br>Mais au-delà du visuel, il y a surtout un ancrage très clair : l’Essonne.<br><br>On est fiers d’être du 91.<br>Et ça se ressent dans tout : notre mentalité, notre matériel, nos idées.<br>On essaie toujours de garder ce lien avec notre territoire.<br><br>Et surtout, il y a un point qui est fondamental pour nous : on est totalement apolitiques.<br><br>Peu importe d’où tu viens, ton quartier, ton milieu social.<br>Que tu viennes de Grigny, Corbeil, Evry ou de Bièvres, ça ne change rien.<br><br>Chez nous, tout le monde est le bienvenu.<br><br>La seule chose qu’on demande, <strong>c’est du respect</strong> : respect des membres du groupe, respect des autres supporters, respect du cadre.<br><br>Mais surtout, on refuse complètement de rattacher le groupe à une idéologie politique quelconque qui nous diviserait.<br><br>Notre seule ligne, notre seule “politique”, c’est le club.<br>Fleury et basta !</div>', '<div>Aujourd’hui, on est encore au début.<br><br></div><div>On parle d’un groupe qui tourne autour d’une dizaine, une quinzaine de personnes en tribune.<br>Mais l’objectif, il est clairement plus large que ça.<br><br></div><div>À terme, on veut entraîner tout Fleury, et même plus largement le 91, derrière le club.<br><br></div><div>Créer une vraie dynamique.<br>Une tribune qui compte.<br>Une identité forte.<br><br></div><div>On s’inscrit dans une mentalité ultra assez simple à résumer :<br>être là dans le malheur comme dans la gloire.<br><br></div><div>Ne jamais lâcher.<br>Ne jamais trahir la tribune.<br>Toujours être présent.<br><br></div><div>Peu importe où joue Fleury.<br><br></div><div>Que ce soit en Corse, dans l’Est, dans le Nord ou ailleurs, l’objectif, c’est d’être là le plus souvent possible.<br>Même si aujourd’hui, avec notre taille, ce n’est pas toujours simple.<br><br></div><div>Mais c’est cette direction qu’on veut prendre.<br><br></div><div>Faire en sorte que, partout où Fleury joue, il y ait une présence.<br>Même petite, mais réelle.<br><br></div>', '<div>Le fonctionnement du groupe repose avant tout sur les gens.<br><br></div><div>On est encore un groupe jeune, donc forcément, tout se construit petit à petit.<br>Mais dès le début, on a voulu quelque chose de simple : de la solidarité et de l’implication.<br><br></div><div>Quand il faut faire du matériel, préparer quelque chose, organiser un déplacement, chacun peut donner un coup de main.<br>C’est comme ça que le groupe avance.<br><br></div><div>Aujourd’hui, il y a un bureau composé de quatre personnes pour structurer tout ça.<br>Mais ça ne veut pas dire que les décisions viennent uniquement d’en haut.<br><br></div><div>Au contraire.<br><br></div><div>On ne demande pas à quelqu’un d’être là depuis des années pour donner son avis.<br>Quelqu’un qui arrive peut très bien proposer des idées rapidement.<br><br>Même si elles ne sont pas parfaites, ce n’est pas grave.<br>Ce qui compte, c’est l’implication.<br><br></div><div>Si une personne montre qu’elle a envie, qu’elle s’investit, elle aura toujours sa place dans le groupe.<br><br></div><div>L’objectif, c’est que chacun puisse évoluer et participer à la construction du groupe.</div>', '<div>Rejoindre le groupe, ce n’est pas juste venir se mettre en tribune.<br><br></div><div>C’est participer à quelque chose.<br><br></div><div>C’est chanter, s’impliquer, venir aux déplacements, aider sur les projets, même à petite échelle.<br><br></div><div>Mais c’est aussi rencontrer des gens, partager des moments, construire quelque chose ensemble.<br><br></div><div>On ne cherche pas des gens parfaits.<br>On cherche des gens motivés.<br><br>On ne naît pas Ultra, on le devient !</div>', '<div>Le système de cartage est volontairement simple.<br><br></div><div>Il sera mis en place à partir de septembre.<br>Et il se fera directement en tribune.<br><br></div><div>Concrètement, il faudra venir nous voir à la table de vente les jours de match.<br>Un code sera donné, et il permettra ensuite de créer son espace membre via le site.<br><br></div><div>Ce système permet de garder un lien direct avec les gens, tout en ayant une organisation un peu plus moderne derrière.<br><br></div><div>On a aussi nos réseaux sociaux, on est joignables facilement.<br><br></div><div>Mais honnêtement, le mieux reste toujours de venir nous voir directement en tribune.<br>C’est là qu’on comprend vraiment comment le groupe fonctionne, viens nous voir <strong>dans le bloc A2</strong> comprendre ce qu\'est la mentalité Lioness.<br><br></div>', 4, 5);

-- --------------------------------------------------------

--
-- Structure de la table `invite_code`
--

CREATE TABLE `invite_code` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `used` tinyint(1) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `invite_code`
--

INSERT INTO `invite_code` (`id`, `code`, `used`, `expires_at`) VALUES
(1, '15afbf2c285dc935836b4c10f1cbdeaf', 1, '2026-05-01 17:12:27'),
(2, '610a08669737bc9c45f6d6a24139b2df', 1, '2026-05-01 17:12:27'),
(3, '521a63135ee110d20f1259d6bc4edc58', 1, '2026-05-01 17:12:27'),
(4, 'a6c1578900f19662c93f95302f911fee', 0, '2026-05-01 17:12:27'),
(5, 'fe8465aac0226500376e09f2871e182f', 1, '2026-05-01 17:12:27'),
(6, '44ecd53c8a79a2762ae80a3682bc70d8', 1, '2026-05-01 17:12:00'),
(7, 'e89b5ff0d49b3ae6b80f68e732d694d3', 1, '2026-05-01 17:12:00'),
(8, '081ea14c691a700fae92468ff7e8f61a', 1, '2026-05-02 02:11:00'),
(9, '5cc3b894e2c24397d1b9fba3329c3ab5', 1, '2026-05-05 12:25:00');

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `gallery_id` int(11) DEFAULT NULL,
  `group_page_id` int(11) DEFAULT NULL,
  `merch_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `media`
--

INSERT INTO `media` (`id`, `path`, `alt`, `updated_at`, `gallery_id`, `group_page_id`, `merch_id`, `event_id`, `post_id`) VALUES
(1, 'logo-fc-fleury-69eb75aca136b262485100.png', NULL, '2026-04-24 15:52:44', NULL, NULL, NULL, NULL, NULL),
(2, 'echarpe-69eb7628a6b88314784174.png', NULL, '2026-04-24 15:54:48', NULL, NULL, NULL, NULL, NULL),
(3, 'sticker-69eb7aaab1259535644106.png', NULL, '2026-04-24 16:14:02', NULL, NULL, NULL, NULL, NULL),
(4, NULL, NULL, '2026-04-24 16:32:59', NULL, NULL, NULL, NULL, NULL),
(5, 'fleury-69eb7f4361371434521434.png', NULL, '2026-04-24 16:33:39', NULL, NULL, NULL, NULL, NULL),
(6, 'logo-fc-fleury-69eb81fcc42b8019760351.png', NULL, '2026-04-24 16:45:16', NULL, NULL, NULL, NULL, NULL),
(7, 'deplacement-j20-caen-fc-fleury-91-smcfcf91-nationale1-fleurymerogis-fleurymaville-ultras-3-69eb83ff7aa54220758515.jpg', NULL, '2026-04-24 16:53:51', NULL, NULL, NULL, NULL, NULL),
(8, 'deplacement-j20-caen-fc-fleury-91-smcfcf91-nationale1-fleurymerogis-fleurymaville-ultras-1-69eb83ff7c617926158450.jpg', NULL, '2026-04-24 16:53:51', NULL, NULL, NULL, 1, NULL),
(9, 'deplacement-j20-caen-fc-fleury-91-smcfcf91-nationale1-fleurymerogis-fleurymaville-ultras-2-69eb83ff7c99d228718015.jpg', NULL, '2026-04-24 16:53:51', NULL, NULL, NULL, 1, NULL),
(10, 'deplacement-j20-caen-fc-fleury-91-smcfcf91-nationale1-fleurymerogis-fleurymaville-ultras-4-69eb83ff7cd58841411102.jpg', NULL, '2026-04-24 16:53:51', NULL, NULL, NULL, 1, NULL),
(11, 'deplacement-j20-caen-fc-fleury-91-smcfcf91-nationale1-fleurymerogis-fleurymaville-ultras-5-69eb83ff7d172590207836.jpg', NULL, '2026-04-24 16:53:51', NULL, NULL, NULL, 1, NULL),
(12, 'deplacement-j20-caen-fc-fleury-91-smcfcf91-nationale1-fleurymerogis-fleurymaville-ultras-69eb83ff7d764557417938.jpg', NULL, '2026-04-24 16:53:51', NULL, NULL, NULL, 1, NULL),
(13, 'j30-fc-fleury-91-valenciennes-fc-ultraslionsfleury-teamfcf91-fcf91-fleurymerogis-fleuryma-3-69eb845590734346854145.jpg', NULL, '2026-04-24 16:55:17', NULL, NULL, NULL, NULL, NULL),
(14, 'j30-fc-fleury-91-valenciennes-fc-ultraslionsfleury-teamfcf91-fcf91-fleurymerogis-fleuryma-1-69eb8455911fb307098323.jpg', NULL, '2026-04-24 16:55:17', NULL, NULL, NULL, 2, NULL),
(15, 'j30-fc-fleury-91-valenciennes-fc-ultraslionsfleury-teamfcf91-fcf91-fleurymerogis-fleuryma-2-69eb8455917ff711429001.jpg', NULL, '2026-04-24 16:55:17', NULL, NULL, NULL, 2, NULL),
(16, 'j30-fc-fleury-91-valenciennes-fc-ultraslionsfleury-teamfcf91-fcf91-fleurymerogis-fleuryma-69eb845591d63539405372.jpg', NULL, '2026-04-24 16:55:17', NULL, NULL, NULL, 2, NULL),
(17, 'downloadgram-org-655372006-17864221635663913-9212036332289932066-n-69eb84ab7c1cd512820171.jpg', NULL, '2026-04-24 16:56:43', NULL, NULL, NULL, NULL, NULL),
(18, 'downloadgram-org-655976219-17864221692663913-988159654477925142-n-69eb84ab7cee0723136920.jpg', NULL, '2026-04-24 16:56:43', NULL, NULL, NULL, 3, NULL),
(19, 'downloadgram-org-656749591-17864221644663913-4968634217420182631-n-69eb84ab7d699696497665.jpg', NULL, '2026-04-24 16:56:43', NULL, NULL, NULL, 3, NULL),
(20, 'downloadgram-org-654632179-17864221662663913-4813100262010751879-n-69eb84ab7de4e402455017.jpg', NULL, '2026-04-24 16:56:43', NULL, NULL, NULL, 3, NULL),
(21, 'downloadgram-org-654593191-17864221659663913-7138926514034742158-n-69eb84ab7e475530504416.jpg', NULL, '2026-04-24 16:56:43', NULL, NULL, NULL, 3, NULL),
(22, 'downloadgram-org-655200240-17864221671663913-1767277147852279153-n-69eb84ab7e9fe489009491.jpg', NULL, '2026-04-24 16:56:43', NULL, NULL, NULL, 3, NULL),
(23, 'downloadgram-org-655301318-17864221713663913-3902205734951434484-n-69eb84ab7ee7a698848149.jpg', NULL, '2026-04-24 16:56:43', NULL, NULL, NULL, 3, NULL),
(24, 'hfjra77xiaaywr6-69eb88f08d60f846519852.jpg', NULL, '2026-04-24 17:14:56', NULL, NULL, NULL, NULL, NULL),
(25, 'hfeawkbwwaa5qtp-69eb89450f0a4965612674.jpg', NULL, '2026-04-24 17:16:21', NULL, NULL, NULL, NULL, NULL),
(26, 'hfeawc6wcaauhyu-69eb89450f93b642836539.jpg', NULL, '2026-04-24 17:16:21', NULL, NULL, NULL, NULL, 2),
(27, 'hfeawdjwyaagrx7-69eb89450fd77609848712.jpg', NULL, '2026-04-24 17:16:21', NULL, NULL, NULL, NULL, 2),
(28, 'ultras-lions-fleury-2026-furies-69ebf10fec15a162517665.mp4', NULL, '2026-04-25 00:39:11', NULL, NULL, NULL, NULL, NULL),
(29, 'logo-fc-fleury-69efcc32db345717355092.png', NULL, '2026-04-27 22:50:58', NULL, NULL, NULL, NULL, NULL),
(30, 'logo-fc-fleury-69f8b06ea0166244346175.png', NULL, '2026-05-04 16:42:54', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `merch`
--

CREATE TABLE `merch` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `price` double NOT NULL,
  `audience` varchar(20) NOT NULL DEFAULT 'public',
  `description` longtext DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `member_price` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `merch`
--

INSERT INTO `merch` (`id`, `title`, `price`, `audience`, `description`, `image_id`, `category_id`, `member_price`) VALUES
(1, 'Écharpe tartan \"Ultras Lions\"', 20, 'members', NULL, 2, 9, NULL),
(2, 'Sticker - Série 2026', 7, 'public', 'Enveloppe de 60 stickers (6x10) de la première série des Ultras Lions', 3, 14, 6);

-- --------------------------------------------------------

--
-- Structure de la table `merch_category`
--

CREATE TABLE `merch_category` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `merch_category`
--

INSERT INTO `merch_category` (`id`, `name`, `slug`) VALUES
(1, 'T-shirt', 't-shirt'),
(2, 'Polo', 'polo'),
(3, 'Chemise', 'chemise'),
(4, 'Pull / Sweat', 'pull-sweat'),
(5, 'Veste', 'veste'),
(6, 'Manteau', 'manteau'),
(7, 'Couvre-Chef (Bob Casquette Bonnet/Cache-Cou)', 'couvre-chef'),
(8, 'Cartage', 'cartage'),
(9, 'Echarpe', 'echarpe'),
(10, 'Gadget (Drapeau, Briquet, Sacoche, Calendrier, Affiche, Lunettes, Sac Banane, Pins, Porte Cle, Sac, DVD, Livre)', 'gadget'),
(11, 'Patch', 'patch'),
(12, 'Short', 'short'),
(13, 'Chaussure', 'chaussure'),
(14, 'Stickers', 'stickers');

-- --------------------------------------------------------

--
-- Structure de la table `merch_order`
--

CREATE TABLE `merch_order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `merch_id` int(11) NOT NULL,
  `email` varchar(180) DEFAULT NULL,
  `customer_first_name` varchar(120) DEFAULT NULL,
  `customer_last_name` varchar(120) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` double NOT NULL,
  `total_price` double NOT NULL,
  `note` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `executed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `merch_stock`
--

CREATE TABLE `merch_stock` (
  `id` int(11) NOT NULL,
  `merch_id` int(11) NOT NULL,
  `size` varchar(30) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `merch_stock`
--

INSERT INTO `merch_stock` (`id`, `merch_id`, `size`, `quantity`) VALUES
(1, 1, 'TU', 12),
(2, 2, 'TU', 50);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `page`
--

CREATE TABLE `page` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `content` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `image_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payment_checkout`
--

CREATE TABLE `payment_checkout` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `sumup_checkout_id` varchar(128) DEFAULT NULL,
  `checkout_reference` varchar(64) DEFAULT NULL,
  `amount` double NOT NULL,
  `currency` varchar(3) NOT NULL,
  `email` varchar(180) DEFAULT NULL,
  `customer_first_name` varchar(120) DEFAULT NULL,
  `customer_last_name` varchar(120) DEFAULT NULL,
  `cart` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`cart`)),
  `created_at` datetime NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `email_sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `payment_checkout`
--

INSERT INTO `payment_checkout` (`id`, `user_id`, `type`, `status`, `sumup_checkout_id`, `checkout_reference`, `amount`, `currency`, `email`, `customer_first_name`, `customer_last_name`, `cart`, `created_at`, `paid_at`, `processed_at`, `email_sent_at`) VALUES
(2, 2, 'mixed', 'paid', '4c7671b3-d4a4-4061-8abf-55229775387b', '8B503F563E05', 6, 'EUR', 'ultraslionsfleury@gmail.com', NULL, NULL, '[{\"type\":\"merch\",\"merch_id\":2,\"title\":\"Sticker - S\\u00e9rie 2026\",\"size\":\"TU\",\"quantity\":1,\"unit_price\":6.0,\"total\":6.0}]', '2026-04-24 16:19:07', '2026-04-24 16:19:29', '2026-04-24 16:19:29', '2026-04-24 16:19:30'),
(3, 2, 'mixed', 'paid', 'fff81f97-e644-4d7c-8849-a3d2231a6db7', 'AFF100E3AAC4', 6, 'EUR', 'ultraslionsfleury@gmail.com', NULL, NULL, '[{\"type\":\"merch\",\"merch_id\":2,\"title\":\"Sticker - S\\u00e9rie 2026\",\"size\":\"TU\",\"quantity\":1,\"unit_price\":6.0,\"total\":6.0}]', '2026-04-24 16:20:24', '2026-04-24 16:20:43', '2026-04-24 16:20:43', '2026-04-24 16:20:44'),
(4, 2, 'mixed', 'failed', NULL, 'A508997085D1', 0, 'EUR', 'ultraslionsfleury@gmail.com', NULL, NULL, '[{\"type\":\"ticket\",\"ticket_id\":1,\"title\":\"FC Fleury\",\"opponent\":\"Le Puy Foot\",\"quantity\":1,\"unit_price\":0.0,\"total\":0.0}]', '2026-04-24 16:37:21', NULL, NULL, NULL),
(5, 2, 'free', 'paid', NULL, '955283984D2A', 0, 'EUR', 'ultraslionsfleury@gmail.com', NULL, NULL, '[{\"type\":\"ticket\",\"ticket_id\":1,\"title\":\"FC Fleury\",\"opponent\":\"Le Puy Foot\",\"quantity\":1,\"unit_price\":0.0,\"total\":0.0}]', '2026-04-24 17:04:52', '2026-04-24 17:04:52', '2026-04-24 17:04:52', '2026-04-24 17:04:53'),
(6, 2, 'mixed', 'paid', '593dc020-2b91-4d7f-a9d5-efa67e9dce72', 'B73E779B41E3', 6, 'EUR', 'ultraslionsfleury@gmail.com', NULL, NULL, '[{\"type\":\"merch\",\"merch_id\":2,\"title\":\"Sticker - S\\u00e9rie 2026\",\"size\":\"TU\",\"quantity\":1,\"unit_price\":6.0,\"total\":6.0},{\"type\":\"ticket\",\"ticket_id\":1,\"title\":\"FC Fleury\",\"opponent\":\"Le Puy Foot\",\"quantity\":1,\"unit_price\":0.0,\"total\":0.0}]', '2026-04-24 17:05:35', '2026-04-24 17:05:58', '2026-04-24 17:05:58', '2026-04-24 17:05:58'),
(7, NULL, 'free', 'paid', NULL, '258AED28AE35', 0, 'EUR', 'max031601@gmail.com', 'Maxime ', 'Leclercq ', '[{\"type\":\"ticket\",\"ticket_id\":1,\"title\":\"FC Fleury\",\"opponent\":\"Le Puy Foot\",\"quantity\":1,\"unit_price\":0.0,\"total\":0.0}]', '2026-04-24 17:28:23', '2026-04-24 17:28:23', '2026-04-24 17:28:23', '2026-04-24 17:28:23'),
(8, NULL, 'mixed', 'paid', 'ded0b367-1bb9-4f2e-8a0d-3673664f0b9c', '8D8B150CB84B', 6, 'EUR', 'nene.almeida78@gmail.com', 'Nelson', 'Almeida', '[{\"type\":\"merch\",\"merch_id\":2,\"title\":\"Sticker - S\\u00e9rie 2026\",\"size\":\"TU\",\"quantity\":1,\"unit_price\":6.0,\"total\":6.0}]', '2026-04-25 01:45:22', '2026-04-25 01:45:39', '2026-04-25 01:45:39', '2026-04-25 01:45:40'),
(9, NULL, 'cash', 'pending', NULL, 'A899E3BB5A19', 6, 'EUR', 'nene.almeida78@gmail.com', 'Nelson', 'Almeida', '[{\"type\":\"merch\",\"merch_id\":2,\"title\":\"Sticker - S\\u00e9rie 2026\",\"size\":\"TU\",\"quantity\":1,\"unit_price\":6.0,\"total\":6.0}]', '2026-04-25 01:46:01', NULL, NULL, '2026-04-25 01:46:02'),
(10, 2, 'mixed', 'pending', '9106a796-ba60-4533-a106-256d58b2621d', '262811868553', 6, 'EUR', 'ultraslionsfleury@gmail.com', NULL, NULL, '[{\"type\":\"merch\",\"merch_id\":2,\"title\":\"Sticker - S\\u00e9rie 2026\",\"size\":\"TU\",\"quantity\":1,\"unit_price\":6.0,\"total\":6.0}]', '2026-04-25 01:56:16', NULL, NULL, NULL),
(11, 2, 'mixed', 'paid', '3f5379e6-d670-4e16-81f8-45170edc0f9b', 'B235CE229E69', 1, 'EUR', 'ultraslionsfleury@gmail.com', NULL, NULL, '[{\"type\":\"merch\",\"merch_id\":2,\"title\":\"Sticker - S\\u00e9rie 2026\",\"size\":\"TU\",\"quantity\":1,\"unit_price\":1.0,\"total\":1.0}]', '2026-04-25 01:57:56', '2026-04-25 01:58:50', '2026-04-25 01:58:50', '2026-04-25 01:58:51'),
(12, NULL, 'mixed', 'pending', 'b5bfdd57-b39a-4a9e-82cc-6ee1b27451a6', 'A5477F9EC419', 1, 'EUR', 'ultraslionsfleury@gmail.com', 'Nelson', 'Almeida ', '[{\"type\":\"merch\",\"merch_id\":2,\"title\":\"Sticker - S\\u00e9rie 2026\",\"size\":\"TU\",\"quantity\":1,\"unit_price\":1.0,\"total\":1.0}]', '2026-04-25 02:00:38', NULL, NULL, NULL),
(13, NULL, 'cartage', 'failed', '6f40578a-fd6a-4bcc-8b8c-8c39fada70ed', 'CARTAGE-0CFB49719E', 20, 'EUR', 'iliasch.pro@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":1,\"title\":\"Cartage\",\"first_name\":\"Ilias\",\"last_name\":\"CHIBOUT\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-04-30 11:54:42', NULL, NULL, NULL),
(14, NULL, 'cartage', 'paid', '6945cec3-777a-4a28-8d44-fc4f9acb72d6', 'CARTAGE-C1B130CE7C', 20, 'EUR', 'iliasch.pro@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":2,\"title\":\"Cartage\",\"first_name\":\"Ilias\",\"last_name\":\"Chibout\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-04-30 12:03:36', '2026-04-30 12:10:05', '2026-04-30 12:10:05', '2026-04-30 12:10:06'),
(15, NULL, 'cartage', 'pending', NULL, 'CARTAGE-B7DE6BF626', 20, 'EUR', 'iliasch.pro@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":3,\"title\":\"Cartage\",\"first_name\":\"Ilias\",\"last_name\":\"C\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-04-30 12:08:42', NULL, NULL, NULL),
(16, NULL, 'cartage', 'pending', NULL, 'CARTAGE-222604EBE4', 20, 'EUR', 'iliasch.pro@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":4,\"title\":\"Cartage\",\"first_name\":\"Ilias\",\"last_name\":\"C\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-04-30 12:08:46', NULL, NULL, NULL),
(17, NULL, 'cartage', 'pending', NULL, 'CARTAGE-BE6FA12A98', 20, 'EUR', 'iliasch.pro@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":5,\"title\":\"Cartage\",\"first_name\":\"Ilias\",\"last_name\":\"C\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-04-30 12:08:50', NULL, NULL, NULL),
(18, NULL, 'cartage', 'pending', NULL, 'CARTAGE-1ECC0F6A55', 20, 'EUR', 'iliasch.pro@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":6,\"title\":\"Cartage\",\"first_name\":\"Ilias\",\"last_name\":\"C\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-04-30 12:08:55', NULL, NULL, NULL),
(19, NULL, 'cartage', 'pending', NULL, 'CARTAGE-387FF4C5D5', 20, 'EUR', 'iliasch.pro@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":7,\"title\":\"Cartage\",\"first_name\":\"Ilias\",\"last_name\":\"CHIBOUT\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-04-30 12:09:17', NULL, NULL, NULL),
(20, NULL, 'cartage', 'pending', NULL, 'CARTAGE-30E41D2345', 20, 'EUR', 'iliasch.pro@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":8,\"title\":\"Cartage\",\"first_name\":\"Ilias\",\"last_name\":\"CHIBOUT\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-04-30 12:09:20', NULL, NULL, NULL),
(28, NULL, 'cartage', 'paid', 'e6184271-2464-4478-8d69-c02f07643d6f', 'CARTAGE-D2CE13871D', 20, 'EUR', 'creeper.psg.78@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":16,\"title\":\"Cartage\",\"first_name\":\"Nelson\",\"last_name\":\"Almeida\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-05-01 01:39:44', '2026-05-01 02:07:17', '2026-05-01 02:07:17', '2026-05-01 02:07:18'),
(29, NULL, 'cartage', 'paid', 'd6b42b0b-7ea3-4a1d-ab2b-65928cab45f6', 'CARTAGE-91156BFD17', 20, 'EUR', 'creeper.psg.78@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":17,\"title\":\"Cartage\",\"first_name\":\"Nelson\",\"last_name\":\"Almeida\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-05-01 01:51:57', '2026-05-01 01:52:18', '2026-05-01 01:52:18', '2026-05-01 01:52:19'),
(30, NULL, 'cartage', 'paid', 'c8434252-999c-4a9b-9f97-3c1558435cde', 'CARTAGE-A48B307C65', 20, 'EUR', 'creeper.psg.78@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":18,\"title\":\"Cartage\",\"first_name\":\"Nelson\",\"last_name\":\"Almeida\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-05-01 01:53:42', '2026-05-01 01:54:02', '2026-05-01 01:54:02', '2026-05-01 01:54:03'),
(31, NULL, 'cartage', 'paid', 'cc9902f4-8220-4136-9cc4-7aaf75760ce6', 'CARTAGE-F218E6C5BE', 20, 'EUR', 'creeper.psg.78@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":19,\"title\":\"Cartage\",\"first_name\":\"Nelson\",\"last_name\":\"Almeida\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-05-01 01:57:46', '2026-05-01 01:58:12', '2026-05-01 01:58:12', NULL),
(32, NULL, 'cartage', 'paid', '7ccb5045-795e-45b3-bdd3-551ef57c39eb', 'CARTAGE-FED7191583', 20, 'EUR', 'creeper.psg.78@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":20,\"title\":\"Cartage\",\"first_name\":\"Nelson\",\"last_name\":\"Almeida\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-05-01 02:09:49', '2026-05-01 02:10:09', '2026-05-01 02:10:09', '2026-05-01 02:10:11'),
(33, NULL, 'cartage', 'paid', 'e50cd3ea-9275-4a24-b9f2-b95753459e71', 'CARTAGE-AFC2AE9B10', 20, 'EUR', 'creeper.psg.78@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":21,\"title\":\"Cartage\",\"first_name\":\"Nelson\",\"last_name\":\"Almeida\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-05-01 02:19:27', '2026-05-01 02:19:50', '2026-05-01 02:19:50', '2026-05-01 02:19:51'),
(34, NULL, 'cartage', 'paid', '2f33f2e9-2acf-4c4e-8e24-a6ed4ef4261c', 'CARTAGE-D1D48CD7C4', 20, 'EUR', 'clemsec91@gmail.com', NULL, NULL, '[{\"cartage_registration_id\":23,\"title\":\"Cartage\",\"first_name\":\"Cl\\u00e9ment\",\"last_name\":\"Secher\",\"quantity\":1,\"unit_price\":20.0,\"total\":20.0}]', '2026-05-01 17:55:27', '2026-05-01 17:56:04', '2026-05-01 17:56:04', '2026-05-01 17:56:05'),
(35, NULL, 'mixed', 'pending', '920047cc-145c-4440-8132-f6a6cc4f0cbd', '62FE226C2ABF', 7, 'EUR', 'kohsey.dufour@gmail.com', 'Kohsey', 'DUFOUR', '[{\"type\":\"merch\",\"merch_id\":2,\"title\":\"Sticker - S\\u00e9rie 2026\",\"size\":\"TU\",\"quantity\":1,\"unit_price\":7.0,\"total\":7.0}]', '2026-05-04 15:51:06', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `content` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id`, `title`, `slug`, `content`, `created_at`, `updated_at`, `image_id`, `category_id`) VALUES
(1, 'FC Fleury 91 : Le Club de l\'Essonne', 'fc-fleury-91-le-club-de-l-essonne', NULL, '2026-04-10 19:30:00', '2026-04-24 17:14:56', 24, 1),
(2, 'Notre passion ne se dissout pas, édition 2026.', 'notre-passion-ne-se-dissout-pas-edition-2026', '<div>Communiqué historique de 125 associations de supporters de toute nature et de tout club contre les dangereux projets de dissolutions des Magic Fans et Green Angels.</div>', '2026-04-09 12:00:00', '2026-04-24 17:16:21', 25, 1),
(3, 'ULTRAS LIONS FLEURY 2026 - FURIES', 'ultras-lions-fleury-2026-furies', '<div><a href=\"https://www.youtube.com/hashtag/football\">#football</a> <a href=\"https://www.youtube.com/hashtag/fleury\">#fleury</a> <a href=\"https://www.youtube.com/hashtag/baseball\">#baseball</a> <a href=\"https://www.youtube.com/hashtag/thewarriors\">#thewarriors</a> <a href=\"https://www.youtube.com/hashtag/pyro\">#pyro</a> <a href=\"https://www.youtube.com/hashtag/furies\">#furies</a> <a href=\"https://www.youtube.com/hashtag/ultras\">#ultras</a></div>', '2026-02-10 19:30:00', '2026-04-25 00:39:11', 28, 4),
(4, 'Nouveau matos : Sticker - Série 2026', 'auto-matos-2', '<p>Nouveau matos disponible : <strong>Sticker - Série 2026</strong>.</p><p>Enveloppe de 60 stickers (6x10) de la première série des Ultras Lions</p><p><a href=\"/table-de-vente/2\">Voir le produit</a></p>', '2026-05-01 02:43:02', '2026-05-01 02:43:02', 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `site_config`
--

CREATE TABLE `site_config` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_subtitle` longtext DEFAULT NULL,
  `chants_subtitle` longtext DEFAULT NULL,
  `chants_title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `site_config`
--

INSERT INTO `site_config` (`id`, `site_name`, `hero_title`, `hero_subtitle`, `chants_subtitle`, `chants_title`) VALUES
(1, 'Ultras Lions Fleury', 'Ultras Lions Fleury', 'Groupe de supporters du FC Fleury 91.', 'Tous nos chants, paroles et audio pour faire vibrer les tribunes.', 'Chants');

-- --------------------------------------------------------

--
-- Structure de la table `ticket`
--

CREATE TABLE `ticket` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `opponent` varchar(180) NOT NULL,
  `match_date` datetime NOT NULL,
  `match_location` varchar(20) DEFAULT NULL,
  `venue` varchar(180) DEFAULT NULL,
  `billetweb_url` varchar(2048) DEFAULT NULL,
  `price` double NOT NULL,
  `description` longtext DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `guest_availability_date` datetime DEFAULT NULL,
  `member_availability_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ticket`
--

INSERT INTO `ticket` (`id`, `title`, `opponent`, `match_date`, `match_location`, `venue`, `billetweb_url`, `price`, `description`, `stock`, `image_id`, `category_id`, `guest_availability_date`, `member_availability_date`) VALUES
(2, 'FC Fleury (Féminine)', 'Paris Saint-Germain FC', '2026-04-25 21:00:00', 'domicile', 'Stade Robert Bobin, Bondoufle', 'https://www.billetweb.fr/arkema-pl-fc-fleury-91-paris-saint-germain&ticket=6968927', 0, 'D1 Arkema - dernière journée de championnat', 0, 6, 1, NULL, NULL),
(3, 'FC Fleury', 'Le Puy Foot 43', '2026-05-01 19:30:00', 'domicile', 'Stade Robert Bobin, Bondoufle', 'https://www.billetweb.fr/n1-j32-fc-fleury-91-le-puy-foot-43&ticket=6960687', 0, 'À l’approche de la rencontre entre le FC Fleury 91 et Le Puy Foot, prévue le 1er mai au stade Robert Bobin, il nous paraît nécessaire de rappeler l’importance de ce rendez-vous.\r\n\r\nDans une fin de saison où chaque point compte, ce match ne peut être considéré comme un simple enchaînement de calendrier. Il oppose deux équipes directement concernées par le haut de tableau, avec des enjeux clairs et immédiats.\r\n\r\nDans ce contexte, nous attendons une mobilisation à la hauteur, tant sur le terrain qu’en tribune.\r\n\r\nLe groupe appelle l’ensemble des présents à adopter une attitude cohérente avec les ambitions du moment. Cela implique une présence en nombre, une implication constante et une volonté affirmée de soutenir l’équipe sans relâche.\r\n\r\nIl ne s’agit pas ici d’un appel symbolique, mais d’une exigence.\r\nUne tribune active, organisée et continue est une composante essentielle dans ce type de rencontre.\r\n\r\nNous rappelons que l\'Essonne Stadium doit rester un point d’appui pour le club, un lieu où l’adversaire ne trouve ni confort, ni relâchement.\r\n\r\nChacun est donc attendu à sa place, en avance, prêt à s’investir pleinement durant l’intégralité de la rencontre,.\r\n\r\nLe contexte impose sérieux et engagement.\r\nLe moment ne laisse place à aucune approximation.\r\n\r\nMessage pour nos membres: écharpe obligatoire et pas de quartier on se défonce en tribune !!', 0, 29, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ticket_category`
--

CREATE TABLE `ticket_category` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ticket_category`
--

INSERT INTO `ticket_category` (`id`, `name`, `slug`) VALUES
(1, 'Domicile', 'domicile'),
(2, 'Exterieur', 'exterieur');

-- --------------------------------------------------------

--
-- Structure de la table `ticket_order`
--

CREATE TABLE `ticket_order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ticket_id` int(11) NOT NULL,
  `email` varchar(180) DEFAULT NULL,
  `customer_first_name` varchar(120) DEFAULT NULL,
  `customer_last_name` varchar(120) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` double NOT NULL,
  `total_price` double NOT NULL,
  `note` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `paid` tinyint(1) NOT NULL,
  `archived_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(12) DEFAULT NULL,
  `taille_tshirt` varchar(10) DEFAULT NULL,
  `taille_polo` varchar(10) DEFAULT NULL,
  `taille_pull` varchar(10) DEFAULT NULL,
  `taille_sweat` varchar(10) DEFAULT NULL,
  `taille_veste` varchar(10) DEFAULT NULL,
  `taille_short` varchar(10) DEFAULT NULL,
  `reset_password_token` varchar(128) DEFAULT NULL,
  `reset_password_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `roles`, `password`, `photo_profil`, `nom`, `prenom`, `date_naissance`, `telephone`, `adresse`, `ville`, `code_postal`, `taille_tshirt`, `taille_polo`, `taille_pull`, `taille_sweat`, `taille_veste`, `taille_short`, `reset_password_token`, `reset_password_token_expires_at`) VALUES
(2, 'ultraslionsfleury@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$DGLEJj7ADMSMrUKC4K9F0u.OmdU9sd0iPbT12kxnAlCBa2UM1GoNO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'nene.almeida78@gmail.com', '[\"ROLE_USER\",\"ROLE_ADMIN\"]', '$2y$13$C.yNc7IA/BIGBJUuk077yu1baxZT11KnaKk9eyqbuD8VE8S0Aza/O', 'pf_69ec0720ca4f5.jpg', 'Almeida', 'Nelson', '2004-08-06', '+33769197860', '31 Boulevard Saint Michel', 'Savigny-sur-Orge', '91600', 'M', NULL, NULL, 'M', 'M', 'M', NULL, NULL),
(4, 'max031601@gmail.com', '[\"ROLE_USER\"]', '$2y$13$iTEZ23ZnGmLwnA0wIXkI2.1/2WYDEJZ7xqw8mKYG7jE5NvLZkXZHy', 'pf_69ec9e7bee1dd.jpg', 'Leclercq', 'Maxime', NULL, '+33782143867', '6 bis rue de Champlan', 'Saulx les chartreux', '91160', 'M', 'M', 'M', 'M', 'M', 'M', NULL, NULL),
(5, 'krng4166@gmail.com', '[\"ROLE_USER\"]', '$2y$13$AEPxV7oaf4pAnu/K3MFngea6lTydtuMzd/SFwOChPJe7RgvftdqDC', 'pf_69efdd2bdea85.png', 'Habib', 'Hicham', NULL, '+33769348103', '16 Rue le Sueur', 'Paris', '75016', 'M', 'M', 'M', 'M', 'M', 'M', NULL, NULL),
(6, 'junioryoro91125@icloud.com', '[\"ROLE_USER\"]', '$2y$13$I/S7QwyGv/IgunK0FQX2beCQBmBHiXuyo0HfQB9vcu8Q9pQsFW3PS', 'pf_69f08fb60f411.png', 'Yoro', 'Arthur', NULL, '+33781516388', '10 rue Jules vernes', 'Massy', '91300', 'XL', NULL, NULL, 'XL', 'XL', 'XL', NULL, NULL),
(7, 'sarahbp1999@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$9KNnUMVcJTTpwthCu.ItperqLSuBRAqdf2hupq3yRzHchAHGzfkya', NULL, 'Berbiche - - Piacentino', 'Sarah', NULL, '+33620985992', '8 b rue Raymond Pitet', 'paris', '75017', 'M', NULL, NULL, 'M', 'M', 'L', NULL, NULL),
(10, 'mickael.darbonnel@gmail.com', '[\"ROLE_USER\"]', '$2y$13$D25BJVPMD9z0Zl58h75jBu4Lrmg1FhhcUsJamWJ6d33Il667SkLym', 'pf_69f3a7ad343f9.jpg', 'Darbonnel', 'Mickael', NULL, '+33659225362', '4 ter avenue du général de gaulle', 'Longjumeau', '91160', 'M', NULL, NULL, 'M', 'M', 'M', NULL, NULL),
(11, 'jeremiepujol@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$21jId6lz5J8mqg68WU/E9ONjAnCelSxAbFercUbLRMEbDOsGnNsTe', NULL, 'Pujol', 'Jeremie', NULL, '+33623803194', '6 rue André malraux', 'Fleury merogis', '91700', 'M', NULL, NULL, 'M', 'M', 'M', NULL, NULL),
(12, 'lantyste@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$Uc.svwY/1RtJGLgWWLdI1.kWMv81pTB0pmgbVFdmQVxLuYGaLysUu', NULL, 'Lanty', 'Stephane', NULL, '+330681995239', '10 Rue Pasteur', 'Longjumeau', '91160', 'XL', NULL, NULL, 'XL', 'XL', 'L', NULL, NULL),
(16, 'clemsec91@gmail.com', '[\"ROLE_USER\"]', '$2y$13$vnjzjP2ibMb4RqaUr6I8M.zx1BeqEJGrW6Iimpx2SydaK.jHJNWbS', NULL, 'Secher', 'Clément', NULL, '0635554233', '15 Rue Georges Charpak', 'Brétigny-sur-Orge', '91220', 'M', NULL, NULL, NULL, 'M', 'M', NULL, NULL),
(17, 'nisa.raymond@gmail.com', '[\"ROLE_USER\"]', '$2y$13$5nED9SNLMmOnrGR0sai9euoz/7Imo0/r2rLSDZQIm1UW7rexP6IM6', NULL, 'Raymond', 'Andrian\'isa', NULL, '0767207486', '89 Rue Rosa Parks', 'Fleury-Merogis', '91700', 'M', NULL, NULL, 'L', 'L', 'M', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `visit`
--

CREATE TABLE `visit` (
  `id` int(11) NOT NULL,
  `visited_at` datetime NOT NULL,
  `page` varchar(255) NOT NULL,
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `visit`
--

INSERT INTO `visit` (`id`, `visited_at`, `page`, `ip`) VALUES
(1, '2026-04-24 11:13:14', '/', 'unknown'),
(2, '2026-04-24 11:15:57', '/', '163.116.174.162'),
(3, '2026-04-24 11:16:29', '/', '165.154.206.204'),
(4, '2026-04-24 11:16:43', '/', '165.154.163.113'),
(5, '2026-04-24 11:17:40', '/', '163.116.174.162'),
(6, '2026-04-24 11:17:42', '/', '163.116.174.162'),
(7, '2026-04-24 11:17:50', '/', '163.116.174.162'),
(8, '2026-04-24 11:17:55', '/', '163.116.174.162'),
(9, '2026-04-24 11:19:16', '/photos-de-match/', '163.116.174.162'),
(10, '2026-04-24 11:19:19', '/', '163.116.174.162'),
(11, '2026-04-24 11:25:50', '/', '163.116.174.162'),
(12, '2026-04-24 11:25:54', '/', '163.116.174.162'),
(13, '2026-04-24 11:28:13', '/', '34.241.117.72'),
(14, '2026-04-24 11:28:14', '/', '34.241.117.72'),
(15, '2026-04-24 11:29:31', '/', '163.116.174.162'),
(16, '2026-04-24 11:30:31', '/', '191.101.217.149'),
(17, '2026-04-24 11:30:39', '/', '158.173.157.189'),
(18, '2026-04-24 11:32:35', '/', '52.205.183.147'),
(19, '2026-04-24 11:33:28', '/', '163.116.174.162'),
(20, '2026-04-24 11:34:01', '/', '163.116.174.162'),
(21, '2026-04-24 11:37:57', '/', '163.116.174.162'),
(22, '2026-04-24 11:41:41', '/', '213.32.27.152'),
(23, '2026-04-24 11:41:42', '/', '213.32.27.152'),
(24, '2026-04-24 11:46:13', '/', '163.116.174.162'),
(25, '2026-04-24 11:46:26', '/actualites/', '163.116.174.162'),
(26, '2026-04-24 11:46:33', '/login', '163.116.174.162'),
(27, '2026-04-24 11:46:42', '/forgot-password', '163.116.174.162'),
(28, '2026-04-24 11:46:48', '/forgot-password', '163.116.174.162'),
(29, '2026-04-24 11:47:16', '/', '163.116.174.162'),
(30, '2026-04-24 11:47:34', '/', '163.116.174.162'),
(31, '2026-04-24 11:47:53', '/login', '163.116.174.162'),
(32, '2026-04-24 11:49:44', '/', '78.240.140.253'),
(33, '2026-04-24 11:50:10', '/actualites/', '78.240.140.253'),
(34, '2026-04-24 11:50:16', '/chants', '78.240.140.253'),
(35, '2026-04-24 11:50:39', '/', '78.240.140.253'),
(36, '2026-04-24 11:50:43', '/billetterie/', '78.240.140.253'),
(37, '2026-04-24 11:51:06', '/', '163.116.174.162'),
(38, '2026-04-24 11:51:41', '/', '78.240.140.253'),
(39, '2026-04-24 11:51:54', '/chants', '163.116.174.162'),
(40, '2026-04-24 11:57:49', '/chants', '163.116.174.162'),
(41, '2026-04-24 11:57:51', '/login', '163.116.174.162'),
(42, '2026-04-24 11:57:57', '/login', '213.44.27.132'),
(43, '2026-04-24 11:57:58', '/', '163.116.174.162'),
(44, '2026-04-24 12:12:45', '/', '3.81.60.78'),
(45, '2026-04-24 12:17:37', '/', '13.220.107.156'),
(46, '2026-04-24 12:17:38', '/', '54.89.57.36'),
(47, '2026-04-24 12:28:00', '/', '54.210.60.45'),
(48, '2026-04-24 12:28:00', '/', '54.210.60.45'),
(49, '2026-04-24 12:28:00', '/', '54.210.60.45'),
(50, '2026-04-24 12:28:01', '/', '54.210.60.45'),
(51, '2026-04-24 12:39:43', '/', '116.202.217.153'),
(52, '2026-04-24 12:43:09', '/', '122.164.126.53'),
(53, '2026-04-24 12:48:32', '/', '206.189.46.109'),
(54, '2026-04-24 12:48:33', '/', '206.189.46.109'),
(55, '2026-04-24 12:48:34', '/', '206.189.46.109'),
(56, '2026-04-24 12:51:09', '/', '163.116.174.162'),
(57, '2026-04-24 12:51:16', '/', '163.116.174.162'),
(58, '2026-04-24 12:51:18', '/login', '163.116.174.162'),
(59, '2026-04-24 12:51:22', '/forgot-password', '163.116.174.162'),
(60, '2026-04-24 12:51:28', '/forgot-password', '163.116.174.162'),
(61, '2026-04-24 12:51:28', '/forgot-password', '163.116.174.162'),
(62, '2026-04-24 12:53:17', '/forgot-password', '163.116.174.162'),
(63, '2026-04-24 12:53:23', '/forgot-password', '163.116.174.162'),
(64, '2026-04-24 12:53:23', '/forgot-password', '163.116.174.162'),
(65, '2026-04-24 12:57:24', '/forgot-password', '163.116.174.162'),
(66, '2026-04-24 12:57:30', '/forgot-password', '163.116.174.162'),
(67, '2026-04-24 12:57:30', '/forgot-password', '163.116.174.162'),
(68, '2026-04-24 13:04:46', '/forgot-password', '163.116.174.162'),
(69, '2026-04-24 13:08:16', '/forgot-password', '163.116.174.162'),
(70, '2026-04-24 13:08:17', '/forgot-password', '163.116.174.162'),
(71, '2026-04-24 13:10:30', '/', '35.197.92.58'),
(72, '2026-04-24 13:15:44', '/', '17.22.253.91'),
(73, '2026-04-24 13:19:50', '/', '122.164.126.53'),
(74, '2026-04-24 13:19:53', '/', '17.246.19.98'),
(75, '2026-04-24 13:20:22', '/', '17.241.227.180'),
(76, '2026-04-24 13:25:22', '/', '187.124.218.238'),
(77, '2026-04-24 13:30:25', '/', '122.164.126.53'),
(78, '2026-04-24 13:30:33', '/', '54.247.223.148'),
(79, '2026-04-24 13:32:39', '/', '92.153.68.12'),
(80, '2026-04-24 13:46:45', '/', '122.164.126.53'),
(81, '2026-04-24 13:46:47', '/', '122.164.126.53'),
(82, '2026-04-24 13:46:49', '/', '122.164.126.53'),
(83, '2026-04-24 13:47:44', '/', '17.246.15.130'),
(84, '2026-04-24 13:50:25', '/', '122.164.126.53'),
(85, '2026-04-24 13:50:26', '/', '122.164.126.53'),
(86, '2026-04-24 13:50:27', '/', '122.164.126.53'),
(87, '2026-04-24 14:30:24', '/', '54.162.245.255'),
(88, '2026-04-24 14:44:20', '/', '91.134.142.208'),
(89, '2026-04-24 14:44:22', '/', '45.157.112.34'),
(90, '2026-04-24 14:46:38', '/', '50.19.129.227'),
(91, '2026-04-24 14:49:35', '/forgot-password', '163.116.174.162'),
(92, '2026-04-24 14:49:41', '/forgot-password', '163.116.174.162'),
(93, '2026-04-24 14:49:42', '/forgot-password', '163.116.174.162'),
(94, '2026-04-24 15:11:45', '/', '185.98.139.119'),
(95, '2026-04-24 15:22:39', '/forgot-password', '163.116.174.162'),
(96, '2026-04-24 15:22:45', '/forgot-password', '163.116.174.162'),
(97, '2026-04-24 15:22:46', '/forgot-password', '163.116.174.162'),
(98, '2026-04-24 15:27:36', '/', '54.160.228.218'),
(99, '2026-04-24 15:44:44', '/forgot-password', '163.116.174.162'),
(100, '2026-04-24 15:44:49', '/forgot-password', '163.116.174.162'),
(101, '2026-04-24 15:44:49', '/forgot-password', '163.116.174.162'),
(102, '2026-04-24 15:49:59', '/forgot-password', '163.116.174.162'),
(103, '2026-04-24 15:50:07', '/forgot-password', '163.116.174.162'),
(104, '2026-04-24 15:50:08', '/login', '163.116.174.162'),
(105, '2026-04-24 15:50:40', '/reset-password/d11b1c4c1ea2d4ce520d56e0a1444ad90d3880c823a39f4690023305c4a13dc5', '163.116.174.162'),
(106, '2026-04-24 15:50:51', '/reset-password/d11b1c4c1ea2d4ce520d56e0a1444ad90d3880c823a39f4690023305c4a13dc5', '173.194.92.180'),
(107, '2026-04-24 15:50:50', '/reset-password/d11b1c4c1ea2d4ce520d56e0a1444ad90d3880c823a39f4690023305c4a13dc5', '163.116.174.162'),
(108, '2026-04-24 15:50:51', '/login', '163.116.174.162'),
(109, '2026-04-24 15:51:00', '/', '163.116.174.162'),
(110, '2026-04-24 15:51:07', '/profil/2', '163.116.174.162'),
(111, '2026-04-24 15:53:04', '/', '34.26.118.151'),
(112, '2026-04-24 15:55:10', '/', '163.116.174.162'),
(113, '2026-04-24 15:55:13', '/table-de-vente/', '163.116.174.162'),
(114, '2026-04-24 16:14:24', '/table-de-vente/', '163.116.174.162'),
(115, '2026-04-24 16:14:45', '/table-de-vente/', '163.116.174.162'),
(116, '2026-04-24 16:14:48', '/table-de-vente/2', '163.116.174.162'),
(117, '2026-04-24 16:14:57', '/table-de-vente/panier/ajouter/2', '163.116.174.162'),
(118, '2026-04-24 16:14:57', '/panier', '163.116.174.162'),
(119, '2026-04-24 16:15:05', '/panier/checkout', '163.116.174.162'),
(120, '2026-04-24 16:18:03', '/forgot-password', '163.116.174.162'),
(121, '2026-04-24 16:18:16', '/', '163.116.174.162'),
(122, '2026-04-24 16:18:53', '/panier', '163.116.174.162'),
(123, '2026-04-24 16:18:56', '/panier', '163.116.174.162'),
(124, '2026-04-24 16:19:02', '/profil/2', '163.116.174.162'),
(125, '2026-04-24 16:19:07', '/panier/checkout', '163.116.174.162'),
(126, '2026-04-24 16:19:29', '/sumup/webhook', '52.48.233.7'),
(127, '2026-04-24 16:19:31', '/merci', '163.116.174.162'),
(128, '2026-04-24 16:19:35', '/merci/pdf', '163.116.174.162'),
(129, '2026-04-24 16:19:55', '/profil/2', '163.116.174.162'),
(130, '2026-04-24 16:20:05', '/profil/commande/1', '163.116.174.162'),
(131, '2026-04-24 16:20:12', '/table-de-vente/', '163.116.174.162'),
(132, '2026-04-24 16:20:15', '/table-de-vente/2', '163.116.174.162'),
(133, '2026-04-24 16:20:18', '/table-de-vente/panier/ajouter/2', '163.116.174.162'),
(134, '2026-04-24 16:20:18', '/panier', '163.116.174.162'),
(135, '2026-04-24 16:20:24', '/panier/checkout', '163.116.174.162'),
(136, '2026-04-24 16:20:43', '/sumup/webhook', '52.48.211.42'),
(137, '2026-04-24 16:20:44', '/merci', '163.116.174.162'),
(138, '2026-04-24 16:20:49', '/', '163.116.174.162'),
(139, '2026-04-24 16:20:51', '/profil/2', '163.116.174.162'),
(140, '2026-04-24 16:21:18', '/', '163.116.174.162'),
(141, '2026-04-24 16:21:22', '/billetterie/1', '163.116.174.162'),
(142, '2026-04-24 16:21:30', '/login', '163.116.174.162'),
(143, '2026-04-24 16:21:34', '/login', '163.116.174.162'),
(144, '2026-04-24 16:22:01', '/billetterie/1', '163.116.174.162'),
(145, '2026-04-24 16:23:38', '/profil/2', '163.116.174.162'),
(146, '2026-04-24 16:25:35', '/billetterie/1', '163.116.174.162'),
(147, '2026-04-24 16:25:37', '/groupe/', '163.116.174.162'),
(148, '2026-04-24 16:33:20', '/groupe/', '163.116.174.162'),
(149, '2026-04-24 16:33:43', '/groupe/', '163.116.174.162'),
(150, '2026-04-24 16:34:53', '/groupe/', '163.116.174.162'),
(151, '2026-04-24 16:34:58', '/billetterie/', '163.116.174.162'),
(152, '2026-04-24 16:35:01', '/', '163.116.174.162'),
(153, '2026-04-24 16:35:04', '/billetterie/', '163.116.174.162'),
(154, '2026-04-24 16:36:32', '/login', '163.116.174.162'),
(155, '2026-04-24 16:37:11', '/billetterie/', '163.116.174.162'),
(156, '2026-04-24 16:37:13', '/billetterie/1', '163.116.174.162'),
(157, '2026-04-24 16:37:17', '/billetterie/panier/ajouter/1', '163.116.174.162'),
(158, '2026-04-24 16:37:18', '/panier', '163.116.174.162'),
(159, '2026-04-24 16:37:21', '/panier/checkout', '163.116.174.162'),
(160, '2026-04-24 16:37:21', '/panier', '163.116.174.162'),
(161, '2026-04-24 16:41:17', '/panier', '163.116.174.162'),
(162, '2026-04-24 16:41:27', '/table-de-vente/', '163.116.174.162'),
(163, '2026-04-24 16:45:20', '/billetterie/', '163.116.174.162'),
(164, '2026-04-24 16:45:22', '/billetterie/2', '163.116.174.162'),
(165, '2026-04-24 16:46:56', '/table-de-vente/', '163.116.174.162'),
(166, '2026-04-24 16:46:58', '/table-de-vente/1', '163.116.174.162'),
(167, '2026-04-24 16:47:05', '/table-de-vente/', '163.116.174.162'),
(168, '2026-04-24 16:47:06', '/table-de-vente/2', '163.116.174.162'),
(169, '2026-04-24 16:47:11', '/table-de-vente/panier', '163.116.174.162'),
(170, '2026-04-24 16:47:11', '/panier', '163.116.174.162'),
(171, '2026-04-24 16:47:36', '/groupe/', '163.116.174.162'),
(172, '2026-04-24 16:47:38', '/', '163.116.174.162'),
(173, '2026-04-24 16:47:47', '/billetterie/1', '163.116.174.162'),
(174, '2026-04-24 16:48:29', '/medias/medias', '163.116.174.162'),
(175, '2026-04-24 16:53:54', '/medias/medias', '163.116.174.162'),
(176, '2026-04-24 16:53:56', '/photos-de-match/', '163.116.174.162'),
(177, '2026-04-24 16:54:02', '/photos-de-match/1', '163.116.174.162'),
(178, '2026-04-24 16:56:49', '/photos-de-match/', '163.116.174.162'),
(179, '2026-04-24 16:58:36', '/profil/2', '163.116.174.162'),
(180, '2026-04-24 16:58:40', '/panier', '163.116.174.162'),
(181, '2026-04-24 16:58:44', '/panier/vider', '163.116.174.162'),
(182, '2026-04-24 16:58:44', '/panier', '163.116.174.162'),
(183, '2026-04-24 17:02:18', '/panier', '163.116.174.162'),
(184, '2026-04-24 17:02:21', '/billetterie/', '163.116.174.162'),
(185, '2026-04-24 17:02:25', '/billetterie/1', '163.116.174.162'),
(186, '2026-04-24 17:02:27', '/billetterie/panier/ajouter/1', '163.116.174.162'),
(187, '2026-04-24 17:02:27', '/panier', '163.116.174.162'),
(188, '2026-04-24 17:04:38', '/panier', '163.116.174.162'),
(189, '2026-04-24 17:04:41', '/panier/supprimer/ticket/1', '163.116.174.162'),
(190, '2026-04-24 17:04:41', '/panier', '163.116.174.162'),
(191, '2026-04-24 17:04:43', '/table-de-vente/', '163.116.174.162'),
(192, '2026-04-24 17:04:47', '/billetterie/', '163.116.174.162'),
(193, '2026-04-24 17:04:49', '/billetterie/1', '163.116.174.162'),
(194, '2026-04-24 17:04:51', '/billetterie/panier/ajouter/1', '163.116.174.162'),
(195, '2026-04-24 17:04:51', '/panier', '163.116.174.162'),
(196, '2026-04-24 17:04:52', '/panier/checkout-cash', '163.116.174.162'),
(197, '2026-04-24 17:04:53', '/merci-liquide', '163.116.174.162'),
(198, '2026-04-24 17:05:16', '/table-de-vente/', '163.116.174.162'),
(199, '2026-04-24 17:05:21', '/table-de-vente/2', '163.116.174.162'),
(200, '2026-04-24 17:05:23', '/table-de-vente/panier/ajouter/2', '163.116.174.162'),
(201, '2026-04-24 17:05:23', '/panier', '163.116.174.162'),
(202, '2026-04-24 17:05:29', '/billetterie/', '163.116.174.162'),
(203, '2026-04-24 17:05:31', '/billetterie/1', '163.116.174.162'),
(204, '2026-04-24 17:05:32', '/billetterie/panier/ajouter/1', '163.116.174.162'),
(205, '2026-04-24 17:05:33', '/panier', '163.116.174.162'),
(206, '2026-04-24 17:05:35', '/panier/checkout', '163.116.174.162'),
(207, '2026-04-24 17:05:57', '/sumup/webhook', '52.48.233.7'),
(208, '2026-04-24 17:06:00', '/merci', '163.116.174.162'),
(209, '2026-04-24 17:06:07', '/profil/2', '163.116.174.162'),
(210, '2026-04-24 17:06:12', '/profil/commande/6', '163.116.174.162'),
(211, '2026-04-24 17:06:17', '/billetterie/', '163.116.174.162'),
(212, '2026-04-24 17:06:19', '/billetterie/2', '163.116.174.162'),
(213, '2026-04-24 17:06:57', '/profil/2', '163.116.174.162'),
(214, '2026-04-24 17:10:54', '/billetterie/', '163.116.174.162'),
(215, '2026-04-24 17:10:56', '/billetterie/2', '163.116.174.162'),
(216, '2026-04-24 17:11:02', '/billetterie/2', '163.116.174.162'),
(217, '2026-04-24 17:12:04', '/profil/2', '163.116.174.162'),
(218, '2026-04-24 17:13:13', '/', '92.184.140.55'),
(219, '2026-04-24 17:16:30', '/', '163.116.174.162'),
(220, '2026-04-24 17:16:38', '/actualites/', '163.116.174.162'),
(221, '2026-04-24 17:16:41', '/medias/medias', '163.116.174.162'),
(222, '2026-04-24 17:16:43', '/actualites/', '163.116.174.162'),
(223, '2026-04-24 17:16:44', '/actualites/fc-fleury-91-le-club-de-l-essonne', '163.116.174.162'),
(224, '2026-04-24 17:16:50', '/actualites/', '163.116.174.162'),
(225, '2026-04-24 17:16:51', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '163.116.174.162'),
(226, '2026-04-24 17:18:29', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '163.116.174.162'),
(227, '2026-04-24 17:18:34', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '163.116.174.162'),
(228, '2026-04-24 17:18:36', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '163.116.174.162'),
(229, '2026-04-24 17:18:36', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '163.116.174.162'),
(230, '2026-04-24 17:18:43', '/', '163.116.174.162'),
(231, '2026-04-24 17:24:40', '/', '92.184.140.167'),
(232, '2026-04-24 17:25:04', '/', '78.242.184.178'),
(233, '2026-04-24 17:25:23', '/', '176.140.218.69'),
(234, '2026-04-24 17:25:55', '/', '172.225.116.188'),
(235, '2026-04-24 17:26:08', '/', '176.140.218.69'),
(236, '2026-04-24 17:26:23', '/chants', '172.225.116.188'),
(237, '2026-04-24 17:26:36', '/table-de-vente/', '172.225.116.188'),
(238, '2026-04-24 17:26:40', '/', '92.184.140.167'),
(239, '2026-04-24 17:26:44', '/login', '92.184.140.167'),
(240, '2026-04-24 17:27:01', '/', '92.184.140.167'),
(241, '2026-04-24 17:27:26', '/', '176.140.218.69'),
(242, '2026-04-24 17:27:27', '/', '80.214.25.245'),
(243, '2026-04-24 17:27:44', '/', '172.226.148.42'),
(244, '2026-04-24 17:27:44', '/', '172.226.148.42'),
(245, '2026-04-24 17:27:45', '/billetterie/1', '80.214.25.245'),
(246, '2026-04-24 17:27:56', '/billetterie/panier/ajouter/1', '80.214.25.245'),
(247, '2026-04-24 17:27:56', '/actualites/fc-fleury-91-le-club-de-l-essonne', '78.240.34.134'),
(248, '2026-04-24 17:27:56', '/panier', '80.214.25.245'),
(249, '2026-04-24 17:28:03', '/', '92.184.146.19'),
(250, '2026-04-24 17:28:19', '/', '176.140.218.69'),
(251, '2026-04-24 17:28:23', '/', '78.241.175.210'),
(252, '2026-04-24 17:28:22', '/panier/checkout-cash', '80.214.25.245'),
(253, '2026-04-24 17:28:24', '/merci-liquide', '80.214.25.245'),
(254, '2026-04-24 17:28:24', '/billetterie/', '176.140.218.69'),
(255, '2026-04-24 17:28:28', '/', '213.44.27.197'),
(256, '2026-04-24 17:28:29', '/', '108.130.235.103'),
(257, '2026-04-24 17:28:29', '/billetterie/2', '176.140.218.69'),
(258, '2026-04-24 17:28:31', '/', '54.170.237.31'),
(259, '2026-04-24 17:28:37', '/photos-de-match/2', '78.241.175.210'),
(260, '2026-04-24 17:28:37', '/', '90.89.18.22'),
(261, '2026-04-24 17:28:46', '/', '90.60.10.226'),
(262, '2026-04-24 17:29:00', '/', '90.76.213.227'),
(263, '2026-04-24 17:29:04', '/photos-de-match/3', '90.60.10.226'),
(264, '2026-04-24 17:29:12', '/billetterie/', '104.28.42.22'),
(265, '2026-04-24 17:29:27', '/', '90.60.10.226'),
(266, '2026-04-24 17:29:34', '/billetterie/2', '104.28.42.22'),
(267, '2026-04-24 17:29:56', '/billetterie/1', '104.28.42.22'),
(268, '2026-04-24 17:30:00', '/', '77.205.19.94'),
(269, '2026-04-24 17:30:07', '/billetterie/panier', '104.28.42.22'),
(270, '2026-04-24 17:30:08', '/panier', '104.28.42.22'),
(271, '2026-04-24 17:30:11', '/billetterie/', '104.28.42.22'),
(272, '2026-04-24 17:30:20', '/billetterie/', '77.205.19.94'),
(273, '2026-04-24 17:30:20', '/billetterie/', '54.216.254.179'),
(274, '2026-04-24 17:30:33', '/', '90.89.18.22'),
(275, '2026-04-24 17:30:35', '/table-de-vente/', '77.205.19.94'),
(276, '2026-04-24 17:30:45', '/', '80.214.25.245'),
(277, '2026-04-24 17:31:14', '/', '80.214.25.245'),
(278, '2026-04-24 17:31:21', '/login', '80.214.25.245'),
(279, '2026-04-24 17:31:39', '/login', '80.214.25.245'),
(280, '2026-04-24 17:31:50', '/forgot-password', '80.214.25.245'),
(281, '2026-04-24 17:31:57', '/forgot-password', '80.214.25.245'),
(282, '2026-04-24 17:31:58', '/login', '80.214.25.245'),
(283, '2026-04-24 17:32:12', '/login', '80.214.25.245'),
(284, '2026-04-24 17:32:32', '/billetterie/', '80.214.25.245'),
(285, '2026-04-24 17:32:40', '/billetterie/2', '80.214.25.245'),
(286, '2026-04-24 17:37:42', '/', '104.236.84.197'),
(287, '2026-04-24 17:37:43', '/', '104.236.84.197'),
(288, '2026-04-24 17:37:43', '/', '104.236.84.197'),
(289, '2026-04-24 17:38:00', '/', '206.189.46.109'),
(290, '2026-04-24 17:38:01', '/', '206.189.46.109'),
(291, '2026-04-24 17:38:02', '/', '206.189.46.109'),
(292, '2026-04-24 17:43:03', '/', '176.189.96.241'),
(293, '2026-04-24 17:43:09', '/billetterie/', '176.189.96.241'),
(294, '2026-04-24 17:43:12', '/billetterie/2', '176.189.96.241'),
(295, '2026-04-24 17:47:36', '/', '90.76.213.227'),
(296, '2026-04-24 17:47:45', '/actualites/fc-fleury-91-le-club-de-l-essonne', '90.76.213.227'),
(297, '2026-04-24 17:47:55', '/photos-de-match/3', '90.76.213.227'),
(298, '2026-04-24 17:48:21', '/photos-de-match/1', '90.76.213.227'),
(299, '2026-04-24 17:48:59', '/groupe/', '90.76.213.227'),
(300, '2026-04-24 17:50:47', '/', '90.60.10.226'),
(301, '2026-04-24 17:50:50', '/', '90.60.10.226'),
(302, '2026-04-24 17:50:56', '/chants', '90.60.10.226'),
(303, '2026-04-24 17:51:06', '/billetterie/', '90.60.10.226'),
(304, '2026-04-24 17:51:24', '/login', '90.60.10.226'),
(305, '2026-04-24 18:00:52', '/', '90.60.10.226'),
(306, '2026-04-24 18:01:05', '/table-de-vente/', '90.60.10.226'),
(307, '2026-04-24 18:01:25', '/chants', '90.60.10.226'),
(308, '2026-04-24 18:01:28', '/chants', '90.60.10.226'),
(309, '2026-04-24 18:01:34', '/groupe/', '90.60.10.226'),
(310, '2026-04-24 18:05:04', '/', '104.28.42.24'),
(311, '2026-04-24 18:05:11', '/', '104.28.42.24'),
(312, '2026-04-24 18:07:05', '/login', '104.28.42.24'),
(313, '2026-04-24 18:08:01', '/actualites/', '90.60.10.226'),
(314, '2026-04-24 18:08:06', '/actualites/fc-fleury-91-le-club-de-l-essonne', '90.60.10.226'),
(315, '2026-04-24 18:09:00', '/', '34.139.193.6'),
(316, '2026-04-24 18:09:00', '/', '34.139.193.6'),
(317, '2026-04-24 18:09:01', '/', '34.139.193.6'),
(318, '2026-04-24 18:09:50', '/', '168.119.213.116'),
(319, '2026-04-24 18:16:18', '/', '34.60.253.234'),
(320, '2026-04-24 18:19:20', '/', '35.226.98.249'),
(321, '2026-04-24 18:24:23', '/', '168.119.213.116'),
(322, '2026-04-24 18:30:35', '/actualites/fc-fleury-91-le-club-de-l-essonne', '78.241.214.106'),
(323, '2026-04-24 18:41:37', '/', '155.2.225.177'),
(324, '2026-04-24 18:43:49', '/', '155.2.225.177'),
(325, '2026-04-24 19:25:48', '/groupe/', '52.205.23.0'),
(326, '2026-04-24 20:06:49', '/', '35.222.127.84'),
(327, '2026-04-24 20:06:50', '/', '35.222.127.84'),
(328, '2026-04-24 20:06:50', '/', '35.222.127.84'),
(329, '2026-04-24 20:40:34', '/', '54.81.138.163'),
(330, '2026-04-24 21:09:12', '/', '192.104.34.34'),
(331, '2026-04-24 22:16:20', '/', '149.57.180.119'),
(332, '2026-04-24 22:20:34', '/', '149.57.180.163'),
(333, '2026-04-24 22:23:29', '/', '152.53.246.211'),
(334, '2026-04-24 22:39:30', '/', '81.231.38.188'),
(335, '2026-04-24 23:13:45', '/billetterie/', '89.85.228.184'),
(336, '2026-04-24 23:14:20', '/', '100.55.149.218'),
(337, '2026-04-24 23:16:05', '/billetterie/', '89.85.228.184'),
(338, '2026-04-24 23:16:08', '/', '89.85.228.184'),
(339, '2026-04-24 23:18:04', '/table-de-vente/', '89.85.228.184'),
(340, '2026-04-24 23:18:14', '/table-de-vente/2', '89.85.228.184'),
(341, '2026-04-24 23:25:08', '/', '83.202.95.42'),
(342, '2026-04-24 23:25:09', '/', '83.202.95.42'),
(343, '2026-04-24 23:25:10', '/table-de-vente/', '83.202.95.42'),
(344, '2026-04-24 23:25:10', '/table-de-vente/', '83.202.95.42'),
(345, '2026-04-24 23:25:18', '/login', '83.202.95.42'),
(346, '2026-04-24 23:25:18', '/login', '83.202.95.42'),
(347, '2026-04-24 23:25:21', '/', '83.202.95.42'),
(348, '2026-04-24 23:25:21', '/', '83.202.95.42'),
(349, '2026-04-24 23:25:24', '/login', '213.44.27.140'),
(350, '2026-04-24 23:25:24', '/profil/2', '83.202.95.42'),
(351, '2026-04-24 23:25:24', '/profil/2', '83.202.95.42'),
(352, '2026-04-24 23:43:25', '/profil/2', '83.202.95.42'),
(353, '2026-04-24 23:43:26', '/profil/2', '83.202.95.42'),
(354, '2026-04-24 23:47:57', '/profil/2', '83.202.95.42'),
(355, '2026-04-24 23:47:57', '/profil/2', '83.202.95.42'),
(356, '2026-04-24 23:48:10', '/profil/2', '83.202.95.42'),
(357, '2026-04-24 23:48:11', '/profil/2', '83.202.95.42'),
(358, '2026-04-24 23:48:28', '/photos-de-match/', '83.202.95.42'),
(359, '2026-04-24 23:48:28', '/photos-de-match/', '83.202.95.42'),
(360, '2026-04-24 23:49:34', '/billetterie/', '83.202.95.42'),
(361, '2026-04-24 23:49:34', '/billetterie/', '83.202.95.42'),
(362, '2026-04-24 23:50:25', '/profil/2', '83.202.95.42'),
(363, '2026-04-24 23:51:58', '/photos-de-match/', '83.202.95.42'),
(364, '2026-04-24 23:51:59', '/photos-de-match/', '83.202.95.42'),
(365, '2026-04-24 23:52:26', '/photos-de-match/', '83.202.95.42'),
(366, '2026-04-24 23:52:26', '/photos-de-match/', '83.202.95.42'),
(367, '2026-04-24 23:52:33', '/actualites/', '83.202.95.42'),
(368, '2026-04-24 23:52:34', '/actualites/', '83.202.95.42'),
(369, '2026-04-24 23:52:39', '/groupe/', '83.202.95.42'),
(370, '2026-04-24 23:52:39', '/groupe/', '83.202.95.42'),
(371, '2026-04-24 23:52:44', '/billetterie/', '83.202.95.42'),
(372, '2026-04-24 23:52:45', '/billetterie/', '83.202.95.42'),
(373, '2026-04-24 23:52:50', '/billetterie/2', '83.202.95.42'),
(374, '2026-04-24 23:52:51', '/billetterie/2', '83.202.95.42'),
(375, '2026-04-24 23:53:07', '/', '149.57.180.142'),
(376, '2026-04-24 23:53:15', '/billetterie/1', '83.202.95.42'),
(377, '2026-04-24 23:53:16', '/billetterie/1', '83.202.95.42'),
(378, '2026-04-24 23:53:30', '/groupe/', '83.202.95.42'),
(379, '2026-04-24 23:53:30', '/groupe/', '83.202.95.42'),
(380, '2026-04-24 23:54:10', '/', '23.27.145.6'),
(381, '2026-04-24 23:54:16', '/table-de-vente/', '83.202.95.42'),
(382, '2026-04-24 23:54:17', '/table-de-vente/', '83.202.95.42'),
(383, '2026-04-24 23:54:18', '/chants', '83.202.95.42'),
(384, '2026-04-24 23:54:19', '/chants', '83.202.95.42'),
(385, '2026-04-24 23:54:36', '/chants', '83.202.95.42'),
(386, '2026-04-24 23:54:36', '/chants', '83.202.95.42'),
(387, '2026-04-24 23:54:45', '/', '161.129.164.255'),
(388, '2026-04-24 23:54:49', '/chants', '83.202.95.42'),
(389, '2026-04-24 23:54:50', '/chants', '83.202.95.42'),
(390, '2026-04-24 23:54:51', '/chants', '83.202.95.42'),
(391, '2026-04-24 23:54:52', '/chants', '83.202.95.42'),
(392, '2026-04-24 23:55:02', '/chants', '83.202.95.42'),
(393, '2026-04-24 23:55:02', '/chants', '83.202.95.42'),
(394, '2026-04-24 23:55:17', '/chants', '83.202.95.42'),
(395, '2026-04-24 23:55:17', '/chants', '83.202.95.42'),
(396, '2026-04-24 23:56:16', '/chants', '83.202.95.42'),
(397, '2026-04-24 23:56:17', '/chants', '83.202.95.42'),
(398, '2026-04-24 23:56:30', '/chants', '83.202.95.42'),
(399, '2026-04-24 23:56:31', '/chants', '83.202.95.42'),
(400, '2026-04-24 23:58:35', '/chants', '83.202.95.42'),
(401, '2026-04-24 23:58:36', '/chants', '83.202.95.42'),
(402, '2026-04-25 00:01:44', '/chants', '83.202.95.42'),
(403, '2026-04-25 00:01:44', '/chants', '83.202.95.42'),
(404, '2026-04-25 00:04:17', '/chants', '83.202.95.42'),
(405, '2026-04-25 00:04:17', '/chants', '83.202.95.42'),
(406, '2026-04-25 00:05:21', '/chants', '83.202.95.42'),
(407, '2026-04-25 00:27:46', '/', '87.236.176.215'),
(408, '2026-04-25 00:28:55', '/', '83.202.95.42'),
(409, '2026-04-25 00:28:59', '/chants', '83.202.95.42'),
(410, '2026-04-25 00:36:23', '/chants', '83.202.95.42'),
(411, '2026-04-25 00:36:56', '/medias/medias', '83.202.95.42'),
(412, '2026-04-25 00:39:19', '/medias/medias', '83.202.95.42'),
(413, '2026-04-25 00:39:23', '/actualites/', '83.202.95.42'),
(414, '2026-04-25 00:39:26', '/actualites/ultras-lions-fleury-2026-furies', '83.202.95.42'),
(415, '2026-04-25 00:41:05', '/', '34.123.170.104'),
(416, '2026-04-25 00:42:26', '/billetterie/', '83.202.95.42'),
(417, '2026-04-25 00:42:31', '/billetterie/2', '83.202.95.42'),
(418, '2026-04-25 00:54:48', '/billetterie/2', '83.202.95.42'),
(419, '2026-04-25 00:54:50', '/table-de-vente/', '83.202.95.42'),
(420, '2026-04-25 00:54:52', '/table-de-vente/2', '83.202.95.42'),
(421, '2026-04-25 00:55:10', '/table-de-vente/2', '83.202.95.42'),
(422, '2026-04-25 00:55:24', '/table-de-vente/1', '83.202.95.42'),
(423, '2026-04-25 00:55:33', '/table-de-vente/', '83.202.95.42'),
(424, '2026-04-25 00:55:34', '/billetterie/', '83.202.95.42'),
(425, '2026-04-25 00:55:35', '/billetterie/1', '83.202.95.42'),
(426, '2026-04-25 00:59:51', '/table-de-vente/', '83.202.95.42'),
(427, '2026-04-25 00:59:58', '/billetterie/', '83.202.95.42'),
(428, '2026-04-25 01:00:00', '/billetterie/2', '83.202.95.42'),
(429, '2026-04-25 01:19:39', '/billetterie/', '83.202.95.42'),
(430, '2026-04-25 01:24:18', '/', '83.202.95.42'),
(431, '2026-04-25 01:26:28', '/', '83.202.95.42'),
(432, '2026-04-25 01:26:29', '/', '83.202.95.42'),
(433, '2026-04-25 01:26:33', '/', '83.202.95.42'),
(434, '2026-04-25 01:28:05', '/', '83.202.95.42'),
(435, '2026-04-25 01:32:12', '/', '83.202.95.42'),
(436, '2026-04-25 01:32:44', '/', '83.202.95.42'),
(437, '2026-04-25 01:32:50', '/table-de-vente/', '83.202.95.42'),
(438, '2026-04-25 01:32:55', '/table-de-vente/2', '83.202.95.42'),
(439, '2026-04-25 01:33:05', '/table-de-vente/1', '83.202.95.42'),
(440, '2026-04-25 01:33:16', '/billetterie/', '83.202.95.42'),
(441, '2026-04-25 01:33:20', '/billetterie/2', '83.202.95.42'),
(442, '2026-04-25 01:34:34', '/groupe/', '83.202.95.42'),
(443, '2026-04-25 01:34:43', '/table-de-vente/', '83.202.95.42'),
(444, '2026-04-25 01:35:02', '/profil/2', '83.202.95.42'),
(445, '2026-04-25 01:39:15', '/', '83.202.95.42'),
(446, '2026-04-25 01:43:35', '/', '83.202.95.42'),
(447, '2026-04-25 01:43:39', '/billetterie/', '83.202.95.42'),
(448, '2026-04-25 01:43:42', '/billetterie/2', '83.202.95.42'),
(449, '2026-04-25 01:43:52', '/billetterie/2/billetweb-preinscription', '83.202.95.42'),
(450, '2026-04-25 01:43:52', '/billetterie/2', '83.202.95.42'),
(451, '2026-04-25 01:44:21', '/billetterie/', '83.202.95.42'),
(452, '2026-04-25 01:44:22', '/billetterie/2', '83.202.95.42'),
(453, '2026-04-25 01:44:26', '/billetterie/2', '83.202.95.42'),
(454, '2026-04-25 01:44:32', '/billetterie/', '83.202.95.42'),
(455, '2026-04-25 01:44:35', '/', '83.202.95.42'),
(456, '2026-04-25 01:44:39', '/billetterie/', '83.202.95.42'),
(457, '2026-04-25 01:44:41', '/billetterie/2', '83.202.95.42'),
(458, '2026-04-25 01:44:45', '/table-de-vente/', '83.202.95.42'),
(459, '2026-04-25 01:44:50', '/table-de-vente/2', '83.202.95.42'),
(460, '2026-04-25 01:45:17', '/table-de-vente/panier/ajouter/2', '83.202.95.42'),
(461, '2026-04-25 01:45:17', '/panier', '83.202.95.42'),
(462, '2026-04-25 01:45:22', '/panier/checkout', '83.202.95.42'),
(463, '2026-04-25 01:45:39', '/sumup/webhook', '52.48.233.7'),
(464, '2026-04-25 01:45:40', '/merci', '83.202.95.42'),
(465, '2026-04-25 01:45:54', '/table-de-vente/', '83.202.95.42'),
(466, '2026-04-25 01:45:55', '/table-de-vente/2', '83.202.95.42'),
(467, '2026-04-25 01:45:57', '/table-de-vente/panier/ajouter/2', '83.202.95.42'),
(468, '2026-04-25 01:45:57', '/panier', '83.202.95.42'),
(469, '2026-04-25 01:46:01', '/panier/checkout-cash', '83.202.95.42'),
(470, '2026-04-25 01:46:02', '/merci-liquide', '83.202.95.42'),
(471, '2026-04-25 01:46:07', '/merci/pdf', '83.202.95.42'),
(472, '2026-04-25 01:46:51', '/login', '83.202.95.42'),
(473, '2026-04-25 01:46:53', '/', '83.202.95.42'),
(474, '2026-04-25 01:46:56', '/profil/2', '83.202.95.42'),
(475, '2026-04-25 01:49:05', '/', '83.202.95.42'),
(476, '2026-04-25 01:49:15', '/billetterie/2', '83.202.95.42'),
(477, '2026-04-25 01:49:25', '/chants', '83.202.95.42'),
(478, '2026-04-25 01:49:35', '/actualites/', '83.202.95.42'),
(479, '2026-04-25 01:49:36', '/actualites/ultras-lions-fleury-2026-furies', '83.202.95.42'),
(480, '2026-04-25 01:49:45', '/medias/medias', '83.202.95.42'),
(481, '2026-04-25 01:49:47', '/actualites/', '83.202.95.42'),
(482, '2026-04-25 01:49:48', '/actualites/ultras-lions-fleury-2026-furies', '83.202.95.42'),
(483, '2026-04-25 01:54:45', '/actualites/ultras-lions-fleury-2026-furies', '83.202.95.42'),
(484, '2026-04-25 01:55:02', '/billetterie/', '83.202.95.42'),
(485, '2026-04-25 01:55:04', '/billetterie/2', '83.202.95.42'),
(486, '2026-04-25 01:55:12', '/billetterie/2/billetweb-preinscription', '83.202.95.42'),
(487, '2026-04-25 01:55:12', '/billetterie/2', '83.202.95.42'),
(488, '2026-04-25 01:56:03', '/table-de-vente/', '83.202.95.42'),
(489, '2026-04-25 01:56:09', '/table-de-vente/2', '83.202.95.42'),
(490, '2026-04-25 01:56:12', '/table-de-vente/panier/ajouter/2', '83.202.95.42'),
(491, '2026-04-25 01:56:13', '/panier', '83.202.95.42'),
(492, '2026-04-25 01:56:16', '/panier/checkout', '83.202.95.42'),
(493, '2026-04-25 01:57:13', '/panier', '83.202.95.42'),
(494, '2026-04-25 01:57:15', '/panier/checkout', '83.202.95.42'),
(495, '2026-04-25 01:57:15', '/panier', '83.202.95.42'),
(496, '2026-04-25 01:57:19', '/panier/supprimer/merch/2%7CTU', '83.202.95.42'),
(497, '2026-04-25 01:57:19', '/panier', '83.202.95.42'),
(498, '2026-04-25 01:57:21', '/table-de-vente/', '83.202.95.42'),
(499, '2026-04-25 01:57:49', '/table-de-vente/', '83.202.95.42'),
(500, '2026-04-25 01:57:51', '/table-de-vente/2', '83.202.95.42'),
(501, '2026-04-25 01:57:53', '/table-de-vente/panier/ajouter/2', '83.202.95.42'),
(502, '2026-04-25 01:57:53', '/panier', '83.202.95.42'),
(503, '2026-04-25 01:57:56', '/panier/checkout', '83.202.95.42'),
(504, '2026-04-25 01:58:50', '/sumup/webhook', '79.125.59.241'),
(505, '2026-04-25 01:58:55', '/merci', '83.202.95.42'),
(506, '2026-04-25 01:59:27', '/table-de-vente/2', '83.202.95.42'),
(507, '2026-04-25 01:59:47', '/', '83.202.95.42'),
(508, '2026-04-25 01:59:50', '/billetterie/', '83.202.95.42'),
(509, '2026-04-25 02:00:18', '/table-de-vente/', '83.202.95.42'),
(510, '2026-04-25 02:00:24', '/table-de-vente/', '83.202.95.42'),
(511, '2026-04-25 02:00:26', '/table-de-vente/2', '83.202.95.42'),
(512, '2026-04-25 02:00:28', '/table-de-vente/panier/ajouter/2', '83.202.95.42'),
(513, '2026-04-25 02:00:28', '/panier', '83.202.95.42'),
(514, '2026-04-25 02:00:38', '/panier/checkout', '83.202.95.42'),
(515, '2026-04-25 02:00:45', '/panier/supprimer/merch/2|TU', '83.202.95.42'),
(516, '2026-04-25 02:00:46', '/panier', '83.202.95.42'),
(517, '2026-04-25 02:02:03', '/', '83.202.95.42'),
(518, '2026-04-25 02:02:49', '/', '212.129.7.58'),
(519, '2026-04-25 02:02:54', '/', '51.15.159.253'),
(520, '2026-04-25 02:02:58', '/actualites/', '51.15.159.253'),
(521, '2026-04-25 02:02:58', '/photos-de-match/', '51.15.159.253'),
(522, '2026-04-25 02:02:59', '/photos-de-match/', '51.15.159.253'),
(523, '2026-04-25 02:03:07', '/medias/medias', '51.15.159.253'),
(524, '2026-04-25 02:03:07', '/chants', '51.15.159.253'),
(525, '2026-04-25 02:03:08', '/table-de-vente/', '51.15.159.253'),
(526, '2026-04-25 02:03:11', '/billetterie/', '51.15.159.253'),
(527, '2026-04-25 02:03:15', '/login', '51.15.159.253'),
(528, '2026-04-25 02:03:15', '/groupe/', '51.15.159.253'),
(529, '2026-04-25 02:05:54', '/', '83.202.95.42'),
(530, '2026-04-25 02:06:01', '/actualites/ultras-lions-fleury-2026-furies', '83.202.95.42'),
(531, '2026-04-25 02:06:29', '/billetterie/', '83.202.95.42'),
(532, '2026-04-25 02:06:31', '/billetterie/2', '83.202.95.42'),
(533, '2026-04-25 02:06:43', '/table-de-vente/', '83.202.95.42'),
(534, '2026-04-25 02:10:57', '/login', '83.202.95.42'),
(535, '2026-04-25 02:11:01', '/', '83.202.95.42'),
(536, '2026-04-25 02:11:59', '/', '83.202.95.42'),
(537, '2026-04-25 02:11:59', '/', '83.202.95.42'),
(538, '2026-04-25 02:12:04', '/', '83.202.95.42'),
(539, '2026-04-25 02:12:08', '/invitation', '83.202.95.42'),
(540, '2026-04-25 02:12:11', '/invitation', '83.202.95.42'),
(541, '2026-04-25 02:12:11', '/register/081ea14c691a700fae92468ff7e8f61a', '83.202.95.42'),
(542, '2026-04-25 02:13:20', '/register/081ea14c691a700fae92468ff7e8f61a', '83.202.95.42'),
(543, '2026-04-25 02:13:21', '/', '83.202.95.42'),
(544, '2026-04-25 02:13:28', '/profil/3', '83.202.95.42'),
(545, '2026-04-25 02:13:55', '/', '83.202.95.42'),
(546, '2026-04-25 02:13:58', '/login', '83.202.95.42'),
(547, '2026-04-25 02:14:09', '/', '83.202.95.42'),
(548, '2026-04-25 02:14:54', '/', '83.202.95.42'),
(549, '2026-04-25 02:14:57', '/login', '83.202.95.42'),
(550, '2026-04-25 02:15:05', '/', '83.202.95.42'),
(551, '2026-04-25 02:15:50', '/actualites/ultras-lions-fleury-2026-furies', '83.202.95.42'),
(552, '2026-04-25 02:36:23', '/', '35.243.158.46'),
(553, '2026-04-25 02:36:23', '/', '35.243.158.46'),
(554, '2026-04-25 02:36:23', '/', '35.243.158.46'),
(555, '2026-04-25 02:36:23', '/', '35.243.158.46'),
(556, '2026-04-25 02:41:54', '/', '87.236.176.155'),
(557, '2026-04-25 03:02:13', '/', '206.189.120.18'),
(558, '2026-04-25 03:02:14', '/', '206.189.120.18'),
(559, '2026-04-25 03:28:22', '/', '109.234.166.148'),
(560, '2026-04-25 03:29:45', '/', '52.73.56.26'),
(561, '2026-04-25 03:47:08', '/', '109.166.42.147'),
(562, '2026-04-25 03:47:09', '/actualites/', '109.166.42.147'),
(563, '2026-04-25 04:02:59', '/', '77.36.124.53'),
(564, '2026-04-25 04:03:00', '/actualites/', '77.36.124.53'),
(565, '2026-04-25 04:26:34', '/', '110.172.98.2'),
(566, '2026-04-25 04:32:28', '/', '18.232.252.197'),
(567, '2026-04-25 04:48:29', '/', '34.174.163.32'),
(568, '2026-04-25 04:48:31', '/', '34.174.163.32'),
(569, '2026-04-25 05:09:19', '/', '54.37.10.247'),
(570, '2026-04-25 05:09:23', '/photos-de-match/3', '51.38.135.19'),
(571, '2026-04-25 05:09:25', '/table-de-vente/', '57.129.4.123'),
(572, '2026-04-25 05:09:27', '/actualites/ultras-lions-fleury-2026-furies', '51.75.162.18'),
(573, '2026-04-25 05:09:28', '/actualites/fc-fleury-91-le-club-de-l-essonne', '51.75.162.18'),
(574, '2026-04-25 05:09:30', '/groupe/', '57.129.4.123'),
(575, '2026-04-25 05:09:31', '/medias/medias', '51.38.135.19'),
(576, '2026-04-25 06:09:58', '/', '205.169.39.51'),
(577, '2026-04-25 06:15:49', '/', '205.169.39.3'),
(578, '2026-04-25 07:21:48', '/', '100.26.240.4'),
(579, '2026-04-25 07:39:56', '/', '95.181.233.21'),
(580, '2026-04-25 07:46:02', '/', '15.204.182.106'),
(581, '2026-04-25 08:11:08', '/', '149.56.150.177'),
(582, '2026-04-25 08:11:10', '/', '149.56.150.177'),
(583, '2026-04-25 08:11:10', '/login', '149.56.150.177'),
(584, '2026-04-25 08:11:11', '/', '149.56.150.177'),
(585, '2026-04-25 08:11:12', '/actualites/', '149.56.150.177'),
(586, '2026-04-25 08:11:13', '/photos-de-match/', '149.56.150.177'),
(587, '2026-04-25 08:11:14', '/photos-de-match/', '149.56.150.177'),
(588, '2026-04-25 08:11:15', '/medias/medias', '149.56.150.177'),
(589, '2026-04-25 08:11:16', '/chants', '149.56.150.177'),
(590, '2026-04-25 08:11:17', '/table-de-vente/', '149.56.150.177'),
(591, '2026-04-25 08:11:18', '/billetterie/', '149.56.150.177'),
(592, '2026-04-25 08:11:20', '/', '149.56.150.177'),
(593, '2026-04-25 08:11:26', '/', '149.56.150.101'),
(594, '2026-04-25 08:11:29', '/', '149.56.150.101'),
(595, '2026-04-25 08:11:31', '/', '149.56.150.101'),
(596, '2026-04-25 08:49:53', '/', '15.204.182.106'),
(597, '2026-04-25 09:11:44', '/', '16.146.149.141'),
(598, '2026-04-25 11:01:45', '/', '87.236.176.190'),
(599, '2026-04-25 11:12:28', '/', '17.241.219.149'),
(600, '2026-04-25 12:10:37', '/', '51.15.16.115'),
(601, '2026-04-25 12:21:07', '/', '83.202.95.42'),
(602, '2026-04-25 12:21:16', '/billetterie/', '83.202.95.42'),
(603, '2026-04-25 12:21:17', '/billetterie/2', '83.202.95.42'),
(604, '2026-04-25 12:22:51', '/', '88.161.222.224'),
(605, '2026-04-25 12:22:56', '/billetterie/', '88.161.222.224'),
(606, '2026-04-25 12:22:57', '/', '90.23.51.38'),
(607, '2026-04-25 12:23:02', '/billetterie/2', '88.161.222.224'),
(608, '2026-04-25 12:23:18', '/billetterie/2/billetweb-preinscription', '88.161.222.224'),
(609, '2026-04-25 12:23:18', '/billetterie/2', '88.161.222.224'),
(610, '2026-04-25 12:23:35', '/billetterie/', '90.23.51.38'),
(611, '2026-04-25 12:23:38', '/billetterie/2', '90.23.51.38'),
(612, '2026-04-25 12:23:56', '/', '88.161.222.224'),
(613, '2026-04-25 12:24:00', '/billetterie/', '88.161.222.224'),
(614, '2026-04-25 12:24:17', '/', '83.202.209.82'),
(615, '2026-04-25 12:25:14', '/', '176.140.215.88'),
(616, '2026-04-25 12:27:24', '/', '88.160.142.5'),
(617, '2026-04-25 12:27:42', '/invitation', '88.160.142.5'),
(618, '2026-04-25 12:27:53', '/invitation', '88.160.142.5'),
(619, '2026-04-25 12:27:53', '/register/521a63135ee110d20f1259d6bc4edc58', '88.160.142.5'),
(620, '2026-04-25 12:32:34', '/login', '83.202.95.42'),
(621, '2026-04-25 12:32:40', '/', '83.202.95.42'),
(622, '2026-04-25 12:32:41', '/', '83.202.95.42'),
(623, '2026-04-25 12:33:22', '/register/521a63135ee110d20f1259d6bc4edc58', '88.160.142.5'),
(624, '2026-04-25 12:33:55', '/', '83.202.95.42'),
(625, '2026-04-25 12:33:55', '/', '83.202.95.42'),
(626, '2026-04-25 12:34:54', '/register/521a63135ee110d20f1259d6bc4edc58', '88.160.142.5'),
(627, '2026-04-25 12:35:50', '/register/521a63135ee110d20f1259d6bc4edc58', '88.160.142.5'),
(628, '2026-04-25 12:35:51', '/', '88.160.142.5'),
(629, '2026-04-25 12:35:56', '/', '213.44.27.139'),
(630, '2026-04-25 12:36:09', '/billetterie/', '88.160.142.5'),
(631, '2026-04-25 12:36:20', '/', '88.160.142.5'),
(632, '2026-04-25 12:36:48', '/photos-de-match/2', '88.160.142.5'),
(633, '2026-04-25 12:36:56', '/chants', '88.160.142.5'),
(634, '2026-04-25 12:37:23', '/', '83.202.209.82'),
(635, '2026-04-25 12:37:36', '/chants', '83.202.209.82'),
(636, '2026-04-25 12:37:43', '/', '54.227.142.159'),
(637, '2026-04-25 12:37:43', '/', '54.227.142.159'),
(638, '2026-04-25 12:37:43', '/', '54.227.142.159'),
(639, '2026-04-25 12:38:13', '/billetterie/', '83.202.209.82'),
(640, '2026-04-25 12:38:24', '/', '212.195.247.176'),
(641, '2026-04-25 12:38:25', '/table-de-vente/', '83.202.209.82'),
(642, '2026-04-25 12:39:23', '/actualites/', '83.202.209.82'),
(643, '2026-04-25 12:39:27', '/', '83.202.209.82'),
(644, '2026-04-25 12:39:38', '/login', '83.202.209.82'),
(645, '2026-04-25 12:47:44', '/', '83.202.95.42'),
(646, '2026-04-25 12:53:59', '/', '88.160.142.5'),
(647, '2026-04-25 12:54:16', '/', '88.160.142.5'),
(648, '2026-04-25 12:54:20', '/', '80.239.186.178'),
(649, '2026-04-25 12:55:12', '/table-de-vente/', '80.239.186.176'),
(650, '2026-04-25 12:55:30', '/table-de-vente/2', '80.239.186.176'),
(651, '2026-04-25 12:55:30', '/billetterie/', '88.160.142.5'),
(652, '2026-04-25 12:55:52', '/', '88.160.142.5'),
(653, '2026-04-25 12:55:54', '/actualites/', '88.160.142.5'),
(654, '2026-04-25 12:55:55', '/table-de-vente/', '88.160.142.5'),
(655, '2026-04-25 12:58:12', '/table-de-vente/2', '88.160.142.5'),
(656, '2026-04-25 12:58:39', '/profil/4/edit', '88.160.142.5'),
(657, '2026-04-25 12:59:07', '/profil/4/edit', '88.160.142.5'),
(658, '2026-04-25 12:59:08', '/profil/4', '88.160.142.5'),
(659, '2026-04-25 12:59:22', '/', '88.160.142.5'),
(660, '2026-04-25 13:13:11', '/', '111.243.112.209'),
(661, '2026-04-25 13:22:58', '/', '17.22.245.96'),
(662, '2026-04-25 13:32:42', '/', '88.160.142.5'),
(663, '2026-04-25 13:32:53', '/photos-de-match/2', '88.160.142.5'),
(664, '2026-04-25 13:33:06', '/login', '88.160.142.5'),
(665, '2026-04-25 13:34:40', '/', '104.28.42.19'),
(666, '2026-04-25 13:34:43', '/', '88.160.142.5'),
(667, '2026-04-25 13:34:48', '/billetterie/', '88.160.142.5'),
(668, '2026-04-25 13:34:55', '/', '88.160.142.5'),
(669, '2026-04-25 13:35:09', '/actualites/ultras-lions-fleury-2026-furies', '88.160.142.5'),
(670, '2026-04-25 13:35:24', '/table-de-vente/', '88.160.142.5'),
(671, '2026-04-25 13:35:25', '/', '88.160.142.5'),
(672, '2026-04-25 13:35:34', '/', '88.160.142.5'),
(673, '2026-04-25 13:39:17', '/', '92.184.144.239'),
(674, '2026-04-25 13:39:29', '/', '92.184.144.239'),
(675, '2026-04-25 13:39:29', '/', '92.184.144.239'),
(676, '2026-04-25 13:39:48', '/', '92.184.144.239'),
(677, '2026-04-25 13:40:10', '/billetterie/2', '92.184.144.239'),
(678, '2026-04-25 13:40:30', '/login', '92.184.144.239'),
(679, '2026-04-25 13:40:34', '/', '92.184.144.239'),
(680, '2026-04-25 13:50:29', '/', '81.65.94.87'),
(681, '2026-04-25 13:50:39', '/photos-de-match/2', '81.65.94.87'),
(682, '2026-04-25 13:51:03', '/actualites/', '81.65.94.87'),
(683, '2026-04-25 13:51:15', '/chants', '81.65.94.87'),
(684, '2026-04-25 13:52:23', '/table-de-vente/', '81.65.94.87'),
(685, '2026-04-25 13:52:27', '/table-de-vente/2', '81.65.94.87'),
(686, '2026-04-25 13:52:38', '/billetterie/', '81.65.94.87'),
(687, '2026-04-25 13:55:40', '/', '92.184.144.239'),
(688, '2026-04-25 13:55:47', '/chants', '92.184.144.239'),
(689, '2026-04-25 13:59:16', '/', '23.27.145.222'),
(690, '2026-04-25 14:03:59', '/', '168.144.103.45'),
(691, '2026-04-25 14:04:00', '/', '168.144.103.45'),
(692, '2026-04-25 14:04:01', '/', '168.144.103.45'),
(693, '2026-04-25 14:05:59', '/', '168.144.41.35'),
(694, '2026-04-25 14:05:59', '/', '168.144.41.35'),
(695, '2026-04-25 14:06:01', '/', '168.144.41.35'),
(696, '2026-04-25 14:38:25', '/', '178.62.198.234'),
(697, '2026-04-25 14:38:26', '/', '178.62.198.234'),
(698, '2026-04-25 15:13:25', '/', '54.209.0.157'),
(699, '2026-04-25 16:03:10', '/', '88.123.2.161'),
(700, '2026-04-25 16:03:14', '/billetterie/', '88.123.2.161'),
(701, '2026-04-25 16:03:22', '/billetterie/2', '88.123.2.161'),
(702, '2026-04-25 16:03:41', '/billetterie/2/billetweb-preinscription', '88.123.2.161'),
(703, '2026-04-25 16:03:41', '/billetterie/2', '88.123.2.161'),
(704, '2026-04-25 17:05:13', '/', '83.202.209.82'),
(705, '2026-04-25 17:08:32', '/billetterie/', '83.202.209.82'),
(706, '2026-04-25 17:08:37', '/billetterie/2', '83.202.209.82'),
(707, '2026-04-25 18:29:55', '/', '155.2.225.177'),
(708, '2026-04-25 18:31:08', '/', '155.2.225.177'),
(709, '2026-04-25 18:40:07', '/', '151.115.90.254'),
(710, '2026-04-25 18:40:08', '/', '151.115.90.254'),
(711, '2026-04-25 18:40:09', '/', '151.115.90.254'),
(712, '2026-04-25 18:40:09', '/', '151.115.90.254'),
(713, '2026-04-25 18:41:43', '/', '52.90.240.207'),
(714, '2026-04-25 18:42:35', '/', '52.90.240.207'),
(715, '2026-04-25 18:54:13', '/', '40.77.167.156'),
(716, '2026-04-25 18:55:00', '/login', '40.77.167.16'),
(717, '2026-04-25 21:31:10', '/', '78.240.125.169'),
(718, '2026-04-25 21:31:13', '/chants', '78.240.125.169'),
(719, '2026-04-25 21:32:29', '/', '47.236.65.119'),
(720, '2026-04-25 21:32:36', '/', '47.236.65.119'),
(721, '2026-04-25 22:13:28', '/', '149.57.180.111'),
(722, '2026-04-25 22:24:42', '/', '176.140.217.122'),
(723, '2026-04-25 22:24:49', '/chants', '176.140.217.122'),
(724, '2026-04-25 22:54:13', '/', '185.247.137.206'),
(725, '2026-04-25 23:10:32', '/', '191.102.149.211'),
(726, '2026-04-25 23:52:21', '/', '23.27.145.29'),
(727, '2026-04-26 00:29:09', '/chants', '78.240.124.115'),
(728, '2026-04-26 00:38:00', '/', '52.167.144.172'),
(729, '2026-04-26 01:42:32', '/', '34.123.170.104'),
(730, '2026-04-26 01:43:51', '/', '170.64.131.250'),
(731, '2026-04-26 01:43:52', '/', '170.64.131.250'),
(732, '2026-04-26 01:43:54', '/', '170.64.131.250'),
(733, '2026-04-26 02:39:33', '/', '88.160.142.5'),
(734, '2026-04-26 02:39:48', '/billetterie/', '88.160.142.5'),
(735, '2026-04-26 03:02:17', '/', '84.32.41.136'),
(736, '2026-04-26 03:02:18', '/', '84.32.41.136'),
(737, '2026-04-26 03:02:18', '/billetterie/', '84.32.41.136'),
(738, '2026-04-26 03:02:19', '/', '84.32.41.136'),
(739, '2026-04-26 03:02:19', '/billetterie/2', '84.32.41.136'),
(740, '2026-04-26 03:02:19', '/groupe/', '84.32.41.136'),
(741, '2026-04-26 03:02:19', '/photos-de-match/1', '84.32.41.136'),
(742, '2026-04-26 03:02:19', '/actualites/', '84.32.41.136'),
(743, '2026-04-26 03:02:19', '/actualites/ultras-lions-fleury-2026-furies', '84.32.41.136'),
(744, '2026-04-26 03:02:20', '/chants', '84.32.41.136'),
(745, '2026-04-26 03:02:20', '/photos-de-match/3', '84.32.41.136'),
(746, '2026-04-26 03:02:20', '/photos-de-match/', '84.32.41.136'),
(747, '2026-04-26 03:02:20', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '84.32.41.136'),
(748, '2026-04-26 03:02:20', '/medias/medias', '84.32.41.136'),
(749, '2026-04-26 03:02:20', '/actualites/fc-fleury-91-le-club-de-l-essonne', '84.32.41.136'),
(750, '2026-04-26 03:02:21', '/photos-de-match/', '84.32.41.136'),
(751, '2026-04-26 03:02:21', '/photos-de-match/2', '84.32.41.136'),
(752, '2026-04-26 03:02:21', '/table-de-vente/', '84.32.41.136'),
(753, '2026-04-26 03:02:21', '/invitation', '84.32.41.136'),
(754, '2026-04-26 03:02:21', '/login', '84.32.41.136'),
(755, '2026-04-26 04:04:44', '/', '23.27.145.50'),
(756, '2026-04-26 06:53:29', '/', '165.154.172.88'),
(757, '2026-04-26 07:22:08', '/', '2.196.198.41'),
(758, '2026-04-26 07:51:50', '/', '54.174.62.150'),
(759, '2026-04-26 07:52:20', '/', '54.174.62.150'),
(760, '2026-04-26 10:39:34', '/', '88.123.2.161'),
(761, '2026-04-26 10:39:46', '/photos-de-match/2', '88.123.2.161'),
(762, '2026-04-26 10:40:10', '/', '88.123.2.161'),
(763, '2026-04-26 10:43:15', '/photos-de-match/1', '88.123.2.161'),
(764, '2026-04-26 10:46:11', '/photos-de-match/1', '88.123.2.161'),
(765, '2026-04-26 11:45:34', '/', '45.66.51.71'),
(766, '2026-04-26 11:45:45', '/login', '193.31.59.190'),
(767, '2026-04-26 11:45:47', '/login', '193.31.59.190'),
(768, '2026-04-26 11:45:48', '/login', '193.149.22.162'),
(769, '2026-04-26 12:14:11', '/', '83.202.209.82'),
(770, '2026-04-26 12:30:31', '/', '3.208.19.29'),
(771, '2026-04-26 12:30:31', '/', '3.208.19.29'),
(772, '2026-04-26 12:30:31', '/', '3.208.19.29'),
(773, '2026-04-26 12:52:44', '/', '66.146.239.164'),
(774, '2026-04-26 12:55:10', '/', '3.234.213.127'),
(775, '2026-04-26 12:55:10', '/', '32.192.233.251'),
(776, '2026-04-26 13:01:38', '/', '35.197.198.194'),
(777, '2026-04-26 13:24:21', '/', '74.7.227.171'),
(778, '2026-04-26 13:25:27', '/photos-de-match/2', '74.7.227.171'),
(779, '2026-04-26 13:26:29', '/actualites/fc-fleury-91-le-club-de-l-essonne', '74.7.227.171'),
(780, '2026-04-26 13:27:32', '/medias/medias', '74.7.227.171'),
(781, '2026-04-26 13:28:35', '/photos-de-match/', '74.7.227.171'),
(782, '2026-04-26 13:29:37', '/actualites/ultras-lions-fleury-2026-furies', '74.7.227.171'),
(783, '2026-04-26 13:31:38', '/photos-de-match/3', '74.7.227.171'),
(784, '2026-04-26 13:33:44', '/chants', '74.7.227.171'),
(785, '2026-04-26 13:34:45', '/groupe/', '74.7.227.171'),
(786, '2026-04-26 13:35:49', '/billetterie/', '74.7.227.171'),
(787, '2026-04-26 13:39:05', '/photos-de-match/1', '74.7.227.171'),
(788, '2026-04-26 13:42:02', '/', '92.184.144.132'),
(789, '2026-04-26 13:42:07', '/login', '92.184.144.132'),
(790, '2026-04-26 13:42:12', '/', '92.184.144.132'),
(791, '2026-04-26 13:42:13', '/', '92.184.144.132'),
(792, '2026-04-26 13:55:49', '/photos-de-match/', '74.7.227.171'),
(793, '2026-04-26 13:58:55', '/login', '74.7.227.171'),
(794, '2026-04-26 14:03:03', '/invitation', '74.7.227.171'),
(795, '2026-04-26 14:06:14', '/table-de-vente/', '74.7.227.171'),
(796, '2026-04-26 14:10:26', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '74.7.227.171'),
(797, '2026-04-26 14:18:50', '/table-de-vente/2', '74.7.227.171'),
(798, '2026-04-26 14:20:55', '/billetterie/2', '74.7.227.171'),
(799, '2026-04-26 14:22:36', '/', '83.202.209.82'),
(800, '2026-04-26 14:22:42', '/table-de-vente/', '83.202.209.82'),
(801, '2026-04-26 14:26:11', '/forgot-password', '74.7.227.171'),
(802, '2026-04-26 14:28:13', '/actualites/', '74.7.227.171'),
(803, '2026-04-26 14:30:03', '/table-de-vente/panier', '74.7.227.171'),
(804, '2026-04-26 14:30:03', '/panier', '74.7.227.171'),
(805, '2026-04-26 14:45:31', '/', '178.128.83.22'),
(806, '2026-04-26 14:45:32', '/', '178.128.83.22'),
(807, '2026-04-26 14:45:34', '/', '178.128.83.22'),
(808, '2026-04-26 15:10:19', '/', '74.7.241.17'),
(809, '2026-04-26 15:10:22', '/actualites/ultras-lions-fleury-2026-furies', '74.7.241.17'),
(810, '2026-04-26 15:11:27', '/billetterie/2', '74.7.241.17'),
(811, '2026-04-26 15:12:28', '/chants', '74.7.241.17'),
(812, '2026-04-26 15:13:32', '/photos-de-match/', '74.7.241.17'),
(813, '2026-04-26 15:15:38', '/invitation', '74.7.241.17'),
(814, '2026-04-26 15:16:40', '/login', '74.7.241.17'),
(815, '2026-04-26 15:22:51', '/', '88.160.142.5'),
(816, '2026-04-26 15:22:59', '/login', '88.160.142.5'),
(817, '2026-04-26 15:23:09', '/', '88.160.142.5'),
(818, '2026-04-26 15:23:18', '/actualites/', '88.160.142.5'),
(819, '2026-04-26 15:23:51', '/profil/4', '88.160.142.5'),
(820, '2026-04-26 15:23:58', '/billetterie/', '88.160.142.5'),
(821, '2026-04-26 15:24:13', '/', '88.160.142.5'),
(822, '2026-04-26 15:25:30', '/chants', '88.160.142.5'),
(823, '2026-04-26 15:33:30', '/photos-de-match/', '74.7.241.17'),
(824, '2026-04-26 15:35:30', '/', '88.160.142.5'),
(825, '2026-04-26 15:35:34', '/table-de-vente/', '88.160.142.5'),
(826, '2026-04-26 15:35:35', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '74.7.241.17'),
(827, '2026-04-26 15:39:50', '/table-de-vente/', '74.7.241.17'),
(828, '2026-04-26 15:42:55', '/groupe/', '74.7.241.17'),
(829, '2026-04-26 15:44:57', '/actualites/fc-fleury-91-le-club-de-l-essonne', '74.7.241.17'),
(830, '2026-04-26 15:46:02', '/actualites/', '74.7.241.17'),
(831, '2026-04-26 15:49:13', '/billetterie/', '74.7.241.17'),
(832, '2026-04-26 15:52:17', '/forgot-password', '74.7.241.17'),
(833, '2026-04-26 15:53:19', '/photos-de-match/1', '74.7.241.17'),
(834, '2026-04-26 15:55:21', '/photos-de-match/3', '74.7.241.17'),
(835, '2026-04-26 15:56:22', '/photos-de-match/2', '74.7.241.17'),
(836, '2026-04-26 15:57:24', '/table-de-vente/2', '74.7.241.17'),
(837, '2026-04-26 15:58:24', '/medias/medias', '74.7.241.17'),
(838, '2026-04-26 16:17:21', '/table-de-vente/panier', '74.7.241.17'),
(839, '2026-04-26 16:17:21', '/panier', '74.7.241.17'),
(840, '2026-04-26 17:09:48', '/', '101.36.127.85'),
(841, '2026-04-26 19:36:21', '/', '34.41.194.234'),
(842, '2026-04-26 20:00:56', '/', '35.225.29.182'),
(843, '2026-04-26 20:18:53', '/', '83.202.95.42'),
(844, '2026-04-26 20:19:01', '/login', '83.202.95.42'),
(845, '2026-04-26 20:24:54', '/chants', '83.202.95.42');
INSERT INTO `visit` (`id`, `visited_at`, `page`, `ip`) VALUES
(846, '2026-04-26 20:25:12', '/billetterie/', '83.202.95.42'),
(847, '2026-04-26 20:25:18', '/table-de-vente/', '83.202.95.42'),
(848, '2026-04-26 20:25:38', '/groupe/', '83.202.95.42'),
(849, '2026-04-26 20:31:20', '/actualites/', '83.202.95.42'),
(850, '2026-04-26 20:39:52', '/actualites/fc-fleury-91-le-club-de-l-essonne', '90.60.10.226'),
(851, '2026-04-26 21:05:54', '/', '52.10.38.211'),
(852, '2026-04-27 00:16:05', '/', '52.167.144.189'),
(853, '2026-04-27 00:52:54', '/', '165.154.41.152'),
(854, '2026-04-27 00:53:07', '/', '165.154.41.152'),
(855, '2026-04-27 00:53:47', '/', '165.154.182.53'),
(856, '2026-04-27 02:42:41', '/', '34.72.176.129'),
(857, '2026-04-27 03:20:08', '/', '52.167.144.229'),
(858, '2026-04-27 03:44:16', '/', '159.223.1.155'),
(859, '2026-04-27 03:44:17', '/', '159.223.1.155'),
(860, '2026-04-27 04:01:29', '/', '54.174.62.146'),
(861, '2026-04-27 04:01:42', '/', '54.174.58.246'),
(862, '2026-04-27 04:01:43', '/', '54.174.58.241'),
(863, '2026-04-27 04:37:43', '/', '54.174.62.141'),
(864, '2026-04-27 07:31:32', '/', '173.249.22.232'),
(865, '2026-04-27 07:47:03', '/', '34.19.0.6'),
(866, '2026-04-27 07:47:04', '/photos-de-match/1', '34.19.0.6'),
(867, '2026-04-27 07:47:05', '/table-de-vente/', '34.19.0.6'),
(868, '2026-04-27 07:47:05', '/invitation', '34.19.0.6'),
(869, '2026-04-27 07:47:05', '/groupe/', '34.19.0.6'),
(870, '2026-04-27 07:47:05', '/', '34.19.0.6'),
(871, '2026-04-27 07:47:05', '/', '34.19.0.6'),
(872, '2026-04-27 07:47:05', '/photos-de-match/', '34.19.0.6'),
(873, '2026-04-27 07:47:05', '/medias/medias', '34.19.0.6'),
(874, '2026-04-27 07:47:05', '/', '34.19.0.6'),
(875, '2026-04-27 07:47:06', '/login', '34.19.0.6'),
(876, '2026-04-27 07:47:06', '/actualites/', '34.19.0.6'),
(877, '2026-04-27 07:47:06', '/actualites/fc-fleury-91-le-club-de-l-essonne', '34.19.0.6'),
(878, '2026-04-27 07:47:06', '/photos-de-match/3', '34.19.0.6'),
(879, '2026-04-27 07:47:06', '/actualites/ultras-lions-fleury-2026-furies', '34.19.0.6'),
(880, '2026-04-27 07:47:06', '/', '34.19.0.6'),
(881, '2026-04-27 07:47:06', '/', '34.19.0.6'),
(882, '2026-04-27 07:47:06', '/billetterie/', '34.19.0.6'),
(883, '2026-04-27 07:47:06', '/photos-de-match/', '34.19.0.6'),
(884, '2026-04-27 07:47:07', '/chants', '34.19.0.6'),
(885, '2026-04-27 07:47:07', '/billetterie/2', '34.19.0.6'),
(886, '2026-04-27 07:47:07', '/table-de-vente/', '34.19.0.6'),
(887, '2026-04-27 07:47:07', '/groupe/', '34.19.0.6'),
(888, '2026-04-27 07:47:07', '/groupe/', '34.19.0.6'),
(889, '2026-04-27 07:47:07', '/photos-de-match/', '34.19.0.6'),
(890, '2026-04-27 07:47:07', '/groupe/', '34.19.0.6'),
(891, '2026-04-27 07:47:07', '/actualites/', '34.19.0.6'),
(892, '2026-04-27 07:47:08', '/billetterie/', '34.19.0.6'),
(893, '2026-04-27 07:47:08', '/groupe/', '34.19.0.6'),
(894, '2026-04-27 07:47:08', '/photos-de-match/', '34.19.0.6'),
(895, '2026-04-27 07:47:08', '/login', '34.19.0.6'),
(896, '2026-04-27 07:47:08', '/chants', '34.19.0.6'),
(897, '2026-04-27 08:35:52', '/', '122.164.126.53'),
(898, '2026-04-27 09:16:57', '/', '122.164.126.53'),
(899, '2026-04-27 09:18:40', '/', '122.164.126.53'),
(900, '2026-04-27 09:25:00', '/', '122.164.126.53'),
(901, '2026-04-27 09:25:01', '/', '122.164.126.53'),
(902, '2026-04-27 09:25:04', '/', '122.164.126.53'),
(903, '2026-04-27 09:26:18', '/', '122.164.126.53'),
(904, '2026-04-27 09:26:22', '/', '122.164.126.53'),
(905, '2026-04-27 09:26:24', '/', '122.164.126.53'),
(906, '2026-04-27 12:06:15', '/', '78.117.235.83'),
(907, '2026-04-27 12:13:29', '/', '178.156.229.216'),
(908, '2026-04-27 12:13:44', '/', '178.156.229.216'),
(909, '2026-04-27 12:14:58', '/', '178.156.229.216'),
(910, '2026-04-27 12:41:48', '/', '3.81.123.53'),
(911, '2026-04-27 12:41:48', '/', '3.81.123.53'),
(912, '2026-04-27 12:41:49', '/', '3.81.123.53'),
(913, '2026-04-27 12:58:47', '/', '68.183.177.200'),
(914, '2026-04-27 12:58:48', '/', '68.183.177.200'),
(915, '2026-04-27 12:58:49', '/', '68.183.177.200'),
(916, '2026-04-27 13:03:09', '/', '152.42.251.170'),
(917, '2026-04-27 13:03:09', '/', '152.42.251.170'),
(918, '2026-04-27 13:03:11', '/', '152.42.251.170'),
(919, '2026-04-27 13:46:25', '/', '23.27.145.48'),
(920, '2026-04-27 15:45:07', '/', '164.177.26.251'),
(921, '2026-04-27 15:45:17', '/table-de-vente/', '164.177.26.251'),
(922, '2026-04-27 15:45:22', '/table-de-vente/1', '164.177.26.251'),
(923, '2026-04-27 15:45:32', '/billetterie/', '164.177.26.251'),
(924, '2026-04-27 15:45:43', '/table-de-vente/', '164.177.26.251'),
(925, '2026-04-27 15:45:47', '/billetterie/', '164.177.26.251'),
(926, '2026-04-27 15:45:51', '/table-de-vente/', '164.177.26.251'),
(927, '2026-04-27 15:45:54', '/table-de-vente/2', '164.177.26.251'),
(928, '2026-04-27 15:47:10', '/photos-de-match/', '164.177.26.251'),
(929, '2026-04-27 15:47:18', '/photos-de-match/1', '164.177.26.251'),
(930, '2026-04-27 15:47:27', '/actualites/', '164.177.26.251'),
(931, '2026-04-27 15:47:29', '/actualites/ultras-lions-fleury-2026-furies', '164.177.26.251'),
(932, '2026-04-27 15:47:39', '/', '164.177.26.251'),
(933, '2026-04-27 15:47:40', '/table-de-vente/', '164.177.26.251'),
(934, '2026-04-27 15:47:52', '/groupe/', '164.177.26.251'),
(935, '2026-04-27 15:48:01', '/profil/2', '164.177.26.251'),
(936, '2026-04-27 15:48:10', '/profil/commande/11', '164.177.26.251'),
(937, '2026-04-27 15:48:12', '/merci/pdf', '164.177.26.251'),
(938, '2026-04-27 15:48:29', '/profil/commande/10', '164.177.26.251'),
(939, '2026-04-27 15:48:33', '/profil/commande/4', '164.177.26.251'),
(940, '2026-04-27 15:48:39', '/profil/commande/5', '164.177.26.251'),
(941, '2026-04-27 15:48:42', '/merci/pdf', '164.177.26.251'),
(942, '2026-04-27 15:48:59', '/groupe/', '164.177.26.251'),
(943, '2026-04-27 15:49:02', '/', '164.177.26.251'),
(944, '2026-04-27 15:49:13', '/groupe/', '164.177.26.251'),
(945, '2026-04-27 15:49:25', '/chants', '164.177.26.251'),
(946, '2026-04-27 15:56:32', '/', '147.182.213.201'),
(947, '2026-04-27 15:56:33', '/', '147.182.213.201'),
(948, '2026-04-27 16:39:46', '/', '68.183.177.200'),
(949, '2026-04-27 16:39:47', '/', '68.183.177.200'),
(950, '2026-04-27 16:39:49', '/', '68.183.177.200'),
(951, '2026-04-27 16:51:08', '/', '34.138.167.48'),
(952, '2026-04-27 16:51:09', '/', '34.138.167.48'),
(953, '2026-04-27 16:51:10', '/', '34.138.167.48'),
(954, '2026-04-27 16:51:31', '/', '35.237.215.43'),
(955, '2026-04-27 16:51:32', '/', '35.237.215.43'),
(956, '2026-04-27 16:51:32', '/', '35.237.215.43'),
(957, '2026-04-27 17:25:24', '/', '52.167.144.17'),
(958, '2026-04-27 17:44:29', '/', '43.98.172.125'),
(959, '2026-04-27 17:44:29', '/', '43.98.191.195'),
(960, '2026-04-27 17:57:54', '/', '118.193.33.85'),
(961, '2026-04-27 18:01:35', '/', '152.32.212.179'),
(962, '2026-04-27 18:01:37', '/', '152.32.212.179'),
(963, '2026-04-27 18:13:20', '/', '155.2.225.177'),
(964, '2026-04-27 18:14:59', '/', '155.2.225.177'),
(965, '2026-04-27 18:59:34', '/login', '17.246.15.218'),
(966, '2026-04-27 19:21:38', '/', '13.58.219.244'),
(967, '2026-04-27 19:25:17', '/table-de-vente/', '17.246.23.92'),
(968, '2026-04-27 20:06:30', '/', '81.53.36.105'),
(969, '2026-04-27 20:27:51', '/photos-de-match/', '17.241.219.234'),
(970, '2026-04-27 20:28:05', '/', '167.86.92.224'),
(971, '2026-04-27 20:30:26', '/', '40.77.167.121'),
(972, '2026-04-27 20:51:54', '/actualites/fc-fleury-91-le-club-de-l-essonne', '90.60.10.226'),
(973, '2026-04-27 21:22:41', '/billetterie/', '52.167.144.166'),
(974, '2026-04-27 21:57:41', '/actualites/', '17.241.227.214'),
(975, '2026-04-27 22:05:20', '/billetterie/', '17.241.227.223'),
(976, '2026-04-27 22:09:53', '/', '23.27.145.0'),
(977, '2026-04-27 22:15:18', '/', '149.57.180.198'),
(978, '2026-04-27 22:32:04', '/', '83.202.95.42'),
(979, '2026-04-27 22:32:13', '/login', '83.202.95.42'),
(980, '2026-04-27 22:32:16', '/', '83.202.95.42'),
(981, '2026-04-27 22:44:57', '/', '83.202.95.42'),
(982, '2026-04-27 22:44:59', '/', '83.202.95.42'),
(983, '2026-04-27 22:46:12', '/', '83.202.95.42'),
(984, '2026-04-27 22:46:16', '/billetterie/', '83.202.95.42'),
(985, '2026-04-27 22:48:35', '/', '88.160.142.5'),
(986, '2026-04-27 22:48:42', '/login', '88.160.142.5'),
(987, '2026-04-27 22:48:51', '/', '88.160.142.5'),
(988, '2026-04-27 22:48:53', '/', '88.160.142.5'),
(989, '2026-04-27 22:48:59', '/billetterie/', '88.160.142.5'),
(990, '2026-04-27 22:49:06', '/', '88.160.142.5'),
(991, '2026-04-27 22:49:56', '/actualites/', '17.22.253.235'),
(992, '2026-04-27 22:51:02', '/billetterie/', '83.202.95.42'),
(993, '2026-04-27 22:51:05', '/billetterie/3', '83.202.95.42'),
(994, '2026-04-27 22:51:13', '/', '83.202.95.42'),
(995, '2026-04-27 22:51:15', '/billetterie/', '83.202.95.42'),
(996, '2026-04-27 22:51:20', '/login', '83.202.95.42'),
(997, '2026-04-27 22:51:33', '/billetterie/', '83.202.95.42'),
(998, '2026-04-27 22:51:35', '/billetterie/3', '83.202.95.42'),
(999, '2026-04-27 22:51:42', '/billetterie/3', '83.202.95.42'),
(1000, '2026-04-27 22:52:55', '/billetterie/3', '78.240.64.94'),
(1001, '2026-04-27 22:54:24', '/table-de-vente/', '78.240.64.94'),
(1002, '2026-04-27 22:54:38', '/billetterie/3', '83.202.95.42'),
(1003, '2026-04-27 22:54:52', '/billetterie/', '78.240.64.94'),
(1004, '2026-04-27 22:54:54', '/billetterie/3', '78.240.64.94'),
(1005, '2026-04-27 22:54:55', '/billetterie/3', '78.242.160.208'),
(1006, '2026-04-27 22:54:55', '/billetterie/3', '78.197.211.90'),
(1007, '2026-04-27 22:54:57', '/', '83.202.95.42'),
(1008, '2026-04-27 22:55:07', '/chants', '78.240.64.94'),
(1009, '2026-04-27 22:55:24', '/', '88.184.40.219'),
(1010, '2026-04-27 22:55:24', '/', '83.202.95.42'),
(1011, '2026-04-27 22:55:26', '/login', '83.202.95.42'),
(1012, '2026-04-27 22:55:32', '/billetterie/', '88.160.142.5'),
(1013, '2026-04-27 22:55:36', '/billetterie/3', '88.160.142.5'),
(1014, '2026-04-27 22:55:44', '/', '78.197.211.90'),
(1015, '2026-04-27 22:56:04', '/', '88.184.40.219'),
(1016, '2026-04-27 22:56:11', '/billetterie/3', '88.184.40.219'),
(1017, '2026-04-27 22:56:15', '/', '83.202.95.42'),
(1018, '2026-04-27 22:56:17', '/billetterie/3/billetweb-preinscription', '88.160.142.5'),
(1019, '2026-04-27 22:56:17', '/billetterie/3', '88.160.142.5'),
(1020, '2026-04-27 22:56:31', '/billetterie/3/billetweb-preinscription', '88.184.40.219'),
(1021, '2026-04-27 22:56:31', '/billetterie/3', '88.184.40.219'),
(1022, '2026-04-27 22:56:41', '/', '83.202.95.42'),
(1023, '2026-04-27 22:56:42', '/login', '83.202.95.42'),
(1024, '2026-04-27 22:56:51', '/', '83.202.95.42'),
(1025, '2026-04-27 22:56:57', '/billetterie/', '83.202.95.42'),
(1026, '2026-04-27 22:56:59', '/billetterie/3', '83.202.95.42'),
(1027, '2026-04-27 22:57:02', '/billetterie/3/billetweb-preinscription', '83.202.95.42'),
(1028, '2026-04-27 22:57:02', '/billetterie/3', '83.202.95.42'),
(1029, '2026-04-27 22:57:04', '/', '88.160.142.5'),
(1030, '2026-04-27 22:57:07', '/billetterie/', '88.160.142.5'),
(1031, '2026-04-27 22:57:09', '/billetterie/3', '88.160.142.5'),
(1032, '2026-04-27 22:57:34', '/profil/3', '83.202.95.42'),
(1033, '2026-04-27 22:58:38', '/', '88.160.142.5'),
(1034, '2026-04-27 22:58:41', '/billetterie/', '88.160.142.5'),
(1035, '2026-04-27 22:58:44', '/billetterie/3', '88.160.142.5'),
(1036, '2026-04-27 23:00:36', '/', '83.202.95.42'),
(1037, '2026-04-27 23:00:38', '/', '88.160.142.5'),
(1038, '2026-04-27 23:00:40', '/login', '83.202.95.42'),
(1039, '2026-04-27 23:00:42', '/', '88.160.142.5'),
(1040, '2026-04-27 23:00:43', '/', '88.160.142.5'),
(1041, '2026-04-27 23:00:44', '/', '83.202.95.42'),
(1042, '2026-04-27 23:00:45', '/', '88.160.142.5'),
(1043, '2026-04-27 23:00:47', '/billetterie/', '83.202.95.42'),
(1044, '2026-04-27 23:00:50', '/billetterie/3', '83.202.95.42'),
(1045, '2026-04-27 23:00:53', '/billetterie/3/billetweb-preinscription', '83.202.95.42'),
(1046, '2026-04-27 23:00:54', '/billetterie/3', '83.202.95.42'),
(1047, '2026-04-27 23:00:56', '/billetterie/', '88.160.142.5'),
(1048, '2026-04-27 23:01:00', '/billetterie/', '88.160.142.5'),
(1049, '2026-04-27 23:01:03', '/billetterie/3', '88.160.142.5'),
(1050, '2026-04-27 23:01:18', '/', '88.160.142.5'),
(1051, '2026-04-27 23:01:18', '/', '77.133.249.29'),
(1052, '2026-04-27 23:01:22', '/billetterie/', '88.160.142.5'),
(1053, '2026-04-27 23:01:23', '/billetterie/', '77.133.249.29'),
(1054, '2026-04-27 23:01:27', '/billetterie/3', '77.133.249.29'),
(1055, '2026-04-27 23:01:28', '/login', '88.160.142.5'),
(1056, '2026-04-27 23:01:29', '/billetterie/3', '66.249.93.14'),
(1057, '2026-04-27 23:01:30', '/billetterie/3', '74.125.210.70'),
(1058, '2026-04-27 23:01:30', '/billetterie/3', '74.125.210.71'),
(1059, '2026-04-27 23:01:35', '/', '88.160.142.5'),
(1060, '2026-04-27 23:01:45', '/login', '77.133.249.29'),
(1061, '2026-04-27 23:01:49', '/billetterie/', '88.160.142.5'),
(1062, '2026-04-27 23:01:50', '/login', '213.44.27.202'),
(1063, '2026-04-27 23:01:50', '/billetterie/3', '88.160.142.5'),
(1064, '2026-04-27 23:01:56', '/billetterie/3/billetweb-preinscription', '88.160.142.5'),
(1065, '2026-04-27 23:01:56', '/billetterie/3', '88.160.142.5'),
(1066, '2026-04-27 23:03:35', '/', '88.160.142.5'),
(1067, '2026-04-27 23:03:39', '/billetterie/', '88.160.142.5'),
(1068, '2026-04-27 23:03:40', '/billetterie/3', '88.160.142.5'),
(1069, '2026-04-27 23:04:55', '/', '88.160.142.5'),
(1070, '2026-04-27 23:05:03', '/billetterie/', '88.160.142.5'),
(1071, '2026-04-27 23:05:06', '/billetterie/3', '88.160.142.5'),
(1072, '2026-04-27 23:05:19', '/billetterie/3/billetweb-preinscription', '88.160.142.5'),
(1073, '2026-04-27 23:05:19', '/billetterie/3', '88.160.142.5'),
(1074, '2026-04-27 23:07:43', '/', '88.160.142.5'),
(1075, '2026-04-27 23:07:49', '/billetterie/', '88.160.142.5'),
(1076, '2026-04-27 23:07:56', '/', '88.160.142.5'),
(1077, '2026-04-27 23:08:16', '/', '172.225.116.191'),
(1078, '2026-04-27 23:08:40', '/billetterie/', '172.225.116.191'),
(1079, '2026-04-27 23:08:43', '/billetterie/3', '172.225.116.191'),
(1080, '2026-04-27 23:09:03', '/chants', '172.225.116.191'),
(1081, '2026-04-27 23:12:06', '/table-de-vente/', '172.225.116.191'),
(1082, '2026-04-27 23:12:12', '/table-de-vente/2', '172.225.116.191'),
(1083, '2026-04-27 23:13:13', '/billetterie/', '83.202.95.42'),
(1084, '2026-04-27 23:13:16', '/table-de-vente/', '83.202.95.42'),
(1085, '2026-04-27 23:13:28', '/table-de-vente/1', '83.202.95.42'),
(1086, '2026-04-27 23:13:32', '/table-de-vente/', '83.202.95.42'),
(1087, '2026-04-27 23:13:39', '/billetterie/', '83.202.95.42'),
(1088, '2026-04-27 23:13:42', '/table-de-vente/', '83.202.95.42'),
(1089, '2026-04-27 23:13:44', '/actualites/', '83.202.95.42'),
(1090, '2026-04-27 23:13:50', '/', '83.202.95.42'),
(1091, '2026-04-27 23:13:59', '/profil/3', '83.202.95.42'),
(1092, '2026-04-27 23:14:05', '/billetterie/', '83.202.95.42'),
(1093, '2026-04-27 23:14:07', '/billetterie/3', '83.202.95.42'),
(1094, '2026-04-27 23:14:23', '/photos-de-match/', '83.202.95.42'),
(1095, '2026-04-27 23:14:30', '/profil/3', '83.202.95.42'),
(1096, '2026-04-27 23:17:26', '/', '83.202.95.42'),
(1097, '2026-04-27 23:17:30', '/photos-de-match/', '83.202.95.42'),
(1098, '2026-04-27 23:17:36', '/photos-de-match/1', '83.202.95.42'),
(1099, '2026-04-27 23:35:05', '/', '34.67.231.19'),
(1100, '2026-04-27 23:35:05', '/', '34.67.231.19'),
(1101, '2026-04-27 23:35:07', '/', '34.67.231.19'),
(1102, '2026-04-27 23:36:13', '/', '35.204.157.136'),
(1103, '2026-04-27 23:50:17', '/', '83.202.95.42'),
(1104, '2026-04-27 23:58:27', '/', '88.161.222.224'),
(1105, '2026-04-27 23:58:32', '/billetterie/', '88.161.222.224'),
(1106, '2026-04-27 23:58:42', '/groupe/', '88.161.222.224'),
(1107, '2026-04-27 23:59:25', '/', '170.64.159.75'),
(1108, '2026-04-27 23:59:25', '/', '170.64.159.75'),
(1109, '2026-04-27 23:59:27', '/', '170.64.159.75'),
(1110, '2026-04-27 23:59:33', '/billetterie/', '88.161.222.224'),
(1111, '2026-04-27 23:59:39', '/invitation', '88.161.222.224'),
(1112, '2026-04-28 00:00:13', '/invitation', '88.161.222.224'),
(1113, '2026-04-28 00:00:13', '/register/15afbf2c285dc935836b4c10f1cbdeaf', '88.161.222.224'),
(1114, '2026-04-28 00:02:14', '/', '83.202.95.42'),
(1115, '2026-04-28 00:02:14', '/', '83.202.95.42'),
(1116, '2026-04-28 00:02:18', '/login', '83.202.95.42'),
(1117, '2026-04-28 00:02:21', '/', '83.202.95.42'),
(1118, '2026-04-28 00:02:22', '/photos-de-match/2', '83.202.95.42'),
(1119, '2026-04-28 00:02:29', '/register/15afbf2c285dc935836b4c10f1cbdeaf', '88.161.222.224'),
(1120, '2026-04-28 00:03:23', '/register/15afbf2c285dc935836b4c10f1cbdeaf', '88.161.222.224'),
(1121, '2026-04-28 00:03:24', '/', '88.161.222.224'),
(1122, '2026-04-28 00:03:31', '/profil/5', '88.161.222.224'),
(1123, '2026-04-28 00:03:37', '/profil/5/edit', '88.161.222.224'),
(1124, '2026-04-28 00:03:43', '/profil/5/edit', '88.161.222.224'),
(1125, '2026-04-28 00:03:44', '/profil/5', '88.161.222.224'),
(1126, '2026-04-28 00:04:05', '/billetterie/', '88.161.222.224'),
(1127, '2026-04-28 00:04:09', '/billetterie/3', '83.202.95.42'),
(1128, '2026-04-28 00:04:10', '/billetterie/3', '88.161.222.224'),
(1129, '2026-04-28 00:04:20', '/billetterie/3/billetweb-preinscription', '88.161.222.224'),
(1130, '2026-04-28 00:04:20', '/billetterie/3', '88.161.222.224'),
(1131, '2026-04-28 00:07:08', '/table-de-vente/', '88.161.222.224'),
(1132, '2026-04-28 00:07:18', '/table-de-vente/', '88.161.222.224'),
(1133, '2026-04-28 00:07:30', '/table-de-vente/2', '88.161.222.224'),
(1134, '2026-04-28 00:09:03', '/actualites/', '88.161.222.224'),
(1135, '2026-04-28 00:09:31', '/chants', '88.161.222.224'),
(1136, '2026-04-28 00:11:35', '/', '88.161.222.224'),
(1137, '2026-04-28 00:11:38', '/photos-de-match/2', '88.161.222.224'),
(1138, '2026-04-28 00:23:49', '/', '88.160.142.5'),
(1139, '2026-04-28 00:23:53', '/billetterie/', '88.160.142.5'),
(1140, '2026-04-28 00:23:55', '/billetterie/3', '88.160.142.5'),
(1141, '2026-04-28 00:43:10', '/', '52.167.144.173'),
(1142, '2026-04-28 00:48:17', '/', '195.211.77.141'),
(1143, '2026-04-28 00:48:36', '/', '195.211.77.141'),
(1144, '2026-04-28 01:02:24', '/photos-de-match/', '17.22.245.43'),
(1145, '2026-04-28 01:12:05', '/', '176.187.25.207'),
(1146, '2026-04-28 01:12:44', '/billetterie/', '176.187.25.207'),
(1147, '2026-04-28 01:13:11', '/chants', '176.187.25.207'),
(1148, '2026-04-28 01:14:20', '/medias/medias', '17.22.245.195'),
(1149, '2026-04-28 01:23:27', '/table-de-vente/', '17.241.75.94'),
(1150, '2026-04-28 01:25:40', '/invitation', '17.241.227.230'),
(1151, '2026-04-28 01:58:32', '/', '103.248.93.51'),
(1152, '2026-04-28 01:58:54', '/login', '17.22.253.64'),
(1153, '2026-04-28 02:12:22', '/', '88.160.142.5'),
(1154, '2026-04-28 02:47:43', '/actualites/', '17.246.19.217'),
(1155, '2026-04-28 03:05:45', '/billetterie/', '17.241.219.162'),
(1156, '2026-04-28 03:41:03', '/invitation', '17.241.227.93'),
(1157, '2026-04-28 03:52:38', '/chants', '40.77.167.254'),
(1158, '2026-04-28 04:04:28', '/', '149.57.180.182'),
(1159, '2026-04-28 04:05:30', '/billetterie/', '17.241.75.235'),
(1160, '2026-04-28 04:44:55', '/', '40.77.167.16'),
(1161, '2026-04-28 05:32:07', '/chants', '17.22.237.80'),
(1162, '2026-04-28 05:46:00', '/groupe/', '17.246.15.13'),
(1163, '2026-04-28 05:47:42', '/', '198.13.62.122'),
(1164, '2026-04-28 05:47:52', '/invitation', '17.241.75.69'),
(1165, '2026-04-28 06:03:01', '/chants', '17.246.15.118'),
(1166, '2026-04-28 06:03:26', '/invitation', '52.167.144.173'),
(1167, '2026-04-28 06:08:32', '/', '51.210.245.68'),
(1168, '2026-04-28 06:08:34', '/', '51.210.245.68'),
(1169, '2026-04-28 06:15:11', '/groupe/', '17.246.19.11'),
(1170, '2026-04-28 06:43:06', '/chants', '17.22.245.34'),
(1171, '2026-04-28 07:08:55', '/medias/medias', '17.241.219.45'),
(1172, '2026-04-28 07:36:33', '/login', '83.202.95.42'),
(1173, '2026-04-28 07:36:39', '/', '83.202.95.42'),
(1174, '2026-04-28 07:39:09', '/billetterie/', '17.246.23.127'),
(1175, '2026-04-28 07:54:07', '/photos-de-match/', '17.246.23.128'),
(1176, '2026-04-28 07:57:13', '/medias/medias', '17.241.227.79'),
(1177, '2026-04-28 08:01:00', '/', '15.237.220.125'),
(1178, '2026-04-28 08:01:02', '/', '15.237.220.125'),
(1179, '2026-04-28 08:03:52', '/medias/medias', '17.241.227.223'),
(1180, '2026-04-28 08:08:46', '/', '92.184.144.76'),
(1181, '2026-04-28 08:10:00', '/', '88.184.40.219'),
(1182, '2026-04-28 08:10:45', '/login', '88.184.40.219'),
(1183, '2026-04-28 08:10:54', '/', '88.184.40.219'),
(1184, '2026-04-28 08:12:29', '/login', '17.22.237.104'),
(1185, '2026-04-28 08:16:12', '/photos-de-match/', '17.22.253.209'),
(1186, '2026-04-28 08:22:38', '/login', '92.184.144.76'),
(1187, '2026-04-28 08:22:43', '/', '92.184.144.76'),
(1188, '2026-04-28 08:27:22', '/billetterie/3', '172.225.189.228'),
(1189, '2026-04-28 08:28:11', '/billetterie/3/billetweb-preinscription', '104.28.42.14'),
(1190, '2026-04-28 08:28:11', '/billetterie/3', '104.28.42.14'),
(1191, '2026-04-28 08:29:57', '/billetterie/3', '92.184.144.76'),
(1192, '2026-04-28 08:31:04', '/', '104.28.42.27'),
(1193, '2026-04-28 08:31:15', '/billetterie/3', '173.252.107.4'),
(1194, '2026-04-28 08:31:36', '/table-de-vente/', '104.28.42.27'),
(1195, '2026-04-28 08:31:43', '/table-de-vente/2', '104.28.42.27'),
(1196, '2026-04-28 08:31:45', '/table-de-vente/2', '104.28.42.27'),
(1197, '2026-04-28 08:31:46', '/billetterie/3', '173.252.95.13'),
(1198, '2026-04-28 08:33:17', '/billetterie/3', '141.255.130.116'),
(1199, '2026-04-28 08:34:09', '/billetterie/3/billetweb-preinscription', '141.255.130.116'),
(1200, '2026-04-28 08:34:10', '/billetterie/3', '141.255.130.116'),
(1201, '2026-04-28 08:42:32', '/', '107.122.71.97'),
(1202, '2026-04-28 08:42:33', '/', '107.122.71.97'),
(1203, '2026-04-28 08:42:34', '/', '194.113.210.242'),
(1204, '2026-04-28 08:49:14', '/login', '92.184.144.76'),
(1205, '2026-04-28 08:49:19', '/', '92.184.144.76'),
(1206, '2026-04-28 08:58:56', '/', '176.191.72.99'),
(1207, '2026-04-28 08:59:00', '/chants', '176.191.72.99'),
(1208, '2026-04-28 09:13:37', '/table-de-vente/', '17.246.15.65'),
(1209, '2026-04-28 09:17:55', '/', '204.76.203.25'),
(1210, '2026-04-28 10:07:04', '/login', '164.177.26.251'),
(1211, '2026-04-28 10:07:13', '/login', '164.177.26.251'),
(1212, '2026-04-28 10:07:18', '/', '164.177.26.251'),
(1213, '2026-04-28 10:07:18', '/', '164.177.26.251'),
(1214, '2026-04-28 10:07:19', '/login', '164.177.26.251'),
(1215, '2026-04-28 10:07:24', '/', '164.177.26.251'),
(1216, '2026-04-28 10:18:41', '/groupe/', '17.241.227.73'),
(1217, '2026-04-28 10:19:02', '/groupe/', '17.22.237.236'),
(1218, '2026-04-28 10:46:13', '/actualites/fc-fleury-91-le-club-de-l-essonne', '90.60.10.226'),
(1219, '2026-04-28 10:46:19', '/', '90.60.10.226'),
(1220, '2026-04-28 10:59:51', '/groupe/', '17.22.237.90'),
(1221, '2026-04-28 11:00:59', '/', '106.219.161.93'),
(1222, '2026-04-28 11:00:59', '/', '106.219.161.93'),
(1223, '2026-04-28 11:01:00', '/billetterie/', '106.219.161.93'),
(1224, '2026-04-28 11:01:00', '/', '106.219.161.93'),
(1225, '2026-04-28 11:01:00', '/billetterie/3', '106.219.161.93'),
(1226, '2026-04-28 11:01:01', '/groupe/', '106.219.161.93'),
(1227, '2026-04-28 11:01:01', '/photos-de-match/1', '106.219.161.93'),
(1228, '2026-04-28 11:01:02', '/actualites/', '106.219.161.93'),
(1229, '2026-04-28 11:01:02', '/actualites/ultras-lions-fleury-2026-furies', '106.219.161.93'),
(1230, '2026-04-28 11:01:03', '/chants', '106.219.161.93'),
(1231, '2026-04-28 11:01:03', '/photos-de-match/3', '106.219.161.93'),
(1232, '2026-04-28 11:01:03', '/photos-de-match/', '106.219.161.93'),
(1233, '2026-04-28 11:01:04', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '106.219.161.93'),
(1234, '2026-04-28 11:01:04', '/medias/medias', '106.219.161.93'),
(1235, '2026-04-28 11:01:05', '/actualites/fc-fleury-91-le-club-de-l-essonne', '106.219.161.93'),
(1236, '2026-04-28 11:01:05', '/photos-de-match/', '106.219.161.93'),
(1237, '2026-04-28 11:01:06', '/photos-de-match/2', '106.219.161.93'),
(1238, '2026-04-28 11:01:06', '/table-de-vente/', '106.219.161.93'),
(1239, '2026-04-28 11:01:06', '/invitation', '106.219.161.93'),
(1240, '2026-04-28 11:01:07', '/login', '106.219.161.93'),
(1241, '2026-04-28 11:28:07', '/', '106.219.164.146'),
(1242, '2026-04-28 11:28:08', '/', '106.219.164.146'),
(1243, '2026-04-28 11:28:09', '/billetterie/', '106.219.164.146'),
(1244, '2026-04-28 11:28:09', '/', '106.219.164.146'),
(1245, '2026-04-28 11:28:10', '/billetterie/3', '106.219.164.146'),
(1246, '2026-04-28 11:28:11', '/groupe/', '106.219.164.146'),
(1247, '2026-04-28 11:28:11', '/photos-de-match/1', '106.219.164.146'),
(1248, '2026-04-28 11:28:12', '/actualites/', '106.219.164.146'),
(1249, '2026-04-28 11:28:13', '/actualites/ultras-lions-fleury-2026-furies', '106.219.164.146'),
(1250, '2026-04-28 11:28:13', '/chants', '106.219.164.146'),
(1251, '2026-04-28 11:28:14', '/photos-de-match/3', '106.219.164.146'),
(1252, '2026-04-28 11:28:15', '/photos-de-match/', '106.219.164.146'),
(1253, '2026-04-28 11:28:15', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '106.219.164.146'),
(1254, '2026-04-28 11:28:16', '/medias/medias', '106.219.164.146'),
(1255, '2026-04-28 11:28:17', '/actualites/fc-fleury-91-le-club-de-l-essonne', '106.219.164.146'),
(1256, '2026-04-28 11:28:17', '/photos-de-match/', '106.219.164.146'),
(1257, '2026-04-28 11:28:18', '/photos-de-match/2', '106.219.164.146'),
(1258, '2026-04-28 11:28:19', '/table-de-vente/', '106.219.164.146'),
(1259, '2026-04-28 11:28:19', '/invitation', '106.219.164.146'),
(1260, '2026-04-28 11:28:20', '/login', '106.219.164.146'),
(1261, '2026-04-28 11:56:40', '/', '106.219.162.169'),
(1262, '2026-04-28 11:56:42', '/', '106.219.162.169'),
(1263, '2026-04-28 11:56:43', '/billetterie/', '106.219.162.169'),
(1264, '2026-04-28 11:56:44', '/', '106.219.162.169'),
(1265, '2026-04-28 11:56:45', '/billetterie/3', '106.219.162.169'),
(1266, '2026-04-28 11:56:45', '/groupe/', '106.219.162.169'),
(1267, '2026-04-28 11:56:53', '/photos-de-match/1', '106.219.162.169'),
(1268, '2026-04-28 11:56:54', '/actualites/', '106.219.162.169'),
(1269, '2026-04-28 11:56:57', '/actualites/ultras-lions-fleury-2026-furies', '106.219.162.169'),
(1270, '2026-04-28 11:56:59', '/chants', '106.219.162.169'),
(1271, '2026-04-28 11:57:00', '/photos-de-match/3', '106.219.162.169'),
(1272, '2026-04-28 11:57:02', '/photos-de-match/', '106.219.162.169'),
(1273, '2026-04-28 11:57:05', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '106.219.162.169'),
(1274, '2026-04-28 11:57:13', '/medias/medias', '106.219.162.169'),
(1275, '2026-04-28 11:57:13', '/actualites/fc-fleury-91-le-club-de-l-essonne', '106.219.162.169'),
(1276, '2026-04-28 11:57:17', '/photos-de-match/', '106.219.162.169'),
(1277, '2026-04-28 11:57:18', '/photos-de-match/2', '106.219.162.169'),
(1278, '2026-04-28 11:57:22', '/table-de-vente/', '106.219.162.169'),
(1279, '2026-04-28 11:57:23', '/invitation', '106.219.162.169'),
(1280, '2026-04-28 11:57:25', '/login', '106.219.162.169'),
(1281, '2026-04-28 12:00:57', '/', '18.117.90.85'),
(1282, '2026-04-28 12:25:38', '/login', '92.184.144.194'),
(1283, '2026-04-28 12:26:58', '/', '92.184.144.194'),
(1284, '2026-04-28 12:27:04', '/', '92.184.144.194'),
(1285, '2026-04-28 12:28:33', '/', '83.202.209.82'),
(1286, '2026-04-28 12:28:45', '/invitation', '83.202.209.82'),
(1287, '2026-04-28 12:28:59', '/invitation', '83.202.209.82'),
(1288, '2026-04-28 12:29:00', '/register/5cc3b894e2c24397d1b9fba3329c3ab5', '83.202.209.82'),
(1289, '2026-04-28 12:32:27', '/register/5cc3b894e2c24397d1b9fba3329c3ab5', '83.202.209.82'),
(1290, '2026-04-28 12:32:51', '/register/5cc3b894e2c24397d1b9fba3329c3ab5', '83.202.209.82'),
(1291, '2026-04-28 12:33:09', '/register/5cc3b894e2c24397d1b9fba3329c3ab5', '83.202.209.82'),
(1292, '2026-04-28 12:34:52', '/register/5cc3b894e2c24397d1b9fba3329c3ab5', '83.202.209.82'),
(1293, '2026-04-28 12:35:03', '/register/5cc3b894e2c24397d1b9fba3329c3ab5', '83.202.209.82'),
(1294, '2026-04-28 12:35:06', '/register/5cc3b894e2c24397d1b9fba3329c3ab5', '83.202.209.82'),
(1295, '2026-04-28 12:36:11', '/register/5cc3b894e2c24397d1b9fba3329c3ab5', '83.202.209.82'),
(1296, '2026-04-28 12:39:42', '/register/5cc3b894e2c24397d1b9fba3329c3ab5', '83.202.209.82'),
(1297, '2026-04-28 12:40:40', '/', '106.219.160.97'),
(1298, '2026-04-28 12:40:41', '/', '106.219.160.97'),
(1299, '2026-04-28 12:40:42', '/billetterie/', '106.219.160.97'),
(1300, '2026-04-28 12:40:43', '/', '106.219.160.97'),
(1301, '2026-04-28 12:40:44', '/billetterie/3', '106.219.160.97'),
(1302, '2026-04-28 12:40:45', '/groupe/', '106.219.160.97'),
(1303, '2026-04-28 12:40:46', '/photos-de-match/1', '106.219.160.97'),
(1304, '2026-04-28 12:40:47', '/actualites/', '106.219.160.97'),
(1305, '2026-04-28 12:40:48', '/actualites/ultras-lions-fleury-2026-furies', '106.219.160.97'),
(1306, '2026-04-28 12:40:49', '/chants', '106.219.160.97'),
(1307, '2026-04-28 12:40:50', '/photos-de-match/3', '106.219.160.97'),
(1308, '2026-04-28 12:40:51', '/photos-de-match/', '106.219.160.97'),
(1309, '2026-04-28 12:40:52', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '106.219.160.97'),
(1310, '2026-04-28 12:40:53', '/medias/medias', '106.219.160.97'),
(1311, '2026-04-28 12:40:54', '/actualites/fc-fleury-91-le-club-de-l-essonne', '106.219.160.97'),
(1312, '2026-04-28 12:40:54', '/photos-de-match/', '106.219.160.97'),
(1313, '2026-04-28 12:45:08', '/', '68.183.177.200'),
(1314, '2026-04-28 12:45:09', '/', '68.183.177.200'),
(1315, '2026-04-28 12:45:10', '/', '68.183.177.200'),
(1316, '2026-04-28 12:45:09', '/register/5cc3b894e2c24397d1b9fba3329c3ab5', '83.202.209.82'),
(1317, '2026-04-28 12:45:10', '/', '83.202.209.82'),
(1318, '2026-04-28 12:45:25', '/', '54.158.186.150'),
(1319, '2026-04-28 12:45:25', '/', '54.158.186.150'),
(1320, '2026-04-28 12:45:25', '/', '54.158.186.150'),
(1321, '2026-04-28 12:50:16', '/', '152.42.217.206'),
(1322, '2026-04-28 12:50:17', '/', '152.42.217.206'),
(1323, '2026-04-28 12:50:18', '/', '152.42.217.206'),
(1324, '2026-04-28 12:51:38', '/table-de-vente/', '83.202.209.82'),
(1325, '2026-04-28 12:51:52', '/billetterie/', '83.202.209.82'),
(1326, '2026-04-28 12:52:35', '/billetterie/3', '83.202.209.82'),
(1327, '2026-04-28 13:08:06', '/', '220.130.196.40'),
(1328, '2026-04-28 13:22:45', '/actualites/', '17.241.227.215'),
(1329, '2026-04-28 13:29:27', '/billetterie/3/billetweb-preinscription', '83.202.209.82'),
(1330, '2026-04-28 13:29:27', '/billetterie/3', '83.202.209.82'),
(1331, '2026-04-28 13:29:43', '/billetterie/3/billetweb-preinscription', '83.202.209.82'),
(1332, '2026-04-28 13:29:43', '/billetterie/3', '83.202.209.82'),
(1333, '2026-04-28 13:41:08', '/', '220.130.196.40'),
(1334, '2026-04-28 13:41:10', '/billetterie/3', '83.202.209.82'),
(1335, '2026-04-28 13:41:32', '/', '78.192.9.6'),
(1336, '2026-04-28 13:42:06', '/invitation', '78.192.9.6'),
(1337, '2026-04-28 13:42:17', '/invitation', '78.192.9.6'),
(1338, '2026-04-28 13:42:17', '/register/e89b5ff0d49b3ae6b80f68e732d694d3', '78.192.9.6'),
(1339, '2026-04-28 13:47:26', '/register/e89b5ff0d49b3ae6b80f68e732d694d3', '78.192.9.6'),
(1340, '2026-04-28 13:47:53', '/register/e89b5ff0d49b3ae6b80f68e732d694d3', '78.192.9.6'),
(1341, '2026-04-28 13:48:29', '/', '140.248.41.30'),
(1342, '2026-04-28 13:48:33', '/register/e89b5ff0d49b3ae6b80f68e732d694d3', '78.192.9.6'),
(1343, '2026-04-28 13:48:55', '/login', '78.192.9.6'),
(1344, '2026-04-28 13:48:58', '/login', '78.192.9.6'),
(1345, '2026-04-28 13:49:06', '/invitation', '78.192.9.6'),
(1346, '2026-04-28 13:49:16', '/invitation', '78.192.9.6'),
(1347, '2026-04-28 13:49:17', '/register/e89b5ff0d49b3ae6b80f68e732d694d3', '78.192.9.6'),
(1348, '2026-04-28 13:50:24', '/register/e89b5ff0d49b3ae6b80f68e732d694d3', '78.192.9.6'),
(1349, '2026-04-28 13:51:35', '/register/e89b5ff0d49b3ae6b80f68e732d694d3', '78.192.9.6'),
(1350, '2026-04-28 13:53:02', '/register/e89b5ff0d49b3ae6b80f68e732d694d3', '78.192.9.6'),
(1351, '2026-04-28 13:53:36', '/register/e89b5ff0d49b3ae6b80f68e732d694d3', '78.192.9.6'),
(1352, '2026-04-28 14:02:27', '/register/e89b5ff0d49b3ae6b80f68e732d694d3', '78.192.9.6'),
(1353, '2026-04-28 14:02:28', '/', '78.192.9.6'),
(1354, '2026-04-28 14:03:00', '/billetterie/', '78.192.9.6'),
(1355, '2026-04-28 14:03:05', '/billetterie/3', '78.192.9.6'),
(1356, '2026-04-28 14:03:15', '/billetterie/3/billetweb-preinscription', '78.192.9.6'),
(1357, '2026-04-28 14:03:15', '/billetterie/3', '78.192.9.6'),
(1358, '2026-04-28 14:06:03', '/invitation', '17.241.227.128'),
(1359, '2026-04-28 14:15:21', '/login', '17.241.227.243'),
(1360, '2026-04-28 14:33:03', '/actualites/fc-fleury-91-le-club-de-l-essonne', '78.192.9.6'),
(1361, '2026-04-28 14:46:22', '/', '164.177.26.251'),
(1362, '2026-04-28 14:47:25', '/', '104.154.213.201'),
(1363, '2026-04-28 14:52:21', '/', '34.56.26.156'),
(1364, '2026-04-28 14:52:23', '/', '164.177.26.251'),
(1365, '2026-04-28 14:52:30', '/billetterie/3', '164.177.26.251'),
(1366, '2026-04-28 15:06:11', '/table-de-vente/', '17.246.23.197'),
(1367, '2026-04-28 15:06:11', '/chants', '17.246.19.240'),
(1368, '2026-04-28 15:51:14', '/', '34.44.14.79'),
(1369, '2026-04-28 15:51:15', '/', '34.44.14.79'),
(1370, '2026-04-28 15:51:16', '/', '34.44.14.79'),
(1371, '2026-04-28 15:52:00', '/', '136.107.191.84'),
(1372, '2026-04-28 15:52:00', '/', '136.107.191.84'),
(1373, '2026-04-28 15:52:01', '/', '136.107.191.84'),
(1374, '2026-04-28 15:55:36', '/', '106.219.165.123'),
(1375, '2026-04-28 15:55:36', '/', '106.219.165.123'),
(1376, '2026-04-28 15:55:37', '/billetterie/', '106.219.165.123'),
(1377, '2026-04-28 15:55:37', '/', '106.219.165.123'),
(1378, '2026-04-28 15:55:38', '/billetterie/3', '106.219.165.123'),
(1379, '2026-04-28 15:55:38', '/groupe/', '106.219.165.123'),
(1380, '2026-04-28 15:55:39', '/photos-de-match/1', '106.219.165.123'),
(1381, '2026-04-28 15:55:39', '/actualites/', '106.219.165.123'),
(1382, '2026-04-28 15:55:39', '/actualites/ultras-lions-fleury-2026-furies', '106.219.165.123'),
(1383, '2026-04-28 15:55:40', '/chants', '106.219.165.123'),
(1384, '2026-04-28 15:55:40', '/photos-de-match/3', '106.219.165.123'),
(1385, '2026-04-28 15:55:41', '/photos-de-match/', '106.219.165.123'),
(1386, '2026-04-28 15:55:41', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '106.219.165.123'),
(1387, '2026-04-28 15:55:42', '/medias/medias', '106.219.165.123'),
(1388, '2026-04-28 15:55:42', '/actualites/fc-fleury-91-le-club-de-l-essonne', '106.219.165.123'),
(1389, '2026-04-28 15:55:43', '/photos-de-match/', '106.219.165.123'),
(1390, '2026-04-28 15:55:43', '/photos-de-match/2', '106.219.165.123'),
(1391, '2026-04-28 15:55:44', '/table-de-vente/', '106.219.165.123'),
(1392, '2026-04-28 15:55:44', '/invitation', '106.219.165.123'),
(1393, '2026-04-28 15:55:45', '/login', '106.219.165.123'),
(1394, '2026-04-28 16:04:43', '/', '83.202.209.82'),
(1395, '2026-04-28 16:04:51', '/billetterie/', '83.202.209.82'),
(1396, '2026-04-28 16:04:53', '/billetterie/3', '83.202.209.82'),
(1397, '2026-04-28 16:05:20', '/photos-de-match/', '52.167.144.189'),
(1398, '2026-04-28 16:13:48', '/', '92.184.144.153'),
(1399, '2026-04-28 16:14:24', '/photos-de-match/2', '92.184.144.153'),
(1400, '2026-04-28 16:15:06', '/billetterie/', '92.184.144.153'),
(1401, '2026-04-28 16:15:11', '/billetterie/3', '92.184.144.153'),
(1402, '2026-04-28 16:15:37', '/billetterie/3/billetweb-preinscription', '92.184.144.153'),
(1403, '2026-04-28 16:15:37', '/billetterie/3', '92.184.144.153'),
(1404, '2026-04-28 16:45:30', '/', '106.219.165.123'),
(1405, '2026-04-28 16:45:31', '/', '106.219.165.123'),
(1406, '2026-04-28 16:45:31', '/billetterie/', '106.219.165.123'),
(1407, '2026-04-28 16:45:32', '/', '106.219.165.123'),
(1408, '2026-04-28 16:45:32', '/billetterie/3', '106.219.165.123'),
(1409, '2026-04-28 16:45:33', '/groupe/', '106.219.165.123'),
(1410, '2026-04-28 16:45:34', '/photos-de-match/1', '106.219.165.123'),
(1411, '2026-04-28 16:45:34', '/actualites/', '106.219.165.123'),
(1412, '2026-04-28 16:45:34', '/actualites/ultras-lions-fleury-2026-furies', '106.219.165.123'),
(1413, '2026-04-28 16:45:35', '/chants', '106.219.165.123'),
(1414, '2026-04-28 16:45:35', '/photos-de-match/3', '106.219.165.123'),
(1415, '2026-04-28 16:45:36', '/photos-de-match/', '106.219.165.123'),
(1416, '2026-04-28 16:45:36', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '106.219.165.123'),
(1417, '2026-04-28 16:45:37', '/medias/medias', '106.219.165.123'),
(1418, '2026-04-28 16:45:37', '/actualites/fc-fleury-91-le-club-de-l-essonne', '106.219.165.123'),
(1419, '2026-04-28 16:45:38', '/photos-de-match/', '106.219.165.123'),
(1420, '2026-04-28 16:45:38', '/photos-de-match/2', '106.219.165.123'),
(1421, '2026-04-28 16:45:38', '/table-de-vente/', '106.219.165.123'),
(1422, '2026-04-28 16:45:39', '/invitation', '106.219.165.123'),
(1423, '2026-04-28 16:45:39', '/login', '106.219.165.123'),
(1424, '2026-04-28 16:46:35', '/', '68.183.177.200'),
(1425, '2026-04-28 16:46:36', '/', '68.183.177.200'),
(1426, '2026-04-28 16:46:38', '/', '68.183.177.200'),
(1427, '2026-04-28 16:54:46', '/', '51.91.156.252'),
(1428, '2026-04-28 16:56:58', '/', '152.42.217.206'),
(1429, '2026-04-28 16:56:59', '/', '152.42.217.206'),
(1430, '2026-04-28 16:57:00', '/', '152.42.217.206'),
(1431, '2026-04-28 16:58:51', '/table-de-vente/', '207.46.13.168'),
(1432, '2026-04-28 16:59:30', '/', '164.177.26.251'),
(1433, '2026-04-28 16:59:37', '/login', '164.177.26.251'),
(1434, '2026-04-28 16:59:41', '/', '164.177.26.251'),
(1435, '2026-04-28 17:14:50', '/photos-de-match/', '17.241.227.10'),
(1436, '2026-04-28 17:16:08', '/', '3.144.140.221'),
(1437, '2026-04-28 17:54:56', '/', '155.2.194.27'),
(1438, '2026-04-28 17:58:59', '/', '100.31.86.28'),
(1439, '2026-04-28 17:59:50', '/', '100.31.86.28'),
(1440, '2026-04-28 18:04:54', '/', '74.7.242.57'),
(1441, '2026-04-28 18:04:57', '/billetterie/3', '74.7.242.57'),
(1442, '2026-04-28 18:43:54', '/table-de-vente/2', '92.184.146.10'),
(1443, '2026-04-28 18:43:54', '/table-de-vente/2', '92.184.146.10'),
(1444, '2026-04-28 18:56:17', '/photos-de-match/', '40.77.167.155'),
(1445, '2026-04-28 19:04:28', '/', '40.77.167.74'),
(1446, '2026-04-28 19:18:37', '/', '136.115.228.38'),
(1447, '2026-04-28 19:19:17', '/table-de-vente/2', '104.28.42.23'),
(1448, '2026-04-28 19:20:15', '/', '34.136.229.204'),
(1449, '2026-04-28 19:26:28', '/', '18.219.176.173'),
(1450, '2026-04-28 19:28:00', '/', '37.165.102.182'),
(1451, '2026-04-28 19:28:10', '/', '37.165.102.182'),
(1452, '2026-04-28 19:28:18', '/', '37.165.102.182'),
(1453, '2026-04-28 19:29:51', '/', '37.165.102.182'),
(1454, '2026-04-28 19:29:58', '/', '37.165.102.182'),
(1455, '2026-04-28 19:30:11', '/billetterie/', '37.165.102.182'),
(1456, '2026-04-28 19:30:31', '/billetterie/', '37.165.102.182'),
(1457, '2026-04-28 19:30:31', '/', '37.165.102.182'),
(1458, '2026-04-28 19:31:29', '/billetterie/', '37.165.102.182'),
(1459, '2026-04-28 19:31:52', '/actualites/', '37.165.102.182'),
(1460, '2026-04-28 19:32:00', '/login', '37.165.102.182'),
(1461, '2026-04-28 19:32:10', '/', '37.165.102.182'),
(1462, '2026-04-28 19:32:17', '/billetterie/', '37.165.102.182'),
(1463, '2026-04-28 19:33:12', '/', '37.165.102.182'),
(1464, '2026-04-28 19:38:21', '/', '93.158.90.72'),
(1465, '2026-04-28 20:05:24', '/', '83.202.95.42'),
(1466, '2026-04-28 20:05:31', '/login', '83.202.95.42'),
(1467, '2026-04-28 20:05:34', '/', '83.202.95.42'),
(1468, '2026-04-28 20:08:32', '/', '210.64.24.100'),
(1469, '2026-04-28 20:19:32', '/', '92.184.141.72'),
(1470, '2026-04-28 20:19:37', '/billetterie/', '92.184.141.72'),
(1471, '2026-04-28 20:19:39', '/billetterie/3', '92.184.141.72'),
(1472, '2026-04-28 20:19:46', '/billetterie/3', '92.184.141.72'),
(1473, '2026-04-28 20:20:12', '/table-de-vente/', '92.184.141.72'),
(1474, '2026-04-28 20:20:24', '/billetterie/3', '90.76.213.227'),
(1475, '2026-04-28 20:20:50', '/billetterie/', '92.184.141.72'),
(1476, '2026-04-28 20:20:51', '/billetterie/3', '92.184.141.72'),
(1477, '2026-04-28 20:40:15', '/photos-de-match/1', '17.241.227.150'),
(1478, '2026-04-28 21:05:59', '/', '142.111.108.54'),
(1479, '2026-04-28 21:06:19', '/', '142.111.108.171'),
(1480, '2026-04-28 21:08:03', '/', '136.0.223.61'),
(1481, '2026-04-28 21:08:15', '/', '142.147.202.62'),
(1482, '2026-04-28 21:23:55', '/', '84.37.205.219'),
(1483, '2026-04-28 21:24:03', '/', '199.84.222.130'),
(1484, '2026-04-28 21:31:15', '/billetterie/', '83.202.209.82'),
(1485, '2026-04-28 21:43:22', '/', '152.42.217.206'),
(1486, '2026-04-28 21:43:22', '/', '152.42.217.206'),
(1487, '2026-04-28 21:43:24', '/', '152.42.217.206'),
(1488, '2026-04-28 22:06:03', '/photos-de-match/2', '40.77.167.144'),
(1489, '2026-04-28 22:06:57', '/', '83.202.95.42'),
(1490, '2026-04-28 22:07:04', '/login', '83.202.95.42'),
(1491, '2026-04-28 22:07:08', '/', '83.202.95.42'),
(1492, '2026-04-28 22:13:56', '/billetterie/', '83.202.95.42'),
(1493, '2026-04-28 22:45:37', '/chants', '83.202.95.42'),
(1494, '2026-04-28 22:45:45', '/groupe/', '83.202.95.42'),
(1495, '2026-04-28 22:45:48', '/', '83.202.95.42'),
(1496, '2026-04-28 22:46:11', '/medias/medias', '83.202.95.42'),
(1497, '2026-04-28 22:50:07', '/medias/medias', '83.202.95.42'),
(1498, '2026-04-28 22:50:18', '/photos-de-match/2', '17.246.15.5'),
(1499, '2026-04-28 22:51:05', '/actualites/', '83.202.95.42'),
(1500, '2026-04-28 22:51:06', '/actualites/', '83.202.95.42'),
(1501, '2026-04-28 22:51:08', '/medias/medias', '83.202.95.42'),
(1502, '2026-04-28 22:53:47', '/login', '104.28.42.21'),
(1503, '2026-04-28 22:54:42', '/', '83.202.95.42'),
(1504, '2026-04-28 22:54:45', '/invitation', '83.202.95.42'),
(1505, '2026-04-28 22:54:46', '/invitation', '104.28.42.22'),
(1506, '2026-04-28 22:55:07', '/invitation', '83.202.95.42'),
(1507, '2026-04-28 22:55:12', '/login', '83.202.95.42'),
(1508, '2026-04-28 22:55:15', '/billetterie/', '83.202.209.82'),
(1509, '2026-04-28 22:55:23', '/invitation', '104.28.42.23'),
(1510, '2026-04-28 22:55:24', '/register/44ecd53c8a79a2762ae80a3682bc70d8', '104.28.42.23'),
(1511, '2026-04-28 22:55:25', '/billetterie/3', '83.202.209.82'),
(1512, '2026-04-28 22:55:40', '/billetterie/3/billetweb-preinscription', '83.202.209.82'),
(1513, '2026-04-28 22:55:40', '/billetterie/3', '83.202.209.82'),
(1514, '2026-04-28 22:58:43', '/', '104.252.191.108'),
(1515, '2026-04-28 22:58:44', '/', '107.172.195.89'),
(1516, '2026-04-28 22:58:50', '/', '104.252.191.108'),
(1517, '2026-04-28 23:01:09', '/', '107.172.195.40'),
(1518, '2026-04-28 23:01:10', '/', '104.164.173.132'),
(1519, '2026-04-28 23:01:11', '/', '107.172.195.40'),
(1520, '2026-04-28 23:01:12', '/actualites/', '107.172.195.40'),
(1521, '2026-04-28 23:01:12', '/photos-de-match/%3Fseason%3D2025-2026', '107.172.195.40'),
(1522, '2026-04-28 23:01:12', '/invitation', '107.172.195.40'),
(1523, '2026-04-28 23:01:12', '/groupe/', '107.172.195.40'),
(1524, '2026-04-28 23:01:12', '/photos-de-match/2', '107.172.195.40'),
(1525, '2026-04-28 23:01:12', '/table-de-vente/', '107.172.195.40'),
(1526, '2026-04-28 23:01:12', '/photos-de-match/3', '107.172.195.40'),
(1527, '2026-04-28 23:01:12', '/medias/medias', '107.172.195.40'),
(1528, '2026-04-28 23:01:12', '/actualites/ultras-lions-fleury-2026-furies', '107.172.195.40'),
(1529, '2026-04-28 23:01:12', '/billetterie/3', '107.172.195.40'),
(1530, '2026-04-28 23:01:12', '/chants', '107.172.195.40'),
(1531, '2026-04-28 23:01:13', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '107.172.195.40'),
(1532, '2026-04-28 23:01:13', '/billetterie/', '107.172.195.40'),
(1533, '2026-04-28 23:01:13', '/photos-de-match/1', '107.172.195.40'),
(1534, '2026-04-28 23:01:13', '/', '107.172.195.40'),
(1535, '2026-04-28 23:01:13', '/login', '107.172.195.40'),
(1536, '2026-04-28 23:01:13', '/actualites/fc-fleury-91-le-club-de-l-essonne', '107.172.195.40'),
(1537, '2026-04-28 23:01:13', '/photos-de-match/', '107.172.195.40'),
(1538, '2026-04-28 23:01:13', '/actualites/%23', '107.172.195.40'),
(1539, '2026-04-28 23:03:45', '/invitation', '83.202.95.42'),
(1540, '2026-04-28 23:03:48', '/', '83.202.95.42'),
(1541, '2026-04-28 23:03:58', '/', '83.202.95.42'),
(1542, '2026-04-28 23:04:16', '/', '92.184.141.100'),
(1543, '2026-04-28 23:07:15', '/', '92.184.141.100'),
(1544, '2026-04-28 23:08:00', '/', '172.111.15.144'),
(1545, '2026-04-28 23:08:00', '/', '172.111.15.112'),
(1546, '2026-04-28 23:09:55', '/', '147.93.155.10'),
(1547, '2026-04-28 23:11:09', '/', '40.77.167.51'),
(1548, '2026-04-28 23:16:24', '/', '45.185.226.168'),
(1549, '2026-04-28 23:31:04', '/invitation', '83.202.95.42'),
(1550, '2026-04-28 23:31:06', '/', '83.202.95.42'),
(1551, '2026-04-28 23:31:14', '/', '83.202.95.42'),
(1552, '2026-04-28 23:31:17', '/login', '83.202.95.42'),
(1553, '2026-04-28 23:31:23', '/billetterie/3', '77.133.249.209'),
(1554, '2026-04-28 23:31:33', '/billetterie/3/billetweb-preinscription', '77.133.249.209'),
(1555, '2026-04-28 23:31:34', '/billetterie/3', '77.133.249.209'),
(1556, '2026-04-28 23:31:36', '/billetterie/3', '66.249.93.14'),
(1557, '2026-04-28 23:31:38', '/billetterie/3', '74.125.210.70'),
(1558, '2026-04-28 23:31:38', '/billetterie/3', '74.125.210.70'),
(1559, '2026-04-28 23:32:48', '/', '91.231.89.20'),
(1560, '2026-04-28 23:33:02', '/', '91.231.89.23'),
(1561, '2026-04-28 23:33:05', '/', '91.231.89.21'),
(1562, '2026-04-28 23:33:54', '/', '91.231.89.16'),
(1563, '2026-04-28 23:34:21', '/medias/medias', '83.202.95.42'),
(1564, '2026-04-28 23:34:26', '/photos-de-match/', '83.202.95.42'),
(1565, '2026-04-28 23:34:28', '/actualites/', '83.202.95.42'),
(1566, '2026-04-28 23:34:30', '/actualites/ultras-lions-fleury-2026-furies', '83.202.95.42'),
(1567, '2026-04-28 23:35:10', '/', '74.7.227.188'),
(1568, '2026-04-28 23:35:39', '/medias/medias', '83.202.95.42'),
(1569, '2026-04-28 23:36:16', '/billetterie/3', '74.7.227.188'),
(1570, '2026-04-28 23:38:53', '/', '83.202.95.42'),
(1571, '2026-04-28 23:39:14', '/groupe/', '83.202.95.42'),
(1572, '2026-04-28 23:53:13', '/', '34.13.17.199'),
(1573, '2026-04-28 23:53:16', '/', '34.13.17.199'),
(1574, '2026-04-28 23:53:16', '/', '34.13.17.199'),
(1575, '2026-04-28 23:53:17', '/login', '34.13.17.199'),
(1576, '2026-04-29 00:13:24', '/', '198.13.158.153'),
(1577, '2026-04-29 00:14:00', '/', '172.236.122.62'),
(1578, '2026-04-29 00:16:16', '/', '172.236.122.62'),
(1579, '2026-04-29 00:16:17', '/', '172.236.122.62'),
(1580, '2026-04-29 00:29:15', '/billetterie/2', '40.77.167.6'),
(1581, '2026-04-29 00:37:15', '/actualites/ultras-lions-fleury-2026-furies', '17.241.219.250'),
(1582, '2026-04-29 00:37:30', '/', '45.185.226.168'),
(1583, '2026-04-29 01:39:02', '/', '81.53.36.105'),
(1584, '2026-04-29 01:39:37', '/actualites/', '157.55.39.62'),
(1585, '2026-04-29 01:53:02', '/', '185.220.101.16'),
(1586, '2026-04-29 02:09:32', '/', '34.217.90.194'),
(1587, '2026-04-29 02:21:13', '/', '124.198.131.57'),
(1588, '2026-04-29 02:28:49', '/', '61.48.133.81'),
(1589, '2026-04-29 02:33:30', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '17.246.23.56'),
(1590, '2026-04-29 02:40:44', '/photos-de-match/1', '207.46.13.102'),
(1591, '2026-04-29 02:42:24', '/', '34.72.176.129'),
(1592, '2026-04-29 02:42:41', '/', '205.169.39.26'),
(1593, '2026-04-29 02:43:20', '/', '89.248.168.222'),
(1594, '2026-04-29 03:16:52', '/', '2.58.56.93'),
(1595, '2026-04-29 03:29:03', '/photos-de-match/', '40.77.167.74'),
(1596, '2026-04-29 04:25:30', '/photos-de-match/3', '17.241.75.30'),
(1597, '2026-04-29 04:30:15', '/', '84.32.41.136'),
(1598, '2026-04-29 04:30:15', '/', '84.32.41.136'),
(1599, '2026-04-29 04:30:15', '/billetterie/', '84.32.41.136'),
(1600, '2026-04-29 04:30:15', '/', '84.32.41.136'),
(1601, '2026-04-29 04:30:16', '/billetterie/3', '84.32.41.136'),
(1602, '2026-04-29 04:30:16', '/groupe/', '84.32.41.136'),
(1603, '2026-04-29 04:30:16', '/photos-de-match/1', '84.32.41.136'),
(1604, '2026-04-29 04:30:16', '/actualites/', '84.32.41.136'),
(1605, '2026-04-29 04:30:16', '/actualites/ultras-lions-fleury-2026-furies', '84.32.41.136'),
(1606, '2026-04-29 04:30:16', '/chants', '84.32.41.136'),
(1607, '2026-04-29 04:30:17', '/photos-de-match/3', '84.32.41.136'),
(1608, '2026-04-29 04:30:17', '/photos-de-match/', '84.32.41.136'),
(1609, '2026-04-29 04:30:17', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '84.32.41.136'),
(1610, '2026-04-29 04:30:17', '/medias/medias', '84.32.41.136'),
(1611, '2026-04-29 04:30:17', '/actualites/fc-fleury-91-le-club-de-l-essonne', '84.32.41.136'),
(1612, '2026-04-29 04:30:17', '/photos-de-match/', '84.32.41.136'),
(1613, '2026-04-29 04:30:18', '/photos-de-match/2', '84.32.41.136'),
(1614, '2026-04-29 04:30:18', '/table-de-vente/', '84.32.41.136'),
(1615, '2026-04-29 04:30:18', '/invitation', '84.32.41.136'),
(1616, '2026-04-29 04:30:18', '/login', '84.32.41.136'),
(1617, '2026-04-29 04:41:37', '/photos-de-match/2', '40.77.167.4'),
(1618, '2026-04-29 05:48:12', '/', '213.139.9.146'),
(1619, '2026-04-29 05:53:43', '/chants', '207.46.13.168'),
(1620, '2026-04-29 06:07:09', '/billetterie/3', '52.167.144.195'),
(1621, '2026-04-29 06:21:02', '/actualites/fc-fleury-91-le-club-de-l-essonne', '17.241.227.106'),
(1622, '2026-04-29 07:14:48', '/billetterie/3', '88.184.40.219'),
(1623, '2026-04-29 07:22:29', '/', '137.74.246.152'),
(1624, '2026-04-29 07:53:13', '/', '192.71.126.27'),
(1625, '2026-04-29 07:55:38', '/', '141.8.198.176'),
(1626, '2026-04-29 07:59:00', '/billetterie/3', '88.184.40.219'),
(1627, '2026-04-29 08:19:52', '/', '62.238.17.239'),
(1628, '2026-04-29 08:22:37', '/actualites/fc-fleury-91-le-club-de-l-essonne', '17.241.219.200'),
(1629, '2026-04-29 08:30:07', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '17.246.23.228'),
(1630, '2026-04-29 08:46:44', '/billetterie/3', '88.184.40.219'),
(1631, '2026-04-29 08:57:37', '/billetterie/3', '86.104.249.40'),
(1632, '2026-04-29 08:58:01', '/billetterie/3/billetweb-preinscription', '86.104.249.40'),
(1633, '2026-04-29 08:58:02', '/billetterie/3', '86.104.249.40'),
(1634, '2026-04-29 08:59:28', '/', '176.191.82.6'),
(1635, '2026-04-29 08:59:31', '/chants', '176.191.82.6'),
(1636, '2026-04-29 09:34:45', '/', '195.211.77.141'),
(1637, '2026-04-29 09:35:06', '/', '195.211.77.141'),
(1638, '2026-04-29 09:48:09', '/billetterie/', '83.202.209.82'),
(1639, '2026-04-29 09:49:59', '/billetterie/', '83.202.209.82'),
(1640, '2026-04-29 09:50:10', '/', '83.202.209.82'),
(1641, '2026-04-29 09:54:29', '/', '45.157.112.219'),
(1642, '2026-04-29 10:26:47', '/', '62.238.17.239'),
(1643, '2026-04-29 10:26:49', '/', '62.238.17.239'),
(1644, '2026-04-29 10:59:52', '/actualites/ultras-lions-fleury-2026-furies', '17.241.227.169'),
(1645, '2026-04-29 11:39:08', '/groupe/', '40.77.167.79'),
(1646, '2026-04-29 11:40:24', '/', '122.161.68.154'),
(1647, '2026-04-29 11:40:25', '/', '122.161.68.154'),
(1648, '2026-04-29 11:40:25', '/billetterie/', '122.161.68.154'),
(1649, '2026-04-29 11:40:26', '/', '122.161.68.154'),
(1650, '2026-04-29 11:40:26', '/billetterie/3', '122.161.68.154'),
(1651, '2026-04-29 11:40:27', '/groupe/', '122.161.68.154'),
(1652, '2026-04-29 11:40:27', '/photos-de-match/1', '122.161.68.154'),
(1653, '2026-04-29 11:40:28', '/actualites/', '122.161.68.154'),
(1654, '2026-04-29 11:40:28', '/actualites/ultras-lions-fleury-2026-furies', '122.161.68.154'),
(1655, '2026-04-29 11:40:29', '/chants', '122.161.68.154'),
(1656, '2026-04-29 11:40:29', '/photos-de-match/3', '122.161.68.154'),
(1657, '2026-04-29 11:40:29', '/photos-de-match/', '122.161.68.154'),
(1658, '2026-04-29 11:40:30', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '122.161.68.154'),
(1659, '2026-04-29 11:40:30', '/medias/medias', '122.161.68.154');
INSERT INTO `visit` (`id`, `visited_at`, `page`, `ip`) VALUES
(1660, '2026-04-29 11:40:31', '/actualites/fc-fleury-91-le-club-de-l-essonne', '122.161.68.154'),
(1661, '2026-04-29 11:40:31', '/photos-de-match/', '122.161.68.154'),
(1662, '2026-04-29 11:40:32', '/photos-de-match/2', '122.161.68.154'),
(1663, '2026-04-29 11:40:32', '/table-de-vente/', '122.161.68.154'),
(1664, '2026-04-29 11:40:33', '/invitation', '122.161.68.154'),
(1665, '2026-04-29 11:40:33', '/login', '122.161.68.154'),
(1666, '2026-04-29 12:16:42', '/', '89.248.168.222'),
(1667, '2026-04-29 12:39:29', '/', '52.91.217.192'),
(1668, '2026-04-29 12:39:29', '/', '52.91.217.192'),
(1669, '2026-04-29 12:39:30', '/', '52.91.217.192'),
(1670, '2026-04-29 12:42:59', '/', '223.233.78.52'),
(1671, '2026-04-29 12:43:00', '/', '223.233.78.52'),
(1672, '2026-04-29 12:43:00', '/billetterie/', '223.233.78.52'),
(1673, '2026-04-29 12:43:01', '/', '223.233.78.52'),
(1674, '2026-04-29 12:43:01', '/billetterie/3', '223.233.78.52'),
(1675, '2026-04-29 12:43:02', '/groupe/', '223.233.78.52'),
(1676, '2026-04-29 12:43:02', '/photos-de-match/1', '223.233.78.52'),
(1677, '2026-04-29 12:43:02', '/actualites/', '223.233.78.52'),
(1678, '2026-04-29 12:43:03', '/actualites/ultras-lions-fleury-2026-furies', '223.233.78.52'),
(1679, '2026-04-29 12:43:03', '/chants', '223.233.78.52'),
(1680, '2026-04-29 12:43:04', '/photos-de-match/3', '223.233.78.52'),
(1681, '2026-04-29 12:43:04', '/photos-de-match/', '223.233.78.52'),
(1682, '2026-04-29 12:43:05', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '223.233.78.52'),
(1683, '2026-04-29 12:43:05', '/medias/medias', '223.233.78.52'),
(1684, '2026-04-29 12:43:06', '/actualites/fc-fleury-91-le-club-de-l-essonne', '223.233.78.52'),
(1685, '2026-04-29 12:43:06', '/photos-de-match/', '223.233.78.52'),
(1686, '2026-04-29 12:43:06', '/photos-de-match/2', '223.233.78.52'),
(1687, '2026-04-29 12:43:07', '/table-de-vente/', '223.233.78.52'),
(1688, '2026-04-29 12:43:07', '/invitation', '223.233.78.52'),
(1689, '2026-04-29 12:43:08', '/login', '223.233.78.52'),
(1690, '2026-04-29 12:44:19', '/billetterie/', '83.202.209.82'),
(1691, '2026-04-29 12:44:37', '/login', '83.202.209.82'),
(1692, '2026-04-29 12:44:41', '/', '83.202.209.82'),
(1693, '2026-04-29 12:46:17', '/invitation', '52.167.144.218'),
(1694, '2026-04-29 13:17:19', '/', '106.161.84.78'),
(1695, '2026-04-29 13:30:22', '/login', '40.77.167.43'),
(1696, '2026-04-29 13:39:14', '/billetterie/3', '46.18.228.222'),
(1697, '2026-04-29 13:39:34', '/billetterie/3/billetweb-preinscription', '46.18.228.222'),
(1698, '2026-04-29 13:39:34', '/billetterie/3', '46.18.228.222'),
(1699, '2026-04-29 13:42:47', '/billetterie/3', '31.13.103.9'),
(1700, '2026-04-29 14:05:25', '/billetterie/3', '83.202.95.42'),
(1701, '2026-04-29 14:05:44', '/login', '83.202.95.42'),
(1702, '2026-04-29 14:05:45', '/login', '83.202.95.42'),
(1703, '2026-04-29 14:06:11', '/', '83.202.95.42'),
(1704, '2026-04-29 15:30:49', '/billetterie/3', '92.184.107.123'),
(1705, '2026-04-29 15:48:06', '/', '168.144.133.214'),
(1706, '2026-04-29 15:48:07', '/', '168.144.133.214'),
(1707, '2026-04-29 15:48:09', '/', '168.144.133.214'),
(1708, '2026-04-29 15:49:43', '/', '152.42.217.206'),
(1709, '2026-04-29 15:49:44', '/', '152.42.217.206'),
(1710, '2026-04-29 15:49:45', '/', '152.42.217.206'),
(1711, '2026-04-29 16:07:41', '/billetterie/', '83.202.95.42'),
(1712, '2026-04-29 16:07:45', '/billetterie/3', '83.202.95.42'),
(1713, '2026-04-29 16:27:27', '/', '62.210.198.165'),
(1714, '2026-04-29 16:27:28', '/', '62.210.198.165'),
(1715, '2026-04-29 16:27:29', '/', '62.210.198.165'),
(1716, '2026-04-29 16:27:34', '/', '62.210.198.165'),
(1717, '2026-04-29 16:28:07', '/', '40.77.167.36'),
(1718, '2026-04-29 16:34:45', '/', '143.110.252.98'),
(1719, '2026-04-29 16:34:46', '/', '143.110.252.98'),
(1720, '2026-04-29 16:52:27', '/billetterie/3', '77.133.249.198'),
(1721, '2026-04-29 17:02:55', '/billetterie/', '37.168.232.8'),
(1722, '2026-04-29 17:03:07', '/login', '37.168.232.8'),
(1723, '2026-04-29 17:03:11', '/', '37.168.232.8'),
(1724, '2026-04-29 17:03:16', '/billetterie/', '37.168.232.8'),
(1725, '2026-04-29 17:40:35', '/', '35.197.198.194'),
(1726, '2026-04-29 17:42:31', '/', '35.222.136.150'),
(1727, '2026-04-29 17:44:32', '/actualites/', '40.77.167.74'),
(1728, '2026-04-29 17:46:52', '/actualites/', '40.77.167.74'),
(1729, '2026-04-29 17:53:59', '/', '34.135.21.114'),
(1730, '2026-04-29 17:58:32', '/billetterie/3', '77.133.249.198'),
(1731, '2026-04-29 17:58:43', '/', '40.77.167.50'),
(1732, '2026-04-29 17:58:51', '/billetterie/3/billetweb-preinscription', '77.133.249.198'),
(1733, '2026-04-29 17:58:51', '/billetterie/3', '77.133.249.198'),
(1734, '2026-04-29 17:58:58', '/', '15.236.144.165'),
(1735, '2026-04-29 18:44:43', '/billetterie/3', '78.243.118.7'),
(1736, '2026-04-29 18:49:14', '/billetterie/', '83.202.209.82'),
(1737, '2026-04-29 18:49:49', '/billetterie/3', '83.202.209.82'),
(1738, '2026-04-29 18:49:54', '/billetterie/3/billetweb-preinscription', '83.202.209.82'),
(1739, '2026-04-29 18:49:54', '/billetterie/3', '83.202.209.82'),
(1740, '2026-04-29 19:20:15', '/billetterie/3', '83.202.174.111'),
(1741, '2026-04-29 19:20:40', '/billetterie/3/billetweb-preinscription', '83.202.174.111'),
(1742, '2026-04-29 19:20:40', '/billetterie/3', '83.202.174.111'),
(1743, '2026-04-29 19:30:33', '/billetterie/3', '46.18.228.222'),
(1744, '2026-04-29 19:30:44', '/billetterie/3', '46.18.228.222'),
(1745, '2026-04-29 19:31:03', '/billetterie/3/billetweb-preinscription', '46.18.228.222'),
(1746, '2026-04-29 19:31:04', '/billetterie/3', '46.18.228.222'),
(1747, '2026-04-29 19:49:09', '/billetterie/3', '83.202.174.111'),
(1748, '2026-04-29 20:07:35', '/photos-de-match/2', '17.22.237.46'),
(1749, '2026-04-29 20:46:13', '/', '81.65.94.87'),
(1750, '2026-04-29 20:46:37', '/', '81.65.94.87'),
(1751, '2026-04-29 20:55:22', '/forgot-password', '17.22.237.244'),
(1752, '2026-04-29 20:56:42', '/', '168.144.133.214'),
(1753, '2026-04-29 20:56:43', '/', '168.144.133.214'),
(1754, '2026-04-29 20:56:45', '/', '168.144.133.214'),
(1755, '2026-04-29 21:02:58', '/', '152.42.217.206'),
(1756, '2026-04-29 21:02:59', '/', '152.42.217.206'),
(1757, '2026-04-29 21:03:01', '/', '152.42.217.206'),
(1758, '2026-04-29 21:30:32', '/billetterie/2', '17.246.23.5'),
(1759, '2026-04-29 21:44:06', '/photos-de-match/', '17.246.15.70'),
(1760, '2026-04-29 21:48:14', '/billetterie/3', '176.132.201.232'),
(1761, '2026-04-29 21:48:53', '/chants', '176.132.201.232'),
(1762, '2026-04-29 21:49:29', '/chants', '176.132.201.232'),
(1763, '2026-04-29 21:53:51', '/billetterie/', '83.202.209.82'),
(1764, '2026-04-29 21:53:53', '/billetterie/3', '83.202.209.82'),
(1765, '2026-04-29 21:54:13', '/', '83.202.209.82'),
(1766, '2026-04-29 21:54:21', '/table-de-vente/', '83.202.209.82'),
(1767, '2026-04-29 21:54:24', '/', '83.202.209.82'),
(1768, '2026-04-29 21:55:05', '/billetterie/3', '83.202.209.82'),
(1769, '2026-04-29 21:55:20', '/billetterie/3', '83.202.209.82'),
(1770, '2026-04-29 21:56:37', '/', '83.202.209.82'),
(1771, '2026-04-29 22:11:41', '/', '45.55.40.211'),
(1772, '2026-04-29 22:11:41', '/', '45.55.40.211'),
(1773, '2026-04-29 22:11:42', '/', '45.55.40.211'),
(1774, '2026-04-29 22:14:57', '/', '187.106.44.244'),
(1775, '2026-04-29 22:43:23', '/', '176.151.39.183'),
(1776, '2026-04-29 22:43:27', '/billetterie/', '176.151.39.183'),
(1777, '2026-04-29 22:43:30', '/billetterie/3', '176.151.39.183'),
(1778, '2026-04-29 22:44:31', '/table-de-vente/', '176.151.39.183'),
(1779, '2026-04-29 22:44:35', '/table-de-vente/2', '176.151.39.183'),
(1780, '2026-04-29 22:44:39', '/table-de-vente/panier/ajouter/2', '176.151.39.183'),
(1781, '2026-04-29 22:44:39', '/panier', '176.151.39.183'),
(1782, '2026-04-29 22:44:52', '/panier/supprimer/merch/2|TU', '176.151.39.183'),
(1783, '2026-04-29 22:44:52', '/panier', '176.151.39.183'),
(1784, '2026-04-29 22:44:59', '/billetterie/', '176.151.39.183'),
(1785, '2026-04-29 22:57:42', '/billetterie/3', '69.63.189.116'),
(1786, '2026-04-29 22:57:43', '/billetterie/3', '90.60.10.226'),
(1787, '2026-04-29 22:57:54', '/table-de-vente/', '90.60.10.226'),
(1788, '2026-04-29 22:57:57', '/table-de-vente/2', '90.60.10.226'),
(1789, '2026-04-29 22:58:00', '/table-de-vente/panier/ajouter/2', '90.60.10.226'),
(1790, '2026-04-29 22:58:00', '/panier', '90.60.10.226'),
(1791, '2026-04-29 22:58:21', '/billetterie/3', '88.190.4.153'),
(1792, '2026-04-29 22:58:52', '/billetterie/3/billetweb-preinscription', '88.190.4.153'),
(1793, '2026-04-29 22:58:52', '/billetterie/3', '88.190.4.153'),
(1794, '2026-04-29 22:59:01', '/medias/medias', '207.46.13.36'),
(1795, '2026-04-29 23:22:19', '/', '51.195.183.43'),
(1796, '2026-04-29 23:23:44', '/billetterie/3', '176.132.201.232'),
(1797, '2026-04-29 23:23:52', '/', '92.184.141.80'),
(1798, '2026-04-29 23:24:04', '/login', '92.184.141.80'),
(1799, '2026-04-29 23:24:08', '/', '92.184.141.80'),
(1800, '2026-04-29 23:24:10', '/', '92.184.141.80'),
(1801, '2026-04-29 23:25:08', '/chants', '176.151.39.183'),
(1802, '2026-04-29 23:25:19', '/', '176.151.39.183'),
(1803, '2026-04-29 23:26:42', '/chants', '176.151.39.183'),
(1804, '2026-04-29 23:31:46', '/billetterie/', '52.167.144.16'),
(1805, '2026-04-29 23:44:22', '/', '83.202.209.82'),
(1806, '2026-04-29 23:59:10', '/photos-de-match/', '17.22.245.209'),
(1807, '2026-04-30 00:22:44', '/', '88.160.142.5'),
(1808, '2026-04-30 00:22:52', '/', '88.160.142.5'),
(1809, '2026-04-30 00:22:58', '/login', '88.160.142.5'),
(1810, '2026-04-30 00:23:05', '/', '88.160.142.5'),
(1811, '2026-04-30 00:23:13', '/actualites/', '88.160.142.5'),
(1812, '2026-04-30 00:23:18', '/', '88.160.142.5'),
(1813, '2026-04-30 00:23:23', '/billetterie/', '88.160.142.5'),
(1814, '2026-04-30 00:23:29', '/', '88.160.142.5'),
(1815, '2026-04-30 00:54:22', '/table-de-vente/2', '17.241.75.183'),
(1816, '2026-04-30 01:06:21', '/photos-de-match/1', '17.22.253.158'),
(1817, '2026-04-30 01:18:56', '/table-de-vente/', '52.167.144.147'),
(1818, '2026-04-30 01:29:52', '/', '81.65.94.87'),
(1819, '2026-04-30 01:29:56', '/login', '81.65.94.87'),
(1820, '2026-04-30 01:30:00', '/', '81.65.94.87'),
(1821, '2026-04-30 01:33:29', '/', '185.146.222.238'),
(1822, '2026-04-30 01:59:08', '/', '3.25.94.114'),
(1823, '2026-04-30 01:59:10', '/', '3.25.94.114'),
(1824, '2026-04-30 02:02:00', '/photos-de-match/3', '52.167.144.171'),
(1825, '2026-04-30 02:08:44', '/billetterie/3', '176.132.201.232'),
(1826, '2026-04-30 02:08:50', '/table-de-vente/', '176.132.201.232'),
(1827, '2026-04-30 02:09:06', '/photos-de-match/', '176.132.201.232'),
(1828, '2026-04-30 02:09:12', '/photos-de-match/2', '176.132.201.232'),
(1829, '2026-04-30 02:09:19', '/actualites/', '176.132.201.232'),
(1830, '2026-04-30 02:09:30', '/groupe/', '176.132.201.232'),
(1831, '2026-04-30 02:12:36', '/billetterie/', '176.132.201.232'),
(1832, '2026-04-30 02:12:41', '/billetterie/3', '176.132.201.232'),
(1833, '2026-04-30 02:16:25', '/billetterie/3/billetweb-preinscription', '176.132.201.232'),
(1834, '2026-04-30 02:16:26', '/billetterie/3', '176.132.201.232'),
(1835, '2026-04-30 02:20:19', '/invitation', '176.132.201.232'),
(1836, '2026-04-30 02:50:18', '/table-de-vente/2', '17.22.253.161'),
(1837, '2026-04-30 03:54:32', '/', '13.236.117.178'),
(1838, '2026-04-30 03:54:34', '/', '13.236.117.178'),
(1839, '2026-04-30 04:27:56', '/photos-de-match/3', '17.22.253.58'),
(1840, '2026-04-30 05:30:00', '/', '62.238.17.239'),
(1841, '2026-04-30 06:02:30', '/forgot-password', '17.22.237.206'),
(1842, '2026-04-30 06:49:47', '/photos-de-match/', '17.241.219.37'),
(1843, '2026-04-30 07:09:58', '/', '13.37.238.10'),
(1844, '2026-04-30 07:09:59', '/', '13.37.238.10'),
(1845, '2026-04-30 07:19:34', '/', '54.71.105.119'),
(1846, '2026-04-30 07:19:36', '/', '54.71.105.119'),
(1847, '2026-04-30 08:11:58', '/login', '81.65.94.87'),
(1848, '2026-04-30 08:47:27', '/table-de-vente/2', '40.77.167.3'),
(1849, '2026-04-30 09:01:18', '/billetterie/', '155.2.128.134'),
(1850, '2026-04-30 09:02:14', '/billetterie/', '155.2.128.134'),
(1851, '2026-04-30 09:02:16', '/billetterie/3', '155.2.128.134'),
(1852, '2026-04-30 09:02:37', '/billetterie/3/billetweb-preinscription', '155.2.128.134'),
(1853, '2026-04-30 09:02:38', '/billetterie/3', '155.2.128.134'),
(1854, '2026-04-30 09:10:48', '/table-de-vente/2', '172.225.116.187'),
(1855, '2026-04-30 10:22:31', '/', '69.10.56.122'),
(1856, '2026-04-30 10:22:32', '/', '69.10.56.122'),
(1857, '2026-04-30 10:51:34', '/table-de-vente/2', '104.28.42.14'),
(1858, '2026-04-30 10:51:45', '/login', '104.28.42.14'),
(1859, '2026-04-30 10:51:53', '/', '104.28.42.14'),
(1860, '2026-04-30 10:58:55', '/table-de-vente/2', '172.225.116.186'),
(1861, '2026-04-30 11:04:31', '/', '54.226.252.91'),
(1862, '2026-04-30 11:04:31', '/', '54.226.252.91'),
(1863, '2026-04-30 11:04:31', '/', '54.226.252.91'),
(1864, '2026-04-30 11:10:57', '/', '104.28.42.20'),
(1865, '2026-04-30 11:11:02', '/', '104.28.42.20'),
(1866, '2026-04-30 11:11:10', '/login', '104.28.42.20'),
(1867, '2026-04-30 11:11:28', '/invitation', '104.28.42.20'),
(1868, '2026-04-30 11:18:06', '/', '141.95.193.191'),
(1869, '2026-04-30 11:18:06', '/', '141.95.193.191'),
(1870, '2026-04-30 11:24:11', '/', '164.177.26.251'),
(1871, '2026-04-30 11:26:59', '/billetterie/2', '17.241.227.106'),
(1872, '2026-04-30 11:28:07', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '164.177.26.251'),
(1873, '2026-04-30 11:28:07', '/cartage', '164.177.26.251'),
(1874, '2026-04-30 11:28:26', '/cartage', '164.177.26.251'),
(1875, '2026-04-30 11:29:34', '/', '92.184.144.54'),
(1876, '2026-04-30 11:29:42', '/', '92.184.144.54'),
(1877, '2026-04-30 11:29:44', '/cartage', '92.184.144.54'),
(1878, '2026-04-30 11:32:01', '/cartage', '104.28.42.18'),
(1879, '2026-04-30 11:32:02', '/cartage', '92.184.144.54'),
(1880, '2026-04-30 11:38:15', '/', '164.177.26.251'),
(1881, '2026-04-30 11:38:16', '/', '164.177.26.251'),
(1882, '2026-04-30 11:38:18', '/', '164.177.26.251'),
(1883, '2026-04-30 11:38:29', '/cartage', '164.177.26.251'),
(1884, '2026-04-30 11:38:31', '/cartage', '164.177.26.251'),
(1885, '2026-04-30 11:42:16', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '164.177.26.251'),
(1886, '2026-04-30 11:42:16', '/cartage', '164.177.26.251'),
(1887, '2026-04-30 11:43:51', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '92.184.144.54'),
(1888, '2026-04-30 11:43:51', '/cartage', '92.184.144.54'),
(1889, '2026-04-30 11:47:35', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '104.28.42.18'),
(1890, '2026-04-30 11:47:36', '/cartage', '104.28.42.18'),
(1891, '2026-04-30 11:52:01', '/cartage', '92.184.144.48'),
(1892, '2026-04-30 11:53:02', '/cartage', '104.28.42.23'),
(1893, '2026-04-30 11:54:42', '/cartage', '104.28.42.20'),
(1894, '2026-04-30 11:54:52', '/sumup/webhook', '52.48.233.7'),
(1895, '2026-04-30 11:54:53', '/sumup/webhook', '79.125.59.241'),
(1896, '2026-04-30 11:55:00', '/sumup/webhook', '79.125.59.241'),
(1897, '2026-04-30 11:55:00', '/sumup/webhook', '52.48.233.7'),
(1898, '2026-04-30 11:56:54', '/cartage', '104.28.42.16'),
(1899, '2026-04-30 11:57:11', '/cartage', '104.28.42.16'),
(1900, '2026-04-30 11:57:18', '/login', '104.28.42.16'),
(1901, '2026-04-30 11:57:21', '/', '104.28.42.16'),
(1902, '2026-04-30 11:57:31', '/cartage', '104.28.42.16'),
(1903, '2026-04-30 11:57:49', '/cartage', '104.28.42.18'),
(1904, '2026-04-30 11:58:05', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '104.28.42.18'),
(1905, '2026-04-30 11:58:05', '/cartage', '104.28.42.18'),
(1906, '2026-04-30 11:58:14', '/', '104.28.42.27'),
(1907, '2026-04-30 11:58:17', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '104.28.42.18'),
(1908, '2026-04-30 11:58:17', '/cartage', '104.28.42.18'),
(1909, '2026-04-30 11:58:38', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '104.28.42.18'),
(1910, '2026-04-30 11:58:38', '/cartage', '104.28.42.18'),
(1911, '2026-04-30 12:01:02', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '104.28.42.23'),
(1912, '2026-04-30 12:01:02', '/cartage', '104.28.42.23'),
(1913, '2026-04-30 12:03:36', '/cartage', '104.28.42.23'),
(1914, '2026-04-30 12:07:45', '/cartage', '92.184.144.48'),
(1915, '2026-04-30 12:08:03', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '104.28.42.16'),
(1916, '2026-04-30 12:08:03', '/cartage', '104.28.42.16'),
(1917, '2026-04-30 12:10:05', '/sumup/webhook', '79.125.59.241'),
(1918, '2026-04-30 12:15:01', '/billetterie/', '37.168.251.8'),
(1919, '2026-04-30 12:15:56', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '104.28.42.25'),
(1920, '2026-04-30 12:15:56', '/cartage', '104.28.42.25'),
(1921, '2026-04-30 12:16:49', '/billetterie/3', '92.184.144.48'),
(1922, '2026-04-30 12:17:51', '/cartage', '104.28.42.18'),
(1923, '2026-04-30 12:18:43', '/cartage', '92.184.144.48'),
(1924, '2026-04-30 12:19:34', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '104.28.42.24'),
(1925, '2026-04-30 12:19:34', '/cartage', '104.28.42.24'),
(1926, '2026-04-30 12:19:44', '/', '92.184.144.54'),
(1927, '2026-04-30 12:19:47', '/', '92.184.144.54'),
(1928, '2026-04-30 12:19:49', '/cartage', '92.184.144.54'),
(1929, '2026-04-30 12:20:30', '/cartage', '104.28.42.24'),
(1930, '2026-04-30 12:20:45', '/cartage', '92.184.144.48'),
(1931, '2026-04-30 12:22:08', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '104.28.42.24'),
(1932, '2026-04-30 12:22:08', '/cartage', '104.28.42.24'),
(1933, '2026-04-30 12:23:14', '/login', '92.184.144.54'),
(1934, '2026-04-30 12:23:17', '/', '92.184.144.54'),
(1935, '2026-04-30 13:56:43', '/', '124.198.131.133'),
(1936, '2026-04-30 14:14:23', '/', '92.184.144.20'),
(1937, '2026-04-30 14:14:24', '/', '92.184.144.20'),
(1938, '2026-04-30 14:14:30', '/login', '92.184.144.20'),
(1939, '2026-04-30 14:14:34', '/', '92.184.144.20'),
(1940, '2026-04-30 14:20:28', '/', '45.80.158.167'),
(1941, '2026-04-30 15:27:15', '/', '18.191.206.71'),
(1942, '2026-04-30 15:57:01', '/', '62.234.46.149'),
(1943, '2026-04-30 15:57:02', '/', '62.234.46.149'),
(1944, '2026-04-30 15:57:03', '/login', '62.234.46.149'),
(1945, '2026-04-30 16:07:46', '/', '168.144.47.209'),
(1946, '2026-04-30 16:07:47', '/', '168.144.47.209'),
(1947, '2026-04-30 16:07:48', '/', '168.144.47.209'),
(1948, '2026-04-30 16:49:03', '/', '178.128.93.222'),
(1949, '2026-04-30 16:49:04', '/', '178.128.93.222'),
(1950, '2026-04-30 16:49:05', '/', '178.128.93.222'),
(1951, '2026-04-30 17:26:56', '/chants', '52.167.144.172'),
(1952, '2026-04-30 17:32:14', '/', '62.238.17.239'),
(1953, '2026-04-30 17:37:31', '/', '92.184.136.34'),
(1954, '2026-04-30 17:37:45', '/login', '92.184.136.34'),
(1955, '2026-04-30 17:37:51', '/', '92.184.136.34'),
(1956, '2026-04-30 17:40:18', '/', '92.184.97.212'),
(1957, '2026-04-30 17:40:25', '/chants', '92.184.97.212'),
(1958, '2026-04-30 17:41:24', '/chants', '31.13.115.18'),
(1959, '2026-04-30 17:41:31', '/chants', '34.221.255.81'),
(1960, '2026-04-30 17:41:55', '/chants', '77.133.249.130'),
(1961, '2026-04-30 17:42:06', '/chants', '77.133.249.130'),
(1962, '2026-04-30 17:42:13', '/chants', '46.18.228.222'),
(1963, '2026-04-30 17:42:24', '/chants', '69.171.234.1'),
(1964, '2026-04-30 17:43:54', '/groupe/', '46.18.228.222'),
(1965, '2026-04-30 17:44:03', '/table-de-vente/', '46.18.228.222'),
(1966, '2026-04-30 17:47:13', '/chants', '78.240.54.151'),
(1967, '2026-04-30 17:47:13', '/', '62.238.17.239'),
(1968, '2026-04-30 17:47:22', '/table-de-vente/', '78.240.54.151'),
(1969, '2026-04-30 17:47:28', '/table-de-vente/2', '78.240.54.151'),
(1970, '2026-04-30 17:55:49', '/table-de-vente/panier/ajouter/2', '46.18.228.222'),
(1971, '2026-04-30 17:55:49', '/panier', '46.18.228.222'),
(1972, '2026-04-30 18:00:53', '/', '205.169.39.109'),
(1973, '2026-04-30 18:01:15', '/', '205.169.39.109'),
(1974, '2026-04-30 18:36:16', '/', '155.2.225.177'),
(1975, '2026-04-30 18:37:55', '/', '155.2.225.177'),
(1976, '2026-04-30 18:44:01', '/chants', '78.240.79.104'),
(1977, '2026-04-30 19:05:35', '/billetterie/3', '78.240.79.104'),
(1978, '2026-04-30 19:06:34', '/', '92.184.97.138'),
(1979, '2026-04-30 19:06:35', '/photos-de-match/2', '92.184.97.138'),
(1980, '2026-04-30 19:06:37', '/billetterie/', '92.184.97.138'),
(1981, '2026-04-30 19:06:40', '/billetterie/3', '92.184.97.138'),
(1982, '2026-04-30 19:12:39', '/billetterie/3', '31.13.127.28'),
(1983, '2026-04-30 19:12:41', '/billetterie/3', '173.252.87.5'),
(1984, '2026-04-30 19:18:56', '/', '136.112.177.36'),
(1985, '2026-04-30 19:18:56', '/', '136.112.177.36'),
(1986, '2026-04-30 19:18:57', '/', '136.112.177.36'),
(1987, '2026-04-30 19:20:52', '/billetterie/3', '78.240.47.57'),
(1988, '2026-04-30 19:21:17', '/', '173.252.127.3'),
(1989, '2026-04-30 19:27:45', '/billetterie/3', '92.184.136.7'),
(1990, '2026-04-30 19:28:22', '/login', '92.184.136.7'),
(1991, '2026-04-30 19:28:38', '/billetterie/3', '88.160.142.5'),
(1992, '2026-04-30 19:36:26', '/login', '92.184.136.7'),
(1993, '2026-04-30 19:40:50', '/billetterie/3', '77.133.250.145'),
(1994, '2026-04-30 19:42:12', '/billetterie/3', '46.193.107.146'),
(1995, '2026-04-30 20:08:34', '/billetterie/3', '176.173.221.241'),
(1996, '2026-04-30 20:11:25', '/billetterie/3', '77.133.250.145'),
(1997, '2026-04-30 20:11:29', '/chants', '77.133.250.145'),
(1998, '2026-04-30 20:11:31', '/chants', '66.249.93.12'),
(1999, '2026-04-30 20:11:32', '/chants', '74.125.210.71'),
(2000, '2026-04-30 20:11:32', '/chants', '74.125.210.71'),
(2001, '2026-04-30 20:29:17', '/', '13.36.176.255'),
(2002, '2026-04-30 20:39:16', '/', '49.49.217.29'),
(2003, '2026-04-30 20:41:29', '/', '184.22.228.86'),
(2004, '2026-04-30 20:47:16', '/', '168.144.47.209'),
(2005, '2026-04-30 20:47:17', '/', '168.144.47.209'),
(2006, '2026-04-30 20:47:19', '/', '168.144.47.209'),
(2007, '2026-04-30 20:48:50', '/billetterie/3', '92.184.136.226'),
(2008, '2026-04-30 20:48:54', '/billetterie/3', '92.184.136.226'),
(2009, '2026-04-30 20:55:18', '/login', '92.184.136.36'),
(2010, '2026-04-30 20:55:23', '/', '92.184.136.36'),
(2011, '2026-04-30 20:55:29', '/billetterie/', '92.184.136.36'),
(2012, '2026-04-30 20:55:30', '/billetterie/3', '92.184.136.36'),
(2013, '2026-04-30 20:55:33', '/billetterie/3/billetweb-preinscription', '92.184.136.36'),
(2014, '2026-04-30 20:55:33', '/billetterie/3', '92.184.136.36'),
(2015, '2026-04-30 20:56:16', '/billetterie/3', '78.240.182.83'),
(2016, '2026-04-30 20:56:37', '/login', '78.240.182.83'),
(2017, '2026-04-30 20:57:11', '/', '78.240.182.83'),
(2018, '2026-04-30 20:57:51', '/invitation', '78.240.182.83'),
(2019, '2026-04-30 20:57:57', '/invitation', '78.240.182.83'),
(2020, '2026-04-30 20:57:57', '/register/610a08669737bc9c45f6d6a24139b2df', '78.240.182.83'),
(2021, '2026-04-30 20:59:51', '/register/610a08669737bc9c45f6d6a24139b2df', '78.240.182.83'),
(2022, '2026-04-30 21:00:43', '/register/610a08669737bc9c45f6d6a24139b2df', '78.240.182.83'),
(2023, '2026-04-30 21:01:39', '/register/610a08669737bc9c45f6d6a24139b2df', '78.240.182.83'),
(2024, '2026-04-30 21:03:28', '/register/610a08669737bc9c45f6d6a24139b2df', '78.240.182.83'),
(2025, '2026-04-30 21:04:13', '/register/610a08669737bc9c45f6d6a24139b2df', '78.240.182.83'),
(2026, '2026-04-30 21:04:14', '/', '78.240.182.83'),
(2027, '2026-04-30 21:04:25', '/billetterie/', '78.240.182.83'),
(2028, '2026-04-30 21:04:36', '/billetterie/3', '78.240.182.83'),
(2029, '2026-04-30 21:04:44', '/billetterie/3/billetweb-preinscription', '78.240.182.83'),
(2030, '2026-04-30 21:04:44', '/billetterie/3', '78.240.182.83'),
(2031, '2026-04-30 21:10:08', '/billetterie/3', '88.141.160.42'),
(2032, '2026-04-30 21:10:18', '/table-de-vente/', '88.141.160.42'),
(2033, '2026-04-30 21:16:04', '/billetterie/3', '109.7.216.232'),
(2034, '2026-04-30 22:11:10', '/', '149.57.180.17'),
(2035, '2026-04-30 22:12:21', '/', '23.27.145.128'),
(2036, '2026-04-30 22:44:43', '/', '161.35.222.135'),
(2037, '2026-04-30 22:44:43', '/', '161.35.222.135'),
(2038, '2026-04-30 22:44:44', '/', '161.35.222.135'),
(2039, '2026-04-30 23:05:18', '/', '178.128.93.222'),
(2040, '2026-04-30 23:05:19', '/', '178.128.93.222'),
(2041, '2026-04-30 23:05:21', '/', '178.128.93.222'),
(2042, '2026-04-30 23:11:03', '/billetterie/3', '92.184.136.130'),
(2043, '2026-04-30 23:11:11', '/login', '92.184.136.130'),
(2044, '2026-04-30 23:11:21', '/login', '92.184.136.130'),
(2045, '2026-04-30 23:11:26', '/', '92.184.136.130'),
(2046, '2026-04-30 23:11:27', '/', '92.184.136.130'),
(2047, '2026-04-30 23:42:57', '/', '92.184.136.254'),
(2048, '2026-04-30 23:43:03', '/billetterie/', '92.184.136.254'),
(2049, '2026-04-30 23:43:05', '/billetterie/3', '92.184.136.254'),
(2050, '2026-04-30 23:43:28', '/billetterie/3', '78.240.182.243'),
(2051, '2026-04-30 23:50:36', '/billetterie/3', '92.184.97.188'),
(2052, '2026-04-30 23:53:46', '/billetterie/3', '92.184.97.188'),
(2053, '2026-04-30 23:53:52', '/login', '92.184.97.188'),
(2054, '2026-04-30 23:54:06', '/invitation', '92.184.97.188'),
(2055, '2026-04-30 23:54:12', '/invitation', '92.184.97.188'),
(2056, '2026-04-30 23:55:37', '/invitation', '92.184.97.188'),
(2057, '2026-04-30 23:55:37', '/register/44ecd53c8a79a2762ae80a3682bc70d8', '92.184.97.188'),
(2058, '2026-04-30 23:57:11', '/register/44ecd53c8a79a2762ae80a3682bc70d8', '92.184.97.188'),
(2059, '2026-04-30 23:57:13', '/', '92.184.97.188'),
(2060, '2026-04-30 23:57:56', '/profil/11', '92.184.97.188'),
(2061, '2026-04-30 23:58:03', '/billetterie/3', '78.197.211.90'),
(2062, '2026-04-30 23:58:22', '/login', '78.197.211.90'),
(2063, '2026-04-30 23:58:34', '/billetterie/', '78.197.211.90'),
(2064, '2026-04-30 23:58:40', '/billetterie/3', '78.197.211.90'),
(2065, '2026-04-30 23:59:08', '/billetterie/3/billetweb-preinscription', '78.197.211.90'),
(2066, '2026-04-30 23:59:08', '/billetterie/3', '78.197.211.90'),
(2067, '2026-05-01 00:01:46', '/', '78.197.211.90'),
(2068, '2026-05-01 00:01:55', '/', '78.197.211.90'),
(2069, '2026-05-01 00:05:59', '/invitation', '78.197.211.90'),
(2070, '2026-05-01 00:07:22', '/invitation', '78.197.211.90'),
(2071, '2026-05-01 00:07:22', '/register/fe8465aac0226500376e09f2871e182f', '78.197.211.90'),
(2072, '2026-05-01 00:07:23', '/invitation', '78.197.211.90'),
(2073, '2026-05-01 00:07:23', '/register/fe8465aac0226500376e09f2871e182f', '78.197.211.90'),
(2074, '2026-05-01 00:08:16', '/register/fe8465aac0226500376e09f2871e182f', '78.197.211.90'),
(2075, '2026-05-01 00:08:17', '/', '78.197.211.90'),
(2076, '2026-05-01 00:08:27', '/profil/12', '78.197.211.90'),
(2077, '2026-05-01 00:52:42', '/', '137.184.160.115'),
(2078, '2026-05-01 00:52:43', '/', '137.184.160.115'),
(2079, '2026-05-01 00:52:44', '/', '137.184.160.115'),
(2080, '2026-05-01 00:56:38', '/billetterie/3', '91.166.29.249'),
(2081, '2026-05-01 00:58:31', '/billetterie/3', '88.187.139.25'),
(2082, '2026-05-01 01:23:11', '/', '83.202.95.42'),
(2083, '2026-05-01 01:32:50', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '92.184.136.214'),
(2084, '2026-05-01 01:32:50', '/cartage', '92.184.136.214'),
(2085, '2026-05-01 01:32:55', '/cartage', '92.184.136.214'),
(2086, '2026-05-01 01:33:02', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '92.184.136.214'),
(2087, '2026-05-01 01:33:02', '/cartage', '92.184.136.214'),
(2088, '2026-05-01 01:36:09', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '83.202.95.42'),
(2089, '2026-05-01 01:36:10', '/cartage', '83.202.95.42'),
(2090, '2026-05-01 01:39:21', '/', '178.128.93.222'),
(2091, '2026-05-01 01:39:23', '/', '178.128.93.222'),
(2092, '2026-05-01 01:39:24', '/', '178.128.93.222'),
(2093, '2026-05-01 01:39:44', '/cartage', '83.202.95.42'),
(2094, '2026-05-01 01:47:46', '/merci', '83.202.95.42'),
(2095, '2026-05-01 01:47:53', '/merci/pdf', '83.202.95.42'),
(2096, '2026-05-01 01:51:32', '/cartage', '83.202.95.42'),
(2097, '2026-05-01 01:51:39', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '83.202.95.42'),
(2098, '2026-05-01 01:51:40', '/cartage', '83.202.95.42'),
(2099, '2026-05-01 01:51:57', '/cartage', '83.202.95.42'),
(2100, '2026-05-01 01:52:18', '/sumup/webhook', '52.48.233.7'),
(2101, '2026-05-01 01:52:26', '/merci', '83.202.95.42'),
(2102, '2026-05-01 01:53:23', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '83.202.95.42'),
(2103, '2026-05-01 01:53:23', '/cartage', '83.202.95.42'),
(2104, '2026-05-01 01:53:42', '/cartage', '83.202.95.42'),
(2105, '2026-05-01 01:54:02', '/sumup/webhook', '52.48.211.42'),
(2106, '2026-05-01 01:55:21', '/cartage', '83.202.95.42'),
(2107, '2026-05-01 01:55:23', '/cartage', '83.202.95.42'),
(2108, '2026-05-01 01:55:27', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '83.202.95.42'),
(2109, '2026-05-01 01:55:27', '/cartage', '83.202.95.42'),
(2110, '2026-05-01 01:57:23', '/cartage', '83.202.95.42'),
(2111, '2026-05-01 01:57:46', '/cartage', '83.202.95.42'),
(2112, '2026-05-01 02:00:23', '/merci', '20.0.53.107'),
(2113, '2026-05-01 02:01:11', '/', '168.144.133.214'),
(2114, '2026-05-01 02:01:12', '/', '168.144.133.214'),
(2115, '2026-05-01 02:01:13', '/', '168.144.133.214'),
(2116, '2026-05-01 02:07:17', '/sumup/webhook', '79.125.59.241'),
(2117, '2026-05-01 02:08:01', '/', '83.202.95.42'),
(2118, '2026-05-01 02:08:04', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '83.202.95.42'),
(2119, '2026-05-01 02:08:04', '/cartage', '83.202.95.42'),
(2120, '2026-05-01 02:08:37', '/cartage', '83.202.95.42'),
(2121, '2026-05-01 02:08:49', '/cartage', '83.202.95.42'),
(2122, '2026-05-01 02:09:34', '/cartage', '83.202.95.42'),
(2123, '2026-05-01 02:09:49', '/cartage', '83.202.95.42'),
(2124, '2026-05-01 02:10:09', '/sumup/webhook', '79.125.59.241'),
(2125, '2026-05-01 02:10:11', '/merci', '83.202.95.42'),
(2126, '2026-05-01 02:10:11', '/profil/13', '83.202.95.42'),
(2127, '2026-05-01 02:10:39', '/', '83.202.95.42'),
(2128, '2026-05-01 02:10:41', '/login', '83.202.95.42'),
(2129, '2026-05-01 02:10:44', '/', '83.202.95.42'),
(2130, '2026-05-01 02:10:48', '/', '83.202.95.42'),
(2131, '2026-05-01 02:10:50', '/login', '83.202.95.42'),
(2132, '2026-05-01 02:10:58', '/login', '83.202.95.42'),
(2133, '2026-05-01 02:11:06', '/login', '83.202.95.42'),
(2134, '2026-05-01 02:13:55', '/login', '83.202.95.42'),
(2135, '2026-05-01 02:18:57', '/login', '83.202.95.42'),
(2136, '2026-05-01 02:19:02', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '83.202.95.42'),
(2137, '2026-05-01 02:19:02', '/cartage', '83.202.95.42'),
(2138, '2026-05-01 02:19:26', '/cartage', '83.202.95.42'),
(2139, '2026-05-01 02:19:50', '/sumup/webhook', '52.48.233.7'),
(2140, '2026-05-01 02:19:51', '/merci', '83.202.95.42'),
(2141, '2026-05-01 02:19:51', '/profil/14', '83.202.95.42'),
(2142, '2026-05-01 02:20:07', '/', '83.202.95.42'),
(2143, '2026-05-01 02:20:12', '/login', '83.202.95.42'),
(2144, '2026-05-01 02:20:24', '/login', '83.202.95.42'),
(2145, '2026-05-01 02:20:39', '/', '83.202.95.42'),
(2146, '2026-05-01 02:20:45', '/', '83.202.95.42'),
(2147, '2026-05-01 02:20:46', '/login', '83.202.95.42'),
(2148, '2026-05-01 02:20:49', '/', '83.202.95.42'),
(2149, '2026-05-01 02:22:07', '/', '83.202.95.42'),
(2150, '2026-05-01 02:22:12', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '83.202.95.42'),
(2151, '2026-05-01 02:22:12', '/cartage', '83.202.95.42'),
(2152, '2026-05-01 02:22:33', '/cartage', '83.202.95.42'),
(2153, '2026-05-01 02:22:53', '/login', '83.202.95.42'),
(2154, '2026-05-01 02:22:57', '/', '83.202.95.42'),
(2155, '2026-05-01 02:23:31', '/', '83.202.95.42'),
(2156, '2026-05-01 02:23:32', '/login', '83.202.95.42'),
(2157, '2026-05-01 02:23:38', '/login', '83.202.95.42'),
(2158, '2026-05-01 02:42:32', '/', '34.122.147.229'),
(2159, '2026-05-01 02:43:08', '/table-de-vente/', '83.202.95.42'),
(2160, '2026-05-01 02:43:34', '/table-de-vente/', '83.202.95.42'),
(2161, '2026-05-01 02:43:46', '/', '83.202.95.42'),
(2162, '2026-05-01 02:43:48', '/table-de-vente/', '83.202.95.42'),
(2163, '2026-05-01 02:44:10', '/login', '83.202.95.42'),
(2164, '2026-05-01 02:44:13', '/', '83.202.95.42'),
(2165, '2026-05-01 02:45:00', '/table-de-vente/', '83.202.95.42'),
(2166, '2026-05-01 02:45:05', '/table-de-vente/', '83.202.95.42'),
(2167, '2026-05-01 02:45:10', '/', '83.202.95.42'),
(2168, '2026-05-01 02:45:12', '/table-de-vente/', '83.202.95.42'),
(2169, '2026-05-01 02:45:14', '/login', '83.202.95.42'),
(2170, '2026-05-01 02:45:16', '/', '83.202.95.42'),
(2171, '2026-05-01 02:47:44', '/', '83.202.95.42'),
(2172, '2026-05-01 02:47:45', '/table-de-vente/', '83.202.95.42'),
(2173, '2026-05-01 02:47:50', '/', '83.202.95.42'),
(2174, '2026-05-01 02:47:52', '/table-de-vente/', '83.202.95.42'),
(2175, '2026-05-01 02:47:54', '/login', '83.202.95.42'),
(2176, '2026-05-01 02:47:57', '/', '83.202.95.42'),
(2177, '2026-05-01 02:59:00', '/', '51.91.156.252'),
(2178, '2026-05-01 04:02:52', '/', '149.57.180.86'),
(2179, '2026-05-01 04:30:35', '/', '134.209.35.236'),
(2180, '2026-05-01 04:30:36', '/', '134.209.35.236'),
(2181, '2026-05-01 04:36:09', '/billetterie/3', '157.55.39.195'),
(2182, '2026-05-01 04:58:24', '/photos-de-match/', '198.244.242.163'),
(2183, '2026-05-01 06:04:36', '/', '103.4.251.137'),
(2184, '2026-05-01 06:04:37', '/', '104.164.126.165'),
(2185, '2026-05-01 06:04:37', '/', '103.4.251.137'),
(2186, '2026-05-01 06:08:48', '/', '66.132.195.116'),
(2187, '2026-05-01 06:34:30', '/', '8.220.204.92'),
(2188, '2026-05-01 07:21:31', '/', '168.144.133.214'),
(2189, '2026-05-01 07:21:32', '/', '168.144.133.214'),
(2190, '2026-05-01 07:21:33', '/', '168.144.133.214'),
(2191, '2026-05-01 07:46:36', '/forgot-password', '17.241.75.12'),
(2192, '2026-05-01 08:17:04', '/forgot-password', '40.77.167.77'),
(2193, '2026-05-01 08:18:59', '/', '66.132.186.168'),
(2194, '2026-05-01 09:02:21', '/billetterie/', '155.2.128.134'),
(2195, '2026-05-01 09:02:25', '/billetterie/', '155.2.128.134'),
(2196, '2026-05-01 09:02:37', '/billetterie/', '155.2.128.134'),
(2197, '2026-05-01 09:02:50', '/', '155.2.128.134'),
(2198, '2026-05-01 09:03:16', '/', '155.2.128.134'),
(2199, '2026-05-01 09:04:08', '/login', '155.2.128.134'),
(2200, '2026-05-01 09:04:19', '/', '155.2.128.134'),
(2201, '2026-05-01 09:04:20', '/', '155.2.128.134'),
(2202, '2026-05-01 09:04:25', '/actualites/auto-matos-2', '155.2.128.134'),
(2203, '2026-05-01 09:04:31', '/table-de-vente/2', '155.2.128.134'),
(2204, '2026-05-01 09:04:44', '/table-de-vente/panier/ajouter/2', '155.2.128.134'),
(2205, '2026-05-01 09:04:44', '/panier', '155.2.128.134'),
(2206, '2026-05-01 09:32:22', '/billetterie/3', '92.184.97.118'),
(2207, '2026-05-01 09:32:22', '/billetterie/3', '92.184.97.118'),
(2208, '2026-05-01 10:05:00', '/', '51.91.156.252'),
(2209, '2026-05-01 10:18:58', '/', '136.113.250.44'),
(2210, '2026-05-01 10:18:59', '/', '136.113.250.44'),
(2211, '2026-05-01 10:19:00', '/', '136.113.250.44'),
(2212, '2026-05-01 10:19:31', '/billetterie/3', '17.22.245.157'),
(2213, '2026-05-01 11:01:49', '/cartage', '149.22.90.139'),
(2214, '2026-05-01 11:02:38', '/', '88.161.222.224'),
(2215, '2026-05-01 11:04:20', '/', '88.161.222.224'),
(2216, '2026-05-01 11:04:25', '/chants', '88.161.222.224'),
(2217, '2026-05-01 11:06:45', '/actualites/ultras-lions-fleury-2026-furies', '17.241.219.166'),
(2218, '2026-05-01 11:11:57', '/', '62.60.130.235'),
(2219, '2026-05-01 11:42:13', '/', '152.53.246.211'),
(2220, '2026-05-01 11:42:59', '/', '77.130.243.122'),
(2221, '2026-05-01 11:45:07', '/actualites/ultras-lions-fleury-2026-furies', '77.130.243.122'),
(2222, '2026-05-01 11:45:56', '/actualites/ultras-lions-fleury-2026-furies', '77.130.243.122'),
(2223, '2026-05-01 11:46:09', '/', '92.184.97.215'),
(2224, '2026-05-01 11:46:38', '/photos-de-match/3', '77.130.243.122'),
(2225, '2026-05-01 11:47:08', '/actualites/fc-fleury-91-le-club-de-l-essonne', '77.130.243.122'),
(2226, '2026-05-01 11:49:06', '/photos-de-match/2', '77.130.243.122'),
(2227, '2026-05-01 11:49:26', '/actualites/auto-matos-2', '77.130.243.122'),
(2228, '2026-05-01 11:49:26', '/actualites/auto-matos-2', '77.130.243.122'),
(2229, '2026-05-01 11:50:05', '/table-de-vente/2', '77.130.243.122'),
(2230, '2026-05-01 11:51:07', '/table-de-vente/', '77.130.243.122'),
(2231, '2026-05-01 11:51:19', '/chants', '77.130.243.122'),
(2232, '2026-05-01 12:11:22', '/', '172.226.148.41'),
(2233, '2026-05-01 12:11:28', '/billetterie/', '172.226.148.41'),
(2234, '2026-05-01 12:11:31', '/billetterie/3', '172.226.148.41'),
(2235, '2026-05-01 12:11:40', '/billetterie/', '172.226.148.41'),
(2236, '2026-05-01 12:15:13', '/billetterie/3', '83.114.2.124'),
(2237, '2026-05-01 12:15:28', '/billetterie/3/billetweb-preinscription', '83.114.2.124'),
(2238, '2026-05-01 12:15:28', '/billetterie/3', '83.114.2.124'),
(2239, '2026-05-01 12:16:41', '/billetterie/3', '83.114.2.124'),
(2240, '2026-05-01 12:22:09', '/billetterie/3', '17.241.75.89'),
(2241, '2026-05-01 13:11:13', '/billetterie/3', '62.35.110.5'),
(2242, '2026-05-01 13:13:46', '/', '54.88.195.180'),
(2243, '2026-05-01 13:13:46', '/', '54.88.195.180'),
(2244, '2026-05-01 13:13:46', '/', '54.88.195.180'),
(2245, '2026-05-01 13:13:46', '/', '54.88.195.180'),
(2246, '2026-05-01 14:09:16', '/', '88.161.222.224'),
(2247, '2026-05-01 14:09:18', '/billetterie/', '88.161.222.224'),
(2248, '2026-05-01 14:09:20', '/billetterie/3', '88.161.222.224'),
(2249, '2026-05-01 14:09:53', '/', '4.43.184.113'),
(2250, '2026-05-01 14:10:05', '/billetterie/3/billetweb-preinscription', '88.161.222.224'),
(2251, '2026-05-01 14:10:06', '/billetterie/3', '88.161.222.224'),
(2252, '2026-05-01 14:12:15', '/', '74.7.227.188'),
(2253, '2026-05-01 14:12:18', '/actualites/auto-matos-2', '74.7.227.188'),
(2254, '2026-05-01 14:13:19', '/medias', '74.7.227.188'),
(2255, '2026-05-01 14:13:39', '/billetterie/3', '88.161.222.224'),
(2256, '2026-05-01 14:38:13', '/', '34.75.247.137'),
(2257, '2026-05-01 14:38:13', '/', '34.75.247.137'),
(2258, '2026-05-01 14:38:13', '/', '34.75.247.137'),
(2259, '2026-05-01 14:58:27', '/', '88.161.222.224'),
(2260, '2026-05-01 14:58:27', '/', '88.161.222.224'),
(2261, '2026-05-01 14:58:35', '/billetterie/', '88.161.222.224'),
(2262, '2026-05-01 14:58:37', '/billetterie/3', '88.161.222.224'),
(2263, '2026-05-01 14:58:52', '/billetterie/3/billetweb-preinscription', '88.161.222.224'),
(2264, '2026-05-01 14:58:52', '/billetterie/3', '88.161.222.224'),
(2265, '2026-05-01 14:59:16', '/billetterie/', '78.197.211.90'),
(2266, '2026-05-01 14:59:27', '/billetterie/3', '88.161.222.224'),
(2267, '2026-05-01 14:59:36', '/billetterie/3/billetweb-preinscription', '88.161.222.224'),
(2268, '2026-05-01 14:59:37', '/billetterie/3', '88.161.222.224'),
(2269, '2026-05-01 14:59:40', '/billetterie/3', '78.197.211.90'),
(2270, '2026-05-01 15:00:36', '/', '88.123.2.161'),
(2271, '2026-05-01 15:00:47', '/groupe/', '88.123.2.161'),
(2272, '2026-05-01 15:00:50', '/invitation', '88.123.2.161'),
(2273, '2026-05-01 15:01:00', '/table-de-vente/', '88.123.2.161'),
(2274, '2026-05-01 15:01:08', '/table-de-vente/2', '88.123.2.161'),
(2275, '2026-05-01 15:01:16', '/chants', '88.123.2.161'),
(2276, '2026-05-01 15:02:26', '/billetterie/3', '109.7.216.232'),
(2277, '2026-05-01 15:03:13', '/billetterie/3', '92.184.97.207'),
(2278, '2026-05-01 15:03:18', '/billetterie/3/billetweb-preinscription', '78.197.211.90'),
(2279, '2026-05-01 15:03:18', '/billetterie/3', '78.197.211.90'),
(2280, '2026-05-01 15:03:47', '/billetterie/3/billetweb-preinscription', '92.184.97.207'),
(2281, '2026-05-01 15:03:48', '/billetterie/3', '92.184.97.207'),
(2282, '2026-05-01 15:18:11', '/billetterie/3', '88.161.222.224'),
(2283, '2026-05-01 15:34:41', '/billetterie/', '52.167.144.238'),
(2284, '2026-05-01 16:03:17', '/', '134.122.87.226'),
(2285, '2026-05-01 16:03:18', '/', '134.122.87.226'),
(2286, '2026-05-01 16:23:05', '/billetterie/3', '78.197.211.90'),
(2287, '2026-05-01 16:23:05', '/billetterie/3', '78.197.211.90'),
(2288, '2026-05-01 16:35:00', '/', '35.232.164.108'),
(2289, '2026-05-01 16:35:01', '/', '35.232.164.108'),
(2290, '2026-05-01 16:35:02', '/', '35.232.164.108'),
(2291, '2026-05-01 17:11:22', '/', '89.187.171.145'),
(2292, '2026-05-01 17:11:22', '/', '82.102.27.91'),
(2293, '2026-05-01 17:11:40', '/', '35.231.147.143'),
(2294, '2026-05-01 17:11:40', '/', '35.231.147.143'),
(2295, '2026-05-01 17:11:41', '/', '35.231.147.143'),
(2296, '2026-05-01 17:33:58', '/', '62.238.17.239'),
(2297, '2026-05-01 17:41:19', '/', '66.132.172.135'),
(2298, '2026-05-01 17:51:37', '/', '62.238.17.239'),
(2299, '2026-05-01 17:54:03', '/', '92.184.136.133'),
(2300, '2026-05-01 17:54:07', '/login', '92.184.136.133'),
(2301, '2026-05-01 17:54:12', '/', '92.184.136.133'),
(2302, '2026-05-01 17:54:31', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '77.133.249.63'),
(2303, '2026-05-01 17:54:31', '/cartage', '77.133.249.63'),
(2304, '2026-05-01 17:55:27', '/cartage', '77.133.249.63'),
(2305, '2026-05-01 17:56:03', '/sumup/webhook', '52.48.211.42'),
(2306, '2026-05-01 17:56:05', '/merci', '77.133.249.63'),
(2307, '2026-05-01 17:56:05', '/profil/16', '77.133.249.63'),
(2308, '2026-05-01 17:56:07', '/profil/16', '66.249.93.14'),
(2309, '2026-05-01 17:56:07', '/profil/16', '66.249.93.13'),
(2310, '2026-05-01 17:56:08', '/profil/16', '74.125.210.69'),
(2311, '2026-05-01 18:06:15', '/cartage/qr/b817cb0f36ccf57d512e0387182734c3163e6c5ecbce7a16', '149.7.98.91'),
(2312, '2026-05-01 18:06:16', '/cartage', '149.7.98.91'),
(2313, '2026-05-01 18:07:46', '/cartage', '149.7.98.90'),
(2314, '2026-05-01 18:07:56', '/cartage', '149.7.98.90'),
(2315, '2026-05-01 18:08:04', '/', '66.132.224.82'),
(2316, '2026-05-01 18:08:21', '/cartage', '149.7.98.90'),
(2317, '2026-05-01 18:10:09', '/', '149.7.98.90'),
(2318, '2026-05-01 18:10:21', '/login', '149.7.98.91'),
(2319, '2026-05-01 18:10:36', '/', '149.7.98.91'),
(2320, '2026-05-01 18:10:37', '/login', '213.44.27.140'),
(2321, '2026-05-01 18:10:45', '/table-de-vente/', '149.7.98.91'),
(2322, '2026-05-01 18:24:43', '/', '167.94.146.63'),
(2323, '2026-05-01 18:27:12', '/', '66.132.172.99'),
(2324, '2026-05-01 18:28:54', '/', '74.7.242.57'),
(2325, '2026-05-01 18:28:57', '/medias', '74.7.242.57'),
(2326, '2026-05-01 18:29:59', '/actualites/auto-matos-2', '74.7.242.57'),
(2327, '2026-05-01 18:31:51', '/table-de-vente/2', '17.22.253.80'),
(2328, '2026-05-01 18:38:39', '/billetterie/3', '104.28.42.18'),
(2329, '2026-05-01 18:38:43', '/chants', '104.28.42.18'),
(2330, '2026-05-01 18:42:40', '/', '92.184.136.133'),
(2331, '2026-05-01 18:42:47', '/billetterie/', '92.184.136.133'),
(2332, '2026-05-01 18:42:48', '/billetterie/3', '92.184.136.133'),
(2333, '2026-05-01 18:42:52', '/billetterie/3/billetweb-preinscription', '92.184.136.133'),
(2334, '2026-05-01 18:42:52', '/billetterie/3', '92.184.136.133'),
(2335, '2026-05-01 19:23:11', '/actualites/fc-fleury-91-le-club-de-l-essonne', '17.241.219.61'),
(2336, '2026-05-01 19:57:35', '/table-de-vente/2', '52.167.144.191'),
(2337, '2026-05-01 20:02:43', '/billetterie/3', '17.22.253.170'),
(2338, '2026-05-01 20:45:12', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '17.22.237.233'),
(2339, '2026-05-01 21:18:40', '/', '91.134.43.151'),
(2340, '2026-05-01 21:48:26', '/invitation', '52.167.144.216'),
(2341, '2026-05-01 22:59:36', '/', '155.117.189.61'),
(2342, '2026-05-01 22:59:38', '/', '155.117.189.61'),
(2343, '2026-05-01 22:59:39', '/billetterie/', '155.117.189.61'),
(2344, '2026-05-01 22:59:39', '/', '155.117.189.61'),
(2345, '2026-05-01 22:59:40', '/actualites/ultras-lions-fleury-2026-furies', '155.117.189.61'),
(2346, '2026-05-01 22:59:41', '/groupe/', '155.117.189.61'),
(2347, '2026-05-01 22:59:42', '/photos-de-match/3', '155.117.189.61'),
(2348, '2026-05-01 22:59:43', '/actualites/', '155.117.189.61'),
(2349, '2026-05-01 22:59:44', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '155.117.189.61'),
(2350, '2026-05-01 22:59:45', '/chants', '155.117.189.61'),
(2351, '2026-05-01 22:59:46', '/actualites/fc-fleury-91-le-club-de-l-essonne', '155.117.189.61'),
(2352, '2026-05-01 22:59:46', '/medias', '155.117.189.61'),
(2353, '2026-05-01 22:59:47', '/photos-de-match/2', '155.117.189.61'),
(2354, '2026-05-01 22:59:48', '/photos-de-match/', '155.117.189.61'),
(2355, '2026-05-01 22:59:48', '/actualites/auto-matos-2', '155.117.189.61'),
(2356, '2026-05-01 22:59:49', '/medias/medias', '155.117.189.61'),
(2357, '2026-05-01 22:59:50', '/invitation', '155.117.189.61'),
(2358, '2026-05-01 22:59:51', '/photos-de-match/', '155.117.189.61'),
(2359, '2026-05-01 22:59:52', '/login', '155.117.189.61'),
(2360, '2026-05-01 22:59:53', '/table-de-vente/', '155.117.189.61'),
(2361, '2026-05-01 23:04:23', '/actualites/ultras-lions-fleury-2026-furies', '77.131.12.144'),
(2362, '2026-05-01 23:04:28', '/', '77.131.12.144'),
(2363, '2026-05-01 23:04:37', '/actualites/auto-matos-2', '77.131.12.144'),
(2364, '2026-05-01 23:04:45', '/table-de-vente/2', '77.131.12.144'),
(2365, '2026-05-01 23:53:42', '/photos-de-match/3', '17.241.75.123'),
(2366, '2026-05-02 00:52:37', '/photos-de-match/1', '17.241.219.107'),
(2367, '2026-05-02 01:26:57', '/photos-de-match/3', '17.22.237.242'),
(2368, '2026-05-02 01:35:56', '/', '194.230.158.210'),
(2369, '2026-05-02 03:11:17', '/photos-de-match/', '157.55.39.197'),
(2370, '2026-05-02 03:33:24', '/', '157.230.41.222'),
(2371, '2026-05-02 03:33:25', '/', '157.230.41.222'),
(2372, '2026-05-02 03:33:26', '/', '157.230.41.222'),
(2373, '2026-05-02 03:42:37', '/', '34.122.147.229'),
(2374, '2026-05-02 03:53:40', '/photos-de-match/2', '207.46.13.116'),
(2375, '2026-05-02 07:23:48', '/actualites/fc-fleury-91-le-club-de-l-essonne', '54.37.118.84'),
(2376, '2026-05-02 07:38:57', '/table-de-vente/panier', '17.22.237.95'),
(2377, '2026-05-02 07:38:58', '/panier', '17.22.237.95'),
(2378, '2026-05-02 08:04:03', '/table-de-vente/', '40.77.167.78'),
(2379, '2026-05-02 08:24:55', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '51.75.236.157'),
(2380, '2026-05-02 08:32:35', '/', '212.33.255.53'),
(2381, '2026-05-02 08:32:36', '/', '212.33.255.53'),
(2382, '2026-05-02 08:45:20', '/photos-de-match/1', '40.77.167.48'),
(2383, '2026-05-02 09:00:29', '/', '157.230.41.222'),
(2384, '2026-05-02 09:00:30', '/', '157.230.41.222'),
(2385, '2026-05-02 09:00:31', '/', '157.230.41.222'),
(2386, '2026-05-02 09:53:32', '/', '78.242.164.101'),
(2387, '2026-05-02 10:14:59', '/photos-de-match/2', '17.246.15.141'),
(2388, '2026-05-02 11:04:42', '/billetterie/3', '94.23.188.203'),
(2389, '2026-05-02 11:18:28', '/', '62.238.17.239'),
(2390, '2026-05-02 12:06:11', '/', '176.125.229.29'),
(2391, '2026-05-02 12:19:24', '/photos-de-match/2', '17.241.219.100'),
(2392, '2026-05-02 12:27:31', '/table-de-vente/2', '17.241.227.27'),
(2393, '2026-05-02 12:39:57', '/', '176.151.39.183'),
(2394, '2026-05-02 12:40:02', '/', '176.151.39.183'),
(2395, '2026-05-02 12:42:11', '/', '176.151.39.183'),
(2396, '2026-05-02 13:23:21', '/', '44.202.41.43'),
(2397, '2026-05-02 13:23:21', '/', '44.202.41.43'),
(2398, '2026-05-02 13:23:21', '/', '44.202.41.43'),
(2399, '2026-05-02 13:29:30', '/', '81.65.94.87'),
(2400, '2026-05-02 13:35:35', '/', '176.151.39.183'),
(2401, '2026-05-02 13:35:37', '/', '176.151.39.183'),
(2402, '2026-05-02 13:57:42', '/', '23.27.145.242'),
(2403, '2026-05-02 14:05:19', '/photos-de-match/1', '17.246.19.251'),
(2404, '2026-05-02 14:44:50', '/', '217.76.60.209'),
(2405, '2026-05-02 15:32:43', '/medias', '40.77.167.7'),
(2406, '2026-05-02 15:45:06', '/billetterie/3', '81.65.94.87'),
(2407, '2026-05-02 16:15:29', '/billetterie/3', '194.230.146.78'),
(2408, '2026-05-02 16:30:58', '/actualites/ultras-lions-fleury-2026-furies', '37.59.204.145'),
(2409, '2026-05-02 16:55:55', '/photos-de-match/', '17.241.75.18'),
(2410, '2026-05-02 17:30:34', '/photos-de-match/', '52.167.144.161'),
(2411, '2026-05-02 17:55:26', '/', '83.202.209.82'),
(2412, '2026-05-02 17:55:40', '/actualites/auto-matos-2', '83.202.209.82'),
(2413, '2026-05-02 17:55:47', '/table-de-vente/2', '83.202.209.82'),
(2414, '2026-05-02 18:18:00', '/chants', '40.77.167.47'),
(2415, '2026-05-02 19:37:51', '/actualites/fc-fleury-91-le-club-de-l-essonne', '90.60.10.226'),
(2416, '2026-05-02 19:37:54', '/actualites/fc-fleury-91-le-club-de-l-essonne', '90.60.10.226'),
(2417, '2026-05-02 19:37:56', '/chants', '90.60.10.226'),
(2418, '2026-05-02 19:38:49', '/actualites/', '90.60.10.226'),
(2419, '2026-05-02 19:39:02', '/actualites/auto-matos-2', '90.60.10.226'),
(2420, '2026-05-02 19:39:05', '/table-de-vente/2', '90.60.10.226'),
(2421, '2026-05-02 19:39:11', '/table-de-vente/panier', '90.60.10.226'),
(2422, '2026-05-02 19:39:11', '/panier', '90.60.10.226'),
(2423, '2026-05-02 19:39:40', '/billetterie/', '90.60.10.226'),
(2424, '2026-05-02 19:39:44', '/', '90.60.10.226'),
(2425, '2026-05-02 19:39:53', '/table-de-vente/', '90.60.10.226'),
(2426, '2026-05-02 19:39:56', '/table-de-vente/2', '90.60.10.226'),
(2427, '2026-05-02 19:40:07', '/table-de-vente/panier/ajouter/2', '90.60.10.226'),
(2428, '2026-05-02 19:40:07', '/panier', '90.60.10.226'),
(2429, '2026-05-02 19:40:16', '/panier/supprimer/merch/2|TU', '90.60.10.226'),
(2430, '2026-05-02 19:40:16', '/panier', '90.60.10.226'),
(2431, '2026-05-02 19:40:18', '/', '90.60.10.226'),
(2432, '2026-05-02 19:40:25', '/photos-de-match/', '90.60.10.226'),
(2433, '2026-05-02 19:40:36', '/groupe/', '90.60.10.226'),
(2434, '2026-05-02 19:52:07', '/invitation', '52.167.144.25'),
(2435, '2026-05-02 19:55:54', '/', '34.147.203.204'),
(2436, '2026-05-02 20:13:31', '/', '210.64.24.100'),
(2437, '2026-05-02 20:19:19', '/photos-de-match/2', '40.77.167.57'),
(2438, '2026-05-02 20:32:52', '/', '167.172.90.59'),
(2439, '2026-05-02 20:32:53', '/', '167.172.90.59'),
(2440, '2026-05-02 20:32:54', '/', '167.172.90.59'),
(2441, '2026-05-02 21:10:37', '/', '83.202.209.82'),
(2442, '2026-05-02 21:11:02', '/billetterie/', '83.202.209.82'),
(2443, '2026-05-02 21:48:07', '/login', '92.184.144.102'),
(2444, '2026-05-02 22:02:39', '/', '83.202.209.82'),
(2445, '2026-05-02 22:02:45', '/chants', '83.202.209.82'),
(2446, '2026-05-02 22:19:51', '/photos-de-match/', '40.77.167.14'),
(2447, '2026-05-02 22:52:42', '/billetterie/3', '92.184.144.16'),
(2448, '2026-05-02 23:33:07', '/', '93.158.90.74'),
(2449, '2026-05-02 23:49:50', '/', '104.28.42.24'),
(2450, '2026-05-03 00:31:40', '/', '80.239.186.178'),
(2451, '2026-05-03 00:31:49', '/login', '80.239.186.177'),
(2452, '2026-05-03 00:31:54', '/', '80.239.186.177'),
(2453, '2026-05-03 01:27:09', '/table-de-vente/', '172.225.189.229'),
(2454, '2026-05-03 01:29:36', '/', '157.230.41.222'),
(2455, '2026-05-03 01:29:37', '/', '157.230.41.222'),
(2456, '2026-05-03 01:29:39', '/', '157.230.41.222'),
(2457, '2026-05-03 01:38:50', '/billetterie/', '52.167.144.137'),
(2458, '2026-05-03 01:43:33', '/', '205.169.39.21'),
(2459, '2026-05-03 02:29:37', '/', '90.60.10.226'),
(2460, '2026-05-03 02:59:08', '/', '172.225.116.186'),
(2461, '2026-05-03 02:59:13', '/', '90.60.10.226'),
(2462, '2026-05-03 02:59:25', '/groupe/', '90.60.10.226'),
(2463, '2026-05-03 03:00:36', '/actualites/ultras-lions-fleury-2026-furies', '172.225.116.186'),
(2464, '2026-05-03 03:00:49', '/photos-de-match/3', '172.225.116.186'),
(2465, '2026-05-03 03:01:50', '/chants', '90.60.10.226'),
(2466, '2026-05-03 03:01:55', '/billetterie/', '90.60.10.226'),
(2467, '2026-05-03 03:01:59', '/', '90.60.10.226'),
(2468, '2026-05-03 04:42:32', '/', '34.122.147.229'),
(2469, '2026-05-03 04:47:33', '/', '34.122.147.229'),
(2470, '2026-05-03 05:25:51', '/chants', '52.167.144.156'),
(2471, '2026-05-03 05:29:54', '/', '205.169.39.22');
INSERT INTO `visit` (`id`, `visited_at`, `page`, `ip`) VALUES
(2472, '2026-05-03 08:13:51', '/medias', '207.46.13.54'),
(2473, '2026-05-03 08:19:51', '/table-de-vente/panier', '17.22.253.68'),
(2474, '2026-05-03 08:19:55', '/panier', '17.22.253.68'),
(2475, '2026-05-03 08:45:32', '/actualites/', '207.46.13.107'),
(2476, '2026-05-03 09:23:31', '/table-de-vente/2', '77.131.12.21'),
(2477, '2026-05-03 09:23:35', '/', '77.131.12.21'),
(2478, '2026-05-03 09:23:43', '/actualites/auto-matos-2', '77.131.12.21'),
(2479, '2026-05-03 09:23:45', '/table-de-vente/2', '77.131.12.21'),
(2480, '2026-05-03 09:24:21', '/table-de-vente/', '77.131.12.21'),
(2481, '2026-05-03 09:24:29', '/billetterie/', '77.131.12.21'),
(2482, '2026-05-03 11:43:31', '/', '34.38.212.192'),
(2483, '2026-05-03 11:43:31', '/', '34.38.212.192'),
(2484, '2026-05-03 12:01:02', '/', '88.161.222.224'),
(2485, '2026-05-03 12:01:10', '/', '88.161.222.224'),
(2486, '2026-05-03 12:01:35', '/', '88.161.222.224'),
(2487, '2026-05-03 12:01:54', '/', '88.161.222.224'),
(2488, '2026-05-03 12:02:42', '/', '88.161.222.224'),
(2489, '2026-05-03 12:37:52', '/invitation', '40.77.167.47'),
(2490, '2026-05-03 12:41:11', '/', '88.123.2.161'),
(2491, '2026-05-03 12:41:13', '/login', '88.123.2.161'),
(2492, '2026-05-03 12:41:16', '/', '88.123.2.161'),
(2493, '2026-05-03 12:41:18', '/profil/17', '88.123.2.161'),
(2494, '2026-05-03 12:41:46', '/actualites/', '88.123.2.161'),
(2495, '2026-05-03 12:41:48', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '88.123.2.161'),
(2496, '2026-05-03 12:41:58', '/photos-de-match/3', '40.77.167.155'),
(2497, '2026-05-03 12:59:06', '/', '98.83.158.92'),
(2498, '2026-05-03 12:59:06', '/', '98.83.158.92'),
(2499, '2026-05-03 12:59:07', '/', '98.83.158.92'),
(2500, '2026-05-03 13:18:35', '/photos-de-match/2', '40.77.167.77'),
(2501, '2026-05-03 14:01:55', '/', '34.45.134.175'),
(2502, '2026-05-03 14:04:10', '/', '34.173.2.215'),
(2503, '2026-05-03 14:17:41', '/chants', '40.77.167.243'),
(2504, '2026-05-03 14:49:01', '/', '81.65.94.87'),
(2505, '2026-05-03 14:54:13', '/', '81.65.94.87'),
(2506, '2026-05-03 14:54:29', '/', '13.212.249.9'),
(2507, '2026-05-03 14:54:31', '/', '13.212.249.9'),
(2508, '2026-05-03 15:07:11', '/', '13.212.174.4'),
(2509, '2026-05-03 15:07:14', '/', '13.212.174.4'),
(2510, '2026-05-03 15:12:30', '/login', '52.167.144.208'),
(2511, '2026-05-03 16:13:45', '/', '83.202.209.82'),
(2512, '2026-05-03 16:13:48', '/billetterie/', '83.202.209.82'),
(2513, '2026-05-03 16:13:54', '/table-de-vente/', '83.202.209.82'),
(2514, '2026-05-03 16:47:36', '/', '74.7.227.188'),
(2515, '2026-05-03 18:00:05', '/', '52.23.167.127'),
(2516, '2026-05-03 18:00:58', '/', '52.23.167.127'),
(2517, '2026-05-03 18:42:02', '/', '88.123.2.161'),
(2518, '2026-05-03 18:42:10', '/photos-de-match/2', '88.123.2.161'),
(2519, '2026-05-03 19:17:32', '/photos-de-match/1', '52.167.144.211'),
(2520, '2026-05-03 19:35:48', '/', '31.13.115.6'),
(2521, '2026-05-03 19:35:48', '/', '69.63.184.1'),
(2522, '2026-05-03 19:35:49', '/', '173.252.95.33'),
(2523, '2026-05-03 20:02:57', '/', '83.202.209.82'),
(2524, '2026-05-03 20:09:03', '/', '176.140.222.81'),
(2525, '2026-05-03 20:09:26', '/billetterie/', '83.202.209.82'),
(2526, '2026-05-03 20:18:36', '/groupe/', '40.77.167.20'),
(2527, '2026-05-03 20:19:18', '/', '185.225.42.72'),
(2528, '2026-05-03 21:36:35', '/', '83.202.209.82'),
(2529, '2026-05-03 22:09:08', '/', '23.27.145.107'),
(2530, '2026-05-03 22:09:32', '/', '23.27.145.226'),
(2531, '2026-05-03 22:42:31', '/', '167.99.69.254'),
(2532, '2026-05-03 22:42:32', '/', '167.99.69.254'),
(2533, '2026-05-03 22:42:33', '/', '167.99.69.254'),
(2534, '2026-05-03 23:20:10', '/medias', '17.241.227.64'),
(2535, '2026-05-03 23:38:25', '/table-de-vente/2', '52.167.144.228'),
(2536, '2026-05-03 23:48:16', '/', '83.202.209.82'),
(2537, '2026-05-03 23:48:22', '/login', '83.202.209.82'),
(2538, '2026-05-03 23:48:33', '/', '83.202.209.82'),
(2539, '2026-05-03 23:48:34', '/', '83.202.209.82'),
(2540, '2026-05-03 23:48:43', '/billetterie/', '83.202.209.82'),
(2541, '2026-05-03 23:51:01', '/billetterie/', '52.167.144.147'),
(2542, '2026-05-03 23:54:19', '/', '83.202.95.42'),
(2543, '2026-05-04 00:33:36', '/medias', '17.22.245.23'),
(2544, '2026-05-04 00:37:50', '/actualites/', '52.167.144.159'),
(2545, '2026-05-04 00:41:21', '/photos-de-match/', '52.167.144.183'),
(2546, '2026-05-04 00:44:43', '/actualites/', '52.167.144.195'),
(2547, '2026-05-04 00:46:12', '/forgot-password', '40.77.167.155'),
(2548, '2026-05-04 01:29:43', '/table-de-vente/', '52.167.144.204'),
(2549, '2026-05-04 01:35:36', '/photos-de-match/', '40.77.167.24'),
(2550, '2026-05-04 02:16:09', '/medias', '17.241.75.120'),
(2551, '2026-05-04 02:47:34', '/billetterie/3', '194.230.146.78'),
(2552, '2026-05-04 02:58:25', '/login', '52.167.144.150'),
(2553, '2026-05-04 03:34:23', '/billetterie/3', '92.184.107.60'),
(2554, '2026-05-04 03:34:27', '/', '92.184.107.60'),
(2555, '2026-05-04 04:06:21', '/chants', '40.77.167.79'),
(2556, '2026-05-04 04:33:36', '/', '184.94.240.88'),
(2557, '2026-05-04 04:46:06', '/', '54.152.193.242'),
(2558, '2026-05-04 04:46:07', '/', '54.152.193.242'),
(2559, '2026-05-04 05:00:40', '/', '57.141.20.68'),
(2560, '2026-05-04 05:00:58', '/photos-de-match/', '57.141.20.10'),
(2561, '2026-05-04 05:00:59', '/billetterie/', '57.141.20.33'),
(2562, '2026-05-04 05:01:01', '/actualites/ultras-lions-fleury-2026-furies', '57.141.20.17'),
(2563, '2026-05-04 05:01:03', '/photos-de-match/', '57.141.20.69'),
(2564, '2026-05-04 05:01:05', '/invitation', '57.141.20.8'),
(2565, '2026-05-04 05:01:09', '/login', '57.141.20.20'),
(2566, '2026-05-04 05:01:10', '/table-de-vente/', '57.141.20.39'),
(2567, '2026-05-04 05:01:12', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '57.141.20.6'),
(2568, '2026-05-04 05:01:14', '/chants', '57.141.20.37'),
(2569, '2026-05-04 05:01:17', '/groupe/', '57.141.20.33'),
(2570, '2026-05-04 05:01:17', '/medias/medias', '57.141.20.68'),
(2571, '2026-05-04 05:01:19', '/actualites/fc-fleury-91-le-club-de-l-essonne', '57.141.20.27'),
(2572, '2026-05-04 05:01:22', '/photos-de-match/3', '57.141.20.34'),
(2573, '2026-05-04 05:01:23', '/photos-de-match/2', '57.141.20.30'),
(2574, '2026-05-04 05:01:30', '/photos-de-match/1', '57.141.20.65'),
(2575, '2026-05-04 05:01:31', '/forgot-password', '57.141.20.28'),
(2576, '2026-05-04 05:01:32', '/actualites/', '57.141.20.54'),
(2577, '2026-05-04 05:01:34', '/table-de-vente/2', '57.141.20.38'),
(2578, '2026-05-04 05:01:36', '/medias', '57.141.20.44'),
(2579, '2026-05-04 05:01:53', '/table-de-vente/panier', '57.141.20.32'),
(2580, '2026-05-04 05:02:08', '/panier', '57.141.20.44'),
(2581, '2026-05-04 05:04:04', '/table-de-vente/panier', '17.22.237.184'),
(2582, '2026-05-04 05:04:05', '/panier', '17.22.237.184'),
(2583, '2026-05-04 06:57:47', '/medias/medias', '52.167.144.213'),
(2584, '2026-05-04 07:23:15', '/profil/16', '66.249.93.12'),
(2585, '2026-05-04 07:23:16', '/profil/16', '66.249.93.14'),
(2586, '2026-05-04 07:23:16', '/profil/16', '66.249.93.13'),
(2587, '2026-05-04 07:28:40', '/invitation', '40.77.167.24'),
(2588, '2026-05-04 07:39:40', '/chants', '40.77.167.54'),
(2589, '2026-05-04 07:41:01', '/billetterie/3', '40.77.167.155'),
(2590, '2026-05-04 08:31:17', '/groupe/', '40.77.167.54'),
(2591, '2026-05-04 09:00:12', '/', '167.172.90.59'),
(2592, '2026-05-04 09:00:13', '/', '167.172.90.59'),
(2593, '2026-05-04 09:00:14', '/', '167.172.90.59'),
(2594, '2026-05-04 09:42:26', '/login', '163.116.174.177'),
(2595, '2026-05-04 09:42:30', '/', '163.116.174.177'),
(2596, '2026-05-04 10:07:23', '/', '88.124.36.60'),
(2597, '2026-05-04 10:07:46', '/actualites/', '88.124.36.60'),
(2598, '2026-05-04 10:08:10', '/actualites/ultras-lions-fleury-2026-furies', '88.124.36.60'),
(2599, '2026-05-04 10:08:15', '/actualites/ultras-lions-fleury-2026-furies', '88.124.36.60'),
(2600, '2026-05-04 11:28:31', '/', '51.75.141.254'),
(2601, '2026-05-04 11:35:13', '/', '163.116.174.72'),
(2602, '2026-05-04 11:35:20', '/actualites/auto-matos-2', '163.116.174.72'),
(2603, '2026-05-04 11:35:24', '/table-de-vente/2', '163.116.174.72'),
(2604, '2026-05-04 11:35:33', '/groupe/', '163.116.174.72'),
(2605, '2026-05-04 11:35:35', '/billetterie/', '163.116.174.72'),
(2606, '2026-05-04 11:35:37', '/actualites/', '163.116.174.72'),
(2607, '2026-05-04 11:35:43', '/medias', '163.116.174.72'),
(2608, '2026-05-04 11:35:48', '/medias/medias', '163.116.174.72'),
(2609, '2026-05-04 11:35:51', '/chants', '163.116.174.72'),
(2610, '2026-05-04 11:35:56', '/actualites/', '163.116.174.72'),
(2611, '2026-05-04 11:35:58', '/profil/3', '163.116.174.72'),
(2612, '2026-05-04 11:41:58', '/', '35.229.174.181'),
(2613, '2026-05-04 12:28:27', '/', '83.202.209.82'),
(2614, '2026-05-04 12:28:27', '/billetterie/', '83.202.209.82'),
(2615, '2026-05-04 12:40:17', '/', '185.253.162.23'),
(2616, '2026-05-04 12:40:18', '/', '185.253.162.23'),
(2617, '2026-05-04 12:46:37', '/actualites/ultras-lions-fleury-2026-furies', '17.246.15.29'),
(2618, '2026-05-04 12:52:53', '/', '34.236.151.197'),
(2619, '2026-05-04 12:52:53', '/', '34.236.151.197'),
(2620, '2026-05-04 12:52:53', '/', '34.236.151.197'),
(2621, '2026-05-04 13:13:29', '/forgot-password', '17.241.219.101'),
(2622, '2026-05-04 13:43:16', '/medias', '17.241.227.189'),
(2623, '2026-05-04 13:50:03', '/', '149.57.180.97'),
(2624, '2026-05-04 15:44:13', '/', '91.166.33.137'),
(2625, '2026-05-04 15:44:15', '/', '43.206.208.110'),
(2626, '2026-05-04 15:44:26', '/billetterie/', '91.166.33.137'),
(2627, '2026-05-04 15:44:33', '/table-de-vente/', '91.166.33.137'),
(2628, '2026-05-04 15:44:55', '/chants', '91.166.33.137'),
(2629, '2026-05-04 15:47:54', '/billetterie/', '91.166.33.137'),
(2630, '2026-05-04 15:47:55', '/groupe/', '91.166.33.137'),
(2631, '2026-05-04 15:48:54', '/photos-de-match/', '91.166.33.137'),
(2632, '2026-05-04 15:49:01', '/medias', '91.166.33.137'),
(2633, '2026-05-04 15:49:15', '/actualites/', '91.166.33.137'),
(2634, '2026-05-04 15:49:30', '/actualites/auto-matos-2', '91.166.33.137'),
(2635, '2026-05-04 15:49:55', '/table-de-vente/2', '91.166.33.137'),
(2636, '2026-05-04 15:50:16', '/table-de-vente/panier/ajouter/2', '91.166.33.137'),
(2637, '2026-05-04 15:50:16', '/panier', '91.166.33.137'),
(2638, '2026-05-04 15:50:41', '/panier/checkout-cash', '91.166.33.137'),
(2639, '2026-05-04 15:50:41', '/panier', '91.166.33.137'),
(2640, '2026-05-04 15:51:05', '/panier/checkout', '91.166.33.137'),
(2641, '2026-05-04 15:51:16', '/actualites/auto-matos-2', '91.166.33.137'),
(2642, '2026-05-04 15:51:21', '/table-de-vente/2', '91.166.33.137'),
(2643, '2026-05-04 16:37:21', '/forgot-password', '163.116.174.72'),
(2644, '2026-05-04 16:43:22', '/', '163.116.174.72'),
(2645, '2026-05-04 16:43:29', '/billetterie/', '163.116.174.72'),
(2646, '2026-05-04 16:53:26', '/table-de-vente/', '52.167.144.159'),
(2647, '2026-05-04 17:03:22', '/', '34.248.215.193'),
(2648, '2026-05-04 17:03:23', '/', '3.249.133.148'),
(2649, '2026-05-04 17:03:23', '/', '34.244.177.156'),
(2650, '2026-05-04 17:03:30', '/', '172.225.116.184'),
(2651, '2026-05-04 17:03:53', '/', '172.226.29.5'),
(2652, '2026-05-04 17:04:27', '/actualites/ultras-lions-fleury-2026-furies', '172.226.148.47'),
(2653, '2026-05-04 17:05:05', '/actualites/ultras-lions-fleury-2026-furies', '172.226.148.47'),
(2654, '2026-05-04 17:06:37', '/chants', '172.226.148.47'),
(2655, '2026-05-04 17:32:40', '/actualites/fc-fleury-91-le-club-de-l-essonne', '17.246.15.226'),
(2656, '2026-05-04 17:42:03', '/', '95.164.156.224'),
(2657, '2026-05-04 17:51:33', '/', '88.124.36.60'),
(2658, '2026-05-04 17:51:40', '/actualites/auto-matos-2', '88.124.36.60'),
(2659, '2026-05-04 17:51:46', '/table-de-vente/2', '88.124.36.60'),
(2660, '2026-05-04 17:53:06', '/', '34.10.45.88'),
(2661, '2026-05-04 18:08:49', '/', '74.7.242.57'),
(2662, '2026-05-04 18:08:52', '/photos-de-match/3', '74.7.242.57'),
(2663, '2026-05-04 18:10:55', '/invitation', '74.7.242.57'),
(2664, '2026-05-04 18:11:59', '/groupe/', '74.7.242.57'),
(2665, '2026-05-04 18:13:00', '/billetterie/', '74.7.242.57'),
(2666, '2026-05-04 18:15:03', '/photos-de-match/', '74.7.242.57'),
(2667, '2026-05-04 18:15:43', '/', '34.60.6.244'),
(2668, '2026-05-04 18:16:08', '/table-de-vente/', '74.7.242.57'),
(2669, '2026-05-04 18:17:10', '/photos-de-match/', '74.7.242.57'),
(2670, '2026-05-04 18:18:15', '/actualites/ultras-lions-fleury-2026-furies', '74.7.242.57'),
(2671, '2026-05-04 18:19:16', '/actualites/notre-passion-ne-se-dissout-pas-edition-2026', '74.7.242.57'),
(2672, '2026-05-04 18:21:23', '/medias/medias', '74.7.242.57'),
(2673, '2026-05-04 18:22:29', '/photos-de-match/2', '74.7.242.57'),
(2674, '2026-05-04 18:23:24', '/actualites/fc-fleury-91-le-club-de-l-essonne', '74.7.242.57'),
(2675, '2026-05-04 18:25:00', '/chants', '74.7.242.57'),
(2676, '2026-05-04 18:25:40', '/actualites/', '74.7.242.57'),
(2677, '2026-05-04 18:53:58', '/', '85.171.31.155'),
(2678, '2026-05-04 19:18:15', '/chants', '185.98.171.236'),
(2679, '2026-05-04 20:01:43', '/', '3.139.242.79'),
(2680, '2026-05-04 20:47:01', '/', '98.93.106.89'),
(2681, '2026-05-04 20:47:02', '/', '54.224.34.137'),
(2682, '2026-05-04 21:03:19', '/billetterie/3', '52.167.144.219'),
(2683, '2026-05-04 21:20:12', '/chants', '185.98.171.236'),
(2684, '2026-05-04 21:20:13', '/chants', '185.98.171.236'),
(2685, '2026-05-04 21:25:07', '/', '88.161.222.224'),
(2686, '2026-05-04 21:25:13', '/', '88.161.222.224'),
(2687, '2026-05-04 21:25:16', '/billetterie/', '88.161.222.224'),
(2688, '2026-05-04 21:25:21', '/billetterie/', '88.161.222.224'),
(2689, '2026-05-04 21:25:26', '/table-de-vente/', '88.161.222.224'),
(2690, '2026-05-04 21:25:54', '/', '88.161.222.224'),
(2691, '2026-05-04 21:25:57', '/chants', '88.161.222.224'),
(2692, '2026-05-04 21:26:12', '/chants', '34.247.66.89'),
(2693, '2026-05-04 21:26:29', '/chants', '176.147.121.33'),
(2694, '2026-05-04 21:27:47', '/chants', '176.147.121.33'),
(2695, '2026-05-04 21:49:04', '/', '123.58.215.102'),
(2696, '2026-05-04 21:49:15', '/', '45.194.70.253'),
(2697, '2026-05-04 21:57:41', '/chants', '185.98.171.236'),
(2698, '2026-05-04 22:40:33', '/chants', '176.147.121.33'),
(2699, '2026-05-04 22:48:41', '/billetterie/3', '52.167.144.208'),
(2700, '2026-05-04 23:14:52', '/chants', '3.249.48.163'),
(2701, '2026-05-04 23:22:47', '/', '88.161.222.224'),
(2702, '2026-05-04 23:24:13', '/', '88.161.222.224'),
(2703, '2026-05-04 23:24:46', '/', '31.220.75.237'),
(2704, '2026-05-04 23:40:38', '/', '83.202.95.42'),
(2705, '2026-05-04 23:53:09', '/', '167.99.69.254'),
(2706, '2026-05-04 23:53:10', '/', '167.99.69.254'),
(2707, '2026-05-04 23:53:12', '/', '167.99.69.254'),
(2708, '2026-05-05 00:32:42', '/', '88.160.142.5'),
(2709, '2026-05-05 00:32:54', '/login', '88.160.142.5'),
(2710, '2026-05-05 00:33:00', '/', '88.160.142.5'),
(2711, '2026-05-05 00:33:01', '/', '88.160.142.5'),
(2712, '2026-05-05 02:10:20', '/', '101.36.97.172'),
(2713, '2026-05-05 02:10:28', '/', '101.36.97.172'),
(2714, '2026-05-05 02:11:24', '/', '118.193.43.141'),
(2715, '2026-05-05 02:11:28', '/', '118.193.44.112'),
(2716, '2026-05-05 03:22:03', '/invitation', '40.77.167.2'),
(2717, '2026-05-05 04:38:21', '/', '67.205.134.75'),
(2718, '2026-05-05 04:38:22', '/', '67.205.134.75'),
(2719, '2026-05-05 04:43:50', '/groupe/', '52.167.144.220'),
(2720, '2026-05-05 05:05:50', '/', '167.172.90.59'),
(2721, '2026-05-05 05:05:51', '/', '167.172.90.59'),
(2722, '2026-05-05 05:05:52', '/', '167.172.90.59'),
(2723, '2026-05-05 05:42:23', '/', '34.123.170.104'),
(2724, '2026-05-05 05:42:42', '/', '205.169.39.43'),
(2725, '2026-05-05 06:04:23', '/', '157.230.41.222'),
(2726, '2026-05-05 06:04:24', '/', '157.230.41.222'),
(2727, '2026-05-05 06:04:26', '/', '157.230.41.222'),
(2728, '2026-05-05 06:29:53', '/', '8.213.196.214'),
(2729, '2026-05-05 06:57:33', '/', '134.122.8.223'),
(2730, '2026-05-05 06:57:34', '/', '134.122.8.223'),
(2731, '2026-05-05 07:03:04', '/chants', '108.130.77.205'),
(2732, '2026-05-05 07:03:04', '/chants', '34.242.108.108'),
(2733, '2026-05-05 09:04:45', '/photos-de-match/', '52.167.144.201'),
(2734, '2026-05-05 09:38:14', '/', '54.152.193.242'),
(2735, '2026-05-05 09:38:14', '/', '54.152.193.242'),
(2736, '2026-05-05 09:43:41', '/billetterie/3', '88.184.40.219'),
(2737, '2026-05-05 09:48:52', '/forgot-password', '52.167.144.230'),
(2738, '2026-05-05 10:06:14', '/', '37.167.163.46'),
(2739, '2026-05-05 10:06:28', '/billetterie/', '37.167.163.46'),
(2740, '2026-05-05 10:59:53', '/', '194.230.147.240'),
(2741, '2026-05-05 10:59:59', '/chants', '194.230.147.240'),
(2742, '2026-05-05 11:00:36', '/chants', '57.141.10.10'),
(2743, '2026-05-05 11:01:39', '/photos-de-match/3', '52.167.144.184'),
(2744, '2026-05-05 11:09:17', '/chants', '176.140.205.164'),
(2745, '2026-05-05 11:13:24', '/chants', '34.245.238.95'),
(2746, '2026-05-05 11:18:48', '/', '3.214.234.248'),
(2747, '2026-05-05 11:19:05', '/chants', '77.204.105.170'),
(2748, '2026-05-05 11:19:51', '/chants', '77.204.105.170'),
(2749, '2026-05-05 11:26:27', '/', '88.151.33.115'),
(2750, '2026-05-05 11:26:27', '/', '88.151.33.115'),
(2751, '2026-05-05 11:26:30', '/', '88.151.33.115'),
(2752, '2026-05-05 11:32:09', '/table-de-vente/2', '40.77.167.49'),
(2753, '2026-05-05 12:07:28', '/', '137.184.130.196'),
(2754, '2026-05-05 12:07:29', '/', '137.184.130.196'),
(2755, '2026-05-05 12:20:57', '/billetterie/', '52.167.144.23'),
(2756, '2026-05-05 12:34:00', '/', '52.203.255.224'),
(2757, '2026-05-05 12:34:00', '/', '52.203.255.224'),
(2758, '2026-05-05 12:34:00', '/', '52.203.255.224'),
(2759, '2026-05-05 12:55:54', '/chants', '176.140.205.164'),
(2760, '2026-05-05 12:56:09', '/chants', '176.140.205.164'),
(2761, '2026-05-05 13:00:34', '/', '92.184.144.192'),
(2762, '2026-05-05 13:00:38', '/actualites/auto-matos-2', '92.184.144.192'),
(2763, '2026-05-05 13:23:37', '/', '149.57.180.178'),
(2764, '2026-05-05 13:27:00', '/chants', '77.204.104.210'),
(2765, '2026-05-05 13:27:23', '/chants', '176.191.73.211'),
(2766, '2026-05-05 13:27:56', '/billetterie/', '77.204.104.210'),
(2767, '2026-05-05 13:54:11', '/', '92.222.108.114'),
(2768, '2026-05-05 13:55:42', '/login', '40.77.167.36'),
(2769, '2026-05-05 14:12:51', '/', '88.124.36.60'),
(2770, '2026-05-05 14:13:41', '/actualites/auto-matos-2', '88.124.36.60'),
(2771, '2026-05-05 14:13:47', '/table-de-vente/2', '88.124.36.60'),
(2772, '2026-05-05 14:15:00', '/', '138.197.172.231'),
(2773, '2026-05-05 14:15:01', '/', '138.197.172.231'),
(2774, '2026-05-05 14:31:09', '/table-de-vente/', '51.75.236.134'),
(2775, '2026-05-05 15:12:59', '/chants', '54.37.118.95'),
(2776, '2026-05-05 15:29:43', '/', '54.152.28.140'),
(2777, '2026-05-05 15:36:24', '/', '93.158.90.74'),
(2778, '2026-05-05 15:48:30', '/', '54.154.195.8'),
(2779, '2026-05-05 16:32:22', '/medias/medias', '52.167.144.150'),
(2780, '2026-05-05 17:10:17', '/', '17.241.219.195'),
(2781, '2026-05-05 17:23:38', '/actualites/', '51.75.236.128'),
(2782, '2026-05-05 17:48:21', '/', '192.121.135.39'),
(2783, '2026-05-05 18:57:19', '/', '176.65.139.168'),
(2784, '2026-05-05 19:25:44', '/chants', '176.191.73.106'),
(2785, '2026-05-05 19:25:47', '/chants', '66.249.93.13'),
(2786, '2026-05-05 19:25:47', '/chants', '64.233.172.46'),
(2787, '2026-05-05 19:25:48', '/chants', '74.125.210.70'),
(2788, '2026-05-05 19:31:05', '/', '90.60.10.226'),
(2789, '2026-05-05 19:31:15', '/', '90.60.10.226'),
(2790, '2026-05-05 19:32:47', '/forgot-password', '52.167.144.187'),
(2791, '2026-05-05 19:35:20', '/medias/medias', '176.31.139.30'),
(2792, '2026-05-05 20:25:09', '/photos-de-match/2', '94.23.188.192'),
(2793, '2026-05-05 20:34:41', '/photos-de-match/1', '37.59.204.133'),
(2794, '2026-05-05 20:49:00', '/groupe/', '92.222.104.214'),
(2795, '2026-05-05 21:11:56', '/', '91.166.33.137'),
(2796, '2026-05-05 21:11:59', '/', '43.206.208.110'),
(2797, '2026-05-05 21:12:06', '/actualites/auto-matos-2', '91.166.33.137'),
(2798, '2026-05-05 21:12:17', '/table-de-vente/2', '91.166.33.137'),
(2799, '2026-05-05 21:28:53', '/', '3.214.234.248'),
(2800, '2026-05-05 21:28:54', '/', '3.214.234.248'),
(2801, '2026-05-05 22:45:45', '/', '83.202.95.42'),
(2802, '2026-05-05 22:45:48', '/login', '83.202.95.42'),
(2803, '2026-05-05 22:45:51', '/', '83.202.95.42'),
(2804, '2026-05-05 23:05:41', '/table-de-vente/2', '40.77.167.116');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `billetweb_lead`
--
ALTER TABLE `billetweb_lead`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_BILLETWEB_LEAD_TICKET` (`ticket_id`),
  ADD KEY `IDX_BILLETWEB_LEAD_USER` (`user_id`);

--
-- Index pour la table `cartage_qr_token`
--
ALTER TABLE `cartage_qr_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_CARTAGE_QR_TOKEN_TOKEN` (`token`);

--
-- Index pour la table `cartage_registration`
--
ALTER TABLE `cartage_registration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CARTAGE_REGISTRATION_QR_TOKEN` (`qr_token_id`),
  ADD KEY `IDX_CARTAGE_REGISTRATION_REFERENCE` (`checkout_reference`),
  ADD KEY `IDX_CARTAGE_REGISTRATION_STATUS` (`status`);

--
-- Index pour la table `chant`
--
ALTER TABLE `chant`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_EVENT_CATEGORY_ID` (`category_id`),
  ADD KEY `IDX_3BAE0AA73DA5256D` (`image_id`);

--
-- Index pour la table `event_category`
--
ALTER TABLE `event_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_EVENT_CATEGORY_SLUG` (`slug`);

--
-- Index pour la table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `group_page`
--
ALTER TABLE `group_page`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3D50ED50F98F144A` (`logo_id`),
  ADD KEY `IDX_3D50ED50684EC833` (`banner_id`);

--
-- Index pour la table `invite_code`
--
ALTER TABLE `invite_code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_6F21F11277153098` (`code`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6A2CA10C4E7AF8F` (`gallery_id`),
  ADD KEY `IDX_6A2CA10C8A4637E8` (`group_page_id`),
  ADD KEY `IDX_6A2CA10C56A273CC` (`merch_id`),
  ADD KEY `IDX_6A2CA10C71F7E88B` (`event_id`),
  ADD KEY `IDX_MEDIA_POST_ID` (`post_id`);

--
-- Index pour la table `merch`
--
ALTER TABLE `merch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F1B42EE03DA5256D` (`image_id`),
  ADD KEY `IDX_F1B42EE012469DE2` (`category_id`);

--
-- Index pour la table `merch_category`
--
ALTER TABLE `merch_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_merch_category_slug` (`slug`);

--
-- Index pour la table `merch_order`
--
ALTER TABLE `merch_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1E733F9EA76ED395` (`user_id`),
  ADD KEY `IDX_1E733F9E67B3B43D` (`merch_id`);

--
-- Index pour la table `merch_stock`
--
ALTER TABLE `merch_stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_merch_stock_merch_size` (`merch_id`,`size`),
  ADD KEY `IDX_8FADF0318A86BD8` (`merch_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_140AB620989D9B62` (`slug`),
  ADD KEY `IDX_140AB6203DA5256D` (`image_id`);

--
-- Index pour la table `payment_checkout`
--
ALTER TABLE `payment_checkout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_73A5E4B6A76ED395` (`user_id`);

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_5A8A6C8D989D9B62` (`slug`),
  ADD KEY `IDX_5A8A6C8D3DA5256D` (`image_id`),
  ADD KEY `IDX_POST_EVENT_CATEGORY_ID` (`category_id`);

--
-- Index pour la table `site_config`
--
ALTER TABLE `site_config`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_97A0ADA43DA5256D` (`image_id`),
  ADD KEY `IDX_97A0ADA412469DE2` (`category_id`);

--
-- Index pour la table `ticket_category`
--
ALTER TABLE `ticket_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_ticket_category_slug` (`slug`);

--
-- Index pour la table `ticket_order`
--
ALTER TABLE `ticket_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E665A4F6A76ED395` (`user_id`),
  ADD KEY `IDX_E665A4F6700047D2` (`ticket_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`),
  ADD KEY `IDX_RESET_PASSWORD_TOKEN` (`reset_password_token`);

--
-- Index pour la table `visit`
--
ALTER TABLE `visit`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `billetweb_lead`
--
ALTER TABLE `billetweb_lead`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `cartage_qr_token`
--
ALTER TABLE `cartage_qr_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `cartage_registration`
--
ALTER TABLE `cartage_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `chant`
--
ALTER TABLE `chant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `event_category`
--
ALTER TABLE `event_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `group_page`
--
ALTER TABLE `group_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `invite_code`
--
ALTER TABLE `invite_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `merch`
--
ALTER TABLE `merch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `merch_category`
--
ALTER TABLE `merch_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `merch_order`
--
ALTER TABLE `merch_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `merch_stock`
--
ALTER TABLE `merch_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `page`
--
ALTER TABLE `page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `payment_checkout`
--
ALTER TABLE `payment_checkout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `site_config`
--
ALTER TABLE `site_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `ticket_category`
--
ALTER TABLE `ticket_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `ticket_order`
--
ALTER TABLE `ticket_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `visit`
--
ALTER TABLE `visit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2805;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `billetweb_lead`
--
ALTER TABLE `billetweb_lead`
  ADD CONSTRAINT `FK_BILLETWEB_LEAD_TICKET` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `cartage_registration`
--
ALTER TABLE `cartage_registration`
  ADD CONSTRAINT `FK_CARTAGE_REGISTRATION_QR_TOKEN` FOREIGN KEY (`qr_token_id`) REFERENCES `cartage_qr_token` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `FK_3BAE0AA73DA5256D` FOREIGN KEY (`image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_EVENT_CATEGORY_ID` FOREIGN KEY (`category_id`) REFERENCES `event_category` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `group_page`
--
ALTER TABLE `group_page`
  ADD CONSTRAINT `FK_3D50ED50684EC833` FOREIGN KEY (`banner_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_3D50ED50F98F144A` FOREIGN KEY (`logo_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `FK_6A2CA10C4E7AF8F` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_6A2CA10C56A273CC` FOREIGN KEY (`merch_id`) REFERENCES `merch` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_6A2CA10C71F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_6A2CA10C8A4637E8` FOREIGN KEY (`group_page_id`) REFERENCES `group_page` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_MEDIA_POST_ID` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `merch`
--
ALTER TABLE `merch`
  ADD CONSTRAINT `FK_F1B42EE012469DE2` FOREIGN KEY (`category_id`) REFERENCES `merch_category` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_F1B42EE03DA5256D` FOREIGN KEY (`image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `merch_order`
--
ALTER TABLE `merch_order`
  ADD CONSTRAINT `FK_1E733F9E67B3B43D` FOREIGN KEY (`merch_id`) REFERENCES `merch` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_1E733F9EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `merch_stock`
--
ALTER TABLE `merch_stock`
  ADD CONSTRAINT `FK_8FADF0318A86BD8` FOREIGN KEY (`merch_id`) REFERENCES `merch` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `page`
--
ALTER TABLE `page`
  ADD CONSTRAINT `FK_140AB6203DA5256D` FOREIGN KEY (`image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `payment_checkout`
--
ALTER TABLE `payment_checkout`
  ADD CONSTRAINT `FK_73A5E4B6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8D3DA5256D` FOREIGN KEY (`image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_POST_EVENT_CATEGORY_ID` FOREIGN KEY (`category_id`) REFERENCES `event_category` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `FK_97A0ADA412469DE2` FOREIGN KEY (`category_id`) REFERENCES `ticket_category` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_97A0ADA43DA5256D` FOREIGN KEY (`image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `ticket_order`
--
ALTER TABLE `ticket_order`
  ADD CONSTRAINT `FK_E665A4F6700047D2` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E665A4F6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
