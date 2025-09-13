<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteVetWorkLocation extends VeterinarianAction {

    protected function action(): Response {
        $form = $this->post();
        $id = $this->validate($form);

        $this->iVetWorkLocationRepository->update([
            '"isActive"' => "FALSE",
            "deleted_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ], "id = $id", "\"isActive\" = TRUE AND deleted_at IS NULL");
         
        return $this->respondWithData($this->success("Local de trabalho excluido com sucesso"));
    }

    private function validate(&$form) {
        $id = filter_var($this->getArg("id"), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        $vet = $this->iVeterinarianRepository->findByUserId($this->USER->sub);
        
        if(is_null($id)) {
            throw new Exception("Local de trabalho não encontrado", 404);
        }
        else if(!$vetWork = $this->iVetWorkLocationRepository->findById($id)){
            throw new Exception("Local de trabalho não encontrado", 404);
        }
        else if($vetWork->getVeterinarianId() != $vet->getId()) {
            throw new Exception("O local de trabalho não pertence a este usuário.", 400);
        }

        return $id;
    }
}