<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use Psr\Http\Message\ResponseInterface as Response;

class InsertUserSubscription extends SubscriptionAction {

    protected function action(): Response {
        $form = $this->post();
        //$paymentMethod = $this->stripe->createPaymentMethod("cus_T2Snj6MTfc1l1H");

        return $this->respondWithData([]);
    }
}