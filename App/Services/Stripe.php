<?php

declare(strict_types=1);
namespace App\Services;

use \Stripe\Stripe as stp;
use \Stripe\Customer;
use \Stripe\Price;
use \Stripe\Checkout\Session as Checkout;
use \Stripe\Subscription;
use \Stripe\SetupIntent;
use \Stripe\PaymentMethod;
class Stripe {
    public function __construct(){
        stp::setApiKey(ENV["STRIPE_SECRET_KEY"]);
    }

    /**
     * Summary of createCustomer
     * @param string $email
     * @param string $name
     * @param string $phone
     * @param array $metadata
     * @return Customer
     */
    public function createCustomer(string $email, string $name, string $phone, array $metadata){
        return Customer::create([
            "email" => $email,
            "name" => $name,
            "phone" => $phone,
            "metadata" => $metadata
        ]);
    }

    // /**
    //  * Summary of createUniquePrice
    //  * @param int $price
    //  * @param array $productData
    //  * @param string $currency
    //  * @return Price
    //  */
    // public function createUniquePrice(int $price, array $productData, string $currency = "brl"){
    //     return Price::create([
    //         "unit_amount" => $price,
    //         "currency" => $currency,
    //         "product_data" => $productData,
    //     ]);
    // }

    // /**
    //  * Summary of createRecurringPrice
    //  * @param int $price
    //  * @param array $productData
    //  * @param string $interval
    //  * @param string $currency
    //  * @return Price
    //  */
    // public function createRecurringPrice(int $price, array $productData, string $interval = "month", string $currency = "brl"){
    //     return Price::create([
    //         "unit_amount" => $price,
    //         "currency" => $currency,
    //         "recurring" => [
    //             "interval" => $interval
    //         ],
    //         "product_data" => $productData,
    //     ]);
    // }

    // /**
    //  * Summary of createUniquePayment
    //  * @param string $customerId
    //  * @param string $priceId
    //  * @param string $successUrl
    //  * @param string $cancelUrl
    //  * @return Checkout
    //  */
    // public function createUniquePayment(string $customerId, string $priceUniqueId, string $successUrl, string $cancelUrl) {
    //     return Checkout::create([
    //         "mode" => "payment",
    //         "payment_method_types" => ["card"],
    //         "customer" => $customerId,
    //         'line_items' => [[
    //             'price' => $priceUniqueId,
    //             'quantity' => 1,
    //         ]],
    //         'success_url' => $successUrl,
    //         'cancel_url' => $cancelUrl,
    //     ]);
    // }

    /**
     * Summary of createRecurringPayment
     * @param string $customerId
     * @param string $priceRecurringId
     * @return Subscription
     */
    public function createSubscription(array $subscriptionData) {
        return Subscription::create($subscriptionData);
    }

    public function listPrices(){
        return Price::all([
            "active" => true,
            "limit" => 100
        ]);
    }

    public function setPaymentMethodToCustomer(string $paymentMethodId, string $customerId){
        $payment = PaymentMethod::retrieve($paymentMethodId);
        $payment->attach(["customer" => $customerId]);
    }

    public function retrieveCustomer(string $customerId){
        return Customer::retrieve($customerId);
    }

    /**
     * Summary of createPayment
     * @param int $customerId
     * @return SetupIntent
     */
    public function createSetupIntent(array $metadata){
        return SetupIntent::create([
            "payment_method_types" => ["card"],
            "metadata" => $metadata
        ]);
    }

    public function cancelSubscription(string $subscriptionId){
        $sub = Subscription::retrieve($subscriptionId);
        $sub->cancel();
        return true;
    }

    public function cancelAllSubscription(string $customerId){
        $subscriptions = Subscription::all([
            'customer' => $customerId,
            'status' => 'all',
        ]);

        foreach ($subscriptions->data as $subscription) {
            $subscription->cancel();
        }

        return true;
    }

    public function removePaymentMethod(string $paymentMethodId){
        $sub = PaymentMethod::retrieve($paymentMethodId);
        $sub->detach();
        return true;
    }

    public function removeAllPaymentMethod(string $customerId){
        $paymentMethods = PaymentMethod::all([
            'customer' => $customerId,
            'type' => 'card',
        ]);

        foreach ($paymentMethods->data as $pm) {
            $pm->detach();
        }

        return true;
    }
}
