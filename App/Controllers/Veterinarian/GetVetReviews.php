<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class GetVetReviews extends VeterinarianAction {

    protected function action(): Response {
        $args = $this->request->getQueryParams();

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

        if(!$vet = $this->iVeterinarianRepository->findByUserId($this->USER->sub))
            throw MessageException::VETERINARIAN_NOT_FOUND($this->USER->sub);

        else if(!$vetWorkLocationSchedules = $this->iVetReviewRepository->findByVeterinarianId($vet->getId(), $options)){
            throw MessageException::VETERINARIAN_NOT_FOUND($this->USER->sub);
        }
        $vetWorkLocationSchedules = is_array($vetWorkLocationSchedules) ? $vetWorkLocationSchedules : [$vetWorkLocationSchedules];
        $this->toArray($vetWorkLocationSchedules);
        
        return $this->respondWithData($vetWorkLocationSchedules);
    }
}