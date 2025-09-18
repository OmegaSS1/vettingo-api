<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class GetPaymentIntentClientSecret extends SubscriptionAction {

    protected function action(): Response {
        //$form = $this->post();
        //$user = $this->validate($form);
        
        try {


            $checkout = $this->stripe->createCheckout("price_1S86OI6aUFqQ4eZQEgDMUif5");
            

            //$userSubscription = $this->iUserSubscriptionRepository->findByUserId($this->USER->sub);
            //$subscription = $this->stripe->retrieveSubscription($userSubscription->getStripeSubscriptionId());

            
            //$setupIntent = $this->stripe->createSetupIntent($form["metadata"]);
            //$this->stripe->setPaymentMethodToCustomer($setupIntent->metadata->stripePaymentMethodId, $user->getStripeCustomerId());
        } catch (Exception $e) {
            throw new Exception("Erro ao criar SetupIntent", 500);
        }

        return $this->respondWithData(["id" => $checkout->id, "url" => $checkout->url]);
        // return $this->respondWithData(["success" => true, "clientSecret" => $setupIntent->client_secret, "setupIntentId" => $setupIntent->id]);
    }

    private function validate(array $form){

        $user = $this->iUserRepository->findById($this->USER->sub);

        if(!$form["metadata"]){
            throw new Exception("Dados necessarias não foram informados", 400);
        }
        else if(!$user){
            throw MessageException::USER_NOT_FOUND(null);
        }

        if($form["metadata"]["userId"]){
            if((int) $form["userId"] != $this->USER->sub){
                throw new Exception("O usuario do cartão não é o mesmo usuário da sessao", 400);
            }
        }

        return $user;
    }
}