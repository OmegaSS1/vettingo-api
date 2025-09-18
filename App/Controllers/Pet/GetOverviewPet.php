<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class GetOverviewPet extends PetAction {

    protected function action(): Response {
        $pet = $this->validate();
        
        $data = [
            "document" => $this->iPetDocumentRepository->findTotal($pet->getId()),
            "vaccine" => $this->iPetVaccineRepository->findTotal($pet->getId()),
            "schedules" => $this->iPetConsultRepository->findTotal($pet->getId()),
        ];

        return $this->respondWithData($data);
    }

    private function validate(){
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        if(!$id || !$pet = $this->iPetRepository->findById($id)){
            throw MessageException::PET_NOT_FOUND(null);
        }
        else if($pet->getOwnerId() != $this->USER->sub){
            throw new Exception("O pet selecionado não pertence a este tutor", 404);
        }
        
        return $pet;
    }
}