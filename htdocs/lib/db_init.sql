drop table if exists reports;
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

create table reports(
    id integer not null primary key,
    reported timestamp,
    client_version varchar(32),
    client_channel varchar(32),
    os varchar(128),
    os_type varchar(32),
    os_version varchar(128),
    cpu varchar(128),
    gpu varchar(128),
    opengl_version varchar(128),
    gpu_driver varchar(128),
    ram integer,
    grid varchar(128),
    region varchar(128),
    crash_reason varchar(128),
    crash_address varchar(16),
    crash_thread integer,
    raw_stacktrace  longtext 
);