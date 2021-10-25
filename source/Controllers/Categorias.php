<?php

    namespace Source\Controllers;

    use CoffeeCode\DataLayer\DataLayer;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    class Categorias extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("service_category", ["nome,icon_white,
            icon_gray"], "idCategoria", true);
        }

        public function categorias(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $cat = (new Categorias())->find()->fetch(true);
            $cat= $cat->data();
            return $response->withJson(["data" => $cat])->withStatus(200);
        }

    }