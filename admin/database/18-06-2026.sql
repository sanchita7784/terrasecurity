CREATE TABLE `company` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `gst_no` VARCHAR(45) NOT NULL,
  `owner_name` VARCHAR(45) NOT NULL,
  `mobile` VARCHAR(15) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `created_by` INT NULL,
  `updated_at` DATETIME NULL,
  `updated_by` INT NULL,
  PRIMARY KEY (`id`));


ALTER TABLE `location` ADD COLUMN `company_id` INT UNSIGNED NOT NULL AFTER `id`;

