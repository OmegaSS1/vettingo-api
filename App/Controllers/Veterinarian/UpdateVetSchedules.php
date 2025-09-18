<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateVetSchedules extends VeterinarianAction {

    private const STATUS = ["AGENDADO","CANCELADO","CONCLUÍDO"];
    
    protected function action(): Response {
        $form = $this->post();
        $schedules = $this->validate($form);

        $data = $this->iPetConsultRepository->update([
            "status" => $form["status"],
            "updated_at" => date("Y-m-d H:i:s")
        ], "id = {$schedules->getId()}");

        $this->toArray($data);
        return $this->respondWithData($data);
    }

    private function validate(array &$form){
        $this->validKeysForm($form, ["status"], ["Status"]);

        $id = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $idSchedules = filter_var($this->getArg('id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        if(!$id || !$user = $this->iUserRepository->findById($id)){
            throw MessageException::USER_NOT_FOUND($id ?? null);
        }
        else if(!$user->getIsActive()){
            throw MessageException::USER_NOT_FOUND($id ?? null);
        }
        else if(!$vet = $this->iVeterinarianRepository->findByUserId($id)){
            throw MessageException::USER_NOT_FOUND($vet->getId() ?? null);
        }
        else if(!$idSchedules || !$schedules = $this->iPetConsultRepository->findById($idSchedules)){
            throw new Exception("Não foi possivel localizar o agendamento.", 400);
        }
        else if($schedules->getVetId() != $vet->getId()){
            throw new Exception("O agendamento não pertence a este veterinário.", 400);
        }
        else if($schedules->getStatus() != "AGUARDANDO"){
            throw new Exception("Não é possivel atualizar um agendamento com o status {$schedules->getStatus()}", 400);
        }
        else if(!in_array($form["status"], self::STATUS)){
            throw new Exception("Não é possível atualizar o status para {$form["status"]}", 400);
        }

        return $schedules;
    }
}