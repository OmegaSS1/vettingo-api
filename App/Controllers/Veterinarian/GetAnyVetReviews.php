<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;

class GetAnyVetReviews extends VeterinarianAction {

    protected function action(): Response {
        $args = $this->request->getQueryParams();
        $id = $this->validate($args["veterinarianId"]);

        $options = [
            "rating" => $args["rating"] ?? "",
            "minRating" => $args["minRating"] ?? "",
            "startDate" => $args["startDate"] ?? "",
            "endDate" => $args["endDate"] ?? "",
            "search" => [
                "column" => "authorName",
                "value" => $args["search"] ?? ""
            ],
            "anonymous" => $args["anonymous"] ?? false,
            "orderBy" => $args["orderBy"] ?? "createdAt",
            "orderDirection" => $args["orderDirection"] ?? "ASC",
            "page" => $args["page"] ?? 1,
            "pageSize" => $args["perPage"] ?? 10
        ];


        if(!$vetWorkLocationSchedules = $this->iVetReviewRepository->findByVeterinarianId($id, $options)){
            throw MessageException::VETERINARIAN_NOT_FOUND($id);
        }
        $vetWorkLocationSchedules = is_array($vetWorkLocationSchedules) ? $vetWorkLocationSchedules : [$vetWorkLocationSchedules];
        $this->toArray($vetWorkLocationSchedules);
        
        return $this->respondWithData($vetWorkLocationSchedules);
    }

    private function validate($id){
        $id = filter_var($id, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$id){
            throw MessageException::VETERINARIAN_NOT_FOUND(null);
        }

        return $id;
    }
}