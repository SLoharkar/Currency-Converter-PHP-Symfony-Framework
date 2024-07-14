<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Authorized_ip")
*/
class AuthorizedIp{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
    */
    private $id;


    /**
     * @ORM\Column(type="string", length=255, unique=true)
    */
    private $ipAddress;


    // Getters and setters...


    public function getId(): ?int {
        return $this->id;
    }

    public function getIpAddress(): ?string {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self {       
        $this->ipAddress = $ipAddress;
        return $this;
    }
    
    public function __toString(): string {
        return $this->ipAddress;
    }
}
?>