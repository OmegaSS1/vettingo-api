<?php

declare(strict_types=1);
namespace App\Controllers\Location;

use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class CitiesByState extends LocationAction {

    protected function action(): Response {
        $args = $this->request->getQueryParams();
        $id = $this->validade();

        $total = $this->iCityRepository->findTotalByStateId($id);
        $options = [
            "active" => $args["activeOnly"] ?? true,
            "search" => [
                "column" => "name", 
                "value" => $args["search"] ?? ""
            ],
            "orderBy" => $args["orderBy"] ?? "name",
            "orderDirection" => $args["orderDirection"] ?? "ASC",
            "page" => $args["page"] ?? 1,
            "pageSize" => $page["pageSize"] ?? $total
        ];

        $cities = $this->iCityRepository->findByStateId($id, $options);
        [$active, $inactive] = $this->iCityRepository->findTotalActiveInactiveByState($id);

        $totalPages = (int) ceil($total / $options["pageSize"]);
        $response = [
            "items" => $cities,
            "pagination" => [
                "page" => $options["page"],
                "pageSize" => $options["pageSize"],
                "totalPages" => $totalPages,
                "total" => $total,
                "hasNext" => $options["page"] < $totalPages ? true : false,
                "hasPrevious" => $options["page"] > 1 && $options["pageSize"] < $total ? true : false,
            ],
            "meta" => [
                "activeCount" => $active['count'],
                "inactiveCount" => $inactive['count'],
            ]
        ];
        
        return $this->respondWithData($response);
    }

    private function validade() {
        $id = filter_var($this->getArg("id"), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(is_null($id)){
            throw new Exception("Estado não encontrado", 404);
        }
        else if(!$state = $this->iStateRepository->findById($id)){
            throw new Exception("Estado com ID $id não encontrado", 404);
        }
        else if(!$state->getIsActive()){
            throw new Exception("Estado {$state->getName()} ({$state->getUf()}) está inativo", 404);
        }


        return $state->getId();
    }
}