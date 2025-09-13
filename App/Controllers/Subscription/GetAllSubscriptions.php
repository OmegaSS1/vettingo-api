<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use Psr\Http\Message\ResponseInterface as Response;

class GetAllSubscriptions extends SubscriptionAction {

    protected function action(): Response {

        $plans = $this->iSubscriptionPlanRepository->findAll();
        $this->toArray($plans);

        return $this->respondWithData($plans);
    }
}