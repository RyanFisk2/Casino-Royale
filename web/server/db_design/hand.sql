CREATE TABLE `hand` (
	`hand_id` VARCHAR(32) NOT NULL,
	`game_id` VARCHAR(32) NOT NULL,
    `comm_1` VARCHAR(2),
	`comm_2` VARCHAR(2),
	`comm_3` VARCHAR(2),
	`comm_4` VARCHAR(2),
	`comm_5` VARCHAR(2),
	`hand_1` VARCHAR(2),
	`hand_2` VARCHAR(2),
	`score` INT,
	`odds` FLOAT,
	`avg_score` FLOAT,
	PRIMARY KEY (`hand_id`)
);