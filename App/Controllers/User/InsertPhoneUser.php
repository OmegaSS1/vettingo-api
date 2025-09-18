<?php

declare(strict_types=1);
namespace App\Controllers\User;

use App\Traits\MessageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class InsertPhoneUser extends UserAction {

    protected function action(): Response {
        $form = $this->post();
        $this->validate($form);

        $user = $this->iUserPhoneRepository->insert([
            "number" => $form["number"],
            '"areaCode"'=> $form["areaCode"],
            '"countryCode"'=> $form["countryCode"],
            "user_id" => $this->USER->sub,
            '"isPublic"' => $form["isPublic"],
            '"isActive"' => $form["isActive"],
            '"isPrimary"' => $form["isPrimary"],
            '"isWhatsapp"' => $form["isWhatsapp"]
        ]);

        $this->toArray($user);
        return $this->respondWithData($user);
    }

    private function validate(&$form){
        $this->validKeysForm($form, 
        ["number","areaCode","countryCode","isActive","isPublic", "isPrimary", "isWhatsapp"],
        ["Telefone","Código de Área (Telefone)","Código do País (Telefone)","Ativo", "Público", "Principal", "WhatsApp"]);
        
        $form["number"] = preg_replace("/\D/", "", $form["number"]);
        $form["areaCode"] = preg_replace("/\D/", "", $form["areaCode"]);
        $form["countryCode"] = preg_replace("/\D/", "", $form["countryCode"]);
        $typeNumber = str_split($form["number"])[0];

        if($typeNumber == "9" and strlen($form['number']) != 9){
            throw MessageException::CELLPHONE();
        }
        else if($typeNumber != "9" and strlen($form['number']) != 8){
            throw MessageException::PHONE();
        }

        if($this->iUserPhoneRepository->findByPhone($form["number"])){
            throw MessageException::ALREADY_EXISTS('NUMERO');
        }

        $acceptValues = [true, false];
        foreach($form as $k => $v){
            if(in_array($k, ["number", "areaCode", "countryCode"])) continue;
            if(!in_array($v, $acceptValues)){
                throw new Exception("O valor informado está inválido.", 400);
            }
            $form[$k] = $v === true ? 'TRUE' : 'FALSE';
        }
    }
}