<?php

$pdo = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$pdo->exec('CREATE TABLE users(
    uuid VARCHAR(36) NOT NULL  PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL ,
    last_name VARCHAR(100) NOT NULL 
)');

$pdo->exec('CREATE TABLE posts(
    uuid TEXT NOT NULL  PRIMARY KEY,
    title TEXT NOT NULL ,
    text TEXT NOT NULL,
    author_uuid TEXT NOT NULL,
    FOREIGN KEY (author_uuid) REFERENCES users(uuid)
)');

$pdo->exec('CREATE TABLE comments(
    uuid VARCHAR(36) NOT NULL  PRIMARY KEY,
    text TEXT NOT NULL,
    user_uuid VARCHAR(36) NOT NULL,
    post_uuid VARCHAR(36) NOT NULL,
    FOREIGN KEY (user_uuid) REFERENCES users(uuid),
    FOREIGN KEY (post_uuid) REFERENCES posts(uuid)
)');

$pdo->exec('CREATE TABLE postsLikes(
    uuid VARCHAR(36) NOT NULL  PRIMARY KEY,
    post_uuid VARCHAR(36) NOT NULL,
    user_uuid VARCHAR(36) NOT NULL,
    FOREIGN KEY (post_uuid) REFERENCES posts(uuid)
    FOREIGN KEY (user_uuid) REFERENCES users(uuid),
)');
$pdo->exec('CREATE TABLE commentsLikes(
    uuid VARCHAR(36) NOT NULL  PRIMARY KEY,
    comment_uuid VARCHAR(36) NOT NULL,
    user_uuid VARCHAR(36) NOT NULL,
    FOREIGN KEY (comment_uuid) REFERENCES comments(uuid)
    FOREIGN KEY (user_uuid) REFERENCES users(uuid),
)');



