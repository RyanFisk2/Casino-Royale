drop table users;
drop table games;

create table users (
    user_id int not null auto_increment, 
    username varchar(32) not null, 
    password varchar(128) not null,
    api_key varchar(64) not null,
    primary key (user_id)
);

create table games (
    game_id varchar(32) not null,
    created_by int not null,
    state int(1) default '0',
    scanned_cards int default 0, 
    comm_1 varchar(2) default 'NC', 
    comm_2 varchar(2) default 'NC', 
    comm_3 varchar(2) default 'NC', 
    comm_4 varchar(2) default 'NC', 
    comm_5 varchar(2) default 'NC', 
    hand_1 varchar(2) default 'NC', 
    hand_2 varchar(2) default 'NC',
    score int, 
    odds float, 
    avg_score float,
    primary key (game_id)
);
