<?php

declare(strict_types=1);
namespace App\Controllers\Auth;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class NewPassword extends AuthAction {

    protected function action(): Response {

        $form = $this->post();
        $userSec = $this->validate($form);

        $this->iUserSecurityProfileRepository->update([
            "password" => $form["newPassword"]
        ], "id = {$userSec->getId()}");

        return $this->respondWithData(["message" => "Senha alterada com sucesso."]);
    }

    private function validate(array &$form){
        $this->validKeysForm($form, ["newPassword"], ["Nova senha"]);

        $idUser = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        
        if(!$idUser || !$user = $this->iUserRepository->findById($idUser)){
            throw MessageException::USER_NOT_FOUND(null);
        }
        else if(!$userSec = $this->iUserSecurityProfileRepository->findByUserId($idUser)){
            throw MessageException::USER_NOT_FOUND(null);
        }
        else if(!$user->getIsActive()){
            throw MessageException::USER_NOT_FOUND(null);
        }

        $form["newPassword"] = password_hash($form["newPassword"], PASSWORD_DEFAULT);

        return $userSec;
 	}
}