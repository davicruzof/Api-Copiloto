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
            $categoriasArray = [];
            foreach ($cat as $c){
                array_push($categoriasArray,$c->data);
            }
            return $response->withJson([
                "data" => $categoriasArray,
            ])->withStatus(200);
        }

    }