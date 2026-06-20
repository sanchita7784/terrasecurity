ALTER TABLE `salary` ADD COLUMN `shift_hours` FLOAT NOT NULL AFTER `year`;

ALTER TABLE `salary` ADD COLUMN `one_hour_salary` FLOAT NOT NULL AFTER `declare_salary`, ADD COLUMN `one_day_salary` FLOAT NOT NULL AFTER `one_hour_salary`;


ALTER TABLE `leaves` CHANGE COLUMN `status` `status` INT(11) NOT NULL DEFAULT 0 ;

ALTER TABLE `salary` CHANGE COLUMN `is_paid` `is_paid` TINYINT(4) NOT NULL DEFAULT 0 ;

