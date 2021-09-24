<?php

    namespace Source\Controllers;

    use CoffeeCode\DataLayer\DataLayer;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    final class User extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("user", [
                "nome","data_nascimento","email","telefone","sexo"
            ], "idUser", true);
        }

        public function insert(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $this->cleanStrings($data);

            $errors = $this->validaDados($data);
            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(400);

            $existUser = $this->getUser($data["email"]);

            if($existUser)
                return $response->withJson(["message" => "Usuário já cadastrado, realize login!"])->withStatus(200);

            if(!filter_var($data["email"], FILTER_VALIDATE_EMAIL))
                return $response->withJson(["message" => "Email inválido, tente outro!"])->withStatus(400);

            $user = new User();
            $user->nome = $data["nome"];
            $user->data_nascimento = $data["data_nascimento"];
            $user->email = $data["email"];
            $user->telefone = $data["telefone"];
            $user->sexo = $data["sexo"];
            $res = $user->save();

            return $response->withJson(['message' => $res ? 'Usuário cadastrado com sucesso!' : 'Não foi possível realizar o cadastro, tente novamente!'])
                ->withStatus($res ? 200 : 500);
        }

        private function cleanStrings(&$data)
        {
            foreach ($data as $key => $elem)
                $data[$key] =  preg_replace('/\\s\\s+/', ' ', $elem);
        }

        private function validaDados($data)
        {
            if (!$data || in_array('', $data) || in_array(null, $data) || in_array('<script>', $data)
                || in_array('UPADTE', $data) || in_array('DELETE', $data)) {
                return true;
            }
            return false;
        }

        private function getUser($email):bool
        {
            $user = (new User())->find("email = :e", "e={$email}")->fetch();
            return is_null($user) ? false : true;
        }
    }
