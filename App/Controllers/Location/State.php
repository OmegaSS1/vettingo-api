<?php

declare(strict_types=1);
namespace App\Controllers\Location;

use Psr\Http\Message\ResponseInterface as Response;

class State extends LocationAction {

    protected function action(): Response {

        $state = $this->iStateRepository->findAll();
        $active = $this->iStateRepository->findTotalActive();
        $inactive = $this->iStateRepository->findTotalInactive();

        $response = [
            "items" => $state,
            "pagination" => [
                "page" => 1,
                "pageSize" => 27,
                "totalPages" => 1,
                "total" => 27,
                "hasNext" => false,
                "hasPrevious" => false
            ],
            "meta" => [
                "activeCount" => $active,
                "inactiveCount" => $inactive,
            ]
        ];
        
        return $this->respondWithData($response);
    }
}