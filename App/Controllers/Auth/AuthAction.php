<?php

declare(strict_types=1);
namespace App\Controllers\Auth;

use App\Controllers\Action;
use App\Repository\Database\IDatabaseRepository;
use App\Repository\User\IUserRepository;
use App\Repository\UserEmail\IUserEmailRepository;
use App\Repository\UserPhone\IUserPhoneRepository;
use App\Repository\UserSecurityProfile\IUserSecurityProfileRepository;
use App\Services\Stripe;
use App\Services\TokenJWT;
use Psr\Log\LoggerInterface;


abstract class AuthAction extends Action
{
	protected IDatabaseRepository $iDatabaseRepository;
    protected IUserRepository $iUserRepository;
    protected IUserEmailRepository $iUserEmailRepository;
    protected IUserPhoneRepository $iUserPhoneRepository;
    protected IUserSecurityProfileRepository $iUserSecurityProfileRepository;
	protected TokenJWT $tokenJWT;
    protected Stripe $stripe;
    
    public function __construct (
        LoggerInterface $logger,
		IDatabaseRepository $iDatabaseRepository,
        IUserRepository $iUserRepository,
        IUserEmailRepository $iUserEmailRepository,
        IUserPhoneRepository $iUserPhoneRepository,
        IUserSecurityProfileRepository $iUserSecurityProfileRepository,
		TokenJWT $tokenJWT,
        Stripe $stripe
    )
    {
        parent::__construct($logger, $iDatabaseRepository);
        $this->iUserRepository = $iUserRepository;
        $this->iUserEmailRepository = $iUserEmailRepository;
        $this->iUserPhoneRepository = $iUserPhoneRepository;
        $this->iUserSecurityProfileRepository = $iUserSecurityProfileRepository;
		$this->tokenJWT = $tokenJWT;
        $this->stripe = $stripe;
    }
}
