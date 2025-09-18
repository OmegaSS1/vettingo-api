<?php

declare(strict_types=1);
namespace App\Services;

use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use \Stripe\Stripe as stp;
use \Stripe\Customer;
use \Stripe\Price;
use \Stripe\Subscription;
use \Stripe\SetupIntent;
use \Stripe\PaymentMethod;
use \Stripe\Product;
use Stripe\Invoice;

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
     * Cria uma assinatura recorrente
     * @param string $customerId
     * @param string $priceRecurringId
     * @return Subscription
     */
    public function createSubscription(array $subscriptionData) {
        return Subscription::create($subscriptionData);
    }

    public function createCheckout(string $customerId, string $priceId){
        return Session::create([
        'customer' => $customerId,
        'line_items' => [[
            'price' => $priceId,
            'quantity' => 1,
        ]],
        'mode' => 'subscription',
        'success_url' => ENV["URL"] . "/confirm-payment?success=true&session_id={CHECKOUT_SESSION_ID}",
        'cancel_url' => ENV["URL"] . "/cancel-payment?canceled=true",
        ]);
    }

    public function retrieveCheckout(string $sessionId){
        return Session::retrieve($sessionId, []);
    }

    /**
     * Cria um novo produto
     * @param string $name
     * @param string $description
     * @return Product
     */
    public function createProduct(string $name, string $description){
        return Product::create([
            'name' => $name,
            'description' => $description
        ]);
    }

    /**
     * Cria um preço para atribuir a um produto
     * @param int $amount
     * @param string $productId
     * @param string $slug
     * @param string $interval
     * @return Price
     */
    public function createPrice(int $amount, string $productId, string $slug, string $interval = 'month'){
        return Price::create([
            'unit_amount' => $amount,
            'currency' => 'brl',
            'recurring' => [
                'interval' => $interval
            ],
            'product' => $productId,
            'metadata' => [
                "plan_slug" => $slug
            ]
        ]);
    }

    /**
     * Lista todos os preços cadastrados ativos
     * @return \Stripe\Collection<Price>
     */
    public function listPrices(){
        return Price::all([
            "active" => true,
            "limit" => 100
        ]);
    }

    public function searchPaymentIntent(array $data){
        return PaymentIntent::search($data);
    }

    public function confirmPayment($paymentIntentId, string $paymentMethodId){
        $payment = PaymentIntent::retrieve($paymentIntentId);
        return $payment->confirm([
            "payment_method" => $paymentMethodId
        ]);
    }

    /**
     * Atribui ao usuario o metodo de pagamento
     * @param string $paymentMethodId
     * @param string $customerId
     * @return void
     */
    public function setPaymentMethodToCustomer(string $paymentMethodId, string $customerId){
        $payment = PaymentMethod::retrieve($paymentMethodId);
        $payment->attach(["customer" => $customerId]);

        Customer::update($customerId, [
            'invoice_settings' => [
                'default_payment_method' => $paymentMethodId
            ]
        ]);
    }

    /**
     * Recupera os dados de customer do cliente
     * @param string $customerId
     * @return Customer
     */
    public function retrieveCustomer(string $customerId){
        return Customer::retrieve($customerId);
    }

    /**
     * Recupera os dados de assinatura do cliente baseado no customer dele
     * @param string $customerId
     * @param string $status
     * @param int $limit
     * @return \Stripe\Collection<Subscription>
     */
    public function retrieveCustomerSubscriptions(string $customerId, string $status = "", int $limit = 100){
        $payload = [
            'customer' => $customerId,
            'limit' => $limit,
        ];

        !empty($status) ? $payload["status"] = $status : "";
        return Subscription::all($payload);
    }

    /**
     * Recupera a fatura gerada a um cliente apos ter criado uma assinatura
     * @param string $invoiceId
     * @return Invoice
     */
    public function retrieveInvoice(string $invoiceId){
        return Invoice::retrieve($invoiceId, ['expand'   => ['payment_intent']]);
    }

    /**
     * Recupera o objeto de cobrança da fatura
     * @param string $intentId
     * @return PaymentIntent
     */
    public function retrievePaymentIntent(string $intentId){
        return PaymentIntent::retrieve($intentId);
    }

    /**
     * Cria a fatura relacionada ao invoice
     * @param mixed $invoice
     * @param string $paymentMethodId
     * @return PaymentIntent
     */
    public function createPaymentIntent($invoice, string $paymentMethodId, string $subscriptionId){
        return PaymentIntent::create([
            "customer" => $invoice->customer,
            "payment_method" => $paymentMethodId,
            "amount" => $invoice->amount_due,
            "currency" => $invoice->currency,
            "confirm" => true,
            'metadata' => [
                'subscription_id' => $subscriptionId,
                'invoice_id' => $invoice->id,
            ],
        ]);
    }

    /**
     * Cria um metodo de pagamento
     * @param int $customerId
     * @return SetupIntent
     */
    public function createSetupIntent(array $metadata){
        return SetupIntent::create([
            "payment_method_types" => ["card"],
            "metadata" => $metadata
        ]);
    }

    public function retrieveSubscription(string $subscriptionId){
        return Subscription::retrieve([
            'id' => $subscriptionId,
            'expand' => ['latest_invoice.payment_intent.payment_method'],
        ]);
    }

    /**
     * Cancela a assinatura de um cliente
     * @param string $subscriptionId
     * @return bool
     */
    public function cancelSubscription(string $subscriptionId){
        $sub = Subscription::retrieve($subscriptionId);
        $sub->cancel();
        return true;
    }

    public function updateSubscription(string $subscriptionId, $cancelAtPeriodEnd = true){
        Subscription::update($subscriptionId, [
            'cancel_at_period_end' => $cancelAtPeriodEnd
        ]);
    }

    /**
     * Cancela todas assinaturas de um cliente
     * @param string $customerId
     * @return bool
     */
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

    /**
     * Remove um metodo de pagamento de um cliente
     * @param string $paymentMethodId
     * @return bool
     */
    public function removePaymentMethod(string $paymentMethodId){
        $sub = PaymentMethod::retrieve($paymentMethodId);
        $sub->detach();
        return true;
    }

    /**
     * Remove todos os metodos de pagamentos de um cliente
     * @param string $customerId
     * @return bool
     */
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
