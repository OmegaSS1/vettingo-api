<?php

declare(strict_types=1);
namespace App\Controllers\Auth;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class ChangePassword extends AuthAction {

    protected function action(): Response {

        $form = $this->post();
        $this->validate($form);

        $this->iUserSecurityProfileRepository->update([
            "password" => $form["newPassword"]
        ], "user_id = {$this->USER->sub}");

        return $this->respondWithData(["message" => "Senha alterada com sucesso."]);
    }

    private function validate(array &$form){
        $this->validKeysForm($form, ["currentPassword", "newPassword"], ["Senha atual", "Nova senha"]);

        $id = $this->USER->sub;

        if(!is_numeric($id)){
            throw MessageException::VETERINARIAN_NOT_FOUND(null);
        }
        else if(!$user = $this->iUserSecurityProfileRepository->findByUserId($id)){
            throw MessageException::VETERINARIAN_NOT_FOUND(null);
        }
        else if(!password_verify($form["currentPassword"],$user->getPassword())){
            throw new Exception("A senha atual está incorreta!", 400);
        }

        $form["newPassword"] = password_hash($form["newPassword"], PASSWORD_DEFAULT);
 	}
}