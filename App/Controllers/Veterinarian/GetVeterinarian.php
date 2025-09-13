<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use Psr\Http\Message\ResponseInterface as Response;

class GetVeterinarian extends VeterinarianAction {

    protected function action(): Response {

        if($vet = $this->iVeterinarianRepository->findByUserId($this->USER->sub)) {
            $userPhone = $this->iUserPhoneRepository->findById($vet->getPhoneId());
            $userEmail = $this->iUserEmailRepository->findById($vet->getProfessionalEmailId());
            
            $data = [
                "id" => $vet->getId(),
                "userId" => $vet->getUserId(),
                "bio" => $vet->getBio(),
                "website" => $vet->getWebsite(),
                "crmv" => $vet->getCrmv(),
                "crmvStateId" => $vet->getCrmvStateId(),
                "phoneId" => $vet->getPhoneId(),
                "professionalEmailId" => $vet->getProfessionalEmailId(),
                "professionalEmail" => $userEmail->getEmail(),
                "professionalPhone" => sprintf("%s %s %s", $userPhone->getCountryCode(), $userPhone->getAreaCode(), $userPhone->getNumber()),
                "avatar" => $vet->getAvatar(),
                "profilePhotos" => $vet->getProfilePhotos(),
                "providesEmergencyService" => $vet->getEmergencialAttendance(),
                "providesHomeService" => $vet->getDomiciliaryAttendance(),
                "isActive" => $vet->getIsActive(),
                "createdAt" => $vet->getCreatedAt(),
                "updatedAt" => $vet->getUpdatedAt(),
                "deletedAt" => $vet->getDeletedAt()
            ];
            $this->toArray($data);
        }
        return $this->respondWithData($data ?? []);
    }
}