<?php
namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


class AdminRepository extends ServiceEntityRepository{

    private $entityManager;


    /**
     * AdminRepository constructor.
     *
     * Initializes the repository with the EntityManager and the ManagerRegistry.
     *
     * @param EntityManagerInterface $entityManager The entity manager instance for handling database operations.
     * @param ManagerRegistry $registry The manager registry for managing multiple entity managers.
    */
    public function __construct(EntityManagerInterface $entityManager,ManagerRegistry $registry){
        parent::__construct($registry, User::class);
        $this->entityManager = $entityManager;
    }


    /**
     * Retrieves all users from the database.
     *
     * This method fetches all user entities stored in the database by calling the `findAll` method.
     * It is typically used to get a complete list of users, such as for displaying in an admin dashboard.
    */
    public function getAllUsers(){
        return $this->findAll();
    }


    /**
     * Finds a user by their ID.
     *
     * This method fetches a single `User` entity from the database using the provided ID.
     * If a user with the given ID is found, it is returned; otherwise, `null` is returned.
    */
    public function findById($id): ?User{
        return $this->find($id);
    }


    /**
     * Finds a user by their username.
     *
     * This method retrieves a single `User` entity from the database based on the provided username.
     * If a user with the given username exists, it is returned; otherwise, `null` is returned.
     *
     * @param string $username The username of the user to retrieve.
     *
     * @return User|null The User entity if found, or `null` if no user with the given username exists.
    */
    public function findByUsername(string $username): ?User {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * Updates the given user entity in the database.
     *
     * This method persists the changes made to the `User` entity and then flushes the changes to the database.
     * It is typically used to save updates to user details such as username, password, and roles.
     *
     * @param User $user The `User` entity to be updated.
    */
    public function adminUserUpdate($user){
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }


    /**
     * Deletes a user entity from the database.
     *
     * This method removes the specified `User` entity from the database and then flushes the changes to ensure
     * that the deletion is committed. It is typically used by administrators to delete user accounts.
     *
     * @param User $user The `User` entity to be deleted.
    */    
    public function adminUserDelete($user){
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
?>