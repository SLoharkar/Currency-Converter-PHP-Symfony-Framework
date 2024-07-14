<?php

namespace App\Service;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\HomeRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Service\AuthorizedIpService;
use Psr\Log\LoggerInterface;


class HomeService extends AbstractController{

    private $homeRepository;
    private $passwordHasher;
    private $authorizedIpService;
    private $logger;


    /**
     * Constructor for the HomeService.
     *
     * Initializes the controller with services required for user management, IP validation, and logging.
     *
     * @param UserPasswordHasherInterface $passwordHasher A service for securely hashing and verifying user passwords.
     * @param HomeRepository $homeRepository A repository for interacting with the home data store.
     * @param AuthorizedIpService $authorizedIpService A service for validating if an IP address is authorized.
     * @param LoggerInterface $logger A service for logging messages, including errors and informational events.
    */
    public function __construct(UserPasswordHasherInterface $passwordHasher, HomeRepository $homeRepository, AuthorizedIpService $authorizedIpService, LoggerInterface $logger){
        $this->passwordHasher = $passwordHasher;
        $this->homeRepository = $homeRepository;
        $this->authorizedIpService = $authorizedIpService;
        $this->logger = $logger;
    }


    /**
     * Validates if the provided IP address is authorized.
     *
     * This method acts as a wrapper around the `validateIp` method of the `AuthorizedIpService` service.
     * It checks whether the given IP address is in the list of authorized IP addresses.
     *
     * @param string $ip The IP address to validate. Should be a valid IP address string.
     *
     * @return Returns `true` if the IP address is authorized, `false` otherwise.
    */
    public function validateIp($ip){
        $this->logger->info('Validating IP address: ' . $ip);
        return $this->authorizedIpService->validateIp($ip);
    }


    /**
     * Registers a new user with the given username and password.
     *
     * This method performs basic validation of the input data, checks if the username already exists,
     * hashes the password, creates a new User entity, and saves it to the database.
     * It also logs significant events in the registration process.
     *
     * @param string $username The username for the new user. Must be unique.
     * @param string $password The plain-text password for the new user. Must not be empty.
     *
     * @throws \InvalidArgumentException If the username or password is empty.
     * @throws \RuntimeException If the username already exists.
    */
    public function register($username,$password){

        $this->logger->info('Attempting to register a new user with username: ' . $username);

        // Validate the provided username and password
        if (empty($username) || empty($password)){
            $this->logger->warning('Registration failed: Username and password are required.');
            throw new \InvalidArgumentException('Username and password are required');
        }

        // Check if the username already exists in the database
        if ($this->homeRepository->findByUsername($username)){
            $this->logger->warning('Registration failed: Username already taken: ' . $username);
            throw new \RuntimeException('Username already taken');
        }

        // Create a new User entity instance and set values
        $user = new User();
        
        $user->setUsername($username);

        // Hash the password using the password hasher service and set it for the User entity
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $password)
        );

        $user->setPlainPassword($password);

        // Assign the default role to the new user
        $user->setRoles(['ROLE_USER']);

        $this->logger->info('Successfully created a new User entity for username: ' . $username);

        // Save the User entity to the database
        $this->homeRepository->register($user);

        $this->logger->info('User registered successfully with username: ' . $username);
    }


    /**
     * Resets the password for a user with the given username.
     *
     * This method validates the input data, checks if the user exists, verifies the external password,
     * hashes the new password, and updates the user's password in the database.
     * It also logs significant events during the password reset process.
     *
     * @param string $username The username of the user whose password is being reset.
     * @param string $extPassword The existing password of the user, used for verification.
     * @param string $newPassword The new password to set for the user.
     *
     * @throws BadRequestHttpException If required parameters are missing or invalid.
    */
    public function reset_password($username,$extPassword,$newPassword){

        $this->logger->info('Attempting to reset password for username: ' . $username);

        if (empty($username) || empty($extPassword) || empty($newPassword)){
            $this->logger->warning('Password reset failed: Missing required parameters.');
            throw new BadRequestHttpException('Missing required parameters');
        }

        // Retrieve the user by username from the database
        $user = $this->homeRepository->findByUsername($username);

        if (!$user){
            $this->logger->warning('Password reset failed: User not found for username: ' . $username);
            throw new BadRequestHttpException('User not found');
        }

        // Check the external password against the stored password
        if (!$this->passwordHasher->isPasswordValid($user, $extPassword)){
            $this->logger->warning('Password reset failed: Invalid existing password for username: ' . $username);
            throw new BadRequestHttpException('Invalid existing password.');
        }

        // Hash the new password and set it for the User entity
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $newPassword)
        );

        $user->setPlainPassword($newPassword);

        // Update the user's password in the database
        $this->homeRepository->reset_password($user);

        $this->logger->info('Password successfully reset for username: ' . $username);    
    }
}
?>