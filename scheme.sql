--
-- File generated with SQLiteStudio v3.1.0 on qua jun 29 17:57:53 2016
--
-- Text encoding used: UTF-8
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: distribuido
CREATE TABLE distribuido (id INTEGER PRIMARY KEY AUTOINCREMENT, nome VARCHAR, status INTEGER DEFAULT (0), trabalho_id INTEGER REFERENCES trabalho (id));

-- Table: processo
CREATE TABLE processo (id INTEGER PRIMARY KEY AUTOINCREMENT, pid INT, status INT, trabalho_id INTEGER REFERENCES trabalho (id) ON DELETE CASCADE ON UPDATE CASCADE, workDir VARCHAR);

-- Table: trabalho
CREATE TABLE trabalho (id INTEGER PRIMARY KEY AUTOINCREMENT, nome VARCHAR, sourceDir VARCHAR, status INT, template VARCHAR);

COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
