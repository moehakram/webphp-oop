CREATE DATABASE php_mvc;

CREATE TABLE users
(
    id                int auto_increment PRIMARY KEY,
    name              varchar(25)  NOT NULL,
    username          varchar(25)  NOT NULL,
    email             varchar(255) NOT NULL,
    password          varchar(255) NOT NULL,
    role              tinyint(1)   NOT NULL DEFAULT 0,
    is_active         tinyint(1)            DEFAULT 0,
    activated_at      datetime              DEFAULT NULL,
    created_at        timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        datetime              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)ENGINE InnoDB;

CREATE TABLE sessions(
    id              int auto_increment PRIMARY KEY,
    user_id         INT         NOT NULL,
    created_at      timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id
    FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE
)ENGINE InnoDB;