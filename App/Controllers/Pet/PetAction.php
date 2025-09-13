<?php

declare(strict_types=1);
namespace App\Controllers\Pet;

use App\Controllers\Action;
use App\Repository\Database\IDatabaseRepository;
use App\Repository\Pet\IPetRepository;
use App\Repository\PetType\IPetTypeRepository;
use App\Repository\PetTypeCategory\IPetTypeCategoryRepository;
use App\Repository\User\IUserRepository;
use App\Services\VettingoBucket;
use Psr\Log\LoggerInterface;


abstract class PetAction extends Action
{
	protected IDatabaseRepository $iDatabaseRepository;
    protected IUserRepository $iUserRepository;
    protected IPetTypeRepository $iPetTypeRepository;
    protected IPetTypeCategoryRepository $iPetCategoryRepository;
    protected IPetRepository $iPetRepository;
    protected VettingoBucket $vettingoBucket;
    
    public function __construct (
        LoggerInterface $logger,
		IDatabaseRepository $iDatabaseRepository,
        IUserRepository $iUserRepository,
        IPetTypeRepository $iPetTypeRepository,
        IPetTypeCategoryRepository $iPetCategoryRepository,
        IPetRepository $iPetRepository,
        VettingoBucket $vettingoBucket
    )
    {
        parent::__construct($logger, $iDatabaseRepository);
        $this->iUserRepository = $iUserRepository;
        $this->iPetTypeRepository = $iPetTypeRepository;
        $this->iPetCategoryRepository = $iPetCategoryRepository;
        $this->iPetRepository = $iPetRepository;
        $this->vettingoBucket = $vettingoBucket;
    }
}
