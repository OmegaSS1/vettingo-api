<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateVeterinarian extends VeterinarianAction {

    protected function action(): Response {
        $form = $this->post();
        $id = $this->validate($form);

        $this->iVeterinarianRepository->update([
            "bio" => $form["bio"],
            "website" => $form["website"],
            "crmv" => $form["crmv"],
            "crmv_state_id" => $form["crmvStateId"],
            "phone_id" => $form["phoneId"],
            "professional_email_id" => $form["professionalEmailId"],
            "avatar" => $form["avatar"],
            "profile_photos" => $form["profilePhotos"],
            "emergencial_attendance" => $form["providesEmergencyService"] === true ? 'TRUE' : 'FALSE',
            "domiciliary_attendance" => $form["providesHomeService"] === true ? 'TRUE' : 'FALSE',
        ], "id = $id");

        return $this->respondWithData($form);
    }

    private function validate(array $form){
        $this->validKeysForm($form,
    ["bio","crmv","crmvStateId","phoneId","professionalEmailId","providesEmergencyService","providesHomeService"],
["Biografia","CRMV","Estado do CRMV","Telefone Profissional","Email Profissional","Atendo Emergências","Atendo em Domicílio"]);

        if(!$vet = $this->iVeterinarianRepository->findByUserId($this->USER->sub)){
            throw new Exception("Nenhum veterinário encontrado para o usuário informado", 404);
        }
        else if(!preg_match("/^\d{3,7}$/", $form["crmv"])){
            throw MessageException::CRMV();
        }
        else if(!$form["crmvStateId"] = filter_var($form["crmvStateId"], FILTER_VALIDATE_INT)){
            throw MessageException::CRMV_STATE_ID();
        }
        else if(!$this->iStateRepository->findById($form["crmvStateId"])){
            throw MessageException::CRMV_STATE_ID();
        }
        else if($this->iVeterinarianRepository->findByCrmv(trim($form["crmv"]), $form["crmvStateId"])){
            throw new Exception("CRMV já cadastrado no sistema",400);
        }
        else if(!$email = $this->iUserEmailRepository->findById($form["professionalEmailId"])){
            throw new Exception("O email informado não foi localizado.", 404);
        }
        else if($email->getUserId() != $this->USER->sub){
            throw new Exception("O email informado não pertence a este profissional.", 400);
        }
        else if(!$phone = $this->iUserPhoneRepository->findById($form["phoneId"])){
            throw new Exception("O telefone informado não foi localizado.", 404);
        }
        else if($phone->getUserId() != $this->USER->sub){
            throw new Exception("O telefone informado não pertence a este profissional.", 400);
        }
        else if(is_null(filter_var($form["providesEmergencyService"], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE))){
            throw new Exception("O valor informado do campo 'Atendo emergências' não é válido.", 400);
        }
        else if(is_null(filter_var($form["providesHomeService"], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE))){
            throw new Exception("O valor informado do campo 'Atendo em domicilio' não é válido.", 400);
        }
        else if(strlen($form["bio"]) < 10){
            throw new Exception("A biografia precisa ter no mínimo 10 caracteres.", 400);
        }

        return $vet->getId();
    }
}