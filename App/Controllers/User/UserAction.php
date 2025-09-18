<?php

declare(strict_types=1);
namespace App\Controllers\User;

use App\Controllers\Action;
use App\Repository\Database\IDatabaseRepository;
use App\Repository\PaymentMethod\IPaymentMethodRepository;
use App\Repository\Pet\IPetRepository;
use App\Repository\PetConsult\IPetConsultRepository;
use App\Repository\SubscriptionPlan\ISubscriptionPlanRepository;
use App\Repository\User\IUserRepository;
use App\Repository\UserEmail\IUserEmailRepository;
use App\Repository\UserPhone\IUserPhoneRepository;
use App\Repository\UserSubscription\IUserSubscriptionRepository;
use App\Repository\Veterinarian\IVeterinarianRepository;
use App\Services\Stripe;
use Psr\Log\LoggerInterface;
use App\Services\VettingoBucket;

abstract class UserAction extends Action
{
	protected IDatabaseRepository $iDatabaseRepository;
    protected IUserRepository $iUserRepository;
    protected IUserPhoneRepository $iUserPhoneRepository;
    protected IUserEmailRepository $iUserEmailRepository;
    protected IUserSubscriptionRepository $iUserSubscriptionRepository;
    protected ISubscriptionPlanRepository $iSubscriptionPlanRepository;
    protected IVeterinarianRepository $iVeterinarianRepository;
    protected IPetConsultRepository $iPetConsultRepository;
    protected IPetRepository $iPetRepository;
    protected VettingoBucket $vettingoBucket; 
    protected Stripe $stripe;
    protected IPaymentMethodRepository $iPaymentMethodRepository;
    
    public function __construct (
        LoggerInterface $logger,
		IDatabaseRepository $iDatabaseRepository,
        IUserRepository $iUserRepository,
        IUserPhoneRepository $iUserPhoneRepository,
        IUserEmailRepository $iUserEmailRepository,
        IUserSubscriptionRepository $iUserSubscriptionRepository,
        ISubscriptionPlanRepository $iSubscriptionPlanRepository,
        IVeterinarianRepository $iVeterinarianRepository,
        IPetConsultRepository $iPetConsultRepository,
        IPetRepository $iPetRepository,
        VettingoBucket $vettingoBucket,
        Stripe $stripe,
        IPaymentMethodRepository $iPaymentMethodRepository
    )
    {
        parent::__construct($logger, $iDatabaseRepository);
        $this->iUserRepository = $iUserRepository;
        $this->iUserPhoneRepository = $iUserPhoneRepository;
        $this->iUserEmailRepository = $iUserEmailRepository;
        $this->iUserSubscriptionRepository = $iUserSubscriptionRepository;
        $this->iSubscriptionPlanRepository = $iSubscriptionPlanRepository;
        $this->iVeterinarianRepository = $iVeterinarianRepository;
        $this->iPetConsultRepository = $iPetConsultRepository;
        $this->iPetRepository = $iPetRepository;
        $this->vettingoBucket = $vettingoBucket;
        $this->stripe = $stripe;
        $this->iPaymentMethodRepository = $iPaymentMethodRepository;
    }
}
