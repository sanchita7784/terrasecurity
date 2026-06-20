ALTER TABLE `employee` ADD COLUMN `is_terminate` TINYINT(1) NOT NULL DEFAULT 0 AFTER `image`,ADD COLUMN `terminate_date` DATE NULL AFTER `is_terminate`;

ALTER TABLE `employee` ADD COLUMN `rejoin_date` DATE NULL AFTER `terminate_date`, ADD COLUMN `company_id` INT NULL AFTER `rejoin_date`;

ALTER TABLE `employee` DROP COLUMN `rejoin_date`;

ALTER TABLE `employee` ADD COLUMN `is_re_join` TINYINT(1) NOT NULL DEFAULT 0 AFTER `terminate_date`;


CREATE TABLE `cron_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cron_name` VARCHAR(45) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 0,
  `sql_log_file` VARCHAR(255) NULL,
  `output` TEXT NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`));
