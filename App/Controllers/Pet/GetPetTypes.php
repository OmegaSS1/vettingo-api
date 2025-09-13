<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;

class GetPetTypes extends PetAction {

    protected function action(): Response {
        $args = $this->request->getQueryParams();

        $options = [
            "active" => $args["activeOnly"] ?? true,
            "search" => [
                "column" => "name",
                "value" => $args["search"] ?? ""
            ],
            "category_id" => $args["categoryId"] ?? "",
            "orderBy" => $args["orderBy"] ?? "name",
            "orderDirection" => $args["orderDirection"] ?? "ASC",
            "page" => $args["page"] ?? 1,
            "pageSize" => $args["perPage"] ?? 0
        ];

        $pets = $this->iPetTypeRepository->findAll($options);
        $this->toArray($pets);

        return $this->respondWithData($pets);
    }
}