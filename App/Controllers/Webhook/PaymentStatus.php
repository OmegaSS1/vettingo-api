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
                // $this->invoiceUpdated($event->data->object);
                break;
            case 'invoice.finalized':
                // $this->invoiceFinalized($event->data->object);
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
                // $this->priceUpdated($event->data->object);
                break;
            default:
                //$this->loggerInterface->log(`Evento não tratado: ${eventData.type}`);
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

        $this->loggerInterface->info("Assinatura criada no Stripe", $log);
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
    }

    private function customerSubscriptionDeleted($subscription){
        $this->loggerInterface->info("Assinatura cancelada", ["Assinatura" => $subscription->id]);

        if($userSub = $this->iUserSubscriptionRepository->findByStripeSubscriptionId($subscription->id)){
            $this->iUserSubscriptionRepository->update(["status" => 'CANCELED', "canceled_at" => date('Y-m-d H:i:s')], "id = {$userSub->getId()}");
        }
    }

    private function customerSubscriptionIncompleteExpired($subscription){
        $this->loggerInterface->info("Assinatura incompleta expirada", ["Assinatura" => $subscription->id]);

        if($userSub = $this->iUserSubscriptionRepository->findByStripeSubscriptionId($subscription->id)){
            $this->iUserSubscriptionRepository->update(["status" => 'INCOMPLETE_EXPIRED', "canceled_at" => date('Y-m-d H:i:s')], "id = {$userSub->getId()}");
        }
    }

    private function customerSubscriptionTrialWillEnd($subscription){
        $this->loggerInterface->info("Assinatura de teste vai terminar", ["Assinatura" => $subscription->id]);

        if($userSub = $this->iUserSubscriptionRepository->findByStripeSubscriptionId($subscription->id)){
            $time = $userSub->getTrialEnd();
            if(!$time) $time = NULL;
            $this->iUserSubscriptionRepository->update(["status" => 'TRIALING', "trial_end" => $time], "id = {$userSub->getId()}");
            $this->loggerInterface->info("Assinatura de teste para {$userSub->getUserId()} atualizada para status TRIALING");
        }
    }

    private function customerSubscriptionPaused($subscription){
        $this->loggerInterface->info("Assinatura pausada", ["Assinatura" => $subscription->id]);

        if($userSub = $this->iUserSubscriptionRepository->findByStripeSubscriptionId($subscription->id)){
            $this->iUserSubscriptionRepository->update(["status" => 'PAUSED', "canceled_at" => date('Y-m-d H:i:s')], "id = {$userSub->getId()}");
            $this->loggerInterface->info("Assinatura de teste para {$userSub->getUserId()} atualizada para status PAUSED");
        }
    }

    private function customerSubscriptionResumed($subscription){
        $this->loggerInterface->info("Assinatura retomada", ["Assinatura" => $subscription->id]);

        if($userSub = $this->iUserSubscriptionRepository->findByStripeSubscriptionId($subscription->id)){
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

        if($payment = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id)){
            $this->iPaymentRepository->update([
                "status" => "SUCCEEDED",
                "paid_at" => date("Y-m-d H:i:s"),
            ], "id = {$payment->getId()}");
        } 
        else if ($invoice?->payment_intent) {
            $this->iPaymentRepository->insert([
                "user_id" => 0,
                "stripe_payment_intent_id" => $invoice->payment_intent,
                "stripe_invoice_id" => $invoice->id,
                "amount" => $invoice->amount_paid,
                "currency" => $invoice->currency,
                "status" => "SUCCEEDED",
                "description" => `Fatura {$invoice->number}`,
                "paid_at" => date("Y-m-d H:i:s"),
            ]);
        }
    }

    private function invoicePaymentFailed($invoice) {
        $this->loggerInterface->info("Pagamento de fatura falhou", ["invoiceId" => $invoice->id]);

        if($payment = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id)){
            $this->iPaymentRepository->update([
                "status" => "FAILED",
                "failed_at" => date("Y-m-d H:i:s"),
            ], "id = {$payment->getId()}");
        }
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

        if(!$this->iPaymentRepository->findByStripePaymentIntentId($paymentIntent->id)){
            if($paymentIntent->customer){
                $this->loggerInterface->info("PaymentIntent {$paymentIntent->id} criado para customer {$paymentIntent->customer}", ["paymentIntentId" => $paymentIntent->id]);
            }
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

        // TODO: Implementar lógica para processar a configuração bem-sucedida
        // Por exemplo, atualizar a configuração do usuário ou criar um novo método de pagamento
    }

    private function invoiceCreated($invoice) {
        $this->loggerInterface->info("Fatura criada", ["invoiceId" => $invoice->id]);

        // Log detalhado da invoice para debug
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

        // Criar registro de Invoice no banco se não existir
        if($invoiceRepo = $this->iPaymentRepository->findByStripeInvoiceId($invoice->id)){
            return $this->loggerInterface->info("Fatura {$invoice->id} já existe no banco");
        }

        $customerId = $invoice->customer;
        $subscriptionId = $invoice->parent?->subscription_details?->subscription;

        $this->loggerInterface->info("Buscando subscription para Stripe ID: $subscriptionId");

        if ($customerId && $subscriptionId) {
            // Buscar subscription pelo Stripe ID
            $userSubscription = $this->iUserSubscriptionRepository->findByStripeSubscriptionId($subscriptionId);

            if ($userSubscription) {
                $this->loggerInterface->info("Subscription encontrada: {$userSubscription->getId()} para usuário: {$userSubscription->getUserId()}");

                // Criar registro de pagamento no histórico
                $paymentId = $this->iPaymentRepository->insert([
                    "userId" => $userSubscription->getUserId(),
                    "subscription_id" => $userSubscription->getId(),
                    "stripe_invoice_id" => $invoice->id,
                    "amount" => $invoice->amount_due,
                    "currency" => $invoice->currency,
                    "status" => "PENDING",
                    "description" => "Fatura " . $invoice->number || $invoice->id . " - {$invoice->billing_reason}",
                ]);

                $this->loggerInterface->info("Registro de pagamento criado para fatura {$invoice->id} - Usuário: {$userSubscription->getUserId()}, Payment ID: {$paymentId}");
            } 
            else {
                $this->loggerInterface->warning("Subscription não encontrada para Stripe ID: {$subscriptionId}");

                // Criar a subscription no banco
                $this->loggerInterface->info("Criando subscription para Stripe ID: {$subscriptionId}");

                // Buscar o plano baseado no preço da invoice
                $priceId = $invoice->lines?->data[0]->price?->id;
                $planId = 1; // Plano padrão

                if ($priceId) {
                    $this->loggerInterface->info("Buscando plano para price ID: {$priceId}");
                    $subscriptionPlan = $this->iSubscriptionPlanRepository->findByStripePriceId($priceId);
                    if ($subscriptionPlan) {
                        $planId = $subscriptionPlan->getId();
                        $this->loggerInterface->info("Plano encontrado: {$subscriptionPlan->getId()} ({$subscriptionPlan->getSlug()})");
                    } 
                    else {
                        $this->loggerInterface->warning("Plano não encontrado para price ID: {$priceId}, usando plano padrão");
                        // Buscar um plano válido como fallback
                        $plans = $this->iSubscriptionPlanRepository->findAll();
                        if ($plans && count($plans) > 0) {
                            $planId = $plans[0]->getId();
                            $this->loggerInterface->info("Usando plano fallback: {$plans[0]->getId()} ({$plans[0]->getSlug()})");
                        } 
                        else {
                            $this->loggerInterface->error(`Nenhum plano disponível no sistema`);
                            return; // Não criar subscription sem plano válido
                        }
                    }
                } 
                else {
                    $this->loggerInterface->warning("Price ID não encontrado na invoice, buscando plano fallback");
                    // Buscar um plano válido como fallback
                    $plans = $this->iSubscriptionPlanRepository->findAll();
                    if ($plans && count($plans) > 0) {
                        $planId = $plans[0]->getId();
                        $this->loggerInterface->info("Usando plano fallback: {$plans[0]->getId()} ({$plans[0]->getSlug()})");
                    } else {
                        $this->loggerInterface->error("Nenhum plano disponível no sistema");
                        return; // Não criar subscription sem plano válido
                    }
                }
            }
            // Buscar usuário pelo customer ID

            // Verificar se o usuário existe
            // Buscar usuário pelo customer ID do Stripe
            if(!$user = $this->iUserRepository->findByStripeCustomerId($customerId)){
                $this->loggerInterface->error("Usuário não encontrado para customer ID: ${customerId}");
                return;
            }
            $userId = $user->getId();
            $this->loggerInterface->info("Usuário encontrado: {$user->getId()} ({$user->getFirstName()} {$user->getLastName()})");

            // Criar a subscription
            $newSubscriptionId = $this->iUserSubscriptionRepository->insert([
                "userId" => $userId,
                "planId" => $planId,
                "status" => "INCOMPLETE",
                "stripe_subscription_id" => $subscriptionId,
                "current_period_start" => is_numeric($invoice->period_start) ? date('Y-m-d H:i:s', $invoice->period_start) : null,
                "current_period_end" => is_numeric($invoice->period_end) ? date('Y-m-d H:i:s', $invoice->period_end) : null,
                "cancel_at_period_end" => false,
            ]);

            $this->loggerInterface->info("Subscription criada: {$newSubscriptionId} para usuário: ${userId}");

            // Agora criar o pagamento
            $payment = $this->iPaymentRepository->insert([
                "user_id" => $userId,
                "subscription_id" => $newSubscriptionId,
                "stripe_invoice_id" => $invoice->id,
                "amount" => $invoice->amount_due,
                "currency" => $invoice->currency,
                "status" => "PENDING",
                "description" => "Fatura ".$invoice->number ?? $invoice->id." - {$invoice->billing_reason}",
            ]);
            $this->loggerInterface->info("Pagamento criado para fatura {$invoice->id} - Usuário: $userId, Payment ID: $paymentId");
        } else {
            $this->loggerInterface->warning("Fatura {$invoice->id} sem customer ou subscription válidos");

            // Tentar criar pagamento mesmo sem subscription para não perder o registro
            if ($customerId) {
                $this->loggerInterface->info("Tentando criar pagamento para customer $customerId sem subscription");

                // Criar subscription para o customer
                $this->loggerInterface->info("Criando subscription para customer $customerId");

                // Buscar o plano baseado no preço da invoice
                $priceId = $invoice->lines?->data[0]->price?->id;
                $planId = 1; // Plano padrão

                if ($priceId) {
                    $this->loggerInterface->info("Buscando plano para price ID: {$priceId}");
                    $subscriptionPlan = $this->iSubscriptionPlanRepository->findByStripePriceId($priceId);
                    if ($subscriptionPlan) {
                        $planId = $subscriptionPlan->getId();
                        $this->loggerInterface->info(`Plano encontrado: {$subscriptionPlan->getId()} ({$subscriptionPlan->getSlug()})`);
                    } else {
                        $this->loggerInterface->warning("Plano não encontrado para price ID: {$priceId}, usando plano padrão");
                        // Buscar um plano válido como fallback
                        $plans = $this->iSubscriptionPlanRepository->findAll();
                        if ($plans && count($plans) > 0) {
                            $planId = $plans[0]->getId();
                            $this->loggerInterface->info("Usando plano fallback: {$plans[0]->getId()} ({$plans[0]->getSlug()})");
                        } else {
                            $this->loggerInterface->error("Nenhum plano disponível no sistema");
                            return; // Não criar subscription sem plano válido
                        }
        
                    }
                } else {
                    $this->loggerInterface->warning("Price ID não encontrado na invoice, buscando plano fallback");
                    $plans = $this->iSubscriptionPlanRepository->findAll();
                    if ($plans && count($plans) > 0) {
                        $planId = $plans[0]->getId();
                        $this->loggerInterface->info("Usando plano fallback: {$plans[0]->getId()} ({$plans[0]->getSlug()})");
                    } else {
                        $this->loggerInterface->error("Nenhum plano disponível no sistema");
                        return; // Não criar subscription sem plano válido
                    }
                }

                // Buscar usuário pelo customer ID do Stripe
                if(!$user = $this->iUserRepository->findByStripeCustomerId($customerId)){
                    $this->loggerInterface->error("Usuário não encontrado para customer ID: {$customerId}");
                    return;
                }
                $userId = $user->getId();
                $this->loggerInterface->info("Usuário encontrado: {$user->getId()} ({$user->getFirstName()} {$user->getLastName()})");

                // Criar a subscription
                $newSubscriptionId = $this->iUserSubscriptionRepository->insert([
                    "userId" => $userId,
                    "planId" => $planId,
                    "status" => "INCOMPLETE",
                    "stripe_subscription_id" => NULL,
                    "current_period_start" => is_numeric($invoice->period_start) ? date('Y-m-d H:i:s', $invoice->period_start) : null,
                    "current_period_end" => is_numeric($invoice->period_end) ? date('Y-m-d H:i:s', $invoice->period_end) : null,
                    "cancel_at_period_end" => false,
                ]);

                $this->loggerInterface->info("Subscription criada: {$newSubscriptionId} para usuário: {$userId}");

                // Agora criar o pagamento
                $paymentId = $this->iPaymentRepository->insert([
                    "user_id" => $userId,
                    "subscription_id" => $newSubscriptionId,
                    "stripe_invoice_id" => $invoice->id,
                    "amount" => $invoice->amount_due,
                    "currency" => $invoice->currency,
                    "status" => "PENDING",
                    "description" => "Fatura ".$invoice->number ?? $invoice->id." - {$invoice->billing_reason} (Subscription criada)",
                ]);

                $this->loggerInterface->info("Pagamento criado para fatura {$invoice->id} - Usuário: $userId, Payment ID: $paymentId");
            }
        }
    }

