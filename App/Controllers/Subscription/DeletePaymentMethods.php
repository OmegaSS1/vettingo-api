<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class DeletePaymentMethods extends SubscriptionAction {

    protected function action(): Response {
        $paymentMethod = $this->validate();

        $this->iPaymentMethodRepository->update(["deleted_at" => date('Y-m-d H:i:s')], "id = {$paymentMethod->getId()}");

        return $this->respondWithData([]);
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