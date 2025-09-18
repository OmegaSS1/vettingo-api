<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class CreateSubscription extends SubscriptionAction {

    protected function action(): Response {
        $form = $this->post();

        $user = $this->validate($form);
        $customer = $this->validateCustomer($user);
        $this->stripe->setPaymentMethodToCustomer($form["paymentMethodId"], $customer->id);

        $this->hasActiveSubscription($customer->id, $form);
        
        if($response = $this->hasIncompleteSubscription($customer->id, $form)){
            return $this->respondWithData([]);
        }

        try {
            $checkout = $this->stripe->createCheckout($customer->id, $form["priceId"], $form["paymentMethodId"]);
        } catch (Exception $e) {
            throw new Exception("Falha ao criar link de pagamento", 500);
        }

        $this->iDatabaseRepository->disableCommit();

        $userSubPayload = [
            "user_id" => $this->USER->sub,
            "plan_id" => $form["planId"],
            "status" => "INCOMPLETE",
            "current_period_start" => null,
            "current_period_end" => null,
            "cancel_at_period_end" => 'FALSE',
            "canceled_at" => NULL,
            "deleted_at" => NULL,
        ];

        if($userSubscription = $this->iUserSubscriptionRepository->findByUserId($this->USER->sub)){
            if(in_array($userSubscription->getStatus(), ['ACTIVE', 'TRIALING'])){
                throw new Exception("O usuario já possui assinatura ativa ou em periodo de teste");
            }
            $userSubscription = $this->iUserSubscriptionRepository->update($userSubPayload, "id = {$userSubscription->getId()}");
        }
        else {
            $userSubscription = $this->iUserSubscriptionRepository->insert($userSubPayload);
        }

        $this->iPaymentRepository->insert([
            'user_id' => $this->USER->sub,
            'subscription_id' => $userSubscription->getId(),
            'stripe_payment_intent_id' => null,
            'stripe_invoice_id' => null,
            'amount' => $checkout->amount_total,
            'currency' => $checkout->currency,
            'status' => 'PENDING',
            'description' => "Assinatura {$form["planSlug"]}",
            "stripe_checkout_id" => $checkout->id,
        ]);

        $this->iDatabaseRepository->commit();

        return $this->respondWithData(["url" => $checkout->url]);
    }

    private function validate(array &$form){
        $this->validKeysForm($form, ["planSlug","period"], ["Plano","Periodo"]);

        $userId = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $plan = $this->iSubscriptionPlanRepository->findBySlug($form["planSlug"]);

        if(!$userId || !$user = $this->iUserRepository->findById($userId)) {
            throw MessageException::USER_NOT_FOUND(null);
        }
        else if(!$priceId = $this->priceStripe($form["planSlug"], $form["period"])){
            throw new Exception("Plano não encontrado", 400);
        }
        else if(!$plan || !$plan->getIsActive()){
            throw new Exception("Plano inativo", 400);
        }
        else if($user->getRole() != "VETERINARIAN"){
            throw new Exception("O usuário precisa ser veterinário", 400);
        }

        if(!$form["paymentMethodId"]){
            if(!$defaultPaymentMethod = $this->iPaymentMethodRepository->findDefaultByUserId($this->USER->sub)){
                throw new Exception("Usuário não possui metodo de pagamento padrao configurado",400);
            }
            $form["paymentMethodId"] = $defaultPaymentMethod->getStripePaymentMethodId();
        }

        $form["planId"] = $plan->getId();
        $form["priceId"] = $priceId;

        return $user;
    }

    private function createSubscription($customer, array &$form){        
        $subscriptionData = [
            'customer' => $customer->id,
            'items' => [
                ['price' => $form["priceId"]],
            ],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => [
                'save_default_payment_method' => 'on_subscription',
            ],
            'expand' => ['latest_invoice.payment_intent'],
            "default_payment_method" => $form["paymentMethodId"],
        ];

        if($form["coupon"]){
            $subscriptionData["coupon"] = $form["couponId"];
        }
        
        $subscription = $this->stripe->createSubscription($subscriptionData);

        $dateStart = $subscription->items?->data[0]?->current_period_start;
        $dateEnd = $subscription->items?->data[0]?->current_period_end;

        try {
            if($dateStart)
                $form["currentPeriodStart"] = date('Y-m-d H:i:s', $dateStart);
            if($dateEnd)
                $form["currentPeriodEnd"] = date('Y-m-d H:i:s', $dateEnd);
        } catch (Exception $e) {
            $form["currentPeriodStart"] = null;
            $form["currentPeriodEnd"] = null;
        }

        return $subscription;
    }

    private function validateCustomer($user){
        try {

            if($user->getStripeCustomerId()){
                if($customer = $this->stripe->retrieveCustomer($user->getStripeCustomerId())){
                    return $customer;
                }
            }

            $customer["name"] = $user->getFirstName()." ".$user->getLastName();
            $customer["phone"] = "";

            if(!$email = $this->iUserEmailRepository->findIsPrimaryByUserId($user->getId())){
                throw new Exception("O usuário não possui email primário.", 400);
            }
            $customer["email"] = $email->getEmail();

            if($phone = $this->iUserPhoneRepository->findIsPrimaryByUserId($user->getId())){
                $customer["phone"] = "+{$phone->getCountryCode()}{$phone->getAreaCode()}{$phone->getNumber()}";
            }
            
            $customer = $this->stripe->createCustomer($customer["email"], $customer["name"], $customer["phone"], ["source" => "vettingo_api"]);
            $this->iUserRepository->update(["stripe_customer_id" => $customer->id], "id = {$user->getId()}");

            return $customer;
        }catch(Exception $e){
            $this->loggerInterface->info("Falha ao Criar Customer", ["message" => $e->getMessage(), "code" => $e->getCode(), "line" => $e->getLine(), "file" => $e->getFile()]);
            throw new Exception("Falha ao criar customer!",500);
        }
    }

    private function priceStripe(string $slug, string $interval = "month"){
        $prices = $this->stripe->listPrices();

        foreach($prices->data as $price){
            if($price?->metadata?->plan_slug == $slug && $price?->recurring?->interval == $interval && $price->active){
                return $price->id;
            }
        }

        return null;
    }

    private function hasActiveSubscription(string $customerId, array &$form){
        $subscriptions = $this->stripe->retrieveCustomerSubscriptions($customerId);
        foreach ($subscriptions->data as $item) {
            if (in_array($item->status, ['active', 'trialing'])) {
                $planSlug = $item->items?->data[0]?->plan?->metadata?->plan_slug;
                $interval = $item->items?->data[0]?->price?->recurring->interval;
                
                if($planSlug == $form["planSlug"] && $interval == $form["interval"]){
                    throw new Exception("o Usuário já possui um plano ativo", 500);
                }
            }
        }
    }

    private function hasIncompleteSubscription(string $customerId, $form){
        $subscriptions = $this->stripe->retrieveCustomerSubscriptions($customerId, 'incomplete', 1);
        if(count($subscriptions->data) === 0) return false;

        $subscription = $subscriptions->data[0];

        //return [
        //    "subscriptionId" => $subscription->id,
        //    "clientSecret" => $clientSecret,
        //];
    }
}