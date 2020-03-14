CREATE TABLE `sessions` (
	`session_id` VARCHAR(32) NOT NULL,
	`created_at` DATETIME NOT NULL,
	`created_by` INT(6) NOT NULL,
	`active` INT(1) DEFAULT 0,
	PRIMARY KEY (`session_id`)
);