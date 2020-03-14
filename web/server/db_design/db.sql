DROP TABLE users;
DROP TABLE sessions;
DROP TABLE games;
DROP TABLE hand;

CREATE TABLE users (
	id INT(6) zerofill NOT NULL AUTO_INCREMENT,
	username VARCHAR(32) NOT NULL,
	password VARCHAR(128) NOT NULL,
	session_active INT(1) DEFAULT 0,
    session_id VARCHAR(32) DEFAULT 0,
	PRIMARY KEY (id)
);

CREATE TABLE `sessions` (
	`session_id` VARCHAR(32) NOT NULL,
	`created_at` DATETIME NOT NULL,
	`created_by` INT(6) NOT NULL,
	`active` INT(1) DEFAULT 0,
	PRIMARY KEY (`session_id`)
);

CREATE TABLE games (
    game_id  VARCHAR(32) NOT NULL,
	session_id VARCHAR(32) NOT NULL,
	hand_id VARCHAR(32),
	is_active INT(1) DEFAULT '0',
	PRIMARY KEY (game_id)
);

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

INSERT INTO users SET id=1, username='reesealanj', password='$2y$10$zQktvLYjexSJX1c1lxDocumE4F1k3JMXCVUA4O7K2OjKeeuvgo4DK', session_active=1, session_id='9d6704590fe8803522c4';
INSERT INTO sessions SET session_id='9d6704590fe8803522c4', created_at=NOW(), created_by=1, active=1;