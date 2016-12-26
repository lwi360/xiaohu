CREATE TABLE user(
   id int(10)unsigned not null auto_increment,
   username varchar(12) unique,
   password varchar(255) not null,

   primary key (id),
   unique key users_username_unique(username)
)engine=innodb;
