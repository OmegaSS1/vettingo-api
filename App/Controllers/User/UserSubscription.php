<?php

declare(strict_types=1);
namespace App\Controllers\User;

use Psr\Http\Message\ResponseInterface as Response;

class UserSubscription extends UserAction {

    protected function action(): Response {
        $sub = $this->iUserSubscriptionRepository->findByUserId($this->USER->sub);
        if(!$sub)
            $sub = $this->iSubscriptionPlanRepository->findBySlug('FREE');
        $this->toArray($sub);

        return $this->respondWithData($sub);
    }
}