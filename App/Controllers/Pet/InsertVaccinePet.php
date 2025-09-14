<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class InsertVaccinePet extends PetAction {

    protected function action(): Response {
        $form = $this->post();
        $pet = $this->validate($form);

        $document = $this->iPetVaccineRepository->insert([
            "vaccine_name" => $form["vaccineName"],
            "pet_id" => $pet->getId(),
            "vaccination_date" => $form["vaccinationDate"],
            "next_due_date" => $form["nextDueDate"],
            "vet_id" => $form["vetId"],
            "batch_number" => $form["batchNumber"],
            "status" => $form["status"],
            "notes" => $form["notes"]
        ]);
 
        $this->toArray($document);
        return $this->respondWithData($document);
    }

    private function validate(array &$form){
        $this->validKeysForm($form, 
        ["vaccineName","vaccinationDate"],
        ["Titulo","Data da aplicação"]);

        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$id || !$pet = $this->iPetRepository->findById($id)){
            throw MessageException::PET_NOT_FOUND(null);
        }
        else if($pet->getOwnerId() != $this->USER->sub){
            throw new Exception("O pet selecionado não pertence a este tutor", 400);
        }

        try {
            $now = new DateTime();

            $form["vaccinationDate"] = $this->convertDate($form["vaccinationDate"]);
            $vaccinationDate = new DateTime($form["vaccinationDate"]);

            if($form["nextDueDate"]){
                $this->convertDate($form["nextDueDate"]);
                $nextDueDate = new DateTime($form["nextDueDate"]);
            }else 
                $form["nextDueDate"] = null;

        } catch (Exception $e) {
            throw new Exception("Data de vacinação inválida", 400);
        }

        if($vaccinationDate && $nextDueDate){
            if($vaccinationDate > $nextDueDate){
                throw new Exception("A data de aplicação não pode ser maior que a data da proxima vacinação.", 400);
            }
        }
        if($vaccinationDate > $now){
                throw new Exception("A data de aplicação não pode ser maior que hoje.", 400);
        }

        $vetId = filter_var($form["vetId"], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if($vetId && !$this->iVeterinarianRepository->findById($vetId)){
            throw MessageException::VETERINARIAN_NOT_FOUND(null);
        }
        else if(strlen($form["notes"]) > 2000) {
            throw new Exception("O campo de observação é muito grande.", 400);
        }

        $statusVaccine = $this->iPetVaccineRepository->findAllStatusVaccine();
        $status = [];
        foreach ($statusVaccine as $s) {
            $status[] = $s["status"];
        }

        if(!in_array($form["status"], $status)){
            throw new Exception("O status definido não é válido.", 400);
        }

        $form["vaccineName"] = trim($form["vaccineName"]);
        $form["batchNumber"] = trim($form["batchNumber"]);
        $form["status"] = trim($form["status"]);
        $form["notes"] = trim($form["notes"]);

        return $pet;
    }
}