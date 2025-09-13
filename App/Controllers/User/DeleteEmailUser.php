<?php

declare(strict_types=1);
namespace App\Controllers\User;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteEmailUser extends UserAction {

    protected function action(): Response {
        $id = $this->validate($form);
        $this->iUserEmailRepository->update([
            '"isActive"' => 'FALSE',
            "deleted_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        ], "id = $id");

        return $this->respondWithData([]);
    }

    private function validate(&$form){
        $totalEmails = $this->iUserEmailRepository->findByUserId($this->USER->sub);
        $totalEmails = is_array($totalEmails) ? $totalEmails : [$totalEmails];

        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        if(!$id || !$email = $this->iUserEmailRepository->findById($id)){
            throw new Exception("Email não encontrado", 404);
        } 
        else if($email->getUserId() != $this->USER->sub){
            throw new Exception("Email não pertence ao usuário", 400);
        }
        else if(count($totalEmails) <= 1){
            throw new Exception("Usuário deve ter pelo menos um email ativo", 400);
        }
        else if($email->getIsPrimary() === true){
            throw new Exception("Não é possível deletar um email primário", 400);
        }

        return $id;
    }
}