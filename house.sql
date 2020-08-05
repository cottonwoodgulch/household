/* create household tables */

drop table household;
create table household (
  household_id int(11) auto_increment primary key,
  name varchar(64),
  salutation varchar(128),
  mailname varchar(128),
  address_id int(11) not null
);

/* can't figure out why foreign key constraints DO NOT WORK in mysql */
,
  constraint `fk_hh_address` foreign key (address_id) references addresses(address_id)

drop table household_members;
create table household_members (
  household_id int(11),
  contact_id int(11),
  email_id int(11),
  primary key (household_id, contact_id),
  constraint unique_contact unique (contact_id)
);

insert into household values(null,'Lesney','Alice and Gary',
    'Alice and Gary Lesney',381);

insert into household_members values(1,1259,2378);
insert into household_members values(1,140,null);

insert into household values(null,'French','Jamey and Robin',
   'Jamey and Robin French',883);

insert into household_members values(2,581,1608);
insert into household_members values(2,1317,null);

insert into household values(null,'Hyde','Tom',
    'Tom Hyde',4022);

insert into household_members values(3,695,2636);

insert into household values(null,'Overgaard','Lynn and Jørgen','Lynn and Jørgen Overgaard',88);
