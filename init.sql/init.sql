-- role uzytkownikow
drop table if exists roles cascade;
create table roles (
    id serial primary key,
    name varchar(50) not null unique
);

-- uzytkownicy 
drop table if exists users cascade;
create table users (
    id serial primary key,
    nickname varchar(100) not null,
    role_id int not null references roles(id) on delete restrict
);

-- dane logowania
drop table if exists auth cascade;
create table auth (
    id int primary key references users(id) on delete cascade,
    email varchar(150) not null unique,
    password varchar(255) not null
);

-- posty
drop table if exists posts cascade;
create table posts (
    id serial primary key,
    user_id int not null references users(id) on delete cascade,
    title varchar(255) not null,
    content text not null,
    image varchar(255),
    created_at timestamp with time zone default now()
);

-- tabela archiwizacji postow
drop table if exists posts_history cascade;
create table posts_history (
    id serial primary key,
    post_id int not null references posts(id) on delete cascade,
    old_title varchar(255) not null,
    old_content text not null,
    image varchar(255),
    archived_at timestamp with time zone default now()
);

--  komentarze
drop table if exists comments cascade;
create table comments (
    id serial primary key,
    post_id int not null references posts(id) on delete cascade,
    user_id int not null references users(id) on delete cascade,
    content text not null,
    created_at timestamp with time zone default now()
);

-- polubienia
drop table if exists likes cascade;
create table likes (
    post_id int not null references posts(id) on delete cascade,
    user_id int not null references users(id) on delete cascade,
    primary key(post_id, user_id)
);

-- loty
drop table if exists flights cascade;
create table flights (
    id serial primary key,
    user_id int not null references users(id) on delete cascade,
    departure_airport varchar(100) not null,
    landing_airport varchar(100) not null,
    aircraft varchar(100) not null,
    flight_time int not null, -- czas lotu w minutach
    departure_time timestamp with time zone not null,
    landing_time timestamp with time zone not null,
    created_at timestamp with time zone default now()
);

-- wiadomosci
drop table if exists messages cascade;
create table messages (
    id serial primary key,
    sender_id int not null references users(id) on delete cascade,
    receiver_id int not null references users(id) on delete cascade,
    content text not null,
    created_at timestamp with time zone default now()
);

-- powiadomienia
drop table if exists notifications cascade;
create table notifications (
    id serial primary key,
    user_id int not null references users(id) on delete cascade,
    content text not null,
    created_at timestamp with time zone default now()
);

-- dane w rolach
insert into roles(name) values ('user'), ('admin');

-- Widok lotow z nazwą użytkownika
create or replace view v_flights as
select
  f.*, u.nickname
from flights f
join users u on f.user_id = u.id
order by f.created_at desc;

-- widok statystyk w mainpage
create or replace view v_global_flight_stats as
select
  count(*) as total_flights,
  sum(flight_time) as total_time,
  avg(flight_time) as avg_time
from flights;

-- total flight tiem (hh:mm)
create or replace function get_total_flight_time(uid int) returns text as $$
begin
  return to_char(
    (select sum(flight_time) * interval '1 minute' from flights where user_id = uid),
    'HH24:MI'
  );
end;
$$ language plpgsql;

-- najczęściej używane lotnisko
drop function if exists get_most_used_airport(int);
create function get_most_used_airport(uid int) returns text as $$
select departure_airport
from flights
where user_id = uid
group by departure_airport
order by count(*) desc
limit 1;
$$ language sql;

-- najczęściej uzywany samolot
drop function if exists get_most_used_aircraft(int);
create function get_most_used_aircraft(uid int) returns text as $$
select aircraft
from flights
where user_id = uid
group by aircraft
order by count(*) desc
limit 1;
$$ language sql;

-- fucnkja do archwiziacji posta
create or replace function fn_archive_post() returns trigger as $$
begin
  insert into posts_history(post_id, old_title, old_content, image)
  values (old.id, old.title, old.content, old.image);
  return new;
end;
$$ language plpgsql;

-- Trigger archiwizacji po usunieciu
create trigger trg_posts_archive
after update on posts
for each row
when (old.title is distinct from new.title or old.content is distinct from new.content)
execute procedure fn_archive_post();

-- function - notification on register
create or replace function notify_on_register() returns trigger as $$
begin
  insert into notifications(user_id, content)
  values (new.id, 'Welcome, ' || new.nickname || '!');
  return new;
end;
$$ language plpgsql;

-- Trigger - notification on user registration
create trigger trg_notify_on_register
after insert on users
for each row
execute procedure notify_on_register();
