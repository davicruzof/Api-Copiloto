<?php

    define("DATA_LAYER_CONFIG", [
        "driver" => "mysql",
        "host" => "us-cdbr-east-04.cleardb.com",
        "port" => "3306",
        "dbname" => "heroku_64cf063fa9a66ff",
        "username" => "b45d0a6111c3dc",
        "passwd" => "116de154",
        "options" => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);


    // b45d0a6111c3dc
    // 116de154
    // us-cdbr-east-04.cleardb.com