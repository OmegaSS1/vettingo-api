<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Controllers\Action;
use App\Repository\Database\IDatabaseRepository;
use App\Repository\Pet\IPetRepository;
use App\Repository\PetDocument\IPetDocumentRepository;
use App\Repository\PetType\IPetTypeRepository;
use App\Repository\PetTypeCategory\IPetTypeCategoryRepository;
use App\Repository\PetVaccine\IPetVaccineRepository;
use App\Repository\User\IUserRepository;
use App\Repository\Veterinarian\IVeterinarianRepository;
use App\Services\VettingoBucket;
use Psr\Log\LoggerInterface;


abstract class PetAction extends Action
{
	protected IDatabaseRepository $iDatabaseRepository;
    protected IUserRepository $iUserRepository;
    protected IPetTypeRepository $iPetTypeRepository;
    protected IPetTypeCategoryRepository $iPetCategoryRepository;
    protected IPetRepository $iPetRepository;
    protected IPetVaccineRepository $iPetVaccineRepository;
    protected VettingoBucket $vettingoBucket;
    protected IPetDocumentRepository $iPetDocumentRepository;
    protected IVeterinarianRepository $iVeterinarianRepository;
    
    public function __construct (
        LoggerInterface $logger,
		IDatabaseRepository $iDatabaseRepository,
        IUserRepository $iUserRepository,
        IPetTypeRepository $iPetTypeRepository,
        IPetTypeCategoryRepository $iPetCategoryRepository,
        IPetRepository $iPetRepository,
        IPetVaccineRepository $iPetVaccineRepository,
        VettingoBucket $vettingoBucket,
        IPetDocumentRepository $iPetDocumentRepository,
        IVeterinarianRepository $iVeterinarianRepository
    )
    {
        parent::__construct($logger, $iDatabaseRepository);
        $this->iUserRepository = $iUserRepository;
        $this->iPetTypeRepository = $iPetTypeRepository;
        $this->iPetCategoryRepository = $iPetCategoryRepository;
        $this->iPetRepository = $iPetRepository;
        $this->iPetVaccineRepository = $iPetVaccineRepository;
        $this->vettingoBucket = $vettingoBucket;
        $this->iPetDocumentRepository = $iPetDocumentRepository;
        $this->iVeterinarianRepository = $iVeterinarianRepository;
    }
}
