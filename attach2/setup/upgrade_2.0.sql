/* 2.0 modified db struct */
ALTER TABLE `cot_attach` ADD `att_area` VARCHAR(64) NOT NULL;
UPDATE `cot_attach` SET `att_area` = 'page' WHERE `att_type` = 'pag';
UPDATE `cot_attach` SET `att_area` = 'forums' WHERE `att_type` = 'frm';
ALTER TABLE `cot_attach` DROP `att_type`;
ALTER TABLE `cot_attach` DROP `att_parent`;

ALTER TABLE `cot_attach` ADD `att_filename` VARCHAR(255) NOT NULL;

ALTER TABLE `cot_attach` ADD COLUMN `att_order` SMALLINT NOT NULL DEFAULT 0;
ALTER TABLE `cot_attach` ADD COLUMN `att_lastmod` INT NOT NULL DEFAULT 0;