(
 `otype` smallint(1) unsigned NOT NULL DEFAULT 1 COMMENT 'obj type',
 `oid` int(10) unsigned NOT NULL COMMENT 'obj id',
 `tid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`otype`,`oid`,`tid`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
