<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class CreatePetSchedulingDashboard extends PetAction {

    protected function action(): Response {
        $form = $this->post();
        $vet = $this->validate($form);

        $pet = $this->iPetConsultRepository->insert([
            "pet_id" => null,
            "tutor_name" => $form["tutorName"],
            "pet_name" => $form["petName"],
            "vet_id" => $vet->getId(),
            "consultation_date" => $form["consultationDate"],
            "reason" => $form["reason"] ?? "",
            "status" => "AGUARDANDO"
        ]);

        $this->toArray($pet);
        return $this->respondWithData($pet);
    }

    private function validate(array &$form){
        $this->validKeysForm($form,
    ["tutorName","petName","consultationDate","vetWorkId","time"],
["Tutor","Pet","Data da consulta","Consultorio", "horario da consulta"]);

        $idVetWork = filter_var($form["vetWorkId"], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!$idVetWork || !$vetWork = $this->iVetWorkLocationRepository->findById($idVetWork)){
            throw new Exception("O veterinario não possui consultorio cadastrado!", 500);
        }
        else if(!$vetWorkLocationSchedule = $this->iVetWorkLocationScheduleRepository->findByVetWorkLocationId($vetWork->getId())){
            throw new Exception("O veterinário não possui agenda disponivel", 500);
        }
        else if(!$vet = $this->iVeterinarianRepository->findById($vetWork->getVeterinarianId())){
            throw MessageException::VETERINARIAN_NOT_FOUND(null);
        }
        else if(!$user = $this->iUserRepository->findById($vet->getUserId())){
            throw MessageException::USER_NOT_FOUND(null);
        }
        
        if($petConsult = $this->iPetConsultRepository->findByConsultationDate($form["consultationDate"])){
            foreach($petConsult as $consult){
                if($consult->getStatus() == "AGUARDANDO" && $consult->getVetId() == $vet->getId())
                    throw new Exception("Você já possui um agendamento marcado com este veterinario neste dia!", 400);
            }
        }
        
        $form["consultationDate"] = $this->convertDate($form["consultationDate"]);
        
        $now = (new DateTime())->format("Y-m-d");
        $dateConsultation = (new DateTime($form["consultationDate"]))->format("Y-m-d");
        if($dateConsultation < $now){
            throw new Exception("Não é possivel agendar uma consulta com a data inferior ao dia atual.", 400);
        }

        $weekToday = (int) (new DateTime($form["consultationDate"]))->format('w');

        $weekIsValid = false;
        $timeIsValid = false;
        $time = new DateTime($form["time"]);
        foreach($vetWorkLocationSchedule as $schedule){
            if($schedule->getDayOfWeek() == $weekToday){
                $weekIsValid = true;
                $start = new DateTime($schedule->getStartTime());
                $end = new DateTime($schedule->getEndTime());
                if($time >= $start && $time <= $end){
                    $timeIsValid = true;
                    break;
                }
            }
        }

        if(!$weekIsValid){
            throw new Exception("O veterinario não possui agenda disponivel na data selecionada.",400);
        }
        else if(!$timeIsValid){
            throw new Exception("O veterinario não possui o horario selecionado disponivel",400);
        }

        $form["consultationDate"] = (new DateTime("{$form["consultationDate"]} {$form["time"]}"))->format('Y-m-d H:i:s');

        return $vet;
    }
}