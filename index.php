<?php

    require __DIR__ . '/vendor/autoload.php';

    use Source\Controllers\User;
    use Source\Controllers\Terms;
    use Source\Controllers\Token;
    use Source\Controllers\Veicle;
    use Source\Controllers\Categorias;

    $app = new \Slim\App([
        'settings' => [
            'displayErrorDetails' => true,
        ],
    ]);

    $app->group('/auth', function () use ($app){
        $app->get('/terms',Terms::class . ":terms");
        $app->post('/login', User::class . ":login");
        $app->post('/create', User::class . ":insert");
        $app->post('/veicle', Veicle::class . ':insert');
        $app->post('/token', Token::class . ":validateToken");
        $app->post('/update', User::class . ":update_password");
        $app->post('/password', User::class . ":createPassword");
        $app->post('/recovery', User::class . ":recovery_password");
    });

    $app->group('/user', function () use ($app) {
        $app->get('/categorias', Categorias::class . ":categorias");
    });

    $app->run();