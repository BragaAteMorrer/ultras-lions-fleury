# Boutique et administration — audit ciblé, 13 octobre 2026

## Constats sur le code
1. La page `/commande/merci` de `CheckoutController` peut marquer un checkout comme payé après une réponse API fondée seulement sur le statut, sans comparer la référence, le montant et le code marchand comme dans le webhook. À centraliser dans un service de confirmation validée.
2. La génération du PDF de commande et certaines pages de remerciement utilisent la référence dans l’URL : protection propriétaire / session invité indispensable et tests fonctionnels requis.
3. Les stocks des billets et des produits sont décrémentés avec `max(0, stock - quantité)`. Ceci masque les insuffisances et les surventes ; une validation et un verrou de stock dédiés sont nécessaires.
4. L’admin généraliste charge les entités avec `findBy()` avant filtrage et pagination PHP. Pour de gros volumes (commandes, membres, médias), remplacer par une requête paginée DB.
5. Les produits de buvette et les précommandes n’existaient pas, pas plus qu’un destinataire de rapport défini par match.
6. Les billets externes gérés sur Billetweb ne sont pas visibles dans `TicketOrder` : le décompte envoyé concerne seulement les billets payés enregistrés dans la base interne.

## Module buvette fourni (phase admin)
- `/admin/manage/buvette-categories` : créer les catégories (boissons, nourriture, etc.).
- `/admin/manage/buvette-produits` : produits et prix en centimes.
- `/admin/manage/buvette-commandes` : enregistrer manuellement les précommandes confirmées par match.
- `/admin/manage/buvette-rapports` : configurer le destinataire pour chaque match.
- Cron LWS horaire : `php bin/console app:buvette:send-reports --env=prod`.
- Le rapport est envoyé à partir de H-24 si le job tourne à cette heure ou après, tant que le match n'a pas commencé, puis `sentAt` évite les renvois lors des jobs suivants.

## Avant mise en production
- Exécuter les migrations après sauvegarde, vérifier les formulaires admin, et tester SMTP avec `MAILER_DSN` privé.
- Les précommandes buvette ne sont pas encore présentes dans le panier public ni connectées à SumUp ; il faut intégrer ces lignes au checkout, gérer les remboursements/annulations et la disponibilité produit.
- Faire un test cron sur un match fictif et valider les données réelles attendues par le destinataire.
- Coordonner la rotation des secrets précédemment exposés.
- Prévoir un suivi des incidents et une protection contre les démarrages cron simultanés pour garantir une unicité stricte d'envoi.
