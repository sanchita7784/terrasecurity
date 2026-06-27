ALTER TABLE `employee` 
ADD COLUMN `salary_payment_mode` INT NULL AFTER `company_id`,
ADD COLUMN `ifsc_code` VARCHAR(45) NULL AFTER `salary_payment_mode`,
ADD COLUMN `bank_account_no` VARCHAR(45) NULL AFTER `ifsc_code`;


ALTER TABLE `employee` 
ADD COLUMN `type` INT NULL AFTER `id`,
ADD COLUMN `agreement_salary` FLOAT NULL AFTER `salary`;


CREATE TABLE `income` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT UNSIGNED NOT NULL,
  `month` INT NOT NULL,
  `year` YEAR NOT NULL,
  `emp_type` INT NOT NULL,
  `declare_income` FLOAT NOT NULL,
  `present_days` INT NOT NULL,
  `cal_income` FLOAT NOT NULL,  
  `created_at` DATETIME NOT NULL,
  `created_by` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));


ALTER TABLE `income` ADD COLUMN `no_of_employees` INT NOT NULL AFTER `emp_type`;

CREATE TABLE `invoice` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT NOT NULL,
  `month` INT NOT NULL,
  `year` YEAR NOT NULL,
  `pdf` VARCHAR(255) NOT NULL,
  `is_email_sent` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `created_by` INT NULL,
  PRIMARY KEY (`id`));


CREATE TABLE `ledger_account` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT NOT NULL,
  `opening_balance` FLOAT NOT NULL DEFAULT 0,
  `balance` FLOAT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `created_by` INT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `ledger_account` ADD COLUMN `updated_at` DATETIME NULL AFTER `created_by`,ADD COLUMN `updated_by` INT NULL AFTER `updated_at`;



CREATE TABLE `ledger_transaction` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `legder_account_id` INT UNSIGNED NOT NULL,
  `amount` FLOAT NOT NULL,
  `comments` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `created_by` INT NOT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `company` ADD COLUMN `email` VARCHAR(80) NULL AFTER `mobile`;
ALTER TABLE `company` ADD COLUMN `address` VARCHAR(255) NULL AFTER `name`;
ALTER TABLE `company` ADD COLUMN `state_id` INT NULL AFTER `address`;
ALTER TABLE `company` CHANGE COLUMN `gst_no` `gst_no` VARCHAR(45) NULL ;


ALTER TABLE `invoice` ADD COLUMN `is_paid` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_email_sent`;
ALTER TABLE `invoice` ADD COLUMN `amount` FLOAT NOT NULL AFTER `pdf`;

ALTER TABLE `income` ADD COLUMN `invoice_id` INT UNSIGNED NOT NULL AFTER `id`;

ALTER TABLE `invoice` ADD COLUMN `invoice_no` VARCHAR(45) NOT NULL AFTER `year`;


