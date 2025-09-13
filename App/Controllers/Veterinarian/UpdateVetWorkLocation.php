<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateVetWorkLocation extends VeterinarianAction {

    protected function action(): Response {
        $form = $this->post();
        $id = $this->validate($form);

        $this->iVetWorkLocationRepository->update([
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
        ], "id = $id", "\"isActive\" = TRUE AND deleted_at IS NULL");

        $vet = $this->iVetWorkLocationRepository->findById($id);
        $this->toArray($vet);
         
        return $this->respondWithData($vet);
    }

    private function validate(&$form) {
        $id = filter_var($this->getArg("id"), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        $this->validKeysForm($form, 
        ["name","stateId","cityId","address","number","neighborhood","zipCode"],
        ["Nome do Local","Estado","Cidade","Endereço","Numero","Bairro","CEP"]);

        $stateId = filter_var($form["stateId"], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $cityId = filter_var($form["cityId"], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        $vet = $this->iVeterinarianRepository->findByUserId($this->USER->sub);

        if(is_null($id)) {
            throw new Exception("Local de trabalho não encontrado", 404);
        }
        else if(!$vetWork = $this->iVetWorkLocationRepository->findById($id)){
            throw new Exception("Local de trabalho não encontrado", 404);
        }
        else if($vetWork->getVeterinarianId() != $vet->getId()) {
            throw new Exception("O local de trabalho não pertence a este usuário.", 400);
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

        $form["stateId"] = $stateId;
        $form["cityId"] = $cityId;
        $form["latitude"] = filter_var($form["latitude"], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
        $form["longitude"] = filter_var($form["longitude"], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
        $form["zipCode"] = preg_replace("/\D/", "", $form["zipCode"]);
        return $id;
    }
}