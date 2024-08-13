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
    * Check if an IP address is within a given CIDR subnet or matches a specific IP.
    *
    * This method determines if the provided IP address falls within the specified CIDR (Classless Inter-Domain Routing)
    * range or matches a specific IP address.
    *
    * @param string $ipAddress The IP address to check.
    * @param string $cidrOrIp The CIDR notation for the subnet (e.g., '192.168.1.0/24') or a specific IP address (e.g., '127.0.0.1').
    * @return bool True if the IP address is within the CIDR subnet or matches the specific IP, false otherwise.
    */
    public function isIpInCidr($ipAddress, $cidrOrIp){

        $this->logger->info('Checking if IP address is within CIDR subnet or matches a specific IP.', ['ip' => $ipAddress, 'cidrOrIp' => $cidrOrIp]);

        // Special case: Handle IPv6 loopback (::1) as equivalent to IPv4 loopback (127.0.0.1)
        if ($ipAddress === '::1') {
            $ipAddress = '127.0.0.1';
        }

        // Check if the input is in CIDR format
        if (strpos($cidrOrIp, '/') !== false) {
            // Handle as CIDR
            list($subnet, $mask) = explode('/', $cidrOrIp);

            if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                // IPv4 handling
                $mask = intval($mask);
                if ($mask < 0 || $mask > 32) {
                    $this->logger->error('Invalid IPv4 subnet mask.', ['cidr' => $cidrOrIp, 'mask' => $mask]);
                    return false;
                }
                $ip = ip2long($ipAddress);
                $subnet = ip2long($subnet);
                if ($ip === false || $subnet === false) {
                    $this->logger->error('Invalid IPv4 address or subnet.', ['ip' => $ipAddress, 'subnet' => $subnet]);
                    return false;
                }
                $wildcard = ~((1 << (32 - $mask)) - 1);
                $result = ($ip & $wildcard) == ($subnet & $wildcard);
            } else {
                $this->logger->error('IP address format is not supported.', ['ip' => $ipAddress]);
                return false;
            }
        } else {
            // Handle as a specific IP match
            $result = $ipAddress === $cidrOrIp;
        }

        if ($result) {
            $this->logger->info('IP address is within the CIDR subnet or matches the specific IP.', ['ip' => $ipAddress, 'cidrOrIp' => $cidrOrIp]);
        } else {
            $this->logger->warning('IP address is not within the CIDR subnet and does not match the specific IP.', ['ip' => $ipAddress, 'cidrOrIp' => $cidrOrIp]);
        }
        return $result;
    }
}
?>