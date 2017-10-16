CREATE TABLE `log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `value` tinyint(1) unsigned NOT NULL,
  `keyword` varchar(8) NOT NULL DEFAULT '',
  `date_time` datetime NOT NULL,
  `message` varchar(1024) NOT NULL DEFAULT '',
  `meta_data` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
