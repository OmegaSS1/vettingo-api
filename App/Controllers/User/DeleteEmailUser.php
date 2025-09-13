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
        $id = $this->args["id"];
        $totalEmails = $this->iUserEmailRepository->findByUserId($this->USER->sub);
        $totalEmails = is_array($totalEmails) ? $totalEmails : [$totalEmails];

        if(!$id = filter_var($id, FILTER_VALIDATE_INT)){
            throw new Exception("ID do email inválido", 400);
        }
        else if(!$email = $this->iUserEmailRepository->findById($id)){
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