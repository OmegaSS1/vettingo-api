<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class GetVetSchedules extends VeterinarianAction {

    protected function action(): Response {
        $vet = $this->validate();

        $data = $this->iPetConsultRepository->findByVetId($vet->getId());
        $data = is_array($data) ? $data : [$data];

        return $this->respondWithData($data);
    }

    private function validate(){
        $id = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        if(!$id || !$user = $this->iUserRepository->findById($id)){
            throw MessageException::USER_NOT_FOUND($id ?? null);
        }
        else if(!$user->getIsActive()){
            throw MessageException::USER_NOT_FOUND($id ?? null);
        }
        else if(!$vet = $this->iVeterinarianRepository->findByUserId($id)){
            throw MessageException::USER_NOT_FOUND($vet->getId() ?? null);
        }

        return $vet;
    }
}