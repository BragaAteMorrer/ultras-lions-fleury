# Recette P1 — paiements simultanés et stocks

## État
Cette PR ajoute des verrous SQL pessimistes sur la ligne du checkout ET sur chaque ligne de stock (billet ou taille de merchandising), avec ordre de verrouillage déterministe. La CI exécute PHP lint et Composer audit, **pas** une concurrence HTTP/MySQL réelle.

## Environnement requis
- Staging avec base MariaDB indépendante, sauvegardée ; 2 utilisateurs ou checkouts distincts.
- SumUp test, ou mock serveur du GET checkout avec identité, référence, montant, devise et marchand vérifiables.
- Journal DB/logs et comptage des lignes `ticket_order`, `merch_order`, `payment_checkout`.
- Ne jamais lancer ces tests sur de vrais paiements en production.

## Cas A — deux callbacks pour un seul checkout
1. Créer un checkout payé de deux tickets, stock initial 10.
2. Faire arriver exactement simultanément deux POST du même checkout.
3. Attendu : un seul lot de commandes, stock final 8, `processed_at` rempli. Les deux POST répondent OK.
4. Rejouer un troisième callback : aucune nouvelle commande ou variation du stock.

## Cas B — deux checkouts distincts, un dernier billet
1. Créer deux checkouts payés de quantité 1, portant sur le même ticket dont stock vaut 1.
2. Déclencher les deux callbacks en parallèle, avec des connexions DB distinctes.
3. Attendu : stock final 0, une seule commande ticket, l'autre checkout `paid_stock_review` avec preuve `paid_at` et aucune commande.
4. Le service clientèle doit traiter ce cas (remboursement ou réapprovisionnement). Aucun stock négatif.

## Cas C — deux checkouts distincts, une taille de merch
Même procédure avec `merch_stock` quantité 1 : une seule `merch_order`, aucun stock négatif.

## Cas D — lignes multiples et stock insuffisant
- Deux lignes du même produit, quantités 2 et 2, stock 3 : le total 4 doit échouer avant toute création d'ordre.
- Panier mixte ticket+merch, stock merch insuffisant : aucun ordre ticket partiel et aucun stock modifié.
- Produit supprimé, quantité nulle, webhook répété : ne pas inventer de stock et marquer `paid_stock_review`.

## Cas E — rollback
Simuler une erreur DB après acquisition des verrous : transaction annulée, pas de commande partielle.

## Précautions restantes
- Le mail récapitulatif n'est pas garanti exactement une fois sous double appel concurrent : prévoir une file d'envoi/outbox et identifiant d'événement unique.
- Les achats simultanés ne réservent pas le stock *avant* paiement : deux clients peuvent payer le dernier article. Le paiement surnuméraire est alors signalé pour intervention manuelle.
- Les ventes physiques doivent partager la même discipline de verrouillage pour une garantie complète.
