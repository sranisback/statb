UPDATE players SET journalier = 0 WHERE type = 1;
UPDATE players SET journalier = 1 WHERE type = 2;

ALTER TABLE players ADD journalier bool;

UPDATE players SET type = 0 WHERE type = 1;
UPDATE players SET type = 1 WHERE type = 2;