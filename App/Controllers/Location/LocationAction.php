<?php

declare(strict_types=1);
namespace App\Controllers\Location;

use App\Controllers\Action;
use App\Repository\City\ICityRepository;
use App\Repository\Database\IDatabaseRepository;
use App\Repository\State\IStateRepository;
use Psr\Log\LoggerInterface;


abstract class LocationAction extends Action
{
	protected IDatabaseRepository $iDatabaseRepository;
    protected IStateRepository $iStateRepository;
    protected ICityRepository $iCityRepository;
    
    public function __construct (
        LoggerInterface $logger,
		IDatabaseRepository $iDatabaseRepository,
        IStateRepository $iStateRepository,
        ICityRepository $iCityRepository
    )
    {
        parent::__construct($logger, $iDatabaseRepository);
        $this->iStateRepository = $iStateRepository;
        $this->iCityRepository = $iCityRepository;
    }
}
