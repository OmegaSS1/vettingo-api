<?php

declare(strict_types=1);
namespace App\Controllers\Webhook;

use Psr\Http\Message\ResponseInterface as Response;
use Stripe\Exception\SignatureVerificationException;
use Exception;

class PaymentStatus extends WebhookAction {

    protected function action(): Response {
        $event = $this->validate();
        // Processar eventos
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->checkoutSessionCompleted($event->data->object);
                break;
            case 'checkout.session.expired':
                $this->checkoutSessionExpired($event->data->object);
                break;
            case 'customer.subscription.created':
                $this->customerSubscriptionCreated($event->data->object);
                break;
            case 'customer.subscription.updated':
                $this->customerSubscriptionUpdated($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->customerSubscriptionDeleted($event->data->object);
                break;
            case 'customer.subscription.incomplete_expired':
                $this->customerSubscriptionIncompleteExpired($event->data->object);
                break;
            case 'customer.subscription.trial_will_end':
                $this->customerSubscriptionTrialWillEnd($event->data->object);
                break;
            case 'customer.subscription.paused':
                $this->customerSubscriptionPaused($event->data->object);
                break;
            case 'customer.subscription.resumed':
                $this->customerSubscriptionResumed($event->data->object);
                break;
            case 'customer.updated':
                $this->customerUpdated($event->data->object);
                break;
            case 'invoice.payment_succeeded':
                $this->invoicePaymentSucceeded($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->invoicePaymentFailed($event->data->object);
                break;
            case 'invoice.payment_action_required':
                $this->invoicePaymentActionRequired($event->data->object);
                break;
            case 'invoice.created':
                $this->invoiceCreated($event->data->object);
                break;
            case 'invoice.updated':
                $this->invoiceUpdated($event->data->object);
                break;
            case 'invoice.finalized':
                $this->invoiceFinalized($event->data->object);
                break;
            case 'invoice.voided':
                $this->invoiceVoided($event->data->object);
                break;
            case 'payment_intent.succeeded':
                $this->paymentIntentSucceeded($event->data->object);
                break;
            case 'payment_intent.payment_failed':
                $this->paymentIntentFailed($event->data->object);
                break;
            case 'payment_intent.created':
                $this->paymentIntentCreated($event->data->object);
                break;
            case 'payment_intent.canceled':
                $this->paymentIntentCanceled($event->data->object);
                break;
            case 'setup_intent.succeeded':
                $this->setupIntentSucceeded($event->data->object);
                break;
            case 'price.updated':
                $this->priceUpdated($event->data->object);
                break;
            default:
                $this->loggerInterface->info("Evento não tratado: {$event->type}");
        }
        return $this->respondWithData(['status' => 'ok'], 400);
    }

    private function validate() {
        $payload = (string) $this->request->getBody();
        $sigHeader = $this->request->getHeaderLine('Stripe-Signature');
        
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                ENV['STRIPE_WEBHOOK_SECRET']
            );
        } catch(\UnexpectedValueException $e) {
            // Payload inválido
            return $this->respondWithData([], 400);
        } catch(SignatureVerificationException $e) {
            // Assinatura inválida
            return $this->respondWithData([], 400);
        }

