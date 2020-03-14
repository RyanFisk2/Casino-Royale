CREATE TABLE games (
    game_id  VARCHAR(32) zerofill NOT NULL AUTO_INCREMENT,
	session_id VARCHAR(32) NOT NULL,
	hand_id VARCHAR(32),
	is_active INT(1) DEFAULT '0',
	PRIMARY KEY (game_id)
);