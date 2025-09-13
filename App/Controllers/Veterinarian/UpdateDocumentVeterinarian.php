<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateDocumentVeterinarian extends VeterinarianAction {

    protected function action(): Response {
        $form = $this->post();
        $id = $this->validate($form);

        $this->iDatabaseRepository->disableCommit();

        try {
            $this->vettingoBucket->upload($form["rgFrontImageFilename"], $form["rgFrontImageDecodedImg"]);
            $this->vettingoBucket->upload($form["rgBackImageFilename"], $form["rgBackImageDecodedImg"]);
            $this->vettingoBucket->upload($form["crmvDocumentImageFilename"], $form["crmvDocumentImageDecodedImg"]);
        }
        catch (Exception $e) {
            $this->vettingoBucket->delete($form["rgFrontImageFilename"]);
            $this->vettingoBucket->delete($form["rgBackImageFilename"]);
            $this->vettingoBucket->delete($form["crmvDocumentImageFilename"]);
            $this->loggerInterface->info('(DO S3 AWS)', array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine()));
            throw new Exception("Falha interna ao salvar imagem.", 500);
        }

        try {
            $status = $this->iVeterinarianApprovalPendingRepository->update([
                "rg_front_image_url" => $form["rgFrontImageFilename"],
                "rg_back_image_url" => $form["rgBackImageFilename"],
                "crmv_document_image_url" => $form["crmvDocumentImageFilename"],
                "status" => 'PENDING_APPROVAL'
            ], "id = $id");
        }
        catch (Exception $e) {
            $this->vettingoBucket->delete($form["rgFrontImageFilename"]);
            $this->vettingoBucket->delete($form["rgBackImageFilename"]);
            $this->vettingoBucket->delete($form["crmvDocumentImageFilename"]);
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $this->iDatabaseRepository->commit();

        $this->toArray($status);
        return $this->respondWithData($status);
    }

    private function validate(array &$form){
        $this->validKeysForm($form,
    ["rgFrontImage","rgBackImage","crmvDocumentImage"],
["RG - Frente", "RG - Verso", "Documento CRMV"]);

        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$id || !$vet = $this->iVeterinarianApprovalPendingRepository->findByVeterinarianId($id)){
            throw new Exception("Registro de aprovação não encontrado para este veterinário", 404);
        }

        $this->transformToUpload("rg-front-".$vet->getId()."-".time(), "rg", "rgFrontImage", $form);
        $this->transformToUpload("rg-back-".$vet->getId()."-".time(), "rg", "rgBackImage", $form);
        $this->transformToUpload("crmv-document-".$vet->getId()."-".time(), "crmv", "crmvDocumentImage", $form);
        
        return $vet->getId();
    }

    private function transformToUpload(string $filename, string $folder, string $keyForm, &$form){
        [$filename, $decodedImg] =  $this->decodeBase64($form[$keyForm], $filename);

        $filename = "veterinarian-approval/$folder/$filename";

        $key1 = "$keyForm" . "DecodedImg";
        $key2 = "$keyForm" . "Filename";
        $form[$key1] = $decodedImg;
        $form[$key2] = $filename;
    }
}