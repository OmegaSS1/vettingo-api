<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class CreateSubscription extends SubscriptionAction {

    protected function action(): Response {
        $form = $this->post();

        $planMap = [
            "FREE" => 1,
            "BASIC" => 1,
            "PREMIUM" => 2,
            "ENTERPRISE" => 3
        ];

        $this->iDatabaseRepository->disableCommit();
        [$form, $subscription] = $this->validate($form);

        $userSubscription = $this->iUserSubscriptionRepository->insert([
            "user_id" => $this->USER->sub,
            "plan_id" => $planMap[$form["planSlug"]],
            "status" => "INCOMPLETE",
            "stripe_subscription_id" => $subscription->id,
            "current_period_start" => $form["currentPeriodStart"],
            "current_period_end" => $form["currentPeriodEnd"],
            "cancel_at_period_end" => false
        ]);

        if (!empty($subscription->latest_invoice) && !empty($subscription->latest_invoice->payment_intent)) {
            $paymentIntent = $subscription->latest_invoice->payment_intent;

            $this->iPaymentRepository->insert([
                'user_id' => $this->USER->sub,
                'subscription_id' => $userSubscription->getId(),
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_invoice_id' => $subscription->latest_invoice->id,
                'amount' => $paymentIntent->amount, // centavos
                'currency' => $paymentIntent->currency,
                'status' => 'PENDING',
                'description' => "Assinatura {$form["planSlug"]}",
            ]);
        }

        $this->iDatabaseRepository->commit();
        return $this->respondWithData($userSubscription);
    }

    private function validate(array &$form){
        $this->validKeysForm($form, ["planSlug"], ["Plano"]);

        $userId = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if(!$userId || !$user = $this->iUserRepository->findById($userId)) {
            throw MessageException::USER_NOT_FOUND(null);
        }
        else if(!$priceId = $this->priceStripe($form["planSlug"])){
            throw new Exception("Price ID não encontrado para o plano {$form["planSlug"]}", 400);
        }

        if(!$form["paymentMethodId"]){
            if(!$defaultPaymentMethod = $this->iPaymentMethodRepository->findDefaultByUserId($this->USER->sub)){
                throw new Exception("Usuário não possui metodo de pagamento padrao configurado",400);
            }

            $form["paymentMethodId"] = $defaultPaymentMethod->getStripePaymentMethodId();
        }

        try {
            if($user->getStripeCustomerId()){
                $customer = $this->stripe->retrieveCustomer($user->getStripeCustomerId());
            }
        } catch (Exception $e) {
            $customer = null;
        }
        if(!$customer)
            $customer = $this->createCustomer($user);

        
        $subscriptionData = [
            'customer' => $customer->id,
            'items' => [
                ['price' => $priceId],
            ],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => [
                'save_default_payment_method' => 'on_subscription',
            ],
            'expand' => ['latest_invoice.payment_intent'],
            "default_payment_method" => $form["paymentMethodId"]
        ];

        $this->stripe->setPaymentMethodToCustomer($form["paymentMethodId"], $customer->id);

        if($form["coupon"]){
            $subscriptionData["coupon"] = $form["couponId"];
        }
        
        $subscription = $this->stripe->createSubscription($subscriptionData);

        try {
            if($subscription->current_period_start)
                $form["currentPeriodStart"] = date('Y-m-d H:i:s', $subscription->current_period_start);
            if($subscription->current_period_end)
                $form["currentPeriodEnd"] = date('Y-m-d H:i:s', $subscription->current_period_end);
        } catch (Exception $e) {
            $form["currentPeriodStart"] = null;
            $form["currentPeriodEnd"] = null;
        }

        return [$form, $subscription];
    }

    private function createCustomer($user){
        try {

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
            if($price?->metadata?->plan_slug == $slug && $price?->recurring?->interval == $interval){
                return $price->id;
            }
        }

        return null;
    }
}