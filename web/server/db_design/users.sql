CREATE TABLE users (
	id INT(6) zerofill NOT NULL AUTO_INCREMENT,
	username VARCHAR(32) NOT NULL,
	password VARCHAR(128) NOT NULL,
	session_active INT(1) DEFAULT 0,
    session_id VARCHAR(32) DEFAULT 0,
	PRIMARY KEY (id)
);

