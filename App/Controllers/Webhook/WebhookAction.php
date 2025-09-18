<?php

declare(strict_types=1);
namespace App\Controllers\Webhook;

use App\Controllers\Action;
use App\Repository\Database\IDatabaseRepository;
use App\Repository\Payment\IPaymentRepository;
use App\Repository\SubscriptionPlan\ISubscriptionPlanRepository;
use App\Repository\User\IUserRepository;
use App\Repository\UserSubscription\IUserSubscriptionRepository;
use App\Repository\Veterinarian\IVeterinarianRepository;
use App\Repository\VeterinarianApprovalPending\IVeterinarianApprovalPendingRepository;
use App\Repository\WebhookEvent\IWebhookEventRepository;
use App\Services\Stripe;
use Psr\Log\LoggerInterface;

abstract class WebhookAction extends Action
{
	protected IDatabaseRepository $iDatabaseRepository;
    protected IWebhookEventRepository $iWebhookEventRepository;
    protected IUserRepository $iUserRepository;
    protected IUserSubscriptionRepository $iUserSubscriptionRepository;
    protected IVeterinarianRepository $iVeterinarianRepository;
    protected IVeterinarianApprovalPendingRepository $iVeterinarianApprovalPendingRepository;
    protected IPaymentRepository $iPaymentRepository;
    protected ISubscriptionPlanRepository $iSubscriptionPlanRepository;
    protected Stripe $stripe;
    
    public function __construct (
        LoggerInterface $logger,
		IDatabaseRepository $iDatabaseRepository,
        IWebhookEventRepository $iWebhookEventRepository,
        IUserRepository $iUserRepository,
        IUserSubscriptionRepository $iUserSubscriptionRepository,
        IVeterinarianRepository $iVeterinarianRepository,
        IVeterinarianApprovalPendingRepository $iVeterinarianApprovalPendingRepository,
        IPaymentRepository $iPaymentRepository,
        ISubscriptionPlanRepository $iSubscriptionPlanRepository,
        Stripe $stripe
    )
    {
        parent::__construct($logger, $iDatabaseRepository);
        $this->iVeterinarianRepository = $iVeterinarianRepository;
        $this->iVeterinarianApprovalPendingRepository = $iVeterinarianApprovalPendingRepository;
        $this->iWebhookEventRepository = $iWebhookEventRepository;
        $this->iUserRepository = $iUserRepository;
        $this->iUserSubscriptionRepository = $iUserSubscriptionRepository;
        $this->iPaymentRepository = $iPaymentRepository;
        $this->iSubscriptionPlanRepository = $iSubscriptionPlanRepository;
        $this->stripe = $stripe;
    }
}
