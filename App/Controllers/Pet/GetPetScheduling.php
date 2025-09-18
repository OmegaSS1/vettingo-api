<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class GetPetScheduling extends PetAction {

    protected function action(): Response {
        $petId = $this->validate();

        $pet = $this->iPetConsultRepository->findByPetId($petId);
        $pet = is_array($pet) ? $pet : [$pet];

        $this->toArray($pet);
        return $this->respondWithData($pet);
    }

    private function validate(){
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $userId = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$userId || !$user = $this->iUserRepository->findById($userId)){
            throw MessageException::USER_NOT_FOUND($userId ?? null);
        }
        else if(!$user->getIsActive()){
            throw MessageException::USER_NOT_FOUND($userId ?? null);
        }
        else if(!$id || !$pet = $this->iPetRepository->findById($id)){
            throw MessageException::PET_NOT_FOUND($id ?? null);
        }
        else if($pet->getOwnerId() != $userId){
            throw new Exception("O pet não pertence a este tutor", 500);
        }

        return $id;
    }
}