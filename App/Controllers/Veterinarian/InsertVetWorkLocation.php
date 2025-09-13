<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class InsertVetWorkLocation extends VeterinarianAction {

    protected function action(): Response {
        $form = $this->post();
        $id = $this->validate($form);

        $vetWork = $this->iVetWorkLocationRepository->insert([
            "veterinarian_id" => $id,
            "name" => $form["name"],
            "state_id" => $form["stateId"],
            "city_id" => $form["cityId"],
            "address" => $form["address"],
            "number" => $form["number"],
            "neighborhood" => $form["neighborhood"],
            "zip_code" => $form["zipCode"],
            '"isActive"' => 'TRUE',
            "complement" => $form["complement"] ?? "",
            "latitude" => $form["latitude"] ?? NULL,
            "longitude" => $form["longitude"] ?? NULL
        ]);

        $this->toArray($vetWork);
         
        return $this->respondWithData($vetWork);
    }

    private function validate(&$form) {
        $this->validKeysForm($form, 
        ["name","stateId","cityId","address","number","neighborhood","zipCode"],
        ["Nome do Local","Estado","Cidade","Endereço","Numero","Bairro","CEP"]);

        $stateId = filter_var($form["stateId"], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $cityId = filter_var($form["cityId"], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        if(!$vet = $this->iVeterinarianRepository->findByUserId((int) $this->USER->sub)){
            throw MessageException::VETERINARIAN_NOT_FOUND($this->USER->sub);
        }
        else if(is_null($stateId)){
            throw MessageException::STATE_NOT_FOUND($form["stateId"]);
        }
        else if(!$this->iStateRepository->findById($form["stateId"])){
            throw MessageException::STATE_NOT_FOUND($stateId);
        }
        else if(is_null($cityId)){
            throw MessageException::CITY_NOT_FOUND($form["cityId"]);
        }
        else if(!$city = $this->iCityRepository->findById($cityId)){
            throw MessageException::STATE_NOT_FOUND($stateId);
        }
        else if($city->getStateId() != $stateId){
            throw new Exception("A cidade não pertence ao estado selecionado", 400);
        }
        else if(!preg_match("/^\d{5}-?\d{3}$/", $form["zipCode"])){
            throw MessageException::CEP_INVALIDO();
        }
        else if(!preg_match("/^(-?[0-8]?\d(\.\d+)?|90(\.0+)?)$/", $form["latitude"])){
            throw MessageException::LATITUDE_INVALID();
        }
        else if(!preg_match("/^(-?(1[0-7]\d|[0-9]?\d)(\.\d+)?|180(\.0+)?)$/", $form["longitude"])){
            throw MessageException::LONGITUDE_INVALID();
        }

        $form["stateId"] = $stateId;
        $form["cityId"] = $cityId;
        $form["zipCode"] = preg_replace("/\D/", "", $form["zipCode"]);
        return $vet->getId();
    }
}