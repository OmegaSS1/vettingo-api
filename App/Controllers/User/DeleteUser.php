<?php

declare(strict_types=1);
namespace App\Controllers\User;

use App\Controllers\User\UserAction;
use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteUser extends UserAction {

    protected function action(): Response {
        $this->iDatabaseRepository->disableCommit();
        
        if(!$user = $this->iUserRepository->findById($this->USER->sub)){
            throw MessageException::USER_NOT_FOUND(null);
        };

        $this->iUserRepository->update([
            '"isActive"' => 'FALSE',
            "deleted_at" => date("Y-m-d H:i:s"),
        ], "id = {$this->USER->sub}");

        try {
            $this->stripe->cancelAllSubscription($user->getStripeCustomerId());
            $this->stripe->removeAllPaymentMethod($user->getStripeCustomerId());
        } catch (Exception $e) {
            $this->loggerInterface->error("Erro ao excluir usuario: ". $e->getMessage());
            throw new Exception("Não foi possivel excluir o usuario nesse momento. Tente novamente mais tarde ou entre em contato com a equipe de suporte do sistema.", 500);
        };

        $this->loggerInterface->info("A conta do usuario {$this->USER->sub} foi excluida! Todos os meios de pagamentos e assinaturas serão cancelados ou removidos");
        $this->iDatabaseRepository->commit();

        return $this->respondWithData(["message" => "Usuario deletado com sucesso"]);
    }
}