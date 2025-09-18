<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class GetVetDashboard extends VeterinarianAction {

    protected function action(): Response {
        [$id, $cityId] = $this->validate();

        $data["veterinarian"] = $this->iVeterinarianRepository->findPreviewDashboard($id, $cityId);
        $data["review"] = $this->iVetReviewRepository->findByVeterinarianId($id);

        return $this->respondWithData($data);
    }

    private function validate(){
        $args = $this->request->getQueryParams();

        $id = filter_var($args['vetId'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $cityId = filter_var($args['cityId'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        if(!$id || !$this->iUserRepository->findById($id)){
            throw MessageException::USER_NOT_FOUND($id ?? null);
        }
        else if(!$this->iVeterinarianRepository->findByUserId($id)){
            throw MessageException::USER_NOT_FOUND($id ?? null);
        }
        else if(!$cityId || !$this->iCityRepository->findById($cityId)){
            throw new Exception("Cidade não localizada", 404);
        }

        return [$id, $cityId];
    }
}