        return $event;
    }

    private function checkoutSessionCompleted($checkout){
        $this->loggerInterface->info("Pagamento realizado {$checkout->id}");

        $this->loggerInterface->info("Checkout session completed", [
            "checkoutId" => $checkout->id,
            "customerId" => $checkout->customer
        ]);

        // Localiza o usuário pelo Stripe Customer ID
        $user = $this->iUserRepository->findByStripeCustomerId($checkout->customer);
        if (!$user) {
            $this->loggerInterface->error("Usuário não encontrado para o customer Stripe", [
                "checkoutId" => $checkout->id,
                "customerId" => $checkout->customer
            ]);
            return;
        }

        $userSubscription = $this->iUserSubscriptionRepository->findByUserId($user->getId());

        // Atualiza ou cria registro de pagamento PENDING
        $payment = $this->iPaymentRepository->findByStripeCheckoutId($checkout->id);
        $paymentData = [
            "user_id" => $user->getId(),
            "subscription_id" => $userSubscription->getId(), // será preenchido depois quando houver invoice/subscription
            "stripe_payment_intent_id" => $checkout->payment_intent ?? "",
            "stripe_invoice_id" => $checkout->invoice ?? "",
            "stripe_checkout_id" => $checkout->id,
            "amount" => $checkout->amount_total ?? null,
            "currency" => $checkout->currency ?? null,
            "status" => "PENDING",
            "description" => "Checkout {$checkout->id}",
            "created_at" => date("Y-m-d H:i:s")
        ];

        if ($payment) {
            $this->iPaymentRepository->update($paymentData, "id = {$payment->getId()}");
        } else {
            $this->iPaymentRepository->insert($paymentData);
        }

        // Atualiza a assinatura do usuário, caso exista
        if (!empty($checkout->subscription)) {
            $this->iUserSubscriptionRepository->update([
                "stripe_subscription_id" => $checkout->subscription
            ], "user_id = {$user->getId()}");
        }

        $this->loggerInterface->info("Checkout session processed com sucesso", [
            "checkoutId" => $checkout->id,
            "userId" => $user->getId()
        ]);

        // $user = $this->iUserRepository->findByStripeCustomerId($checkout->customer);
        // if(!$this->iPaymentRepository->findByStripeCheckoutId($checkout->id)){
        //     $this->loggerInterface->error("Não foi possivel localizar o checkout {$checkout->id}");
        // }

        // $payload = [
        //     "stripe_payment_intent_id" => !empty($checkout?->payment_intent) ? $checkout->payment_intent : "",
        //     "stripe_invoice_id" => !empty($checkout?->invoice) ? $checkout?->invoice : "",
        //     "status" => "PENDING"
        // ];

        // $this->iPaymentRepository->update($payload, "stripe_checkout_id = '{$checkout->id}'");
        // $this->iUserSubscriptionRepository->update(["stripe_subscription_id" => $checkout->subscription ?? ""], "user_id = {$user->getId()}");
    }
    private function customerSubscriptionCreated($subscription){
        $log = [
            "id" => $subscription?->id,
            "customerId" => $subscription?->customer,
            "status" => $subscription?->status,
            "planSlug" => $subscription?->items?->data[0]?->plan?->metadata?->plan_slug ?? 'N/A',
            "amount" => $subscription?->items?->data[0]?->plan?->amount ?? 0,
            "currency" => $subscription?->currency,
            "interval" => $subscription?->items?->data[0]?->plan?->recurring?->interval ?? 'N/A',
            "latestInvoice" => $subscription?->latest_invoice
        ];

        $user = $this->iUserRepository->findByStripeCustomerId($subscription->customer);
        if (!$user) return;

        $existing = $this->iUserSubscriptionRepository->findByUserId($user->getId());
        $status = "";
        switch($subscription->status) {
            case "incomplete":
                $status = "INCOMPLETE";
                break;
            case "trialing":
                $status = "TRIALING";
                break;
            case "active":
                $status = "ACTIVE";
                break;
            default:
                $this->loggerInterface->warning("Status de assinatura desconhecido no Stripe", [
                    "subscriptionId" => $subscription->id,
                    "status" => $subscription->status
                ]);
        }
        if ($existing) {
            // Atualiza status e datas
            $this->iUserSubscriptionRepository->update([
                "status" => !empty($status) ? $status : strtoupper($subscription->status),
                "stripe_subscription_id" => $subscription?->id,
                "current_period_start" => date("Y-m-d H:i:s", $subscription->current_period_start),
                "current_period_end" => date("Y-m-d H:i:s", $subscription->current_period_end),
            ], "id = {$existing->getId()}");
        } else {
            // Cria nova assinatura
            $this->iUserSubscriptionRepository->insert([
                "user_id" => $user->getId(),
                "plan_id" => $subscription->items->data[0]->plan->id,
                "status" => !empty($status) ? $status : strtoupper($subscription->status),
                "stripe_subscription_id" => $subscription->id,
                "current_period_start" => date("Y-m-d H:i:s", $subscription->current_period_start),
                "current_period_end" => date("Y-m-d H:i:s", $subscription->current_period_end),
                "trial_start" => date("Y-m-d H:i:s", $subscription->trial_start ?? time()),
                "trial_end" => date("Y-m-d H:i:s", $subscription->trial_end ?? time()),
                "cancel_at_period_end" => $subscription->cancel_at_period_end,
                "canceled_at" => $subscription->canceled_at ?? null,
                "created_at" => date("Y-m-d H:i:s")
            ]);
        }

        return;

        // $this->loggerInterface->info("Assinatura criada no Stripe", $log);

        // $user = $this->iUserRepository->findByStripeCustomerId($subscription->customer);
        // $userSubscription = $this->iUserSubscriptionRepository->findByUserId($user->getId());
        // $plan = $this->iSubscriptionPlanRepository->findBySlug($log["planSlug"]);
        // $payload = [                    
        //     "user_id" => $user->getId(),
        //     "stripe_subscription_id" => $subscription->id,
        //     "plan_id" => $plan->getId(),
        //     "current_period_start" => date("Y-m-d H:i:s", $subscription?->current_period_start ?? time()),
        //     "current_period_end" => date("Y-m-d H:i:s", $subscription?->current_period_end ?? time()),
        //     "cancel_at_period_end" => 'FALSE',
        //     "canceled_at" => NULL,
        //     "deleted_at" => NULL
        // ];

        // $update = function($payload, $status, $userSubscription){
        //     if($userSubscription)
        //         $this->iUserSubscriptionRepository->update(["status" => $status], "id = {$userSubscription->getId()}");
        //     else{
        //         $payload["status"] = $status;
        //         $this->iUserSubscriptionRepository->insert($payload);
        //     } 
        // };

        // switch($subscription->status) {
        //     case "incomplete":
        //         $update($payload,"INCOMPLETE", $userSubscription);
        //         break;
        //     case "trialing":
        //         $update($payload,"TRIALING", $userSubscription);
        //         break;
        //     case "active":
        //         $update($payload,"ACTIVE", $userSubscription);
        //         break;
        //     default:
        //         $this->loggerInterface->warning("Status de assinatura desconhecido no Stripe", [
        //             "subscriptionId" => $subscription->id,
        //             "status" => $subscription->status
        //         ]);
        // }
    }

    private function customerSubscriptionUpdated($subscription){
        $log = [
            "id" => $subscription->id,
            "customerId" => $subscription->customer,
            "status" => $subscription->status,
            "planSlug" => $subscription->items?->data[0]->plan?->metadata?->plan_slug ?? 'N/A',
            "amount" => $subscription->items?->data[0]->plan?->amount ?? 0,
            "currency" => $subscription->currency,
            "interval" => $subscription?->items?->data[0]->plan?->recurring?->interval ?? 'N/A',
            "latestInvoice" => $subscription->latest_invoice,
            "canceledAt" => $subscription->canceled_at,
            "endedAt" => $subscription->ended_at,
            "isCanceled" => in_array($subscription->status, ['canceled', 'incomplete_expired'], true)
        ];
        $this->loggerInterface->info("Assinatura atualizada", $log);

        $user = $this->iUserRepository->findByStripeCustomerId($subscription->customer);
        $userSubscription = $this->iUserSubscriptionRepository->findByUserId($user->getId());

        if (!$userSubscription) {
            // Caso não exista, cria registro inicial
            $user = $this->iUserRepository->findByStripeCustomerId($subscription->customer);
            $plan = $this->iSubscriptionPlanRepository->findBySlug($log["planSlug"]);
            $payload = [
                "user_id" => $user->getId(),
                "stripe_subscription_id" => $subscription->id,
                "plan_id" => $plan->getId(),
                "status" => strtoupper($subscription->status),
                "current_period_start" => date("Y-m-d H:i:s", $subscription?->current_period_start ?? time()),
                "current_period_end" => date("Y-m-d H:i:s", $subscription?->current_period_end ?? time()),
                "canceled_at" => $subscription->canceled_at ? date("Y-m-d H:i:s", $subscription->canceled_at) : null,
            ];
            $this->iUserSubscriptionRepository->insert($payload);
            return;
        }

        // Atualiza assinatura existente
        $updateData = [
            "status" => $log['isCanceled'] ? 'CANCELED' : strtoupper($subscription->status),
            "current_period_start" => date("Y-m-d H:i:s", $subscription?->current_period_start ?? time()),
            "current_period_end" => date("Y-m-d H:i:s", $subscription?->current_period_end ?? time()),
            "canceled_at" => $subscription->canceled_at ? date("Y-m-d H:i:s", $subscription->canceled_at) : null,
        ];

        $plan = $this->iSubscriptionPlanRepository->findBySlug($log["planSlug"]);
        if ($plan && $plan->getId() !== $userSubscription->getPlanId()) {
            $updateData['plan_id'] = $plan->getId();
        }

        $this->iUserSubscriptionRepository->update($updateData, "id = {$userSubscription->getId()}");
    }

    private function customerSubscriptionDeleted($subscription){
        $this->loggerInterface->info("Assinatura cancelada", ["Assinatura" => $subscription->id]);

        if($userSub = $this->iUserSubscriptionRepository->findByStripeSubscriptionId($subscription->id)){
            $this->iUserSubscriptionRepository->update(["status" => 'CANCELED', "canceled_at" => date('Y-m-d H:i:s')], "id = {$userSub->getId()}");
            
            if($payments = $this->iPaymentRepository->findBySubscriptionId($userSub->getId())){
                $payments = is_array($payments) ? $payments : [$payments];
                foreach($payments as $payment){
                    if(in_array($payment->getStatus(), ['PENDING', 'INCOMPLETE'])){
                        $this->iPaymentRepository->update([
                            "status" => "CANCELED"
                        ], "id = {$payment->getId()}");
                    }
                }
            }
        }
        else {
            $this->loggerInterface->error("Assinatura não encontrada no banco para deletar", [
                "subscriptionId" => $subscription->id
            ]);
        }
    }

    private function customerSubscriptionIncompleteExpired($subscription){
        $this->loggerInterface->info("Assinatura incompleta expirada", ["Assinatura" => $subscription->id]);

        $user = $this->iUserRepository->findByStripeCustomerId($subscription->customer);
        if($userSub = $this->iUserSubscriptionRepository->findByUserId($user->getId())){
            $this->iUserSubscriptionRepository->update(["status" => 'INCOMPLETE_EXPIRED', "canceled_at" => date('Y-m-d H:i:s')], "id = {$userSub->getId()}");
        }
    }

    private function customerSubscriptionTrialWillEnd($subscription){
        $this->loggerInterface->info("Assinatura de teste vai terminar", ["Assinatura" => $subscription->id]);

        $user = $this->iUserRepository->findByStripeCustomerId($subscription->customer);
        if($userSub = $this->iUserSubscriptionRepository->findByUserId($user->getId())){
            $time = $userSub->getTrialEnd();
            if(!$time) $time = NULL;
            $this->iUserSubscriptionRepository->update(["status" => 'TRIALING', "trial_end" => $time], "id = {$userSub->getId()}");
            $this->loggerInterface->info("Assinatura de teste para {$userSub->getUserId()} atualizada para status TRIALING");
        }
    }

    private function customerSubscriptionPaused($subscription){
        $this->loggerInterface->info("Assinatura pausada", ["Assinatura" => $subscription->id]);

        $user = $this->iUserRepository->findByStripeCustomerId($subscription->customer);
        if($userSub = $this->iUserSubscriptionRepository->findByUserId($user->getId())){
            $this->iUserSubscriptionRepository->update(["status" => 'PAUSED', "canceled_at" => date('Y-m-d H:i:s')], "id = {$userSub->getId()}");
            $this->loggerInterface->info("Assinatura de teste para {$userSub->getUserId()} atualizada para status PAUSED");
        }
    }

    private function customerSubscriptionResumed($subscription){
        $this->loggerInterface->info("Assinatura retomada", ["Assinatura" => $subscription->id]);

        $user = $this->iUserRepository->findByStripeCustomerId($subscription->customer);
        if($userSub = $this->iUserSubscriptionRepository->findByUserId($user->getId())){
            $this->iUserSubscriptionRepository->update(["status" => 'ACTIVE', "canceled_at" => NULL], "id = {$userSub->getId()}");
            $this->loggerInterface->info("Assinatura de teste para {$userSub->getUserId()} atualizada para status PAUSED");
        }
    }

    private function customerUpdated($customer){
        $this->loggerInterface->info("CUSTOMER UPDATED", ["customerId" => $customer->id]);

        // IMPORTANTE: Só processar mudanças de CONFIGURAÇÃO, não dados pessoais
        // Dados pessoais (email, telefone, nome, address) NÃO devem ser alterados automaticamente

        // Verificar mudanças de configuração de cobrança
        if ($customer?->currency) {
            $this->loggerInterface->info("Moeda alterada para: {$customer->currency}", ["customerId" => $customer->id]);
        }

        // Verificar mudanças nas configurações de fatura
        if ($customer->invoice_settings) {
            $this->loggerInterface->info("Configurações de fatura atualizadas", ["customerId" => $customer->id]);
        }

        // Verificar mudanças nos metadados do sistema
        if ($customer->metadata?->source) {
            $this->loggerInterface->info("Fonte do sistema: {$customer->metadata->source}", ["customerId" => $customer->id]);
        }

        // Log para auditoria (sem alterar dados pessoais)
        $log = [
            "id" => $customer->id,
            "currency" => $customer->currency,
            "invoiceSettings" => $customer->invoice_settings ? 'Atualizado' : 'Não alterado',
            "metadata" => $customer->metadata,
        ];

        $this->loggerInterface->info("Customer {$customer->id} atualizado - Configurações processadas:", $log);
    }

    private function invoicePaymentSucceeded($invoice){
        $this->loggerInterface->info("Pagamento de fatura realizado", ["invoiceId" => $invoice->id]);

        $subscriptionId = $invoice->parent?->subscription_details?->subscription ?? $invoice->subscription;
        if (!$subscriptionId) {
            $this->loggerInterface->error("Fatura sem assinatura vinculada", ["invoiceId" => $invoice->id]);
            return;
        }
        
        $user = $this->iUserRepository->findByStripeCustomerId($invoice->customer);
        $userSubscription = $this->iUserSubscriptionRepository->findByUserId($user->getId());
        if(!$userSubscription){
            $this->loggerInterface->error("Assinatura não encontrada para fatura paga", [
                "invoiceId" => $invoice->id,
                "subscriptionId" => $subscriptionId
            ]);
            return;
        }

        $payment = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id);
        $paymentData = [
            "status" => "SUCCEEDED",
            "paid_at" => date("Y-m-d H:i:s"),
            "user_id" => $userSubscription->getUserId(),
            "subscription_id" => $userSubscription->getId(),
            "stripe_payment_intent_id" => $invoice->payment_intent ?? null,
            "stripe_invoice_id" => $invoice->id,
            "amount" => $invoice->amount_paid,
            "currency" => $invoice->currency,
            "description" => "Fatura {$invoice->number}",
        ];

        if ($payment) {
            $this->iPaymentRepository->update($paymentData, "id = {$payment->getId()}");
        } else {
            $this->iPaymentRepository->insert($paymentData);
        }

        if (in_array($userSubscription->getStatus(), ["INCOMPLETE", "PENDING"])) {
            $this->iUserSubscriptionRepository->update([
                "status" => "ACTIVE",
                "current_period_start" => date("Y-m-d H:i:s", $invoice->lines->data[0]->period->start ?? time()),
                "current_period_end"   => date("Y-m-d H:i:s", $invoice->lines->data[0]->period->end ?? time()),
                "stripe_subscription_id" => $subscriptionId
            ], "id = {$userSubscription->getId()}");
        }
    }

    private function invoicePaymentFailed($invoice) {
        $this->loggerInterface->warning("Pagamento de fatura falhou", [
            "invoiceId" => $invoice->id,
            "amount_due" => $invoice->amount_due,
            "currency" => $invoice->currency
        ]);

        $subscriptionId = $invoice->parent?->subscription_details?->subscription ?? $invoice->subscription;
        if (!$subscriptionId) {
            $this->loggerInterface->error("Fatura com falha não possui assinatura vinculada", [
                "invoiceId" => $invoice->id
            ]);
            return;
        }

        $user = $this->iUserRepository->findByStripeCustomerId($invoice->customer);
        $userSubscription = $this->iUserSubscriptionRepository->findByUserId($user->getId());
        if (!$userSubscription) {
            $this->loggerInterface->error("Assinatura não encontrada para fatura com falha", [
                "invoiceId" => $invoice->id,
                "subscriptionId" => $subscriptionId
            ]);
            return;
        }

        $this->iUserSubscriptionRepository->update([
            "status" => "INCOMPLETE"
        ], "id = {$userSubscription->getId()}");

        $payment = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id);
        $paymentData = [
            "user_id" => $userSubscription->getUserId(),
            "subscription_id" => $userSubscription->getId(),
            "stripe_payment_intent_id" => $invoice->payment_intent ?? null,
            "stripe_invoice_id" => $invoice->id,
            "amount" => $invoice->amount_due,
            "currency" => $invoice->currency,
            "status" => "FAILED",
            "description" => "Fatura {$invoice->number}",
            "failed_at" => date("Y-m-d H:i:s")
        ];

        if ($payment) {
            $this->iPaymentRepository->update($paymentData, "id = {$payment->getId()}");
        } else {
            $this->iPaymentRepository->insert($paymentData);
        }

        $this->loggerInterface->info("Fatura com falha processada", [
            "invoiceId" => $invoice->id,
            "userId" => $userSubscription->getUserId()
        ]);
    }

    private function invoicePaymentActionRequired($invoice) {
        $this->loggerInterface->info("Pagamento requer ação do usuário", ["invoiceId" => $invoice->id]);

        if($payment = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id)){
            $this->iPaymentRepository->update([
                "status" => "ACTION_REQUIRED",
                "failed_at" => date("Y-m-d H:i:s"),
            ], "id = {$payment->getId()}");
        }
    }

    private function invoiceVoided($invoice) {
        $this->loggerInterface->info("Fatura anulada", ["invoiceId" => $invoice->id]);

        if($payment = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id)){
            $this->iPaymentRepository->update([
                "status" => "CANCELED",
                "amount" => $invoice->amount_due,
                "currency" => $invoice->currency
            ], "id = {$payment->getId()}");

            $this->loggerInterface->info("Fatura {$invoice->id} anulada e atualizada", [
                "payment_id" => $payment->getId(),
                "status" => "CANCELED",
                "amount" => $invoice->amount_due,
                "currency" => $invoice->currency,
                "voided_at" => is_numeric($invoice?->status_transitions?->voided_at) ? date("Y-m-d H:i:s", $invoice?->status_transitions?->voided_at) : NULL
            ]);
        } else {
            $this->loggerInterface->warning("Registro de pagamento não encontrado para fatura anulada", ["invoiceId" => $invoice->id]);
        }
    }

    private function paymentIntentSucceeded($paymentIntent) {
        $this->loggerInterface->info("PaymentIntent realizado", ["paymentIntentId" => $paymentIntent->id]);

        if($payment = $this->iPaymentRepository->findByStripePaymentIntentId($paymentIntent->id)){
            $this->iPaymentRepository->update([
                "status" => "SUCCEEDED",
                "paid_at" => date("Y-m-d H:i:s"),
            ], "id = {$payment->getId()}");
        }
    }
    private function paymentIntentFailed($paymentIntent) {
        $this->loggerInterface->info("PaymentIntent falhou", ["paymentIntentId" => $paymentIntent->id]);

        if($payment = $this->iPaymentRepository->findByStripePaymentIntentId($paymentIntent->id)){
            $this->iPaymentRepository->update([
                "status" => "FAILED",
                "failed_at" => date("Y-m-d H:i:s"),
            ], "id = {$payment->getId()}");
        }
    }

    private function paymentIntentCreated($paymentIntent) {
        $this->loggerInterface->info("PaymentIntent criado", ["paymentIntentId" => $paymentIntent->id]);
        
        if($paymentIntent->customer){
            $this->loggerInterface->info("PaymentIntent {$paymentIntent->id} criado para customer {$paymentIntent->customer}", ["paymentIntentId" => $paymentIntent->id]);
        }
    }

    private function paymentIntentCanceled($paymentIntent) {
        $this->loggerInterface->info("PaymentIntent cancelado", ["paymentIntentId" => $paymentIntent->id]);

        if($payment = $this->iPaymentRepository->findByStripePaymentIntentId($paymentIntent->id)){
            $this->iPaymentRepository->update([
                "status" => "CANCELED"
            ], "id = {$payment->getId()}");

            $this->loggerInterface->info("PaymentIntent {$paymentIntent->id} cancelado e atualizado",
                [
                "paymentId" => $payment->getId(),
                "status" => "CANCELED",
                "cancellationReason" => $paymentIntent->cancellation_reason,
                "canceledAt" => date('Y-m-d H:i:s', $paymentIntent->canceled_at)
                ]);
        }
        else {
            $this->loggerInterface->info("PaymentIntent {$paymentIntent->id} (sem registro local)",
                [
                "id" => $paymentIntent->id,
                "customerId" => $paymentIntent->customer,
                "amount" => $paymentIntent->amount,
                "currency" => $paymentIntent->currency,
                "cancellationReason" => $paymentIntent->cancellation_reason,
                "canceledAt" => date('Y-m-d H:i:s', $paymentIntent->canceled_at)
                ]);
        }
    }

    private function setupIntentSucceeded($setupIntent) {
        $this->loggerInterface->info("SetupIntent bem-sucedido", ["setupIntent" => $setupIntent->id]);

        // Log para auditoria
        $this->loggerInterface->info("SetupIntent {$setupIntent->id} configurado com sucesso:", [
            "id" => $setupIntent->id,
            "customerId" => $setupIntent->customer,
            "status" => $setupIntent->status,
            "paymentMethod" => $setupIntent->payment_method,
            "usage" => $setupIntent->usage,
            "createdAt" => date('Y-m-d H:i:s',$setupIntent->created),
        ]);
    }

    private function invoiceCreated($invoice) {
        $this->loggerInterface->info("Detalhes da fatura {$invoice->id}", [
            "id" => $invoice->id,
            "customerId" => $invoice->customer,
            "subscriptionId" => $invoice->parent?->subscription_details?->subscription,
            "billingReason" => $invoice->billing_reason,
            "amount" => $invoice->amount_due,
            "currency" => $invoice->currency,
            "status" => $invoice->status,
            "number" => $invoice->number,
        ]);

        $subscriptionId = $invoice->parent?->subscription_details?->subscription ?? null;
        if (!$subscriptionId) return;

        $userSubscription = $this->iUserSubscriptionRepository->findByStripeSubscriptionId($subscriptionId);
        if (!$userSubscription) return;

        $payment = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id);
        if (!$payment) {
            $this->iPaymentRepository->insert([
                "user_id" => $userSubscription->getUserId(),
                "subscription_id" => $userSubscription->getId(),
                "stripe_invoice_id" => $invoice->id,
                "stripe_payment_intent_id" => $invoice->payment_intent ?? null,
                "amount" => $invoice->amount_due,
                "currency" => $invoice->currency,
                "status" => "PENDING",
                "description" => "Fatura {$invoice->number}",
                "created_at" => date("Y-m-d H:i:s")
            ]);
        }
        
        return;
        $customerId = $invoice->customer;
        $subscriptionId = $invoice->parent?->subscription_details?->subscription;

        $userSubscription = $this->iUserSubscriptionRepository->findByStripeSubscriptionId($subscriptionId);
        $user = $this->iUserRepository->findByStripeCustomerId($customerId);
        $plan = $this->iSubscriptionPlanRepository->findByStripePriceId($invoice->lines->data[0]->pricing->price_details->price);
        
        try{
            $periodStart = $invoice?->lines?->data[0]?->period->start;
            $periodEnd = $invoice?->lines?->data[0]?->period->end;
            $periodStart = date("Y-m-d H:i:s", $periodStart);
            $periodEnd = date("Y-m-d H:i:s", $periodEnd);
        } catch(Exception $e){
            $periodStart = date("Y-m-d H:i:s");
            $periodEnd = date("Y-m-d H:i:s");
        }
        $subPayload = [
            "user_id" => $user->getId(),
            "plan_id" => $plan->getId(),
            "status" => "INCOMPLETE",
            "stripe_subscription_id" => $subscriptionId,
            "current_period_start" => $periodStart,
            "current_period_end" => $periodEnd,
            "cancel_at_period_end" => 'FALSE'
        ];

        $this->loggerInterface->info("Buscando assinatura existente", ["invoiceId" => $invoice->id]);

        if(!$userSubscription){
            $this->loggerInterface->info("Assinatura nao encontrada. Efetuando criação.");
            $userSubscription = $this->iUserSubscriptionRepository->insert($subPayload);

            $this->loggerInterface->info("Assinatura criada com sucesso.", ["subscriptionId" => $userSubscription->getId()]);
        }
        else {
            $this->iUserSubscriptionRepository->update($subPayload, "id = {$userSubscription->getId()}");
        }
        
        $this->loggerInterface->info("Buscando fatura existente...", ["invoiceId" => $invoice->id]);
        $paymentPayload = [
            "user_id" => $userSubscription->getUserId(),
            "subscription_id" => $userSubscription->getId(),
            "stripe_invoice_id" => $invoice->id,
            "amount" => $invoice->amount_due,
            "currency" => $invoice->currency,
            "status" => "PENDING",
            "description" => "Fatura {$invoice->number} || {$invoice->id} - {$invoice->billing_reason}",
        ];

        if(!$payment = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id)){
            $this->loggerInterface->info("Fatura não localizada. Criando nova fatura");

            $payment = $this->iPaymentRepository->insert($paymentPayload);

            $this->loggerInterface->info("Fatura criada com sucesso.", ["subscriptionId" => $payment->getId()]);
        }
        else {
            $this->loggerInterface->info("Já existente uma fatura cadastrada no banco", ["invoiceId" => $invoice->id]);
        }
    }

    private function invoiceFinalized($invoice) {
        $this->loggerInterface->info("Fatura finalizada: {$invoice->id}");

        // Buscar o registro de pagamento existente
        $payment = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id);

        if ($payment) {
            // Atualizar status baseado no status da invoice
            $paymentStatus = "PENDING";
            $finalizationDate = date("Y-m-d H:i:s");

            // Quando finalizada, a invoice ainda não foi paga
            switch($invoice->status){
                case 'open':
                    $paymentStatus = "PENDING";

                    // Marcar como finalizada mas aguardando pagamento
                    if ($invoice->status_transitions?->finalized_at) {
                        try{
                            $finalizationDate = date('Y-m-d H:i:s', $invoice?->status_transitions->finalized_at);
                        }catch(Exception $e){
                            $finalizationDate = date('Y-m-d H:i:s');
                        }
                    }
                    break;
                case 'paid':
                    $paymentStatus = "SUCCEEDED";
                    break;
                case 'void':
                    $paymentStatus = "CANCELED";

            }

            // Atualizar o registro de pagamento
            $updateData = [
                "status" => $paymentStatus,
                "amount" => $invoice->amount_due,
                "currency" => $invoice->currency,
            ];

            // Adicionar data de finalização se disponível
            if ($finalizationDate) {
                $updateData["failed_at"] = $finalizationDate;
            }

            $this->iPaymentRepository->update($updateData, "id = {$payment->getId()}");

            $this->loggerInterface->info(`Fatura {$invoice->id} finalizada e atualizada:`, [
                    "paymentId" => $payment->getId(),
                    "status" => $paymentStatus,
                    "amount" => $invoice->amount_due,
                    "currency" => $invoice->currency,
                    "finalizedAt" => $finalizationDate,
                ]);
        } else {
            $this->loggerInterface->warning(`Registro de pagamento não encontrado para fatura finalizada: {$invoice->id}`);
        }
    }

    private function invoiceUpdated($invoice) {
        $this->loggerInterface->info(`Fatura atualizada: {$invoice->id}`);

        // Buscar o registro de pagamento existente
        $existingPayment = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id);

        if ($existingPayment) {
            // Atualizar informações da fatura
            $updateData = [
                "amount" => $invoice->amount_due,
                "currency" => $invoice->currency,
                "description" => `Fatura {$invoice->number} - $invoice->id} - {$invoice->billing_reason}`
            ];

            switch($invoice->status){
                case 'open':
                    $paymentStatus = "PENDING";
                    break;
                case 'paid':
                    $paymentStatus = "SUCCEEDED";
                    break;
                case 'void':
                    $paymentStatus = "CANCELED";
            }

            $updateData["status"] = $paymentStatus;
            $this->iPaymentRepository->update($updateData, "id = {$existingPayment->getId()}");

            $this->loggerInterface->info(`Fatura {$invoice->id} atualizada:`, [
                "paymentId" => $existingPayment->getId(),
                "status" => $updateData["status"],
                "amount" => $invoice->amount_due,
                "currency" => $invoice->currency,
            ]);
        } else {
            $this->loggerInterface->warning("Registro de pagamento não encontrado para fatura atualizada: {$invoice->id}");
        }
    }
    

  private function priceUpdated($price) {
    $this->loggerInterface->info("Preço atualizado: {$price->id}");

    $subscriptionPlan = $this->iSubscriptionPlanRepository->findByStripePriceId($price->id);

    if ($subscriptionPlan) {
        $billingPeriod = $price->metadata?->billing_period;
        $unitAmount = $price->unit_amount;

        switch($billingPeriod){
            case 'monthly':
                $this->iSubscriptionPlanRepository->update(["price_monthly" => $unitAmount, "stripe_price_id_monthly" => $price->id], "id = {$subscriptionPlan->getId()}");
                $this->loggerInterface->info("Preço mensal atualizado para {$unitAmount} no plano {$subscriptionPlan->getSlug()}");
                break;
            case 'yearly':
                $this->iSubscriptionPlanRepository->update(["price_yearly" => $unitAmount, "stripe_price_id_yearly" => $price->id], "id = {$subscriptionPlan->getId()}");
                $this->loggerInterface->info("Preço anual atualizado para {$unitAmount} no plano {$subscriptionPlan->getSlug()}");
                break;
        }
    } else {
      $this->loggerInterface->warning("Plano de assinatura não encontrado para o preço {$price->id}");
    }
  }
}