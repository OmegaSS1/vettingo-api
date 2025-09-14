<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class GetPetByOwner extends PetAction {

    protected function action(): Response {
        $owner = $this->validate();
        
        $pets = $this->iPetRepository->findByOwnerId($owner->getId());
        $pets = is_array($pets) ? $pets : [$pets];
        $this->toArray($pets);

        return $this->respondWithData($pets);
    }

    private function validate(){
        $id = filter_var($this->getArg('ownerid'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!$id || !$owner = $this->iUserRepository->findById($id)){
            throw new Exception("Nenhum pet localizado para este tutor", 404);
        }

        return $owner;
    }
}