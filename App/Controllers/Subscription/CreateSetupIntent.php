<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class CreateSetupIntent extends SubscriptionAction {

    protected function action(): Response {
        $form = $this->post();
        $this->validate($form);
        
        try {
            $setupIntent = $this->stripe->createSetupIntent($form["metadata"]);
        } catch (Exception $e) {
            throw new Exception("Erro ao criar SetupIntent", 500);
        }

        return $this->respondWithData(["success" => true, "clientSecret" => $setupIntent->client_secret, "setupIntentId" => $setupIntent->id]);
    }

    private function validate(array $form){

        $user = $this->iUserRepository->findById($this->USER->sub);

        if(!$form["metadata"]){
            throw new Exception("Dados necessarias não foram informados", 400);
        }
        else if(!$user){
            throw MessageException::USER_NOT_FOUND(null);
        }

        if($form["metadata"]["userId"]){
            if((int) $form["userId"] != $this->USER->sub){
                throw new Exception("O usuario do cartão não é o mesmo usuário da sessao", 400);
            }
        }
    }
}