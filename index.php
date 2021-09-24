<?php

    require __DIR__ . '/vendor/autoload.php';

    use Source\Controllers\User;

    $app = new \Slim\App([
        'settings' => [
            'displayErrorDetails' => true,
        ],
    ]);

    $app->group('/auth', function () use ($app){

        $app->post('/create', User::class . ":insert");

    });

    $app->run();