//     private async handleInvoiceFinalized(invoice: any) {
//     this.logger.log(`Fatura finalizada: ${invoice.id}`);

//     // Buscar o registro de pagamento existente
//     const payment = await this.paymentService.findByStripeInvoiceId(invoice.id);

//     if (payment) {
//       // Atualizar status baseado no status da invoice
//       let paymentStatus = PaymentStatus.PENDING;
//       let finalizationDate: Date | undefined;

//       // Quando finalizada, a invoice ainda não foi paga
//       if (invoice.status === 'open') {
//         paymentStatus = PaymentStatus.PENDING;
//         // Marcar como finalizada mas aguardando pagamento
//         if (invoice.status_transitions?.finalized_at) {
//           finalizationDate = stripeTimestampToDate(
//             invoice.status_transitions.finalized_at,
//           );
//         }
//       } else if (invoice.status === 'paid') {
//         paymentStatus = PaymentStatus.SUCCEEDED;
//       } else if (invoice.status === 'void') {
//         paymentStatus = PaymentStatus.CANCELED;
//       }

//       // Atualizar o registro de pagamento
//       const updateData: any = {
//         status: paymentStatus,
//         amount: invoice.amount_due,
//         currency: invoice.currency,
//       };

//       // Adicionar data de finalização se disponível
//       if (finalizationDate) {
//         updateData.failedAt = finalizationDate;
//       }

