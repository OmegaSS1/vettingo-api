<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class GetVetWorkLocationSchedule extends VeterinarianAction {

    protected function action(): Response {
        $id = $this->validate();
        
        $args = $this->request->getQueryParams();
        $options = [
            "active" => $args["active"] ?? true,
            "orderBy" => $args["orderBy"] ?? "dayOfWeek",
            "orderDirection" => $args["orderDirection"] ?? "ASC"
        ];

        if(!$vetWork = $this->iVetWorkLocationRepository->findById($id))
            throw new Exception("Nenhum veterinario localizado!", 404);

        $vetWorkLocationSchedules = $this->iVetWorkLocationScheduleRepository->findByVetWorkLocationId($vetWork->getId(), $options);
        $vetWorkLocationSchedules = is_array($vetWorkLocationSchedules) ? $vetWorkLocationSchedules : [$vetWorkLocationSchedules];
        $this->toArray($vetWorkLocationSchedules);
        
        return $this->respondWithData($vetWorkLocationSchedules);
    }

    private function validate(){
        $id = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!$id){
            throw new Exception('Horarios de trabalho não localizados.', 404);
        }

        return $id;
    }
}