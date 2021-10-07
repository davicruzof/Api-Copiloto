<?php

    namespace Source\Controllers;

    require __DIR__ . '/../../vendor/autoload.php';
    use CoffeeCode\DataLayer\DataLayer;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;
    use Source\Controllers\Token;

    final class User extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("user", [
                "nome", "data_nascimento", "email", "telefone", "sexo"
            ], "idUser", true);
        }

        public function insert(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $errors = $this->validateData($data);
            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(400);

            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL))
                return $response->withJson(["message" => "Email inválido, tente com outro!"])->withStatus(400);

            $existUser = $this->getUser($data["email"]);

            if ($existUser)
                return $response->withJson(["message" => "Usuário já cadastrado, realize login!"])->withStatus(200);

            if(strlen($data['telefone']) > 15)
                return $response->withJson(["message" => "Telefone inválido, tente com outro!"])->withStatus(400);

            if($data['sexo'] != "f" && $data['sexo'] != "m")
                return $response->withJson(["message" => "Sexo inválido!"])->withStatus(400);

            $date = str_replace("-","/",$data["data_nascimento"]);

            $date = explode("/",$date);

            if(!checkdate(intval($date[1]),intval($date[2]),intval($date[0])))
                return $response->withJson(["message" => "Data de nascimento inválido!"])->withStatus(400);

            $user = new User();
            $user->nome = $data["nome"];
            $user->data_nascimento = $data["data_nascimento"];
            $user->email = $data["email"];
            $user->telefone = $data["telefone"];
            $user->sexo = $data["sexo"];
            $res = $user->save();

            if($res)
                $result = (new Token())->insert($user->data());
                if($result)
                    return $response->withJson(['message' => 'Um token foi enviado para o seu email'])->withStatus(200);
            return $response->withJson(['message' => 'Não foi possível realizar o cadastro, tente novamente!'])->withStatus(500);
        }

        public function createPassword(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $errors = $this->validateData($data);
            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(400);

            $user = (new User())->findById($data["id"]);

            if (is_null($user))
                return $response->withJson(["message" => "Usuário não encontrado!"])->withStatus(400);

            $user->senha = md5($data['senha']);
            $res = $user->save();

            if ($res)
                return $response->withJson(["message" => "Senha criada com sucesso!"])->withStatus(200);
        }

        private function getUser($email): bool
        {
            $user = (new User())->find("email = :e", "e={$email}")->fetch();
            return is_null($user) ? false : true;
        }

        private function validateData($data): bool
        {
            if(in_array("",$data) || in_array(null, $data) || in_array("delete",$data)
                || in_array("update",$data))
                return true;
            return false;
        }

    }