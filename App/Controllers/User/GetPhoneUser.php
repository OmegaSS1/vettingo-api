<?php

declare(strict_types=1);
namespace App\Controllers\User;

use Psr\Http\Message\ResponseInterface as Response;

class GetPhoneUser extends UserAction {

    protected function action(): Response {

        $user = $this->iUserPhoneRepository->findByUserId($this->USER->sub);
        $user = is_array($user) ? $user : [$user];
        $this->toArray($user);
        
        return $this->respondWithData($user);
    }
}