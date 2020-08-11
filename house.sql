/* create household tables */

drop table household;
create table household (
  household_id int(11) auto_increment primary key,
  name varchar(64),
  salutation varchar(128),
  mailname varchar(128),
  address_id int(11),
  email_id int(11),
  modified datetime
);

/* can't figure out why foreign key constraints DO NOT WORK in mysql */
,
  constraint `fk_hh_address` foreign key (address_id) references addresses(address_id)

drop table household_members;
create table household_members (
  household_member_id int(11),
  household_id int(11),
  contact_id int(11),
  modified datetime,
  primary key (household_id, contact_id),
  constraint unique_contact unique (contact_id)
);

insert into household values(null,'Lesney','Alice and Gary',
    'Alice and Gary Lesney',381,null,now());

insert into household_members (household_id,contact_id,modified)
   values(1,1259,now());
insert into household_members (household_id,contact_id,modified)
    values(1,140,now());

insert into household values(null,'French','Jamey and Robin',
   'Jamey and Robin French',883,null,now());

insert into household_members (household_id,contact_id,modified)
    values(2,581,now());
insert into household_members (household_id,contact_id,modified)
    values(2,1317,now());

insert into household values(null,'Hyde','Tom',
    'Tom Hyde',4022,null,now());

insert into household_members (household_id,contact_id,modified)
    values(3,695,now());

insert into household values(null,'Overgaard','Lynn and Jørgen','Lynn and Jørgen Overgaard',88,null,now());
