CREATE TABLE `room` (
  `id_room` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_room`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
