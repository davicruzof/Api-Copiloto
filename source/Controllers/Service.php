<?php

    namespace Source\Controllers;

    use CoffeeCode\DataLayer\DataLayer;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    class Service extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("service", ["nome,service_category_idCategoria"],
                "idservice", true);
        }

        public function services(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $errors = $this->validateData($data);
            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(200);

            $serv = (new Service())
                ->find('service_category_idCategoria = :s',"s={$data['id']}")
                ->fetch(true);
            $categoriasArray = [];
            foreach ($serv as $c){
                array_push($categoriasArray,$c->data);
            }
            return $response->withJson([
                "data" => $categoriasArray,
            ])->withStatus(200);
        }

        private function validateData($data): bool
        {
            if(in_array("",$data) || in_array(null, $data) || in_array("delete",$data)
                || in_array("update",$data))
                return true;
            return false;
        }
    }