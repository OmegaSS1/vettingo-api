<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class CheckoutCancel extends SubscriptionAction {

    protected function action(): Response {
        $queryParams = $this->request->getQueryParams();
        $sessionId = $queryParams['session_id'] ?? null;

        if ($sessionId) {
            // Aqui você poderia buscar a sessão e atualizar no banco se necessário
            throw new Exception("Pagamento cancelado. Session ID: $sessionId", 500);
        }

        // Retorna status 200
        return $this->respondWithData(["Pagamento cancelado. Nenhuma sessão informada."]);
    }
}