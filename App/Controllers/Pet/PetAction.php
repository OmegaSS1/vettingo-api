<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Controllers\Action;
use App\Repository\Database\IDatabaseRepository;
use App\Repository\Pet\IPetRepository;
use App\Repository\PetConsult\IPetConsultRepository;
use App\Repository\PetDocument\IPetDocumentRepository;
use App\Repository\PetType\IPetTypeRepository;
use App\Repository\PetTypeCategory\IPetTypeCategoryRepository;
use App\Repository\PetVaccine\IPetVaccineRepository;
use App\Repository\User\IUserRepository;
use App\Repository\Veterinarian\IVeterinarianRepository;
use App\Repository\VetWorkLocation\IVetWorkLocationRepository;
use App\Repository\VetWorkLocationSchedule\IVetWorkLocationScheduleRepository;
use App\Services\VettingoBucket;
use Psr\Log\LoggerInterface;


abstract class PetAction extends Action
{
	protected IDatabaseRepository $iDatabaseRepository;
    protected IUserRepository $iUserRepository;
    protected IPetTypeRepository $iPetTypeRepository;
    protected IPetTypeCategoryRepository $iPetCategoryRepository;
    protected IPetRepository $iPetRepository;
    protected IPetConsultRepository $iPetConsultRepository;
    protected IPetVaccineRepository $iPetVaccineRepository;
    protected VettingoBucket $vettingoBucket;
    protected IPetDocumentRepository $iPetDocumentRepository;
    protected IVeterinarianRepository $iVeterinarianRepository;
    protected IVetWorkLocationRepository $iVetWorkLocationRepository;
    protected IVetWorkLocationScheduleRepository $iVetWorkLocationScheduleRepository;

    public function __construct (
        LoggerInterface $logger,
		IDatabaseRepository $iDatabaseRepository,
        IUserRepository $iUserRepository,
        IPetTypeRepository $iPetTypeRepository,
        IPetTypeCategoryRepository $iPetCategoryRepository,
        IPetRepository $iPetRepository,
        IPetConsultRepository $iPetConsultRepository,
        IPetVaccineRepository $iPetVaccineRepository,
        VettingoBucket $vettingoBucket,
        IPetDocumentRepository $iPetDocumentRepository,
        IVeterinarianRepository $iVeterinarianRepository,
        IVetWorkLocationRepository $iVetWorkLocationRepository,
        IVetWorkLocationScheduleRepository $iVetWorkLocationScheduleRepository
    )
    {
        parent::__construct($logger, $iDatabaseRepository);
        $this->iUserRepository = $iUserRepository;
        $this->iPetTypeRepository = $iPetTypeRepository;
        $this->iPetCategoryRepository = $iPetCategoryRepository;
        $this->iPetRepository = $iPetRepository;
        $this->iPetConsultRepository = $iPetConsultRepository;
        $this->iPetVaccineRepository = $iPetVaccineRepository;
        $this->vettingoBucket = $vettingoBucket;
        $this->iPetDocumentRepository = $iPetDocumentRepository;
        $this->iVeterinarianRepository = $iVeterinarianRepository;
        $this->iVetWorkLocationRepository = $iVetWorkLocationRepository;
        $this->iVetWorkLocationScheduleRepository = $iVetWorkLocationScheduleRepository;
    }
}
