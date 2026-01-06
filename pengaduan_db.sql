# Host: localhost  (Version 5.5.5-10.4.32-MariaDB)
# Date: 2026-01-05 11:50:14
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "tanggapan"
#

DROP TABLE IF EXISTS `tanggapan`;
CREATE TABLE `tanggapan` (
  `id_tanggapan` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengaduan` int(11) DEFAULT NULL,
  `isi_tanggapan` text DEFAULT NULL,
  `tanggal_tanggapan` date DEFAULT NULL,
  PRIMARY KEY (`id_tanggapan`),
  KEY `id_pengaduan` (`id_pengaduan`),
  CONSTRAINT `tanggapan_ibfk_1` FOREIGN KEY (`id_pengaduan`) REFERENCES `pengaduan` (`id_pengaduan`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Data for table "tanggapan"
#

INSERT INTO `tanggapan` VALUES (1,1,'baik','2026-01-01'),(2,1,'okeoke\r\n','2026-01-01'),(8,7,'yes\r\n','2026-01-04'),(9,9,'Baik akan saya terbitkan surat nya uyyyy','2026-01-05');

#
# Structure for table "users"
#

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Data for table "users"
#

INSERT INTO `users` VALUES (1,'Admin','admin','admin123','admin'),(2,'Rifki','rifki','rifki123','user'),(3,'Tia','Tia','Tia123','admin'),(4,'Chigo','Chigo','Chigo123','user');

#
# Structure for table "pengaduan"
#

DROP TABLE IF EXISTS `pengaduan`;
CREATE TABLE `pengaduan` (
  `id_pengaduan` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `judul` varchar(100) DEFAULT NULL,
  `isi_pengaduan` text DEFAULT NULL,
  `tanggal_pengaduan` date DEFAULT NULL,
  `status` enum('diproses','selesai') DEFAULT NULL,
  PRIMARY KEY (`id_pengaduan`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `pengaduan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Data for table "pengaduan"
#

INSERT INTO `pengaduan` VALUES (1,2,'Pemerintah','saya sedang mandi','2026-01-01','selesai'),(2,2,'kopken','kopken enak','2026-01-04','selesai'),(7,4,'Buat Admin','Admin jago banget bikin programnya','2026-01-04','selesai'),(9,4,'Masyarakat','Tolong adakan kerja bakti setiap minggu','2026-01-05','selesai');
