# Ré-audit boutique / administration — 14 octobre 2026

Périmètre : code de la branche `main`, état des PR #2–#5, contrôles publics non authentifiés du site. Ce document ne prétend pas constituer un test d'intrusion ou une recette des fonctions admin.

| Chantier | État vérifié | Suite |
|---|---|---|
| P0 Secrets dans le fichier versionné | `backend/.env` ne contient plus les identifiants de production dans main (PR #2 fusionnée) | ROTATION réelle DB/SMTP/SumUp et nettoyage de l'historique Git non vérifiés |
| P0 Webhook SumUp | Vérification serveur API et rapprochement montants/marchand dans main | Tester checkout réel, reprise en cas d'erreur |
| P0 Confirmation checkout depuis navigateur | La page de remerciement avait encore le droit de marquer une commande payée en dehors du webhook | **Corrigé dans cette PR** : seul le traitement serveur doit finaliser |
| P1 Doublons et surventes | PR #4 ouverte, pas dans main | Tests de simultanéité; verrouiller aussi les lignes de stock |
| P1 Justificatifs | PR #4 ouverte, pas dans main | Test clients connectés/invités et récupération du document après changement de navigateur |
| P1 Audit LWS | Non réalisable avec accès GitHub/public uniquement | Vérifier document root backend/public, blocage .env/.git/logs, rotation |
| P2 Audit boutique public | Page catalogue et panier accessibles sur le domaine public | Tester les actions POST et modes de paiement en environnement de recette |
| P2 CI/CD | PR #3 fusionnée, dernier CI main en échec | Résoudre la cause exacte dans les logs avant intégration de fonctionnalités |
| P2 Buvette H-24 | PR #5 ouverte, pas dans main | Le code est une base admin : absence de panier/checkout public boissons ; cron non configuré sur LWS |
| P2 Médias | Les nouveaux uploads sont ignorés par Git (PR #3) | Sauvegardes et migration des anciens fichiers suivis à planifier |

## Autres constats
- L'admin générique `CustomCrudController::index` charge les listes en mémoire avant pagination ; risque de lenteurs sur grandes tables.
- La boutique décrémente certains stocks à l'aide de `max(0, stock - quantité)`, qui peut masquer une insuffisance ; ajouter verrou de stock et refus explicite.
- Les commandes externes Billetweb ne sont pas automatiquement regroupées avec la billetterie interne dans le récapitulatif buvette.
- Aucune validation de fonctionnement de l'admin n'a été faite en session authentifiée ; recueillir les chemins, erreurs et captures pour une recette ciblée.

## Ordre de déploiement recommandé
1. Renouveler les identifiants exposés et mettre les secrets hors du dépôt.
2. Fusionner et tester le correctif de paiement navigateur dans cette PR.
3. Terminer et valider PR #4 (accès aux justificatifs, verrous et SMTP).
4. Corriger la CI main, puis compléter PR #5 (commande buvette côté client, migrations/cron et déduplication).
5. Mettre en place tests de bout en bout achats/billets/cartage, sauvegardes et audit LWS.
