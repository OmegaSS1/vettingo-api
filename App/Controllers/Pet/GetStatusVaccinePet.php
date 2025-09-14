<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use Psr\Http\Message\ResponseInterface as Response;

class GetStatusVaccinePet extends PetAction {

    protected function action(): Response {
        $status = $this->iPetVaccineRepository->findAllStatusVaccine();
        $toArr = [];
        foreach ($status as $s) {
            $toArr[] = $s["status"];
        }
        return $this->respondWithData($toArr);
    }
}