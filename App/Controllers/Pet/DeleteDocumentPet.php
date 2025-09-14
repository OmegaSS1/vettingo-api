<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteDocumentPet extends PetAction {

    protected function action(): Response {
        $petDocument = $this->validate();

        $this->iDatabaseRepository->disableCommit();
        try {

            $this->iPetDocumentRepository->update([
                "deleted_at"=> date("Y-m-d H:i:s")
                
            ], "id = {$petDocument->getId()}");

            if($petDocument->getDocument()){
                $this->vettingoBucket->delete($petDocument->getDocument());
            }
        }
        catch (Exception $e) {
            throw new Exception("Falha ao remover documento", 500);
        }

        $this->iDatabaseRepository->commit();

        return $this->respondWithData([]);
    }

    private function validate(){
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!$id || !$petDocument = $this->iPetDocumentRepository->findById($id)){
            throw MessageException::PET_DOCUMENT_NOT_FOUND();
        }
        else if(!$pet = $this->iPetRepository->findById($petDocument->getPetId())){
            throw MessageException::PET_NOT_FOUND(null);
        }
        else if($pet->getOwnerId() != $this->USER->sub){
            throw new Exception("O pet selecionado não pertence a este tutor", 400);
        }

        return $petDocument;
    }
}