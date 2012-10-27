-- Main attach table
CREATE TABLE IF NOT EXISTS `cot_attach` (
	`att_id` INT NOT NULL AUTO_INCREMENT,
	`att_user` INT NOT NULL,
	`att_area` VARCHAR(64) NOT NULL,
	`att_item` INT NOT NULL,
	`att_path` VARCHAR(255) NOT NULL,
	`att_filename` VARCHAR(255) NOT NULL,
	`att_ext` VARCHAR(16) NOT NULL,
	`att_img` TINYINT NOT NULL DEFAULT 0,
	`att_size` INT NOT NULL,
	`att_title` VARCHAR(255) NOT NULL,
	`att_count` INT NOT NULL DEFAULT 0,
	`att_order` SMALLINT NOT NULL DEFAULT 0,
	`att_lastmod` INT NOT NULL DEFAULT 0,
	PRIMARY KEY(`att_id`),
	KEY (`att_area`, `att_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
