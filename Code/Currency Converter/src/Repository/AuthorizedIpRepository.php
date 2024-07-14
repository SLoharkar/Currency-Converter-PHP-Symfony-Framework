<?php
namespace App\Repository;

use App\Entity\AuthorizedIp;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


class AuthorizedIpRepository extends ServiceEntityRepository{


    /**
     * Constructor for the AuthorizedIpRepository class.
     *
     * This constructor initializes the repository with the provided ManagerRegistry instance
     * and sets the entity class for this repository to AuthorizedIp.
     *
     * It extends the ServiceEntityRepository class from Doctrine, which provides methods for
     * interacting with the database for the AuthorizedIp entity.
     *
     * @param ManagerRegistry $registry The ManagerRegistry service that manages entity managers and repositories.
    */
    public function __construct(ManagerRegistry $registry){
        parent::__construct($registry, AuthorizedIp::class);
    }


    /**
     * Find an AuthorizedIp entity by its IP address.
     *
     * This method retrieves an `AuthorizedIp` entity from the database based on the provided IP address.
     * It uses the `findOneBy` method to perform a query that searches for an entity where the `ipAddress`
     * field matches the given value.
     *
     * @param string $ipAddress The IP address to search for in the database.
     *
     * @return AuthorizedIp|null Returns the `AuthorizedIp` entity if found, or null if no entity matches the IP address.
    */
    public function findByIpAddress(string $ipAddress): ?AuthorizedIp{
        return $this->findOneBy(['ipAddress' => $ipAddress]);
    }
}
?>