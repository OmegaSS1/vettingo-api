<?php

declare (strict_types= 1);

use Psr\Log\LoggerInterface;
use DI\ContainerBuilder;

use App\Repository\Address\{IAddressRepository, AddressRepository};
use App\Repository\City\{ICityRepository, CityRepository};
use App\Repository\Coupon\{ICouponRepository, CouponRepository};
use App\Repository\Database\{IDatabaseRepository, DatabaseRepository};
use App\Repository\Invoice\{IInvoiceRepository, InvoiceRepository};
use App\Repository\Payment\{IPaymentRepository, PaymentRepository};
use App\Repository\PaymentAttempt\{IPaymentAttemptRepository, PaymentAttemptRepository};
use App\Repository\PaymentMethod\{IPaymentMethodRepository, PaymentMethodRepository};
use App\Repository\Pet\{IPetRepository, PetRepository};
use App\Repository\PetType\{IPetTypeRepository, PetTypeRepository};
use App\Repository\PetTypeCategory\{IPetTypeCategoryRepository, PetTypeCategoryRepository};
use App\Repository\PhoneVerification\{IPhoneVerificationRepository, PhoneVerificationRepository};
use App\Repository\Refund\{IRefundRepository, RefundRepository};
use App\Repository\Specialization\{ISpecializationRepository, SpecializationRepository};
use App\Repository\SpecializationCategory\{ISpecializationCategoryRepository, SpecializationCategoryRepository};
use App\Repository\State\{IStateRepository, StateRepository};
use App\Repository\SubscriptionCoupon\{ISubscriptionCouponRepository, SubscriptionCouponRepository};
use App\Repository\SubscriptionHistory\{ISubscriptionHistoryRepository, SubscriptionHistoryRepository};
use App\Repository\SubscriptionItem\{ISubscriptionItemRepository, SubscriptionItemRepository};
use App\Repository\SubscriptionPlan\{ISubscriptionPlanRepository, SubscriptionPlanRepository};
use App\Repository\SubscriptionUsage\{ISubscriptionUsageRepository, SubscriptionUsageRepository};
use App\Repository\User\{IUserRepository, UserRepository};
use App\Repository\UserBillingInfo\{IUserBillingInfoRepository, UserBillingInfoRepository};
use App\Repository\UserEmail\{IUserEmailRepository, UserEmailRepository};
use App\Repository\UserEmailVerification\{IUserEmailVerificationRepository, UserEmailVerificationRepository};
use App\Repository\UserPhone\{IUserPhoneRepository, UserPhoneRepository};
use App\Repository\UserSecurityProfile\{IUserSecurityProfileRepository, UserSecurityProfileRepository};
use App\Repository\UserSubscription\{IUserSubscriptionRepository, UserSubscriptionRepository};
use App\Repository\Veterinarian\{IVeterinarianRepository, VeterinarianRepository};
use App\Repository\VeterinarianApprovalPending\{IVeterinarianApprovalPendingRepository, VeterinarianApprovalPendingRepository};
use App\Repository\VetPetType\{IVetPetTypeRepository, VetPetTypeRepository};
use App\Repository\VetReview\{IVetReviewRepository, VetReviewRepository};
use App\Repository\VetReviewRemoveRequest\{IVetReviewRemoveRequestRepository, VetReviewRemoveRequestRepository};
use App\Repository\VetReviewRemoveRequestStatus\{IVetReviewRemoveRequestStatusRepository, VetReviewRemoveRequestStatusRepository};
use App\Repository\VetSpecialization\{IVetSpecializationRepository, VetSpecializationRepository};
use App\Repository\VetWorkLocation\{IVetWorkLocationRepository, VetWorkLocationRepository};
use App\Repository\VetWorkLocationSchedule\{IVetWorkLocationScheduleRepository, VetWorkLocationScheduleRepository};
use App\Repository\WebhookEvent\{IWebhookEventRepository, WebhookEventRepository};

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        IAddressRepository::class                      => \DI\autowire(AddressRepository::class),
        ICityRepository::class                         => \DI\autowire(CityRepository::class),
        ICouponRepository::class                       => \DI\autowire(CouponRepository::class),
        IDatabaseRepository::class                     => \DI\autowire(DatabaseRepository::class),
        IInvoiceRepository::class                      => \DI\autowire(InvoiceRepository::class),
        IPaymentRepository::class                      => \DI\autowire(PaymentRepository::class),
        IPaymentAttemptRepository::class               => \DI\autowire(PaymentAttemptRepository::class),
        IPaymentMethodRepository::class                => \DI\autowire(PaymentMethodRepository::class),
        IPetRepository::class                          => \DI\autowire(PetRepository::class),
        IPetTypeRepository::class                      => \DI\autowire(PetTypeRepository::class),
        IPetTypeCategoryRepository::class              => \DI\autowire(PetTypeCategoryRepository::class),
        IPhoneVerificationRepository::class            => \DI\autowire(PhoneVerificationRepository::class),
        IRefundRepository::class                       => \DI\autowire(RefundRepository::class),
        ISpecializationRepository::class               => \DI\autowire(SpecializationRepository::class),
        ISpecializationCategoryRepository::class       => \DI\autowire(SpecializationCategoryRepository::class),
        IStateRepository::class                        => \DI\autowire(StateRepository::class),
        ISubscriptionCouponRepository::class           => \DI\autowire(SubscriptionCouponRepository::class),
        ISubscriptionHistoryRepository::class          => \DI\autowire(SubscriptionHistoryRepository::class),
        ISubscriptionItemRepository::class             => \DI\autowire(SubscriptionItemRepository::class),
        ISubscriptionPlanRepository::class             => \DI\autowire(SubscriptionPlanRepository::class),
        ISubscriptionUsageRepository::class            => \DI\autowire(SubscriptionUsageRepository::class),
        IUserRepository::class                         => \DI\autowire(UserRepository::class),
        IUserBillingInfoRepository::class              => \DI\autowire(UserBillingInfoRepository::class),
        IUserEmailRepository::class                    => \DI\autowire(UserEmailRepository::class),
        IUserEmailVerificationRepository::class        => \DI\autowire(UserEmailVerificationRepository::class),
        IUserPhoneRepository::class                    => \DI\autowire(UserPhoneRepository::class),
        IUserSecurityProfileRepository::class          => \DI\autowire(UserSecurityProfileRepository::class),
        IUserSubscriptionRepository::class             => \DI\autowire(UserSubscriptionRepository::class),
        IVeterinarianRepository::class                 => \DI\autowire(VeterinarianRepository::class),
        IVeterinarianApprovalPendingRepository::class  => \DI\autowire(VeterinarianApprovalPendingRepository::class),
        IVetPetTypeRepository::class                   => \DI\autowire(VetPetTypeRepository::class),
        IVetReviewRepository::class                    => \DI\autowire(VetReviewRepository::class),
        IVetReviewRemoveRequestRepository::class       => \DI\autowire(VetReviewRemoveRequestRepository::class),
        IVetReviewRemoveRequestStatusRepository::class => \DI\autowire(VetReviewRemoveRequestStatusRepository::class),
        IVetSpecializationRepository::class            => \DI\autowire(VetSpecializationRepository::class),
        IVetWorkLocationRepository::class              => \DI\autowire(VetWorkLocationRepository::class),
        IVetWorkLocationScheduleRepository::class      => \DI\autowire(VetWorkLocationScheduleRepository::class),
        IWebhookEventRepository::class                 => \DI\autowire(WebhookEventRepository::class),
        "loggerDatabase"                               => \DI\autowire(LoggerInterface::class)
    ]);
};