//       await this.paymentService.update(payment.id, updateData);

//       this.logger.log(`Fatura ${invoice.id} finalizada e atualizada:`, {
//         paymentId: payment.id,
//         status: paymentStatus,
//         amount: invoice.amount_due,
//         currency: invoice.currency,
//         finalizedAt: finalizationDate,
//       });
//     } else {
//       this.logger.warn(
//         `Registro de pagamento não encontrado para fatura finalizada: ${invoice.id}`,
//       );
//     }
//   }

//   private async handleInvoiceUpdated(invoice: any) {
//     this.logger.log(`Fatura atualizada: ${invoice.id}`);

//     // Buscar o registro de pagamento existente
//     const existingPayment = await this.paymentService.findByStripeInvoiceId(
//       invoice.id,
//     );

//     if (existingPayment) {
//       // Atualizar informações da fatura
//       const updateData: any = {
//         amount: invoice.amount_due,
//         currency: invoice.currency,
//         description: `Fatura ${invoice.number || invoice.id} - ${invoice.billing_reason}`,
//       };

//       // Verificar mudanças de status
//       if (invoice.status === 'paid') {
//         updateData.status = PaymentStatus.SUCCEEDED;
//       } else if (invoice.status === 'open') {
//         updateData.status = PaymentStatus.PENDING;
//       } else if (invoice.status === 'void') {
//         updateData.status = PaymentStatus.CANCELED;
//       }

