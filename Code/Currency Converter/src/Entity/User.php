<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
    */
    private $id;


    /**
     * @ORM\Column(type="string", length=180, unique=true)
    */
    private $username;


    /**
     * @ORM\Column(type="string")
    */
    private $password;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $plainPassword;


    /**
     * @ORM\Column(type="json")
    */
    private $roles = [];


    // Getters and setters...


    /**
     * Get a string representation of the User entity.
     *
     * This method provides a readable string representation of the User object, 
     * including the username, the plain password, and the roles of the user. 
     * This method is intended for development and debugging purposes only. 
     * **Do not use this in production** as it exposes sensitive information.
     *
     * @return string A string representing the User entity.
    */
    /*
    public function __toString(){
        return sprintf(
            'User [ID: %d, Username: %s, Plain Password: %s, Roles: %s]',
            $this->id,
            $this->username,
            $this->plainPassword,  // Includes plain password for debugging purposes
            implode(', ', $this->roles)  // Convert roles array to a comma-separated string
        );
    }
    */
    
    public function getPlainPassword(): ?string {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getRoles(): array {
        $roles = $this->roles;
        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }


    // Implementing methods from UserInterface

    public function getSalt(): ?string {
        // If you're not using bcrypt or sodium for hashing passwords, you might need a salt.
        // Since bcrypt and sodium generate their own salt internally, you can return null here.
        return null;
    }

    public function eraseCredentials(){
        // If you store any temporary, sensitive data on the user, clear it here.
    }

    public function getUserIdentifier(): string {
        // This method should return a unique identifier for the user, usually the username or email.
        return $this->username;
    }
}
?>
