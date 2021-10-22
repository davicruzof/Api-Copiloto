<?php

    namespace Source\Controllers;

    require __DIR__ . '/../../vendor/autoload.php';
    use CoffeeCode\DataLayer\DataLayer;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

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
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(200);

            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL))
                return $response->withJson(["message" => "Email inválido, tente com outro!"])->withStatus(200);

            $existUser = $this->getUser($data["email"]);

            if ($existUser)
                return $response->withJson(["message" => "Usuário já cadastrado, realize login!"])->withStatus(200);

            if(strlen($data['telefone']) > 15)
                return $response->withJson(["message" => "Telefone inválido, tente com outro!"])->withStatus(200);

            if($data['sexo'] != "Feminino" && $data['sexo'] != "Masculino")
                return $response->withJson(["message" => "Sexo inválido!"])->withStatus(200);

            $date = str_replace("-","/",$data["data_nascimento"]);

            $date = explode("/",$date);

            if(!checkdate(intval($date[1]),intval($date[2]),intval($date[0])))
                return $response->withJson(["message" => "Data de nascimento inválido!"])->withStatus(200);

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
                    return $response->withJson(['message' => 'Um código foi enviado para o seu email'])->withStatus(200);

            return $response->withJson(['message' => 'Não foi possível realizar o cadastro, tente novamente!'])->withStatus(200);
        }

        public function createPassword(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $errors = $this->validateData($data);
            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(200);

            if(!is_numeric($data['idUser']))
                return $response->withJson(["message" => "Usuário inválido!"])->withStatus(200);

            $user = (new User())->findById($data["idUser"]);

            if (is_null($user))
                return $response->withJson(["message" => "Usuário não encontrado!"])->withStatus(200);

            $user->senha = md5($data['senha']);
            $res = $user->save();

            if ($res)
                return $response->withJson(["message" => "Senha criada com sucesso!"])->withStatus(200);

            return $response->withJson(["message" => "Erro ao inserir senha!"])->withStatus(200);
        }

        public function recovery_password(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $errors = $this->validateData($data);

            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(200);

            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL))
                return $response->withJson(["message" => "Email inválido, tente com outro!"])->withStatus(200);

            $existUser = $this->getUser($data["email"]);

            if (!$existUser)
                return $response->withJson(["message" => "Usuário não encontrado!"])->withStatus(200);

            $user = (new User())->find('email = :e',"e={$data['email']}")->fetch();

            $result = (new Token())->insert($user->data());

            if($result)
                return $response->withJson(['message' => 'Um código foi enviado para o seu email'])->withStatus(200);

            return $response->withJson(['message' => 'Não foi possivel enviar o código para o seu email'])->withStatus(200);
        }

        public function login(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $errors = $this->validateData($data);
            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(200);

            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL))
                return $response->withJson(["message" => "Email inválido, tente com outro!"])->withStatus(200);

            $senha = md5($data['senha']);

            $user = (new User())->find("email = :e and senha = :s", "e={$data["email"]}&s={$senha}")->fetch();

            if (is_null($user))
                return $response->withJson(["message" => "Email ou senha invalido!"])->withStatus(200);

            return $response->withJson([
                "message" => "Login com sucesso!",
                "user" => $user->data()
            ])->withStatus(200);

        }

        public function update_password(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $errors = $this->validateData($data);
            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(200);

            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL))
                return $response->withJson(["message" => "Email inválido, tente com outro!"])->withStatus(200);

            $user = (new User())->find("email = :e ", "e={$data["email"]}")->fetch();

            $senha = md5($data['senha']);

            if (is_null($user))
                return $response->withJson(["message" => "Email invalido!"])->withStatus(200);

            $user->senha = $senha;
            $res = $user->save();

            if($res)
                return $response->withJson(["message" => "Senha criada com sucesso!"])->withStatus(200);

            return $response->withJson(["message" => $user->fail()->getMessage()])->withStatus(200);
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