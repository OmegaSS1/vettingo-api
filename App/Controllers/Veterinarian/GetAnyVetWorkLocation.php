<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class GetAnyVetWorkLocation extends VeterinarianAction {

    protected function action(): Response {
        $args = $this->request->getQueryParams();
        $this->validate($args);

        $options = [
            "cityId" => $args["cityId"] ?? "",
            "orderBy" => $args["orderBy"] ?? "name",
            "orderDirection" => $args["orderDirection"] ?? "ASC",
            "page" => $args["page"] ?? 1,
            "pageSize" => $args["perPage"] ?? 0,
        ];

        $vetWorkLocation = $this->iVetWorkLocationRepository->findAnyVeterinarian($options);

        return $this->respondWithData($vetWorkLocation);
    }

    private function validate($args){
        //$id = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        //
        //if(!$id || !$user = $this->iUserRepository->findById($id)){
        //    throw MessageException::USER_NOT_FOUND($id ?? null);
        //}
        //else if(!$user->getStatus()){
        //    throw MessageException::USER_NOT_FOUND($id ?? null);
        //}
        if(empty($args["cityId"])){
            throw new Exception("É necessário informar a cidade", 400);
        }
    }
}