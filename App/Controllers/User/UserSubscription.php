<?php

declare(strict_types=1);
namespace App\Controllers\User;

use Psr\Http\Message\ResponseInterface as Response;

class UserSubscription extends UserAction {

    protected function action(): Response {
        $sub = $this->iUserSubscriptionRepository->findByUserId($this->USER->sub);
        if(!$sub){
            $freePlan = $this->iSubscriptionPlanRepository->findBySlug('FREE');
            $sub = [
                "id" => 0,
                "userId" => $this->USER->sub,
                "planId" => $freePlan->getId(),
                "planSlug" => $freePlan->getSlug(),
                "planName" => $freePlan->getName(),
                "status" => "ACTIVE",
                "stripeSubscriptionId" => null,
                "currentPeriodStart" => null,
                "currentPeriodEnd" => null,
                "cancelAtPeriodEnd" => false,
                "canceledAt" => null,
                "trialStart" => null,
                "trialEnd" => null,
                "createdAt" => date("Y-m-d H:i:s"),
                "updatedAt" => date("Y-m-d H:i:s"),
            ];
        }

        $this->toArray($sub);
        return $this->respondWithData($sub);
    }
}