<?php

    namespace Source\Controllers;

    use CoffeeCode\DataLayer\DataLayer;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    class Veicle extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("veicle", ["idUser", "marca", "modelo", "ano", "ultima_revisao", "tempo_dono"],
                "idVeicle", true);
        }

        public function insert(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $errors = $this->validateData($data);

            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(400);

            $date = str_replace("-","/",$data["ultima_revisao"]);

            $date = explode("/",$date);

            if(!checkdate(intval($date[1]),intval($date[2]),intval($date[0])))
                return $response->withJson(["message" => "Última revisão não é uma data inválido!"])->withStatus(204);

            if(!is_numeric($data['idUser']))
                return $response->withJson(["message" => "Usuário inválido!"])->withStatus(400);

            $veicle = new Veicle();
            $veicle->idUser = $data['idUser'];
            $veicle->marca = $data['marca'];
            $veicle->modelo = $data['modelo'];
            $veicle->ano = $data['ano'];
            $veicle->ultima_revisao = $data['ultima_revisao'];
            $veicle->tempo_dono = $data['tempo_dono'];
            $res = $veicle->save();

            if($res)
                return $response->withJson(["message" => "Veículo inserido!"])->withStatus(200);

            return $response->withJson(["message" => "Erro ao inserir veículo!"])->withStatus(204);

        }

        private function validateData($data): bool
        {
            if(in_array("",$data) || in_array(null, $data) || in_array("delete",$data)
                || in_array("update",$data))
                return true;
            return false;
        }
    }