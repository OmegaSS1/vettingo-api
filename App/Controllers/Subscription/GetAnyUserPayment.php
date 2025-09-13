<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;

class GetAnyUserPayment extends SubscriptionAction {

    protected function action(): Response {
        $id = $this->validate();
        $payments = $this->iPaymentRepository->findByUserId($id);
        $payments = is_array($payments) ? $payments : [$payments];
        $this->toArray($payments);

        return $this->respondWithData($payments);
    }

    private function validate(){
        $id = filter_var($this->getArg("id"), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$id){
            throw MessageException::USER_NOT_FOUND(null);
        }

        return $id;
    }
}