<?php

declare(strict_types=1);
namespace App\Controllers\Veterinarian;

use App\Controllers\Action;
use App\Repository\City\ICityRepository;
use App\Repository\Database\IDatabaseRepository;
use App\Repository\State\IStateRepository;
use App\Repository\UserEmail\IUserEmailRepository;
use App\Repository\UserPhone\IUserPhoneRepository;
use App\Repository\Veterinarian\IVeterinarianRepository;
use App\Repository\VeterinarianApprovalPending\IVeterinarianApprovalPendingRepository;
use App\Repository\VetReview\IVetReviewRepository;
use App\Repository\VetWorkLocation\IVetWorkLocationRepository;
use App\Repository\VetWorkLocationSchedule\IVetWorkLocationScheduleRepository;
use Psr\Log\LoggerInterface;
use App\Services\VettingoBucket;

abstract class VeterinarianAction extends Action
{
	protected IDatabaseRepository $iDatabaseRepository;
    protected IVeterinarianRepository $iVeterinarianRepository;
    protected IVeterinarianApprovalPendingRepository $iVeterinarianApprovalPendingRepository;
    protected IVetWorkLocationRepository $iVetWorkLocationRepository;
    protected IVetWorkLocationScheduleRepository $iVetWorkLocationScheduleRepository;
    protected IVetReviewRepository $iVetReviewRepository;
    protected IUserPhoneRepository $iUserPhoneRepository;
    protected IUserEmailRepository $iUserEmailRepository;
    protected IStateRepository $iStateRepository;
    protected ICityRepository $iCityRepository;
    protected VettingoBucket $vettingoBucket; 

    public function __construct (
        LoggerInterface $logger,
		IDatabaseRepository $iDatabaseRepository,
        IVeterinarianRepository $iVeterinarianRepository,
        IVetWorkLocationRepository $iVetWorkLocationRepository,
        IVetWorkLocationScheduleRepository $iVetWorkLocationScheduleRepository,
        IVeterinarianApprovalPendingRepository $iVeterinarianApprovalPendingRepository,
        IVetReviewRepository $iVetReviewRepository,
        IUserPhoneRepository $iUserPhoneRepository,
        IUserEmailRepository $iUserEmailRepository,
        IStateRepository $iStateRepository,
        ICityRepository $iCityRepository,
        VettingoBucket $vettingoBucket
    )
    {
        parent::__construct($logger, $iDatabaseRepository);
        $this->iVeterinarianRepository = $iVeterinarianRepository;
        $this->iVetWorkLocationRepository = $iVetWorkLocationRepository;
        $this->iVetWorkLocationScheduleRepository = $iVetWorkLocationScheduleRepository;
        $this->iVeterinarianApprovalPendingRepository = $iVeterinarianApprovalPendingRepository;
        $this->iVetReviewRepository = $iVetReviewRepository;
        $this->iUserPhoneRepository = $iUserPhoneRepository;
        $this->iUserEmailRepository = $iUserEmailRepository;
        $this->iStateRepository = $iStateRepository;
        $this->iCityRepository = $iCityRepository;
        $this->vettingoBucket = $vettingoBucket;
    }
}
