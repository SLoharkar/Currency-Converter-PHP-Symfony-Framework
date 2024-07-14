<?php
namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


/**
 * Repository for handling User entity operations.
 *
 * This class manages CRUD operations for the User entity, including user registration
 * and password reset functionalities. It extends the base ServiceEntityRepository
 * class to provide specific repository methods for the User entity.
*/
class HomeRepository extends ServiceEntityRepository{

    private $entityManager;


    /**
     * HomeRepository constructor.
     *
     * @param EntityManagerInterface $entityManager The Entity Manager for database operations.
     * @param ManagerRegistry $registry The Manager Registry for managing the Doctrine entities.
    */
    public function __construct(EntityManagerInterface $entityManager,ManagerRegistry $registry){
        parent::__construct($registry, User::class);
        $this->entityManager = $entityManager;
    }


    /**
     * Registers a new user by persisting the User entity to the database.
     *
     * @param User $user The User entity to be registered.
    */
    public function register($user){
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }


    /**
     * Finds a user by their username.
     *
     * This method retrieves a User entity based on the provided username.
     *
     * @param string $username The username of the user to find.
     *
     * @return User|null The User entity or null if no user is found.
    */
    public function findByUsername(string $username): ?User{
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * Resets the password for a user and persists the changes.
     *
     * @param User $user The User entity with the new password.
     *
     * @return User The User entity that was updated.
    */
    public function reset_password($user): ?User{
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }
}
?>