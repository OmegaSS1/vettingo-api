<?php

declare(strict_types=1);
namespace App\Controllers\User;

use App\Controllers\User\UserAction;
use Psr\Http\Message\ResponseInterface as Response;

class GetUser extends UserAction {

    protected function action(): Response {

        $user = $this->iUserRepository->findById($this->USER->sub);
        $this->toArray($user);
        
        return $this->respondWithData($user);
    }
}