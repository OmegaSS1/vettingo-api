<?php

declare(strict_types=1);
namespace App\Controllers\User;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UpdatePhoneUser extends UserAction {

    protected function action(): Response {
        $form = $this->post();
        $id = $this->validate($form);

        $this->iUserPhoneRepository->update([
            '"isPublic"' => $form["isPublic"],
            '"isActive"' => $form["isActive"],
            '"isPrimary"' => $form["isPrimary"],
            '"isWhatsapp"' => $form["isWhatsapp"] ?? "FALSE"
        ], "id = $id");

        $user = $this->iUserPhoneRepository->findByUserId($this->USER->sub);
        $this->toArray($user);
        
        return $this->respondWithData($user);
    }

    private function validate(&$form){
        $this->validKeysForm($form, 
        ["isActive", "isPublic", "isPrimary"],
    ["Ativo", "Público", "Principal"]);

        $id = $this->args["id"];
        
        if(!$id = filter_var($id, FILTER_VALIDATE_INT)){
            throw new Exception("ID do telefone inválido", 400);
        }
        else if(!$phone = $this->iUserPhoneRepository->findById($id)){
            throw new Exception("Telefone não encontrado", 404);
        } 
        else if($phone->getUserId() != $this->USER->sub){
            throw new Exception("Telefone não pertence ao usuário", 400);
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