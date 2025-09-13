<?php

declare(strict_types=1);
namespace App\Controllers\Auth;

use App\Traits\MessageException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class Register extends AuthAction {

    protected function action(): Response {

        $form = $this->post();
        $this->validate($form);

        $this->iDatabaseRepository->disableCommit();
        
        $fisrtName = trim($form["userFirstName"]);
        $lastName = trim($form["userLastName"]);

        $user = $this->iUserRepository->insert([
            "role" => trim($form['role']),
            "cpf" => $form['userCpf'],
            "first_name" => $fisrtName,
            "last_name"=> $lastName,
            "gender" => $form["userGender"],
            "birth_date" => $form["userBirthDate"],
            "wants_newsletter" => $form["userWantsNewsletter"],
        ]);

        $this->iUserEmailRepository->insert([
            "email" => $form["email"],
            "user_id" => $user->getId()
        ]);

        $this->iUserPhoneRepository->insert([
            "number" => $form["phoneNumber"],
            '"areaCode"' => $form["phoneDDD"],
            '"countryCode"' => $form["phoneDDI"],
            "user_id" => $user->getId()
        ]);

        $password_hash = password_hash(trim($form["userPassword"]), PASSWORD_DEFAULT);
        $this->iUserSecurityProfileRepository->insert([
            "password" => $password_hash,
            "user_id" => $user->getId()
        ]);

        $fullName = "$fisrtName $lastName";
        $phone = "+{$form["phoneDDI"]}{$form["phoneDDD"]}{$form["phoneNumber"]}";
        
        $customer = $this->stripe->createCustomer($form['email'], $fullName, $phone, ["user_id" => $user->getId(), "cpf" => $form["userCpf"]]);
        $stripe_customer_id = $customer->id;
        $user = $this->iUserRepository->update(["stripe_customer_id" => $stripe_customer_id], "id = {$user->getId()}");

        $this->iDatabaseRepository->commit();

        $this->toArray($user);
        return $this->respondWithData([$user, "Usuário criado com sucesso!"]);
    }

    private function validate(array &$form): void{
        $statusCode = 400;

        $this->validKeysForm($form, 
            ["userCpf","userFirstName","userLastName","userGender","userBirthDate","role", "email","phoneNumber","phoneDDD","phoneDDI","userPassword"],
            ["CPF","Primeiro Nome","Sobrenome","Gênero","Data de Nascimento","Tipo de Conta","Email","Número","DDD","DDI do Estado","Senha"]
        );

        $form["phoneNumber"] = preg_replace("/\D/", "", $form["phoneNumber"]);
        $form["phoneDDD"] = preg_replace("/\D/", "", $form["phoneDDD"]);
        $form["phoneDDI"] = preg_replace("/\D/", "", $form["phoneDDI"]);
        $typeNumber = str_split($form["phoneNumber"])[0];
       
        $form['userCpf'] = preg_replace("/\D/", "", $form["userCpf"]);

        $form["email"] = strtolower($form["email"]);

        $age = $this->diffBetweenDatetimes($form['userBirthDate'], date('Y-m-d'), 'y');

        $this->validateCPF($form['userCpf']);
        $this->validateStrongPassword($form['userPassword'], 8);

        if($age < 18) {
            throw new Exception("O usuário precisa ter 18 anos ou mais!", $statusCode);
        }
        else if($age > 125){
            throw new Exception("A data de nascimento está inválida!", $statusCode);
        }
        else if(!filter_var($form['email'], FILTER_VALIDATE_EMAIL)){
            throw MessageException::EMAIL();
        }
        else if($this->iUserRepository->findByCpf($form["userCpf"])) {
            throw MessageException::ALREADY_EXISTS("CPF");
        }
        else if($this->iUserEmailRepository->findByEmail($form["email"])) {
            throw MessageException::ALREADY_EXISTS("EMAIL");
        }
        else if($typeNumber == "9" and strlen($form['phoneNumber']) != 9){
            throw MessageException::CELLPHONE();
        }
        else if($typeNumber != "9" and strlen($form['phoneNumber']) != 8){
            throw MessageException::PHONE();
        }
        else if(!in_array($form['userGender'], ['F', 'M', 'O'])){
            throw new Exception('O gênero selecionado está inválido.', $statusCode);
        }

        $form["userWantsNewsletter"] = $form["userWantsNewsletter"] === true ? 'TRUE' : 'FALSE';
        $form['email'] = filter_var($form['email'], FILTER_SANITIZE_EMAIL);
 	}
}