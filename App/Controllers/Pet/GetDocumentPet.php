<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class GetDocumentPet extends PetAction {

    protected function action(): Response {
        $pet = $this->validate();

        $documents = $this->iPetDocumentRepository->findByPetId($pet->getId());
        $documents = is_array($documents) ? $documents : [$documents];
        $this->toArray($documents);

        return $this->respondWithData($documents);
    }

    private function validate(){
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!$id || !$pet = $this->iPetRepository->findById($id)){
            throw MessageException::PET_NOT_FOUND(null);
        }
        else if($pet->getOwnerId() != $this->USER->sub){
            throw new Exception("O pet selecionado não pertence a este tutor", 400);
        }

        return $pet;
    }
}