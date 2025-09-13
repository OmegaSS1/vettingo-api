<?php

declare(strict_types=1);
namespace App\Controllers\Auth;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class Login extends AuthAction {

    protected function action(): Response {

        $form = $this->post();
        $this->validate($form);

        $user = $this->iUserEmailRepository->findByEmail($form["email"]);

        $tokenJWT = $this->tokenJWT->getToken($user->getUserId());
        
        return $this->respondWithData(["token" => $tokenJWT]);
    }

    private function validate(array &$form): void{
        $statusCode = 400;

        $this->validKeysForm($form, 
            ["email","password"],
            ["Email","Senha"]
        );

        if(!filter_var($form['email'], FILTER_VALIDATE_EMAIL)){
            throw MessageException::EMAIL();
        }

        $email = $this->iUserEmailRepository->findByEmail($form["email"]);
        if(!$email) {
            throw new Exception("O usuário e/ou senha informados estão inválidos.", $statusCode);
        }

        $password = $this->iUserSecurityProfileRepository->findByUserId($email->getUserId());
        if(!$password || !password_verify($form['password'], $password->getPassword())) {
            throw new Exception("O usuário e/ou senha informados estão inválidos.", $statusCode);
        }
 	}
}