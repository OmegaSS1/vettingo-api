<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;

class GetUserPet extends PetAction {

    protected function action(): Response {
        $this->validate();
        
        $pets = $this->iPetRepository->findByOwnerId($this->USER->sub);
        $pets = is_array($pets) ? $pets : [$pets];
        $this->toArray($pets);

        return $this->respondWithData($pets);
    }

    private function validate(){
        $id = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!$id || !$this->iUserRepository->findById($id)){
            throw MessageException::TUTOR_NOT_FOUND(null);
        }
    }
}