<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class CreatePaymentMethods extends SubscriptionAction {

    protected function action(): Response {
        $form = $this->post();
        $user = $this->validate($form);
        
        try {
            $setupIntent = $this->stripe->createSetupIntent($form);
            $this->stripe->setPaymentMethodToCustomer($setupIntent->metadata->stripePaymentMethodId, $user->getStripeCustomerId());
        } catch (Exception $e) {
            throw new Exception("Erro ao criar SetupIntent", 500);
        }

        $method = $this->iPaymentMethodRepository->insert([
            "user_id" => $user->getId(),
            "stripe_payment_method_id" => $form["stripePaymentMethodId"],
            "type" => $form["type"],
            "brand" => $form["brand"],
            "last4" => $form["last4"],
            "exp_month" => $form["expMonth"],
            "exp_year" => $form["expYear"],
            "is_default" => $form["isDefault"],
        ]);

        $this->toArray($method);
        return $this->respondWithData($method);
        //return $this->respondWithData(["success" => true, "clientSecret" => $setupIntent->client_secret, "setupIntentId" => $setupIntent->id]);
    }

    private function validate(array &$form){
        $this->validKeysForm($form, ["stripePaymentMethodId", "type"], ["Id do Stripe", "Tipo"]);

        $user = $this->iUserRepository->findById($this->USER->sub);

        if(!$user){
            throw MessageException::USER_NOT_FOUND(null);
        }
        else if(!$user->getStripeCustomerId()){
            throw new Exception("O usuario não possui customer configurado no Stripe", 500);
        }
        
        $form["isDefault"] = "FALSE";
        if($form["isDefault"] === true)
            $form["isDefault"] = "TRUE";

        return $user;
    }
}