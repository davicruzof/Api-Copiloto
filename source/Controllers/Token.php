<?php

    namespace Source\Controllers;

    use CoffeeCode\DataLayer\DataLayer;
    use PHPMailer\PHPMailer\PHPMailer;
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    final class Token extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("token", ["token","idUser","expired"], "id", true);
        }

        public function insert($data): bool
        {
            $token = $this->genereteToken();
            $validade = time() + ( 2 * 60 * 60);

            $tk = new Token();
            $tk->token = md5($token);
            $tk->idUser = $data->idUser;
            $tk->expired = $validade;
            $res = $tk->save();

            if($res)
                return $this->sendMail($token,$data->email);
            return false;
        }

        private function genereteToken(){
            $token = rand(1000, 9999);
            return $token;
        }

        private function sendMail($token,$email): bool
        {

            $recipient = $email;

            $port = 587;

            $subject = 'Token App Copiloto';

            $bodyHtml = "<p>o seu código de validação de conta é <h2 style='background: #fff; color: #000; padding: 10px; text-align: center;'>{$token}</h2> </p>";

            $mail = new PHPMailer(true);

            try {
                // Specify the SMTP settings.
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'copiloto20212@gmail.com';                     // SMTP username
                $mail->Password   = 'copiloto20123';                               // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = $port;

                $mail->From = "copiloto@app.com"; // Seu e-mail
                $mail->FromName = "Copiloto"; // Seu nome

                $mail->addAddress($recipient);

                $mail->isHTML(true);
                $mail->Subject    = $subject;
                $mail->Body       = $bodyHtml;
                $mail->Send();
                return true;
            } catch (\Exception $e) {
                return false;
            }

        }

        public function validateToken(RequestInterface $request, ResponseInterface $response): ResponseInterface
        {
            $data = $request->getParsedBody();

            $errors = $this->validateData($data);

            if ($errors)
                return $response->withJson(["message" => "Preencha todos os campos!"])->withStatus(200);

            $token_valid = md5($data['token']);

            $tk = (new Token())->find("token = :tk","tk={$token_valid}")->fetch();

            if(is_null($tk))
                return $response->withJson(["message" => "Código inválido!"])->withStatus(200);

            if(intval($tk->expired) < time()) {
                $tk->destroy();
                if($this->refreshToken($data["idUser"],$data->email))
                    return $response->withJson(["message" => "Código expirado! Um novo código foi enviado para o seu email!"])->withStatus(204);
            }else{
                $user = (new User())->findById($tk->idUser);
                $user->confirm = True;
                $user->save();
                $tk->destroy();
                return $response->withJson(["message" => "token valido"])->withStatus(200);
            }
        }

        private function validateData($data): bool
        {
            if(in_array("",$data) || in_array(null, $data) || in_array("delete",$data)
                || in_array("update",$data))
                return true;
            return false;
        }

        private function refreshToken($data,$email): bool
        {
            $user = (new User())->findById($data);
            $token = $this->genereteToken();
            $validade = time() + ( 2 * 60 * 60);

            $tk = new Token();
            $tk->token = md5($token);
            $tk->idUser = $user->idUser;
            $tk->expired = $validade;
            $res = $tk->save();

            if($res)
                return $this->sendMail($token,$email);
            return false;
        }

    }