-- migrations/20260316120000_add_group_page_sections.sql

ALTER TABLE group_page
  ADD histoire_text LONGTEXT DEFAULT NULL,
  ADD mentalite_text LONGTEXT DEFAULT NULL,
  ADD fonctionnement_text LONGTEXT DEFAULT NULL,
  ADD rejoindre_text LONGTEXT DEFAULT NULL,
  ADD se_carter_text LONGTEXT DEFAULT NULL;
