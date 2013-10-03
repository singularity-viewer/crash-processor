drop table if exists raw_reports;
drop table if exists users;
drop table if exists options;
drop table if exists session;

create table session(
    sid varchar(128) not null primary key,
    user_id integer,
    authenticated tinyint,
    expires datetime,
    persist text
);

create table options(
    name varchar(128),
    value text
);

create table users(
    user_id integer not null auto_increment primary key,
    name varchar(255),
    email varchar(255),
    login varchar(255),
    password varchar(255),
    is_admin tinyint,
    is_allowed tinyint
);

create table raw_reports(
    report_id integer not null auto_increment primary key,
    reported timestamp not null default current_timestamp,
    processed integer not null default 0,
    raw_data longtext
);