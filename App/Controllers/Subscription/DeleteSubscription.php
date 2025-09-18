<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteSubscription extends SubscriptionAction {

    protected function action(): Response {
        $form = $this->post();

        $this->iDatabaseRepository->disableCommit();
        
        $userSubscription = $this->validate($form);
        $payload = [
            "canceled_at" => date("Y-m-d H:i:s")
        ];

        try {
            if($form["cancelAtPeriodEnd"]){
                $this->stripe->updateSubscription($userSubscription->getStripeSubscriptionId(), true);
                $payload['cancel_at_period_end'] = 'TRUE';
            }
            else {
                $this->stripe->cancelSubscription($userSubscription->getStripeSubscriptionId());
                $payload['cancel_at_period_end'] = 'FALSE';
                $payload['status'] = 'CANCELED';
            }

            $userSubscription = $this->iUserSubscriptionRepository->update($payload, "id = {$userSubscription->getId()}");

        } catch (Exception $e) {
            $this->loggerInterface->error("Falha ao cancelar assinatura", ["message" => $e->getMessage(), "code" => $e->getCode(), "file" => $e->getFile(), "line" => $e->getLine()]);
            throw new Exception("Falha ao cancelar a assinatura", 500);
        }
        $this->iDatabaseRepository->commit();

        $this->toArray($userSubscription);
        return $this->respondWithData($userSubscription);
    }

    private function validate(array &$form){
        $userId = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$userId || !$this->iUserRepository->findById($userId)) {
            throw MessageException::USER_NOT_FOUND(null);
        }
        else if(!$userSubscription = $this->iUserSubscriptionRepository->findByUserId($userId)){
            throw new Exception("Usuário não possui assinatura ativa");
        }
        else if(!$userSubscription->getStripeSubscriptionId()){
            throw new Exception("Assinatura não possui ID do Stripe");
        }

        $form["cancelAtPeriodEnd"] = (bool) $form["cancelAtPeriodEnd"] !== false ? true : false;
        return $userSubscription;
    }
}