<?php

declare(strict_types=1);
namespace App\Controllers\User;

use App\Controllers\User\UserAction;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateUser extends UserAction {

    protected function action(): Response {
        $form = $this->post();
        $this->validate($form);

        $this->iDatabaseRepository->disableCommit();
        $user = $this->iUserRepository->findById($this->USER->sub);
        
        if($form["avatar"]){
            try {
                $oldAvatar = $user->getAvatar();
                $form["avatar"] = $this->vettingoBucket->upload($form['filename'], $form['decodedImg']);
            } catch (Exception $e) {
                $this->loggerInterface->info('(DO S3 AWS)', array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine()));
                throw new Exception("Falha interna ao salvar imagem.", 500);
            }
        }

        try {
            $user = $this->iUserRepository->update([
                "first_name" => $form["firstName"],
                "last_name" => $form["lastName"],
                "avatar" => $form["avatar"]
            ], "id = " . (int) $this->USER->sub, "");

            if($oldAvatar) {
                $this->vettingoBucket->delete($oldAvatar);
            }
        } catch (Exception $e) {
            if($form["avatar"])
                $this->vettingoBucket->delete($form["avatar"]);

            $this->loggerInterface->info("Falha ao tentar atualizar o profissional", ["message" => $e->getMessage(), "code" => $e->getCode(), "line" => $e->getLine(), "file" => $e->getFile()]);
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $this->iDatabaseRepository->commit();

        $this->toArray($user);
        return $this->respondWithData($user);
    }

    private function validate(&$form){
        $this->validKeysForm($form, 
        ["firstName", "lastName"],
        ["Primeiro Nome", "Sobrenome"]);

        if($form["avatar"]){
            $filename = "user-" . $this->USER->sub . "-" . time();
            [$filename, $decodedImg] =  $this->decodeBase64($form["avatar"], $filename);
            $folder = "avatars/user/";

            $filename = $folder . $filename;

            $form["decodedImg"] = $decodedImg;
            $form["filename"] = $filename;
        }
    }
}