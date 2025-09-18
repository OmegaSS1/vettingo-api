<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class CreatePlan extends SubscriptionAction {

    protected function action(): Response {
        $form = $this->post();
        $this->validate($form);
        
        try {

            $product = $this->stripe->createProduct($form["name"], $form["description"]);
            $price = $this->stripe->createPrice($form["unitAmount"], $product->id, $form["slug"], $form["interval"]);

            $plan = $this->iSubscriptionPlanRepository->findBySlug($form["slug"]);

            switch($form["interval"]){
                case "month":
                    $payload = [
                        "price_monthly" => (int) $form["unitAmount"],
                        "stripe_price_id_monthly" => $price->id
                    ];
                    break;
                case "year":
                    $payload = [
                        "price_yearly" => (int) $form["unitAmount"],
                        "stripe_price_id_yearly" => $price->id
                    ];
                    break;
            }

            $payload["name"] = $form["name"];
            $payload["description"] = $form["description"];
            $payload["slug"] = $form["slug"];
            $payload["features"] = $form["features"];
            $payload["max_listings"] = $form["maxListings"];
            $payload["max_photos"] = $form["maxPhotos"];
            $payload["analytics_access"] = $form["analyticsAccess"];
                    
            if($plan){
                $plan = $this->iSubscriptionPlanRepository->update($payload, "id = {$plan->getId()}");
            }
            else {
                $plan = $this->iSubscriptionPlanRepository->insert($payload);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }

        $this->toArray($plan);
        return $this->respondWithData($plan);
    }

    private function validate(array &$form){
        $this->validKeysForm($form,
        ["name","description","unitAmount","slug","features","interval","maxListings","maxPhotos","analyticsAccess"],
        ["Nome do plano","Descrição","Valor","Apelido","Beneficios","Intervalo","Maximo de listagens","Maximo de Fotos","Analise de estatisticas"]);

        $id = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!$id || !$user = $this->iUserRepository->findById($id)){
            throw MessageException::USER_NOT_FOUND(null);
        }
        //else if($user->getRole() !== 'ADMIN'){
        //    throw new Exception("Permissao negada", 401);
        //}

        $allowedValues = ["month","year"];
        if($form["interval"] and !in_array($form["interval"], $allowedValues)){
            throw new Exception("Periodo invalido", 400);
        }

        $form["analyticsAccess"] = 'FALSE';
        if($form["analyticsAccess"] === true){
            $form["analyticsAccess"] = 'TRUE';
        }
    }
}