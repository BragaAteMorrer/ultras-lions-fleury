# Cron LWS — rapports billets / buvette

## Planification
Dans LWS Panel > Tâches cron, créer une tâche **toutes les minutes** (`* * * * *`) exécutant PHP CLI depuis le dossier `backend` de l'application déployée :

```sh
cd /CHEMIN/ABSOLU/DU/SITE/backend && /CHEMIN/VERIFIE/VERS/php bin/console app:buvette:send-reports --env=prod --no-debug
```

**Important :** les chemins doivent être relevés sur LWS (`pwd` et `command -v php`). Ne pas copier les chemins d'exemple sans vérification. Selon l'offre LWS, la fréquence minimale peut être supérieure à une minute ; dans ce cas, un envoi exactement à H+1 minute n'est pas garanti.

## Contrôles
1. Sauvegarder la migration Doctrine et l'appliquer avant d'activer le cron.
2. Vérifier `MAILER_DSN` et `MAILER_FROM` dans la configuration privée du serveur.
3. Créer un rapport test avec une date de fermeture proche et une adresse de réception autorisée.
4. Exécuter la commande manuellement et vérifier la réception du mail ainsi que `sent_at`.
5. Simuler un échec SMTP : `sent_at` doit rester vide et l'exécution suivante doit réessayer.
6. Surveiller les logs et éviter plusieurs hôtes qui exécutent le même cron simultanément. Le verrou de fichier ne couvre qu'un hôte.

## Limites
- Les paiements SumUp initiés avant fermeture mais confirmés après peuvent arriver après le rapport. Une politique explicite de délai / finalisation des paiements est nécessaire avant de considérer les totaux définitifs.
- La fonctionnalité d'achat de boissons depuis la boutique publique n'est pas encore développée ; les commandes manuelles buvette sont distinctes.
- Les billets Billetweb ne sont pas synchronisés.
