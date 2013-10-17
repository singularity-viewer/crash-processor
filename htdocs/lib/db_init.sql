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
    client_version_s varchar(32),
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
    raw_stacktrace  longtext,
    signature_id integer
);

create table signature(
    id integer not null auto_increment primary key,
    hash varchar(32) not null unique,
    signature text,
    has_comments integer
);

create table comment(
    id integer not null auto_increment primary key,
    signature_id integer,
    user_id integer,
    commented timestamp not null default current_timestamp,
    comment text
);

create table builds(
    build_nr integer,
    chan varchar(64),
    version varchar(64),
    hash varchar(64),
    modified timestamp,
    primary key(chan, build_nr)
);

drop function ver_expand;

delimiter $$
create function ver_expand(ver varchar(32))
returns varchar(32) deterministic
    begin
        declare ret varchar(32);
        declare i int;
        set i = 1;
        set ret = '';
        while (i < 5) do
            set ret = concat(ret, lpad(SUBSTRING_INDEX(SUBSTRING_INDEX(ver , '.', i ), '.' , -1), 6, '0'));
            set i = i + 1;
        end while;
       return ret;
    end$$
delimiter ;
