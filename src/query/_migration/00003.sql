CREATE TABLE `message` (
  `id_message` INT NOT NULL AUTO_INCREMENT,
  `id_room` INT NULL,
  `id_user` INT NULL,
  `message` TEXT NULL,
  `createdAt` DATETIME NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id_message`));
