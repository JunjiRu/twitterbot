CREATE TABLE `tweet_box` (
  `content` varchar(140) DEFAULT NULL,
  `author` char(255) NOT NULL,
  UNIQUE KEY `content` (`content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `number` (
  `number` int(11) NOT NULL DEFAULT '0',
  `prime_factor` text,
  PRIMARY KEY (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
