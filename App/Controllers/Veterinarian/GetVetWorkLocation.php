<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class GetVetWorkLocation extends VeterinarianAction {

    protected function action(): Response {
        $args = $this->request->getQueryParams();
        $options = [
            "active" => $args["active"] ?? true,
            "stateId" => $args["stateId"] ?? "",
            "cityId" => $args["cityId"] ?? "",
            "orderBy" => $args["orderBy"] ?? "name",
            "orderDirection" => $args["orderDirection"] ?? "ASC"
        ];

        if(!$vet = $this->iVeterinarianRepository->findByUserId($this->USER->sub))
            throw new Exception("Nenhum veterinario localizado!", 404);

        $vetWorkLocation = $this->iVetWorkLocationRepository->findByVeterinarianId($vet->getId(), $options);
        $vetWorkLocation = is_array($vetWorkLocation) ? $vetWorkLocation : [$vetWorkLocation];
        $this->toArray($vetWorkLocation);
        
        return $this->respondWithData($vetWorkLocation);
    }
}