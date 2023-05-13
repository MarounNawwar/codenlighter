-- SQLINES DEMO *** rated by MySQL Workbench
-- SQLINES DEMO *** 42 2022
-- SQLINES DEMO ***    Version: 1.0
-- SQLINES DEMO *** orward Engineering

/* SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0; */
/* SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0; */
/* SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'; */

-- SQLINES DEMO *** ------------------------------------
-- Schema codenlighter
-- SQLINES DEMO *** ------------------------------------

-- SQLINES DEMO *** ------------------------------------
-- Schema codenlighter
-- SQLINES DEMO *** ------------------------------------
CREATE SCHEMA IF NOT EXISTS codenlighter DEFAULT CHARACTER SET utf8 ;
SET SCHEMA 'codenlighter' ;

-- SQLINES DEMO *** ------------------------------------
-- SQLINES DEMO *** er`.`auth_group`
-- SQLINES DEMO *** ------------------------------------
DROP TABLE IF EXISTS codenlighter.auth_group ;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE IF NOT EXISTS codenlighter.auth_group (
  id INT NOT NULL,
  name VARCHAR(80) NULL,
  PRIMARY KEY (id))
;


-- SQLINES DEMO *** ------------------------------------
-- SQLINES DEMO *** er`.`auth_group_permissions`
-- SQLINES DEMO *** ------------------------------------
DROP TABLE IF EXISTS codenlighter.auth_group_permissions ;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE IF NOT EXISTS codenlighter.auth_group_permissions (
  id INT NOT NULL,
  group_id INT NULL,
  permission_id INT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_group_permissions_group_id
    FOREIGN KEY ()
    REFERENCES codenlighter.auth_group ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_group_permissions_permission_id
    FOREIGN KEY ()
    REFERENCES codenlighter.auth_permission ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;


-- SQLINES DEMO *** ------------------------------------
-- SQLINES DEMO *** er`.`auth_permission`
-- SQLINES DEMO *** ------------------------------------
DROP TABLE IF EXISTS codenlighter.auth_permission ;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE IF NOT EXISTS codenlighter.auth_permission (
  id INT NOT NULL,
  name VARCHAR(250) NULL,
  codename VARCHAR(45) NULL,
  PRIMARY KEY (id))
;


-- SQLINES DEMO *** ------------------------------------
-- SQLINES DEMO *** er`.`auth_user`
-- SQLINES DEMO *** ------------------------------------
DROP TABLE IF EXISTS codenlighter.auth_user ;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE IF NOT EXISTS codenlighter.auth_user (
  id INT NOT NULL,
  username VARCHAR(150) NULL,
  first_name VARCHAR(150) NULL,
  last_name VARCHAR(150) NULL,
  password_id INT NULL,
  email VARCHAR(254) NULL,
  is_active SMALLINT NULL,
  is_admin SMALLINT NULL,
  is_super_admin SMALLINT NULL,
  date_joined TIME(0) NULL,
  last_login TIME(0) NULL,
  session_id INT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_user_session_id
    FOREIGN KEY ()
    REFERENCES codenlighter.auth_user_session ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_user_password_id
    FOREIGN KEY (password_id)
    REFERENCES codenlighter.auth_user_password (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE UNIQUE INDEX username_UNIQUE ON codenlighter.auth_user (username ASC) VISIBLE;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE INDEX fk_user_password_id_idx ON codenlighter.auth_user (password_id ASC) VISIBLE;


-- SQLINES DEMO *** ------------------------------------
-- SQLINES DEMO *** er`.`auth_user_groups`
-- SQLINES DEMO *** ------------------------------------
DROP TABLE IF EXISTS codenlighter.auth_user_groups ;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE IF NOT EXISTS codenlighter.auth_user_groups (
  id INT NOT NULL,
  user_id INT NULL,
  group_id INT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_auth_user_id_groups
    FOREIGN KEY ()
    REFERENCES codenlighter.auth_user ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_auth_group_id_user_groups
    FOREIGN KEY ()
    REFERENCES codenlighter.auth_group ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;


-- SQLINES DEMO *** ------------------------------------
-- SQLINES DEMO *** er`.`auth_user_password`
-- SQLINES DEMO *** ------------------------------------
DROP TABLE IF EXISTS codenlighter.auth_user_password ;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE IF NOT EXISTS codenlighter.auth_user_password (
  id INT NOT NULL,
  password VARCHAR(255) NULL,
  salt VARCHAR(255) NULL,
  PRIMARY KEY (id))
;


-- SQLINES DEMO *** ------------------------------------
-- SQLINES DEMO *** er`.`auth_user_permissions`
-- SQLINES DEMO *** ------------------------------------
DROP TABLE IF EXISTS codenlighter.auth_user_permissions ;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE IF NOT EXISTS codenlighter.auth_user_permissions (
  id INT NOT NULL,
  user_id INT NULL,
  permission_id INT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_user_permission_user_id
    FOREIGN KEY ()
    REFERENCES codenlighter.auth_user ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_user_permission_permission_id
    FOREIGN KEY ()
    REFERENCES codenlighter.auth_permission ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;


-- SQLINES DEMO *** ------------------------------------
-- SQLINES DEMO *** er`.`auth_user_session`
-- SQLINES DEMO *** ------------------------------------
DROP TABLE IF EXISTS codenlighter.auth_user_session ;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE IF NOT EXISTS codenlighter.auth_user_session (
  key INT NOT NULL,
  create_time TIME(0) NULL,
  ip_address VARCHAR(45) NULL,
  PRIMARY KEY (key))
;


/* SET SQL_MODE=@OLD_SQL_MODE; */
/* SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS; */
/* SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS; */
