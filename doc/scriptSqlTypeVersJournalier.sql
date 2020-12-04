UPDATE players SET type = 0 WHERE type = 1;
UPDATE players SET type = 1 WHERE type = 2;

ALTER TABLE players CHANGE type journalier bool;