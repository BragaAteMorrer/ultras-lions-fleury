# Envois de récapitulatifs de commande sur LWS

Cette évolution remplace les envois depuis la page de retour SumUp et depuis le webhook par une file persistante `checkout_email_delivery`. Le webhook écrit une ligne `pending` en même temps que la commande payée. Le worker envoie et confirme `sent`.

**Avant déploiement** : sauvegarder la base ; exécuter la migration `Version20261014205000` et vérifier les colonnes et la contrainte d'unicité. **Ne pas fusionner ou déployer sans activer un mécanisme d'exécution du worker**, sinon les nouveaux emails resteront en attente.

Dans le terminal LWS, après avoir vérifié la version PHP CLI et les chemins réels :

```sh
cd ~/htdocs/backend
APP_ENV=prod APP_DEBUG=0 php bin/console app:checkout-emails:send
```

Créer un cron indépendant pour cette commande, à la fréquence permise par l'offre LWS. Exemple générique (remplacer PHP et répertoire par les chemins réels) :

```sh
cd /CHEMIN/ABSOLU/htdocs/backend && /CHEMIN/PHP bin/console app:checkout-emails:send --env=prod --no-debug
```

Éviter les redirections de logs vers un dossier web. Tester avec une commande de recette dont le paiement SumUp est simulé ou sandbox.

## Incidents et idempotence

- `pending` : email pas encore envoyé, peut être traité par worker ;
- `sending` : prise en charge enregistrée. Si le worker s'est arrêté, vérification manuelle avant toute tentative, pour éviter le doublon ;
- `sent` : SMTP accepté et statut enregistré ;
- `uncertain` : une exception s'est produite, le résultat SMTP doit être rapproché manuellement.

Ne pas passer directement `sending` ou `uncertain` à `pending` sans vérifier les journaux du fournisseur SMTP et le destinataire. Les emails ne sont pas garantis délivrés à la boîte finale simplement parce que SMTP a accepté le message.

## Recette obligatoire

- Un webhook PAID répété ne crée qu'une seule ligne d'envoi ni plusieurs commandes.
- Une commande payée de billets, merchandising ou boissons met une ligne en attente.
- Visiter /commande/merci ne déclenche aucun second email.
- Le worker consomme une fois puis positionne sent et email_sent_at.
- Une coupure SMTP passe en uncertain sans boucle de renvoi automatique.
- Le cron s'exécute sur LWS et les journaux confirment les opérations.

La vérification effective du serveur LWS nécessite son accès administrateur ; elle ne peut pas être attestée depuis GitHub.
