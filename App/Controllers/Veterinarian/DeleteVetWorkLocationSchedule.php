<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteVetWorkLocationSchedule extends VeterinarianAction {

    protected function action(): Response {
        $id = $this->validate();

        $this->iVetWorkLocationScheduleRepository->update([
            '"isActive"' => 'FALSE',
            "updated_at" => date("Y-m-d H:i:s"),
            "deleted_at" => date("Y-m-d H:i:s")
        ],"id = $id");

        $schedule = $this->iVetWorkLocationScheduleRepository->findById($id);
        $this->toArray($schedule);
        
        return $this->respondWithData($schedule);
    }

    private function validate(){
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        if(!$id){
            throw new Exception('Horarios de trabalho não localizados.', 404);
        }
        else if(!$this->iVetWorkLocationScheduleRepository->findById($id)){
            throw MessageException::VETERINARIAN_WORK_LOCATION_NOT_FOUND();
        }
        
        return $id;
    }
}