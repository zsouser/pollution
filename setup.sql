CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(250) NOT NULL,
  `site` varchar(100) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) 

CREATE TABLE IF NOT EXISTS `rankings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fbuid` bigint(20) DEFAULT NULL,
  `article_id` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) 