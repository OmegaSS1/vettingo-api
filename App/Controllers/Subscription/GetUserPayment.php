<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use Psr\Http\Message\ResponseInterface as Response;

class GetUserPayment extends SubscriptionAction {

    protected function action(): Response {

        $payments = $this->iPaymentRepository->findByUserId($this->USER->sub);
        $payments = is_array($payments) ? $payments : [$payments];
        $this->toArray($payments);

        return $this->respondWithData($payments);
    }
}