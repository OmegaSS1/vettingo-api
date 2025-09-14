<?php

use App\Middleware\AuthenticationUser;
use App\Middleware\AuthenticationUserRole;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

use App\Controllers\User\{
	GetUser,UpdateUser,UserSubscription,
	GetPhoneUser,InsertPhoneUser,UpdatePhoneUser,DeletePhoneUser,
	GetEmailUser,InsertEmailUser,UpdateEmailUser,DeleteEmailUser
};
use App\Controllers\Auth\{Register,Login, ChangePassword};
use App\Controllers\Veterinarian\{
	GetVeterinarian,InsertVeterinarian,UpdateVeterinarian,
	VeterinarianApprovalStatus,UpdateDocumentVeterinarian,
	GetVetWorkLocation,InsertVetWorkLocation,UpdateVetWorkLocation,DeleteVetWorkLocation,
	GetVetWorkLocationSchedule, InsertVetWorkLocationSchedule, UpdateVetWorkLocationSchedule, DeleteVetWorkLocationSchedule,
	GetVetReviews, GetAnyVetReviews
};
use App\Controllers\Location\{
	State,
	CitiesByState
};
use App\Controllers\Subscription\{
	GetAllSubscriptions,CreateSubscription,
	CreateSetupIntent, GetUserPaymentMethods, GetUserDefaultPaymentMethods, UpdateDefaultPaymentMethods, DeletePaymentMethods,
	CreatePaymentMethod,
	GetUserPayment, GetAnyUserPayment,
};
use App\Controllers\Webhook\PaymentStatus;
use App\Controllers\Pet\{
	GetPet,InsertPet,UpdatePet,DeletePet,
	GetPetTypes,GetAnyPet,GetOverviewPet,
	GetPetByOwner,
	GetDocumentPet, InsertDocumentPet, DeleteDocumentPet,
	GetVaccinePet, InsertVaccinePet, DeleteVaccinePet, GetStatusVaccinePet
	};

return function (App $app): void {
	$container = $app->getContainer();
	$logger = $container->get(Psr\Log\LoggerInterface::class);

	// Auth
	$app->group('/auth', function(Group $auth) {		
		$auth->post('/register', Register::class);
		$auth->post('/login', Login::class);
		$auth->post('/change-password', ChangePassword::class)->add(AuthenticationUser::class);
	});

	// User
	$app->group('/users', function(Group $users) {		
		$users->get('/me', GetUser::class);
		$users->put('/me', UpdateUser::class);
		$users->delete('/me', UpdateUser::class);
		
		$users->get('/me/phones', GetPhoneUser::class);
		$users->put('/me/phones/{id}', UpdatePhoneUser::class);
		$users->post('/me/phones', InsertPhoneUser::class);
		$users->delete('/me/phones/{id}', DeletePhoneUser::class);

		$users->get('/me/emails', GetEmailUser::class);
		$users->put('/me/emails/{id}', UpdateEmailUser::class);
		$users->delete('/me/emails/{id}', DeleteEmailUser::class);
		$users->post('/me/emails', InsertEmailUser::class);
	})->add(AuthenticationUser::class);

	// Veterinarian
	$app->group('', function(Group $vet) {		
		$vet->group('/veterinarians', function(Group $vet) {		
			$vet->get('/me', GetVeterinarian::class);
			$vet->post('/me', InsertVeterinarian::class);
			$vet->put('/me', UpdateVeterinarian::class);
			$vet->get('/{id}/approval-status', VeterinarianApprovalStatus::class);
			$vet->post('/{id}/submit-approval-documents', UpdateDocumentVeterinarian::class);
		});

		$vet->group('/vet-reviews', function(Group $vet) {		
			$vet->get('/me', GetVetReviews::class);
			$vet->get('', GetAnyVetReviews::class);
		});

		$vet->group('/vet-work-locations', function(Group $vetWork) {		
			$vetWork->get('', GetVetWorkLocation::class);
			$vetWork->post('', InsertVetWorkLocation::class);
			$vetWork->put('/{id}', UpdateVetWorkLocation::class);
			$vetWork->delete('/{id}', DeleteVetWorkLocation::class);

			$vetWork->get('/{id}/schedules', GetVetWorkLocationSchedule::class);
			$vetWork->post('/{id}/schedules', InsertVetWorkLocationSchedule::class);
			$vetWork->put('/schedules/{id}', UpdateVetWorkLocationSchedule::class);
			$vetWork->delete('/schedules/{id}', DeleteVetWorkLocationSchedule::class);

		});
	})->add(new AuthenticationUserRole('VETERINARIAN', $logger))->add(AuthenticationUser::class);

	// Location
	$app->group('/location', function(Group $location) {		
		$location->get('/states', State::class);
		$location->get('/states/{id}/cities', CitiesByState::class);
	});

	// Pet
	$app->group('', function(Group $pet) use($logger) {		
		$pet->group('/pet-types', function(Group $type) {		
			$type->get('/active', GetPetTypes::class);
		});

		$pet->group('/pet-document', function(Group $document) {		
			$document->get('/{id}', GetDocumentPet::class);
			$document->post('/{id}', InsertDocumentPet::class);
			$document->delete('/{id}', DeleteDocumentPet::class);
		});

		$pet->group('/pet-vaccine', function(Group $vaccine) {		
			$vaccine->get('/status', GetStatusVaccinePet::class);
			$vaccine->get('/{id}', GetVaccinePet::class);
			$vaccine->post('/{id}', InsertVaccinePet::class);
			$vaccine->delete('/{id}', DeleteVaccinePet::class);
			//$petDocument->delete('/{id}', DeleteDocumentPet::class);
		});

		$pet->group('/pets', function(Group $pets) use($logger) {		
			$pets->post('', InsertPet::class);
			$pets->get('', GetPet::class);
			$pets->get('/owner/{ownerid}', GetPetByOwner::class)->add(new AuthenticationUserRole('ADMIN', $logger));
			$pets->get('/overview/{id}', GetOverviewPet::class);
			$pets->get('/{id}', GetAnyPet::class);
			$pets->put('/{id}', UpdatePet::class);
			$pets->delete('/{id}', DeletePet::class);
		});
	})->add(AuthenticationUser::class);

	// Subscription
	$app->group('', function(Group $subscription) {
		$subscription->group('/subscriptions', function(Group $subscription) {
			$subscription->get('/me', UserSubscription::class);
			$subscription->post('', CreateSubscription::class);
		})->add(AuthenticationUser::class); 
		$subscription->get('/subscription-plans', GetAllSubscriptions::class);

		$subscription->group('/payment-methods', function(Group $payments) {
			$payments->get('/user/me', GetUserPaymentMethods::class);
			$payments->get('/user/me/default', GetUserDefaultPaymentMethods::class);
			$payments->post('/create-setup-intent', CreateSetupIntent::class);
			$payments->put('/{id}/default', UpdateDefaultPaymentMethods::class);
			$payments->delete('/{id}', DeletePaymentMethods::class);

			$payments->post('', CreatePaymentMethod::class);
		})->add(AuthenticationUser::class);

		$subscription->group('/payments', function(Group $payments) {
			$payments->get('/user/me', GetUserPayment::class);
			//$payments->get('/user/{id}', GetAnyUserPayment::class);
			//$payments->get('/user/me/default', GetUserDefaultPaymentMethods::class);
		})->add(AuthenticationUser::class);
	});

	// Subscription
	$app->group('/webhook', function(Group $webhook) {
		$webhook->post('/payment-status', PaymentStatus::class);
	});
};