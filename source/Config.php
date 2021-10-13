<?php

    define("DATA_LAYER_CONFIG", [
        "driver" => "mysql",
        "host" => "us-cdbr-east-04.cleardb.com",
        "port" => "3306",
        "dbname" => "heroku_bb327258bc113db",
        "username" => "bb8e546052f318",
        "passwd" => "541fb054",
        "options" => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);