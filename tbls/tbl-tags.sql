(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `brief` varchar(255) DEFAULT NULL,
  `ip_debut` varchar(45) NOT NULL,
  `ip_lmod` varchar(45) DEFAULT NULL,
  `debut` int(10) unsigned NOT NULL,
  `lmod` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `name` (`name`),
  KEY `name_3` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;
