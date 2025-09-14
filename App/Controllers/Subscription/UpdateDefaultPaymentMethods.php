<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateDefaultPaymentMethods extends SubscriptionAction {

    protected function action(): Response {
        $paymentMethod = $this->validate();

        $this->iDatabaseRepository->disableCommit();

        $allPaymentMethods = $this->iPaymentMethodRepository->findByUserId($this->USER->sub);
        foreach ($allPaymentMethods as $p) {
            if($p->getId() == $paymentMethod->getId())
                $this->iPaymentMethodRepository->update(["is_default" => "FALSE"], "id = {$p->getId()}");
        }

        $payment = $this->iPaymentMethodRepository->update(["is_default" => "TRUE"], "id = {$paymentMethod->getId()}");
        
        $this->iDatabaseRepository->commit();

        $this->toArray($payment);
        return $this->respondWithData($payment);
    }

    private function validate(){    
        $id = filter_var($this->getArg("id"), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $userId = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$id || !$paymentMethod = $this->iPaymentMethodRepository->findById($id)) {
            throw new Exception("Metodo de pagamento não localizado", 400);
        }
        else if(!$userId || !$user = $this->iUserRepository->findById($userId)) {
            throw MessageException::USER_NOT_FOUND(null);
        }
        else if($user->getId() != $paymentMethod->getUserId()) {
            throw new Exception("Metodo de pagamento não pertence ao usuário", 400);
        }

        return $paymentMethod;
    }
}