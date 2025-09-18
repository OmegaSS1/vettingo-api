<?php

declare(strict_types=1);
namespace App\Controllers\Auth;

use Psr\Http\Message\ResponseInterface as Response;

class ForgotPassword extends AuthAction {

    protected function action(): Response {
        $form = $this->post();
        $email = $this->validate($form);

        if(!$email){
            return $this->respondWithData(["message" => "Um email de recuperação de senha foi enviado para o endereço informado!"]);
        }

        $token = $this->tokenJWT->getToken($email->getUserId());
        $url = "http://localhost:3333/redefinir-senha?token=$token";
        $link = "<a href=$url> Alterar Senha </a>";

        $this->email->send("Recuperação de senha", "Olá, vimos que você solicitou a redefinição de senha. <br> 
        Clique no link para ser redirecionado: $link", $email->getEmail());
        
        return $this->respondWithData(["message" => "Um email de recuperação de senha foi enviado para o endereço informado!"]);
    }

    private function validate(array &$form){
        $this->validKeysForm($form, ["email"], ["Email"]);

        if(empty($form["email"]) || !$email = $this->iUserEmailRepository->findByEmail($form["email"])){
            return false;
        }

        return $email;
 	}
}