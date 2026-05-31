-- Seed default "Le Groupe" page (FR) if table is empty
INSERT INTO group_page (name, description, histoire_text, mentalite_text, fonctionnement_text, rejoindre_text, se_carter_text)
SELECT
    'Le Groupe',
    NULL,
    'Nes en tribune a Fleury, on a bati notre identite match apres match. Rouge et noir, fideles, presents partout.',
    'Fidelite, respect, solidarite. On chante, on pousse, on ne lache rien, a domicile comme en deplacement.',
    'Le groupe tourne grace aux benevoles, aux reunions et aux decisions collectives. Chacun a sa place, chacun met la main.',
    'Pour vivre le match autrement, participer aux tifos et deplacements, et faire partie d''une famille rouge et noire.',
    'Passe a la table de vente les jours de match, recupere ton code, puis inscris-toi pour acceder a l''espace membres.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM group_page);
