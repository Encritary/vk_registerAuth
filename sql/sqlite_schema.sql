CREATE TABLE users (
    id integer primary key,
    email varchar(320) not null,
    passwordHash varchar(60) not null
);

CREATE INDEX user_by_email ON users (email);