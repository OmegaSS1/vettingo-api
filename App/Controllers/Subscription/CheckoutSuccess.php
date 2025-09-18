<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class CheckoutSuccess extends SubscriptionAction {

    protected function action(): Response {
        $queryParams = $this->request->getQueryParams();
		$sessionId = $queryParams['session_id'] ?? null;

		if (!$sessionId) {
            throw new Exception('ID da sessao nao informado', 500);
		}

		try {
			// Recupera a sessão do Stripe
			$session = $this->stripe->retrieveCheckout($sessionId);
			$user = $this->iUserRepository->findByStripeCustomerId($session->customer);

			$payload = [
				"stripe_invoice_id" => $session->invoice,
				"status" => "PENDING"
			];

			if($session->payment_status === "paid"){
				$payload["status"] = "SUCCEEDED";
			}

			$paymentMethodId = $session->payment_method_configuration_details->id;
			
			try{

				if($paymentMethodId){
					$this->stripe->setPaymentMethodToCustomer($paymentMethodId, $session->customer);
				}
			}catch(Exception $e){
				$this->loggerInterface->info("Não foi possivel adicionar um cartão de pagamento default no {$session->customer}", ["message" => $e->getMessage()]);
			}
	
			$this->iUserSubscriptionRepository->update(["stripe_subscription_id" => $session->subscription], "user_id = {$user->getId()}");
			$this->iPaymentRepository->update($payload, "stripe_checkout_id = '{$session->id}'");


			return $this->respondWithData([]);

		} catch (Exception $e) {
			throw new Exception("Falha ao registrar o pagamento checkout: {$session->id} - invoice: ${$session->invoice}" . $e->getMessage());
		}
    }
}