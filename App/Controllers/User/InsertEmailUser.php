<?php

declare(strict_types=1);
namespace App\Controllers\User;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class InsertEmailUser extends UserAction {

    protected function action(): Response {
        $form = $this->post();
        $this->validate($form);

        $id = $this->iUserEmailRepository->insert([
            "email" => $form["email"],
            "user_id" => $this->USER->sub,
            '"isPublic"' => $form["isPublic"],
            '"isActive"' => $form["isActive"],
            '"isPrimary"' => $form["isPrimary"],
        ]);

        $user = $this->iUserEmailRepository->findByUserId($id);

        return $this->respondWithData([]);
    }

    private function validate(&$form){
        $this->validKeysForm($form, 
        ["email","isActive", "isPublic", "isPrimary"],
        ["Emails","Ativo", "Público", "Principal"]);
        
        if(!filter_var($form["email"], FILTER_VALIDATE_EMAIL)){
            throw MessageException::EMAIL();
        }

        if($this->iUserEmailRepository->findByEmail($form["email"])){
            throw MessageException::ALREADY_EXISTS('EMAIL');
        }

        $acceptValues = [true, false];
        foreach($form as $k => $v){
            if($k == "email") continue;
            if(!in_array($v, $acceptValues)){
                throw new Exception("O valor informado está inválido.", 400);
            }
            $form[$k] = $v === true ? 'TRUE' : 'FALSE';
        }

        $form["email"] = filter_var($form["email"], FILTER_SANITIZE_EMAIL);
    }
}