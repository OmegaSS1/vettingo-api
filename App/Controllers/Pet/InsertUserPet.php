<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Traits\MessageException;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;


class InsertUserPet extends PetAction {

    protected function action(): Response {

        $form = $this->post();
        $this->validate($form);

        $this->iDatabaseRepository->disableCommit();
        if($form["avatar"]){
            try {
                $form["avatar"] = $this->vettingoBucket->upload($form['filename'], $form['decodedImg']);
            } catch (Exception $e) {
                $this->loggerInterface->info('(DO S3 AWS)', array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine()));
                throw new Exception("Falha interna ao salvar imagem.", 500);
            }
        }

        try {
            $pet = $this->iPetRepository->insert([
                "name" => $form["name"],
                "owner_id" => $this->USER->sub,
                "pet_type_id"=> $form["petTypeId"],
                "breed"=> $form["breed"] ?? "",
                "birth_date"=> $form["birthDate"] ?? NULL,
                "gender"=> $form["gender"] ?? "O",
                "weight"=> $form["weight"] ?? "",
                "has_pedigree"=> $form["hasPedigree"],
                "pedigree_number"=> $form["pedigreeNumber"] ?? "",
                "avatar"=> $form["avatar"] ?? "",
                "description" => $form["description"] ?? "",
                '"isActive"' => 'TRUE'
            ], true);
        } catch (Exception $e) {
            if(!empty($form["avatar"]))
                $this->vettingoBucket->delete($form["avatar"]);

            $this->loggerInterface->info("Falha ao tentar cadastrar o pet", ["message" => $e->getMessage(), "code" => $e->getCode(), "line" => $e->getLine(), "file" => $e->getFile()]);
            throw new Exception('Falha ao tentar cadastrar o pet', 400);
        }

        $this->iDatabaseRepository->commit();
        return $this->respondWithData($pet);
    }

    private function validate(array &$form){
        $this->validKeysForm($form, 
            ["name","petTypeId"], 
            ["Nome do Pet","Tipo de Pet"]);

        $idTutor = filter_var($this->USER->sub, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $petTypeId = filter_var($form["petTypeId"], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if(!$idTutor || !$this->iUserRepository->findById($idTutor)){
            throw MessageException::TUTOR_NOT_FOUND(null);
        }
        else if(!$petTypeId || !$this->iPetTypeRepository->findById($petTypeId)){
            throw MessageException::PET_TYPE_NOT_FOUND(null);
        }
        else if(!empty($form["birthDate"])){
            try {
                if(new DateTimeImmutable($form["birthDate"]) > new DateTimeImmutable('today')){
                    throw new Exception("Data de nascimento não pode ser no futuro", 400);
                }
            } catch (Exception $e) {
                    throw new Exception("Data de nascimento inválida.", 400);
            }
        }

        $allowedValues = [true, false];
        if(!in_array($form["hasPedigree"], $allowedValues)){
            throw new Exception("Opção invalida para o campo 'Este pet possui pedigree'", 400);
        }
        
        if($form["hasPedigree"] === true){
            $form["hasPedigree"] = 'TRUE';
            $this->validKeysForm($form, ["pedigreeNumber"], ["Numero do Pedigree"]);
            if($this->iPetRepository->findByPedigreeNumber($form["pedigreeNumber"])){
                throw MessageException::ALREADY_EXISTS("Pedigree Number");
            }
        }else {
            $form["hasPedigree"] = 'FALSE';
        }

        if($form["avatar"]){
            $filename = "pet-avatar-" . $this->USER->sub . "-" . time();
            [$filename, $decodedImg] =  $this->decodeBase64($form["avatar"], $filename);
            $folder = "avatars/pet/";

            $filename = $folder . $filename;

            $form["decodedImg"] = $decodedImg;
            $form["filename"] = $filename;
        }
 	}
}