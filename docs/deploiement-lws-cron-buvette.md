# Vérification de production LWS — billetterie et buvette

Ce document est un **protocole de recette**, pas une attestation de configuration du serveur. Ne pas publier les identifiants SMTP, les clés SumUp, le contenu de `.env.local`, ni les logs de commandes contenant des données personnelles.

## 1. Repérer le déploiement et la commande (terminal LWS)

```sh
cd ~/htdocs/backend || exit 1
pwd
php -v
php bin/console about --env=prod
php bin/console list --env=prod | grep -Ei 'buvette|report|messenger'
```

Si l'application est déployée dans un autre dossier, adapter le chemin. En cas d'erreur de dépendances, vérifier que `vendor/` est présent et que le PHP CLI LWS répond aux exigences de `backend/composer.json`. Ne pas lancer `composer update` sur le serveur de production pour « réparer » le cron.

## 2. Exécution manuelle du rapport

La commande attendue est `app:buvette:send-reports`. **Vérifier sa présence avant de configurer le cron** :

```sh
APP_ENV=prod APP_DEBUG=0 php bin/console app:buvette:send-reports --help
```

Sur un environnement de test avec un match et une clôture de commande artificielle, vérifier les cas suivants : avant clôture (pas de rapport), après clôture (rapport émis), deuxième exécution (pas de doublon), SMTP inaccessible puis rétabli (nouvelle tentative réussie), billet et boissons recensés dans un seul état cohérent. **Ne pas lancer manuellement en production après une vraie clôture sans vérifier l'idempotence** : risque de doublon d'email.

## 3. Cron LWS

Créer dans le LWS Panel une tâche exécutée chaque minute (* * * * *) **si le plan d'hébergement autorise cette fréquence**. Utiliser le chemin absolu réel du PHP CLI et celui de l'application ; ne jamais supposer que `php` pointe sur la version web :

```sh
/usr/bin/php /CHEMIN/ABSOLU/htdocs/backend/bin/console app:buvette:send-reports --env=prod --no-debug
```

Le chemin `/usr/bin/php` est **un exemple à remplacer** : le confirmer avec `command -v php`, `php -v` et la documentation LWS. Lancer depuis le répertoire de l'application si nécessaire, par exemple : `cd /CHEMIN/ABSOLU/htdocs/backend && /CHEMIN/PHP bin/console app:buvette:send-reports --env=prod --no-debug`.

Le cron doit consigner son exécution (horodatage, code de sortie, identifiant de match, état d'envoi **sans données personnelles**) dans un emplacement non public, avec rotation. Éviter de rediriger silencieusement toute la sortie vers `/dev/null`.

## 4. Vérification de la délivrabilité et de la reprise

- Vérifier que `MAILER_DSN` est réellement configuré dans l'environnement de production (ne pas afficher sa valeur ni la coller dans un ticket).
- Si Symfony Messenger est utilisé pour l'envoi différé, vérifier que le transport Doctrine est créé, que les messages sont effectivement consommés et qu'un worker ou une tâche planifiée distincte fonctionne. **Le cron de rapport seul ne garantit pas l'expédition des messages en file.**
- Vérifier les mécanismes existants de réessai, journalisation, idempotence et verrouillage concurrentiel. Ne pas présumer qu'ils existent.
- Pour une panne SMTP, conserver l'état « à réessayer » ; ne marquer « envoyé » qu'après confirmation du transport. Valider également l'absence de double email après réexécution.
- Vérifier les règles du pare-feu et SMTP sortant LWS ainsi que la délivrabilité SPF/DKIM/DMARC.

## 5. Conditions d'acceptation

1. La commande apparaît dans `bin/console list` en production.
2. Le cron est enregistré sur LWS et une exécution est visible dans les journaux à la fréquence réellement permise.
3. Aucun achat de billet ou de boisson n'est accepté **côté serveur** après la clôture ; test à la limite temporelle.
4. Le rapport contient les commandes payées et prises en compte jusqu'à la clôture, sans répétition.
5. Une panne SMTP ne fait pas perdre le rapport ; un nouvel essai finit par l'envoyer une seule fois.
6. Aucune donnée sensible ou personnelle n'est révélée dans un log web/public.
7. La configuration du worker Messenger est vérifiée si l'envoi est asynchrone.

**État : non vérifié sur le serveur LWS** tant que les points ci-dessus n'ont pas été observés sur l'hébergement réel.
