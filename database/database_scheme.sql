CREATE DATABASE IF NOT EXISTS curso_social_network
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;
USE curso_social_network;

CREATE TABLE users(
  id          INT(255) AUTO_INCREMENT NOT NULL,
  role        VARCHAR(20),
  email       VARCHAR(255),
  name        VARCHAR(255),
  surname     VARCHAR(255),
  password    VARCHAR(255),
  nick        VARCHAR(50),
  bio         VARCHAR(255),
  active      VARCHAR(2),
  image       VARCHAR(255),
  CONSTRAINT users_unique_fields UNIQUE (email, nick),
  CONSTRAINT pk_users PRIMARY KEY (id)
)ENGINE = InnoDb;

CREATE TABLE publications(
  id          INT(255) AUTO_INCREMENT NOT NULL,
  user_id     INT(255),
  text        MEDIUMTEXT,
  document    VARCHAR(100),
  image       VARCHAR(255),
  status      VARCHAR(30),
  created_at  DATETIME,
  CONSTRAINT pk_publications PRIMARY KEY (id),
  CONSTRAINT fk_publications_users FOREIGN KEY (user_id) REFERENCES users(id)
)ENGINE = InnoDb;

CREATE TABLE following(
  id          INT(255) AUTO_INCREMENT NOT NULL,
  user        INT(255),
  followed    INT(255),
  CONSTRAINT pk_following PRIMARY KEY (id),
  CONSTRAINT fk_following_users FOREIGN KEY (user) REFERENCES users(id),
  CONSTRAINT fk_followed FOREIGN KEY (followed) REFERENCES users(id)
)ENGINE = InnoDb;

CREATE TABLE private_messages(
  id          INT(255) AUTO_INCREMENT NOT NULL,
  message     LONGTEXT,
  emitter     INT(255),
  receiver    INT(255),
  file        VARCHAR(255),
  image       VARCHAR(255),
  readed      VARCHAR(3),
  created_at  DATETIME,
  CONSTRAINT pk_private_messages PRIMARY KEY (id),
  CONSTRAINT fk_emitter_privates FOREIGN KEY (emitter) REFERENCES users(id),
  CONSTRAINT fk_receiver_privates FOREIGN KEY (receiver) REFERENCES users(id)
)ENGINE = InnoDb;

CREATE TABLE likes(
  id          INT(255) AUTO_INCREMENT NOT NULL,
  user        INT(255),
  publication INT(255),
  CONSTRAINT pk_likes PRIMARY KEY (id),
  CONSTRAINT fk_likes_users FOREIGN KEY (user) REFERENCES users(id),
  CONSTRAINT fk_likes_publication FOREIGN KEY (publication) REFERENCES publications(id)
)ENGINE = InnoDb;

CREATE TABLE notifications(
  id          INT(255) AUTO_INCREMENT NOT NULL,
  user_id     INT(255),
  type        VARCHAR(255),
  type_id     INT(255),
  readed      VARCHAR(3),
  created_at  DATETIME,
  extra       VARCHAR(100),
  CONSTRAINT pk_notifications PRIMARY KEY (id),
  CONSTRAINT fk_notifications_users FOREIGN KEY (user_id) REFERENCES users(id)
)ENGINE = InnoDb;


