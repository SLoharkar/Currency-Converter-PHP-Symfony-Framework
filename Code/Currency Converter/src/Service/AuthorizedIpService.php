<?php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\AuthorizedIpRepository;
use App\Repository\HomeRepository;


class AuthorizedIpService extends AbstractController{

    protected $authorizedIpRepository;
    protected $homeRepository;
    private $logger;

  
    /**
     * Constructor method for initializing services and repositories.
     *
     * This method is used to inject dependencies such as the AuthorizedIpRepository,
     * HomeRepository, and LoggerInterface into the service.
     *
     * @param AuthorizedIpRepository $authorizedIpRepository The repository for authorized IP addresses.
     * @param HomeRepository $homeRepository The repository for home-related data operations.
     * @param LoggerInterface $logger The logger service for logging messages.
    */
    public function __construct(AuthorizedIpRepository $authorizedIpRepository, HomeRepository $homeRepository, LoggerInterface $logger){
        $this->authorizedIpRepository = $authorizedIpRepository;
        $this->homeRepository = $homeRepository;
        $this->logger = $logger;
    }


    /**
     * Validate the given IP address.
     *
     * This method checks if the provided IP address is within the authorized IP ranges stored
     * in the repository. It iterates through all authorized IPs and uses the isIpInCidr method
     * to determine if the IP address is authorized.
     *
     * @param string $ip The IP address to validate.
     * @return bool True if the IP address is authorized, false otherwise.
    */
    public function validateIp($ip){

        $this->logger->info('Validating IP address.', ['ip' => $ip]);

        $authorizedIps = $this->authorizedIpRepository->findAll();

        foreach ($authorizedIps as $authorizedIp){
            if ($this->isIpInCidr($ip, $authorizedIp->getIpAddress())){
                $this->logger->info('IP address is authorized.', ['ip' => $ip]);
                return true;
            }
        }
        $this->logger->warning('IP address is not authorized.', ['ip' => $ip]);
        return false;
    }


    /**
     * Check if an IP address is within a given CIDR subnet.
     *
     * This method determines if the provided IP address falls within the specified CIDR (Classless Inter-Domain Routing)
     * range. It calculates the subnet and mask to perform the comparison.
     *
     * @param string $ipAddress The IP address to check.
     * @param string $cidr The CIDR notation for the subnet (e.g., '192.168.1.0/24').
     * @return bool True if the IP address is within the CIDR subnet, false otherwise.
    */
    public function isIpInCidr($ipAddress, $cidr){

        $this->logger->info('Checking if IP address is within CIDR subnet.', ['ip' => $ipAddress, 'cidr' => $cidr]);

        // Split the CIDR into the subnet address and the subnet mask length
        list($subnet, $mask) = explode('/', $cidr);
        $mask = intval($mask);

        // Convert IP address and subnet address to long integers
        $ip = ip2long($ipAddress);
        $subnet = ip2long($subnet);

        // Calculate the wildcard mask for the given subnet mask
        $wildcard = ~((1 << (32 - $mask)) - 1);

        // Perform the bitwise comparison to check if the IP address is within the subnet
        $result = ($ip & $wildcard) == ($subnet & $wildcard);

        // Log the result of the CIDR check
        if ($result){
            $this->logger->info('IP address is within the CIDR subnet.', ['ip' => $ipAddress, 'cidr' => $cidr]);
        } 
        else{
            $this->logger->warning('IP address is not within the CIDR subnet.', ['ip' => $ipAddress, 'cidr' => $cidr]);
        }
        return $result;
    }
}
?>