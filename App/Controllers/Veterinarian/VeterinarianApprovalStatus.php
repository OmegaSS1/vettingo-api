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
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$id){
            throw new Exception('ID do veterinário é obrigatório e deve ser positivo', 400);
        }

        return $id;
    }
}