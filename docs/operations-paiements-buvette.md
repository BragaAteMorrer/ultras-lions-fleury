# Régularisation des commandes SumUp

## Paiements après H-24 et stocks insuffisants

Le paiement peut réussir après l'heure de clôture d'un match, ou alors qu'un produit vient de devenir indisponible. Le webhook classe alors la commande en `paid_stock_review`, conserve `paidAt`, et **ne doit pas produire une nouvelle précommande silencieusement**.

Sur LWS, dans l'application Symfony, exécuter :

```sh
APP_ENV=prod APP_DEBUG=0 php bin/console app:payments:pending-review
```

Pour chaque ligne, rapprocher l'identifiant du paiement et la référence checkout de la transaction sur le tableau de bord SumUp. Vérifier si la transaction a été remboursée, si elle a fait l'objet d'une régularisation, si elle appartient à une commande déjà traitée et si un rapport H-24 a déjà été envoyé.

Après remboursement réellement effectué dans SumUp (ou après exécution et preuve de la livraison), enregistrer sa référence :

```sh
APP_ENV=prod APP_DEBUG=0 php bin/console app:payments:resolve-review 123 refunded REFERENCE_SUMUP
# ou, seulement si la commande a réellement été honorée :
APP_ENV=prod APP_DEBUG=0 php bin/console app:payments:resolve-review 123 fulfilled PREUVE_LIVRAISON
```

Ces actions documentent la régularisation sans modifier le statut de paiement ni contacter l'API SumUp. Le suivi de remboursements partiels et la vérification automatique des preuves ne sont pas disponibles.

**Aucun remboursement automatique n'est exécuté par cette commande.** Réaliser les remboursements depuis la procédure SumUp autorisée, conserver la preuve de transaction et prévenir le client. Une action manuelle sur le statut en base sans preuve de remboursement n'est pas une résolution valide.

## Avant mise en production

- Sauvegarder la base puis appliquer et valider les migrations nécessaires sur un environnement de recette.
- Vérifier PHP CLI, cron buvette, SMTP et les logs sur LWS ; l'accès GitHub ne prouve pas leur fonctionnement.
- Faire un paiement SumUp en sandbox, simuler une clôture H-24, un webhook répété et un stock insuffisant.
- S'assurer qu'aucune commande réglée après clôture n'est ajoutée au rapport initial sans communication au destinataire.
- Documenter les remboursements et les statuts de régularisation avant d'activer les achats auprès du public.

La rotation des secrets a été confirmée par l'administrateur le 14 octobre 2026.