//       await this.paymentService.update(existingPayment.id, updateData);

//       this.logger.log(`Fatura ${invoice.id} atualizada:`, {
//         paymentId: existingPayment.id,
//         status: updateData.status,
//         amount: invoice.amount_due,
//         currency: invoice.currency,
//       });
//     } else {
//       this.logger.warn(
//         `Registro de pagamento não encontrado para fatura atualizada: ${invoice.id}`,
//       );
//     }
//   }

//   private async handlePriceUpdated(price: any) {
//     this.logger.log(`Preço atualizado: ${price.id}`);

//     const subscriptionPlan =
//       await this.subscriptionPlanService.findByStripePriceId(price.id);

//     if (subscriptionPlan) {
//       const billingPeriod = price.metadata?.billing_period;
//       const unitAmount = price.unit_amount;

//       if (billingPeriod === 'monthly') {
//         await this.subscriptionPlanService.updatePrices(
//           subscriptionPlan.id,
//           unitAmount,
//         );
//         this.logger.log(
//           `Preço mensal atualizado para ${unitAmount} no plano ${subscriptionPlan.slug}`,
//         );
//       } else if (billingPeriod === 'yearly') {
//         await this.subscriptionPlanService.updatePrices(
//           subscriptionPlan.id,
//           undefined,
//           unitAmount,
//         );
//         this.logger.log(
//           `Preço anual atualizado para ${unitAmount} no plano ${subscriptionPlan.slug}`,
//         );
//       }
//     } else {
//       this.logger.warn(
//         `Plano de assinatura não encontrado para o preço ${price.id}`,
//       );
//     }
//   }
}