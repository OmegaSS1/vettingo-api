<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class VeterinarianApprovalStatus extends VeterinarianAction {

    protected function action(): Response {
        $id = $this->validate();
        
        $vet = $this->iVeterinarianApprovalPendingRepository->findByVeterinarianId($id);
        $this->toArray($vet);
        
        return $this->respondWithData($vet);
    }

    private function validate(){
        $id = $this->args["id"];

        if(!$id = filter_var($id, FILTER_VALIDATE_INT)){
            throw new Exception('ID do veterinário é obrigatório e deve ser positivo', 400);
        }

        return $id;
    }
}