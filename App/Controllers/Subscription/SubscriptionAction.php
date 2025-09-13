<?php

declare(strict_types=1);
namespace App\Controllers\Subscription;

use App\Controllers\Action;
use App\Repository\Database\IDatabaseRepository;
use App\Repository\Payment\IPaymentRepository;
use App\Repository\PaymentMethod\IPaymentMethodRepository;
use App\Repository\SubscriptionPlan\ISubscriptionPlanRepository;
use App\Repository\User\IUserRepository;
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

    public function __construct (
        LoggerInterface $logger,
		IDatabaseRepository $iDatabaseRepository,
        IUserRepository $iUserRepository,
        ISubscriptionPlanRepository $iSubscriptionPlanRepository,
        IPaymentRepository $iPaymentRepository,
        IPaymentMethodRepository $iPaymentMethodRepository,
        Stripe $stripe
    )
    {
        parent::__construct($logger, $iDatabaseRepository);
        $this->iUserRepository = $iUserRepository;
        $this->iSubscriptionPlanRepository = $iSubscriptionPlanRepository;
        $this->iPaymentRepository = $iPaymentRepository;
        $this->iPaymentMethodRepository = $iPaymentMethodRepository;
        $this->stripe = $stripe;
    }
}
