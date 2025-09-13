<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateVetWorkLocationSchedule extends VeterinarianAction {

    protected function action(): Response {
        $form = $this->post();
        $id = $this->validate($form);

        $schedule = $this->iVetWorkLocationScheduleRepository->update($form,"id = $id");

        $this->toArray($schedule);
        return $this->respondWithData($schedule);
    }

    private function validate(&$form){
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        foreach($form as $key => $value) {
            $message = "";
            switch($key) {
                case "startTime":
                    $message = "Horario inicial";
                    $form["start_time"] = $form["startTime"];
                    break;
                case "endTime":
                    $message = "Horario final";
                    $form["end_time"] = $form["endTime"];
                    break;
                case "isActive":
                    $form['"isActive"'] = $form["isActive"] === false ? "FALSE" : "TRUE";
                    $form["deleted_at"] = date("Y-m-d H:i:s");
            }
            $this->validKeysForm($form,
            [$key],
            [$message]);
        }

        if(!$id){
            throw new Exception('Horarios de trabalho não localizados.', 404);
        }
        else if(!$schedule = $this->iVetWorkLocationScheduleRepository->findById($id)){
            throw MessageException::VETERINARIAN_WORK_LOCATION_NOT_FOUND();
        }
        else if(isset($form["startTime"])){ 
            if(!$this->validateTime($form["startTime"]))
                throw MessageException::TIME_INVALID();
            else if($schedule->getEndTime() < $form["startTime"])
                throw MessageException::START_TIME_BEFORE_END_TIME();
        }
        else if(isset($form["endTime"])){ 
            if(!$this->validateTime($form["endTime"]))
                throw MessageException::TIME_INVALID();
            else if($schedule->getStartTime() > $form["endTime"])
                throw MessageException::START_TIME_BEFORE_END_TIME();
        }

        unset($form["startTime"], $form["endTime"], $form["isActive"]);
        $form["updated_at"] = date("Y-m-d H:i:s");

        return $id;
    }
}