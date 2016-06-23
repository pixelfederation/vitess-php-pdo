create table user_seq(id int, next_id bigint, cache bigint, increment bigint, primary key(id)) comment 'vitess_sequence';
insert into user_seq values(0, 1, 3, 1);
create table music_seq(id int, next_id bigint, cache bigint, increment bigint, primary key(id)) comment 'vitess_sequence';
insert into music_seq values(0, 1, 2, 1);
create table name_user_idx(name varchar(128), user_id bigint, primary key(name, user_id));
create table index_test(name varchar(128), id bigint, primary key(id), key `test_key` (`name`));
