<?php

    namespace Source\Controllers;

    use CoffeeCode\DataLayer\DataLayer;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    class Terms extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("terms", ["terms"], "id", true);
        }

        public function terms(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $term = (new Terms())->findById(1);
            $term = $term->data();
            return $response->withJson(["data" => $term->terms])->withStatus(200);
        }

    }