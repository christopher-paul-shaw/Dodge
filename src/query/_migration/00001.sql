CREATE TABLE `dodge`.`user` (
  `id_user` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NULL,
  `password` VARCHAR(45) NULL,
  `name` VARCHAR(45) NULL,
  `createdAt` DATETIME NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id_user`));

