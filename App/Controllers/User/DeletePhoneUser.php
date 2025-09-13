<?php

declare(strict_types=1);
namespace App\Controllers\User;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class DeletePhoneUser extends UserAction {

    protected function action(): Response {
        $id = $this->validate($form);
        $this->iUserPhoneRepository->update([
            '"isActive"' => 'FALSE',
            "deleted_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        ], "id = $id");

        return $this->respondWithData([]);
    }

    private function validate(&$form){
        $id = $this->args["id"];
        $totalPhones = $this->iUserPhoneRepository->findByUserId($this->USER->sub);
        $totalPhones = is_array($totalPhones) ? $totalPhones : [$totalPhones];

        if(!$id = filter_var($id, FILTER_VALIDATE_INT)){
            throw new Exception("ID do telefone inválido", 400);
        }
        else if(!$phone = $this->iUserPhoneRepository->findById($id)){
            throw new Exception("Telfone não encontrado", 404);
        } 
        else if($phone->getUserId() != $this->USER->sub){
            throw new Exception("Telfone não pertence ao usuário", 400);
        }
        else if(count($totalPhones) <= 1){
            throw new Exception("Usuário deve ter pelo menos um telefone ativo", 400);
        }
        else if($phone->getIsPrimary() === true){
            throw new Exception("Não é possível deletar um telefone primário", 400);
        }

        return $id;
    }
}