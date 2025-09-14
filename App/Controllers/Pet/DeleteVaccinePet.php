<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteVaccinePet extends PetAction {

    protected function action(): Response {
        $petVaccine = $this->validate();

        $this->iPetVaccineRepository->update([
            "deleted_at"=> date("Y-m-d H:i:s")
            
        ], "id = {$petVaccine->getId()}");


        return $this->respondWithData([]);
    }

    private function validate(){
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!$id || !$petVaccine = $this->iPetVaccineRepository->findById($id)){
            throw MessageException::PET_VACCINE_NOT_FOUND();
        }
        else if(!$pet = $this->iPetRepository->findById($petVaccine->getPetId())){
            throw MessageException::PET_NOT_FOUND(null);
        }
        else if($pet->getOwnerId() != $this->USER->sub){
            throw new Exception("O pet selecionado não pertence a este tutor", 400);
        }

        return $petVaccine;
    }
}