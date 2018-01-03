DROP TABLE IF EXISTS `import_permamed`;
CREATE TABLE `import_permamed` (
  `riziv`     INT(14)    NOT NULL,
  `naam`      VARCHAR(64),
  `voornaam`  VARCHAR(64),
  `geslacht`  VARCHAR(5),
  `processed` VARCHAR(1) NOT NULL DEFAULT 'N',
  `message`   VARCHAR(2000)       DEFAULT NULL,
  PRIMARY KEY (`riziv`)
);