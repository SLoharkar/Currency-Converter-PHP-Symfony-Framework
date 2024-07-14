<?php
namespace App\Service;
use App\Repository\AdminRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;



class AdminService extends AbstractController{

    private $adminRepository;
    private $passwordHasher;
    private $logger;
    private $security;



    /**
     * AdminService constructor.
     *
     * This constructor initializes the AdminService with the necessary dependencies.
     *
     * @param AdminRepository $adminRepository The repository for accessing admin-related data.
     * @param UserPasswordHasherInterface $passwordHasher The service for hashing passwords.
     * @param LoggerInterface $logger The logger service for logging information and errors.
     * @param Security $security The security service for retrieving the current user and managing security-related operations.
    */
    public function __construct(AdminRepository $adminRepository, UserPasswordHasherInterface $passwordHasher, LoggerInterface $logger, Security $security){
        $this->adminRepository = $adminRepository;
        $this->passwordHasher = $passwordHasher;
        $this->logger = $logger;
        $this->security = $security;
    }


    /**
     * Retrieve all users.
     *
     * This method fetches all users from the repository.
     *
     * @return array The list of all users.
    */
    public function getAllUsers(){

        $this->logger->info('Fetching all users from the repository.');
        
        // Fetch all users from the AdminRepository
        $users = $this->adminRepository->getAllUsers();

        $this->logger->info(sprintf('Retrieved %d users from the repository.', count($users)));

        return $users;
    }

    /**
     * Find a user by ID.
     *
     * This method retrieves a user entity from the repository based on the provided ID.
     *
     * @param int $id The ID of the user to find.
     * @return User|null The user entity if found, or null if not found.
    */
    public function findById($id){

        $this->logger->info(sprintf('Attempting to find user with ID: %d', $id));

        // Fetch the user from the AdminRepository by ID
        $user = $this->adminRepository->findById($id);

        // Check if the user was found
        if ($user){        
            $this->logger->info(sprintf('User with ID: %d found.', $id));
        }
        else{
            $this->logger->warning(sprintf('No user found with ID: %d', $id));
        }

        // Return the user entity if found, or null if not found
        return $user;
    }


    /**
     * Update a user's details.
     *
     * This method updates the username, password, and roles of an existing user.
     * It also checks if the current admin user is trying to update their own roles,
     * in which case it will return a flag indicating that a logout is required.
     *
     * @param string $username The new username for the user.
     * @param string $password The new password for the user.
     * @param array $roles The new roles for the user.
     * @param int $extId The ID of the user to update.
     * 
     * @throws \InvalidArgumentException if required fields are empty.
     * @throws \RuntimeException if the username is already taken by another user.
     * 
     * @return bool Returns true if the role change requires a logout, false otherwise.
    */
    public function adminUserUpdate($username,$password,$roles,$extId){

        $this->logger->info(sprintf('Attempting to update user with ID: %d', $extId));

        // Find the user by ID
        $user = $this->adminRepository->findById($extId);

        // Validate the data (basic example)
        if (empty($username) || empty($password)){
            $this->logger->error('All fields are required for user update.');
            throw new \InvalidArgumentException('All Fields are required');
        }

        // Check if the username is already taken by another user
        $existingUser = $this->adminRepository->findByUsername($username);

        if($existingUser && $existingUser->getId() !== $user->getId()){
            $this->logger->error('Username already taken by another user.');
            throw new \RuntimeException('Username already taken');
        }

        // Update the user's username
        $user->setUsername($username);
    
        // Hash the password using the password hasher service and set it for the User entity
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $password)
        );

        // Set the plain password
        $user->setPlainPassword($password);

        // Ensure roles is an array
        if (!is_array($roles)){
            $this->logger->warning('Roles parameter is not an array, resetting to empty array.');
            $roles = [];
        }

        // Update the user's roles
        $user->setRoles($roles);

        // Save the updated user to the database
        $this->adminRepository->adminUserUpdate($user);

        $this->logger->info(sprintf('User with ID: %d successfully updated.', $extId));

        // Check if the current admin user is trying to update their own roles to 'ROLE_USER'
        $currentUser = $this->security->getUser();
        if ($currentUser->getId() === $user->getId() && in_array('ROLE_USER', $roles)){
            $this->logger->info('Current admin user updated their own role to ROLE_USER. A logout will be required.');
            return true;
        }
        
        return false;
    }


    /**
     * Delete a user by their ID.
     *
     * This method deletes a user from the system based on their ID.
     * It checks that the user exists and that the currently logged-in user is not trying to delete themselves.
     *
     * @param int $id The ID of the user to delete.
     * @throws \InvalidArgumentException if the user is not found.
     * @throws \LogicException if the current user is trying to delete themselves.
    */
    public function adminUserDelete($id){

        $this->logger->info(sprintf('Attempting to delete user with ID: %d', $id));

        // Find the user by ID
        $user = $this->adminRepository->findById($id);

        if (!$user){
            $this->logger->error(sprintf('User with ID: %d not found.', $id));
            throw new \InvalidArgumentException('User not found');
        }

        // Get the current logged-in user
        $currentUser = $this->security->getUser();

        // Check if the current user is trying to delete themselves
        if ($currentUser->getId() === $user->getId()) {
            $this->logger->error(sprintf('User with ID: %d attempted to delete themselves. Operation denied.', $currentUser->getId()));
            throw new \LogicException('You cannot delete yourself.');
        }

        // Proceed to delete the user
        $this->adminRepository->adminUserDelete($user);

        $this->logger->info(sprintf('User with ID: %d successfully deleted.', $id));
    }
}
?>