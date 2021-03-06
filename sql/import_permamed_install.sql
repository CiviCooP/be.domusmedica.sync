DROP TABLE IF EXISTS `import_permamed`;
CREATE TABLE `import_permamed`  (
  `id`                      INT                  AUTO_INCREMENT,
  `riziv`                   VARCHAR(14) NOT NULL,
  `naam`                    VARCHAR(64),
  `voornaam`                VARCHAR(64),
  `straat`                  VARCHAR(96),
  `huisnummer`              VARCHAR(20),
  `postcode`                VARCHAR(10),
  `stad`                    VARCHAR(64),
  `telefoon`                VARCHAR(32),
  `gsm`                     VARCHAR(32),
  `email`                   VARCHAR(100),
  `fax`                     VARCHAR(32),
  `praktijknaam`            VARCHAR(128),
  `website`                 VARCHAR(128),
  `rekeningnummer`          VARCHAR(255),
  `straat_prive`            VARCHAR(96),
  `huisnummer_prive`        VARCHAR(20),
  `postcode_prive`          VARCHAR(10),
  `stad_prive`              VARCHAR(64),
  `telefoon_prive`          VARCHAR(32),
  `gsm_prive`               VARCHAR(32),
  `email_prive`             VARCHAR(100),
  `rekeningnummer_prive`    VARCHAR(255),
  `fax_prive`               VARCHAR(32),
  `geslacht`                VARCHAR(10),
  `haio`                    SMALLINT    NOT NULL DEFAULT 0,
  `opleidingsjaar`          varchar(1),
  `praktijk_opleider`       VARCHAR(100),
  `actief_voor_wachtdienst` SMALLINT    NOT NULL DEFAULT 0,
  `emd`                     VARCHAR(20),
  `processed`               VARCHAR(1)  NOT NULL DEFAULT 'N',
  `message`                 TEXT                 DEFAULT NULL,
  PRIMARY KEY (`id`)
) COLLATE `utf8_unicode_ci`;