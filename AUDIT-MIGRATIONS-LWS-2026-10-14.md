# Audit migrations Doctrine — LWS — 14 octobre 2026

## Portée
Inventaire des 26 fichiers PHP dans backend/migrations sur main, de 20250104192616 à 20261014163000. Inspection statique du code GitHub ; **aucun accès à la base LWS, aucune exécution SQL ou test d'intégration**. Ne pas déclarer production validée.

## Cause racine
La table doctrine_migration_versions ne reflète pas toujours le schéma réel (colonnes et tables déjà créées). MariaDB peut conserver certains ALTER TABLE lorsque Doctrine considère la migration en échec. Les migrations historiques se chevauchent et plusieurs opérations CREATE/ADD/RENAME sont non idempotentes.

## Classification exhaustive (26 migrations)
- 20250104192616 : création `user` et messenger_messages, à réconcilier avec historique réel.
- 20251201212207 : migration initiale volumineuse créant de nombreuses tables ; chevauchements majeurs avec 20251202, 20251206 et 20260213. À baseliner seulement après vérification de toutes les tables/index/FK.
- 20251202215857 : création répétée site_config/visit + renommage index. Risque élevé.
- 20251202220409 : doublon fonctionnel de 20251202215857. Risque élevé.
- 20251206013450 : création répétée users/invite_code/site_config ; risque élevé.
- 20251215120000 : création catégorie merch, colonne+FK+index, insertion initiale ; risque de doublon et de conflit de données.
- 20251215124500 : création merch_stock + FK ; risque de doublon.
- 20251216120000 : création ticket/ticket_category, FK et données de base ; risque de doublon.
- 20260213104945 : nouvelle migration initiale auto-générée recréant de nombreuses tables existantes ; risque très élevé.
- 20260311120000 : colonne, index et FK media.merch_id ; gardes ajoutées sur cette branche.
- 20260311123000 : colonne, index et FK media.event_id ; gardes ajoutées.
- 20260316120000 : cinq colonnes textes de group_page ; gardes ajoutées.
- 20260408120000 : gallery.description ; garde ajoutée.
- 20260408123000 : insertion idempotente dans group_page par NOT EXISTS ; contrôler les contenus.
- 20260411120000 : création chant ; garde ajoutée.
- 20260424120000 : event_category, plusieurs colonnes/index/FK et inserts ; nécessite migration dédiée vérifiant contraintes et données. Risque élevé.
- 20260424123000 : ticket.billetweb_url (fonctionnalité historique) ; garde ajoutée. Retrait du schéma nécessiterait une migration distincte, pas une suppression silencieuse.
- 20260424130000 : merch_order + FK ; risque de doublon.
- 20260424131000 : ticket_order et payment_checkout + FK ; risque de doublon.
- 20260424140000 : reset password colonnes+index ; corrigé dans main via PR #15 (à vérifier fusion), ici déjà pris en compte par branche base.
- 20260425090000 : insertion de chants ; vérifier la clause d'évitement des doublons avant rejouement.
- 20260505230000 : colonnes internes cartage + pages légales conditionnelles ; gardes ajoutées, correction du blocage actuel.
- 20260506023500 : site_config.google_analytics_id ; garde ajoutée.
- 20260520190000 : création user_account_link + FK ; risque de doublon.
- 20261013153000 : création des quatre tables buvette ; la branche fonctionnelle comporte un correctif partiel, vérifier cohérence finale avant fusion.
- 20261014163000 : champs de fiabilité/envoi + index sur refreshment_report ; gardes ajoutées.

## Déploiement sûr
1. Faire un export SQL intégral de la base LWS, en conserver la copie hors du répertoire web.
2. Sur une copie de cette base, récupérer le code corrigé et inspecter `doctrine:migrations:status` et `doctrine:migrations:list`.
3. Comparer les migrations encore marquées 'not migrated' à leur effet attendu : tables, colonnes, types, index, clés étrangères et données de référence. Ne marquer comme exécutées que les versions dont tous les effets sont effectivement présents.
4. Si une migration est partiellement appliquée, corriger les éléments manquants et valider la compatibilité, puis seulement la marquer comme exécutée ou la rejouer lorsqu'elle est sûre.
5. Exécuter les migrations sur copie, puis `doctrine:schema:validate` et tests fonctionnels. Corriger les migrations historiques de création qui restent réellement nécessaires, sans écraser de données.
6. Ensuite seulement appliquer à LWS, avec fenêtre de maintenance et plan de restauration.

## Commandes de diagnostic non destructives
```bash
APP_ENV=prod php bin/console doctrine:migrations:status
APP_ENV=prod php bin/console doctrine:migrations:list
APP_ENV=prod php bin/console doctrine:query:sql "SELECT version, executed_at FROM doctrine_migration_versions ORDER BY version"
```
La troisième commande suppose que la table de versions existe et porte ces colonnes ; le nom de table est configurable.

## Limite importante
Ces gardes ne garantissent pas la compatibilité des types de colonnes et des contraintes déjà présentes, ni l'unicité des envois externes. Ne jamais forcer toutes les migrations à 'executed', ni désactiver globalement les erreurs SQL. On doit comparer la base réelle à un schéma cible sur staging.
