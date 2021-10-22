<?php

    namespace Source\Controllers;

    use CoffeeCode\DataLayer\DataLayer;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    class Veicle extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("veicle", ["idUser", "marca", "modelo", "ano", "ultima_revisao"],
                "idVeicle", true);
        }

        public function insert(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $errors = $this->validateData($data);

            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(200);

            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL))
                return $response->withJson(["message" => "Email inválido, tente com outro!"])->withStatus(200);

            $user = (new User())->find("email = :e ", "e={$data["email"]}")->fetch();

            if (is_null($user))
                return $response->withJson(["message" => "Email invalido!"])->withStatus(200);

            $date = str_replace("-","/",$data["ultima_revisao"]);

            $date = explode("/",$date);

            if(!checkdate(intval($date[1]),intval($date[2]),intval($date[0])))
                return $response->withJson(["message" => "Última revisão não é uma data inválido!"])->withStatus(200);

            $veicle = new Veicle();
            $veicle->idUser = $user->id;
            $veicle->marca = $data['marca'];
            $veicle->modelo = $data['modelo'];
            $veicle->ano = $data['ano'];
            $veicle->ultima_revisao = $data['ultima_revisao'];
            $veicle->kmRevisao = $data['kmRevisao'];
            $veicle->kmAtual = $data['kmAtual'];
            $res = $veicle->save();

            if($res)
                return $response->withJson(["message" => "Veículo inserido!"])->withStatus(200);

            return $response->withJson(["message" => $veicle->fail()])->withStatus(200);

        }

        private function validateData($data): bool
        {
            if(in_array("",$data) || in_array(null, $data) || in_array("delete",$data)
                || in_array("update",$data))
                return true;
            return false;
        }
    }