CREATE TABLE `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(45) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(250) DEFAULT NULL,
  `token` varchar(250) DEFAULT NULL,
  `permission` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `dodge`.`user` (`email`, `password`, `name`, `permission`) VALUES ('admin@dodge', '$2y$10$29VbfkbK5bQILLmayQ6auejnWrNu84AE6AeF2exu6BI3MWldxuRsW', 'Default Admin', 'admin');
