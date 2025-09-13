<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;

class InsertUserPaymentMethod extends SubscriptionAction {

    protected function action(): Response {
        $user = $this->validate();
        $paymentMethod = $this->stripe->createPaymentMethod($user->getStripeCustomerId());

        return $this->respondWithData(["clientSecret" => $paymentMethod->client_secret]);
    }

    private function validate(){
        $user = $this->iUserRepository->findById($this->USER->sub);

        if(!$user){
            throw MessageException::USER_NOT_FOUND(null);
        }

        return $user;
    }
}