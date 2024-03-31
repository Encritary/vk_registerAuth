CREATE TABLE users (
    id int unsigned not null auto_increment primary key,
    email varchar(320) not null,
    passwordHash varchar(60) not null,
    INDEX (email)
);