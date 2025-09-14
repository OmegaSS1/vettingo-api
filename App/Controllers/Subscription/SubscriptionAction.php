<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Controllers\Action;
use App\Repository\Database\IDatabaseRepository;
use App\Repository\Payment\IPaymentRepository;
use App\Repository\PaymentMethod\IPaymentMethodRepository;
use App\Repository\SubscriptionPlan\ISubscriptionPlanRepository;
use App\Repository\User\IUserRepository;
use App\Repository\UserEmail\IUserEmailRepository;
use App\Repository\UserPhone\IUserPhoneRepository;
use App\Repository\UserSubscription\IUserSubscriptionRepository;
use App\Services\Stripe;
use Psr\Log\LoggerInterface;


abstract class SubscriptionAction extends Action
{
	protected IDatabaseRepository $iDatabaseRepository;
    protected IUserRepository $iUserRepository;
    protected ISubscriptionPlanRepository $iSubscriptionPlanRepository;
    protected IPaymentRepository $iPaymentRepository;
    protected IPaymentMethodRepository $iPaymentMethodRepository;
    protected Stripe $stripe;
    protected IUserEmailRepository $iUserEmailRepository;
    protected IUserPhoneRepository $iUserPhoneRepository;
    protected IUserSubscriptionRepository $iUserSubscriptionRepository;

    public function __construct (
        LoggerInterface $logger,
		IDatabaseRepository $iDatabaseRepository,
        IUserRepository $iUserRepository,
        ISubscriptionPlanRepository $iSubscriptionPlanRepository,
        IPaymentRepository $iPaymentRepository,
        IPaymentMethodRepository $iPaymentMethodRepository,
        Stripe $stripe,
        IUserEmailRepository $iUserEmailRepository,
        IUserPhoneRepository $iUserPhoneRepository,
        IUserSubscriptionRepository $iUserSubscriptionRepository
    )
    {
        parent::__construct($logger, $iDatabaseRepository);
        $this->iUserRepository = $iUserRepository;
        $this->iSubscriptionPlanRepository = $iSubscriptionPlanRepository;
        $this->iPaymentRepository = $iPaymentRepository;
        $this->iPaymentMethodRepository = $iPaymentMethodRepository;
        $this->stripe = $stripe;
        $this->iUserEmailRepository = $iUserEmailRepository;
        $this->iUserPhoneRepository = $iUserPhoneRepository;
        $this->iUserSubscriptionRepository = $iUserSubscriptionRepository;
    }
}
