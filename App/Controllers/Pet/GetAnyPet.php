<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;

class GetAnyPet extends PetAction {

    protected function action(): Response {
        $id = $this->validate();
        
        $pets = $this->iPetRepository->findById($id);
        $pets = is_array($pets) ? $pets : [$pets];
        $this->toArray($pets);

        return $this->respondWithData($pets);
    }

    private function validate(){
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!$id){
            throw MessageException::PET_NOT_FOUND(null);
        }

        return $id;
    }
}