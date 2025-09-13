<?php

declare(strict_types=1);
namespace App\Controllers\User;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateEmailUser extends UserAction {

    protected function action(): Response {
        $form = $this->post();
        $id = $this->validate($form);

        $user = $this->iUserEmailRepository->update([
            '"isPublic"' => $form["isPublic"],
            '"isActive"' => $form["isActive"],
            '"isPrimary"' => $form["isPrimary"],
        ], "id = $id");

        $this->toArray($user);
        
        return $this->respondWithData($user);
    }

    private function validate(&$form){
        $this->validKeysForm($form, 
        ["isActive", "isPublic", "isPrimary"],
    ["Ativo", "Público", "Principal"]);
        
        $id = filter_var($this->getArg("id"), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        if(!$id || !$email = $this->iUserEmailRepository->findById($id)){
            throw new Exception("Email não encontrado", 404);
        } 
        else if($email->getUserId() != $this->USER->sub){
            throw new Exception("Email não pertence ao usuário", 400);
        }

        $acceptValues = [true, false];
        foreach($form as $k => $v){
            if(!in_array($v, $acceptValues)){
                throw new Exception("O valor informado está inválido.", 400);
            }
            $form[$k] = $v === true ? 'TRUE' : 'FALSE';
        }

        return $id;
    }
}