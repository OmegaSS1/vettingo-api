<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;


class DeletePet extends PetAction {

    protected function action(): Response {
        $pet = $this->validate();

        $pet = $this->iPetRepository->update([
            '"isActive"' => 'FALSE',
            "deleted_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        ], "id = {$pet->getId()}");


        return $this->respondWithData([]);
    }

    private function validate(){
        $idPet = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$idPet || !$pet = $this->iPetRepository->findById($idPet)){
            throw MessageException::PET_NOT_FOUND(null);
        }
        else if($pet->getOwnerId() != $this->USER->sub){
            throw new Exception("O pet selecionado não faz parte desse tutor.", 400);
        }

        return $pet;
 	}
}