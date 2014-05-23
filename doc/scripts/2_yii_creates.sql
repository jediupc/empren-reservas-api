CREATE TABLE usuari (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nom_usuari VARCHAR(45) NOT NULL,
  contrasenya VARCHAR(20) NULL,
  nom VARCHAR(45) NULL,
  cognoms VARCHAR(255) NULL,
  es_admin BOOL NULL,
  PRIMARY KEY(id),
  UNIQUE INDEX usuari_AK(nom_usuari)
)
TYPE=InnoDB;

CREATE TABLE espai (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  codi VARCHAR(20) NOT NULL,
  descripcio VARCHAR(255) NULL,
  PRIMARY KEY(id),
  UNIQUE INDEX espai_ak(codi)
)
TYPE=InnoDB;

CREATE TABLE reserva (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  data_hora DATETIME NOT NULL,
  espai_id INTEGER UNSIGNED NOT NULL,
  usuari_id INTEGER UNSIGNED NOT NULL,
  data_modif DATETIME NULL,
  PRIMARY KEY(id),
  INDEX reserva_FKIndex1(espai_id),
  UNIQUE INDEX reserva_AK(data_hora, espai_id),
  INDEX reserva_FKIndex3(usuari_id),
  FOREIGN KEY(espai_id)
    REFERENCES espai(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(usuari_id)
    REFERENCES usuari(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE permis (
  espai_id INTEGER UNSIGNED NOT NULL,
  usuari_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(espai_id, usuari_id),
  INDEX espai_has_usuari_FKIndex1(espai_id),
  INDEX espai_has_usuari_FKIndex2(usuari_id),
  FOREIGN KEY(espai_id)
    REFERENCES espai(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(usuari_id)
    REFERENCES usuari(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

