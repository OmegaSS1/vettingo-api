<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class InsertDocumentPet extends PetAction {

    protected function action(): Response {
        $form = $this->post();
        $pet = $this->validate($form);

        $this->iDatabaseRepository->disableCommit();

        if($form["document"]){
            try {
                $form["document"] = $this->vettingoBucket->upload($form['filename'], $form['decodedImg']);
            } catch (Exception $e) {
                $this->loggerInterface->info('(DO S3 AWS)', array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine()));
                throw new Exception("Falha interna ao salvar imagem.", 500);
            }
        }

        try {
            $document = $this->iPetDocumentRepository->insert([
                "title" => $form["title"],
                "pet_id" => $pet->getId(),
                "document" => $form["document"],
                "document_length" => $form["filesize"],
                "created_at" => date("Y-m-d H:i:s"),
            ]);

        } catch (Exception $e) {
            if($form["document"])
                $this->vettingoBucket->delete($form["document"]);

            throw new Exception('Falha ao tentar cadastrar o documento', 400);

        }

        $this->iDatabaseRepository->commit();

        $this->toArray($document);
        return $this->respondWithData($document);
    }

    private function validate(array &$form){
        $this->validKeysForm($form, 
        ["title","document"],
        ["Titulo","Documento"]);

        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$id || !$pet = $this->iPetRepository->findById($id)){
            throw MessageException::PET_NOT_FOUND(null);
        }
        else if($pet->getOwnerId() != $this->USER->sub){
            throw new Exception("O pet selecionado não pertence a este tutor", 400);
        }

        if($form["document"]){
            $filename = "pet-" . $this->USER->sub . "-" . time();
            [$filename, $decodedImg, $filesize] =  $this->decodeBase64($form["document"], $filename);
            $folder = "documents/pet/";

            $filename = $folder . $filename;

            $form["decodedImg"] = $decodedImg;
            $form["filename"] = $filename;
            $form["filesize"] = $filesize;
        }

        return $pet;
    }
}