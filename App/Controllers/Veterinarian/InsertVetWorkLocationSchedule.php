<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class InsertVetWorkLocationSchedule extends VeterinarianAction {

    protected function action(): Response {
        $form = $this->post();
        $id = $this->validate($form);
        
        if($existingSchedule = $this->iVetWorkLocationScheduleRepository->findExistingSchedule($id, $form["dayOfWeek"])){
            if($existingSchedule->getIsActive()){
                throw new Exception("Ja existe um agendamento para este dia", 400);
            }
            $this->iVetWorkLocationScheduleRepository->update([
                '"isActive"' => 'TRUE',
                "deleted_at" => NULL,
                "updated_at" => date("Y-m-d H:i:s")
            ], "id = {$existingSchedule->getId()}");

            $form["id"] = $existingSchedule->getId();
            $form["vetWorkLocationId"] = $existingSchedule->getVetWorkLocationId();
        }
        else {        
            $lastId = $this->iVetWorkLocationScheduleRepository->insert([
                "vet_work_location_id" => $id,
                "day_of_week" => $form["dayOfWeek"],
                "start_time" => $form["startTime"],
                "end_time" => $form["endTime"],
                '"isActive"' => 'TRUE',
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s')
            ]);

            $form["id"] = $lastId;
            $form["vetWorkLocationId"] = $id;

        }

        $form["isActive"] = true;
        $form["createdAt"] = date("Y-m-d H:i:s");
        $form["updatedAt"] = date("Y-m-d H:i:s");

        return $this->respondWithData($form);
    }

    private function validate(&$form){
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        $this->validKeysForm($form,
        ["dayOfWeek","startTime","endTime"],
        ["Dia da Semana", "Horario inicial", "Horario final"]);
        

        if(!$id){
            throw new Exception('Horarios de trabalho não localizados.', 404);
        }
        else if(!in_array($form["dayOfWeek"], [0,1,2,3,4,5,6])){
            throw MessageException::DAY_OF_WEEK_INVALID();
        }
        else if(!$this->iVetWorkLocationRepository->findById($id)){
            throw MessageException::VETERINARIAN_WORK_LOCATION_NOT_FOUND();
        }
        else if(!$this->validateTime($form["startTime"]) || !$this->validateTime($form["endTime"])){
            throw MessageException::TIME_INVALID();
        }
        else if($form["startTime"] > $form["endTime"]){
            throw MessageException::START_TIME_BEFORE_END_TIME();
        }

        return $id;
    }
}