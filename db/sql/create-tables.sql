DROP TABLE IF EXISTS `tbtest`;

CREATE TABLE IF NOT EXISTS `tbtest`
(
  `id`    INT(20) AUTO_INCREMENT,
  `name`  VARCHAR(32) NOT NULL,
  `comment` TEXT,
  `password`  VARCHAR(64) NOT NULL,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;