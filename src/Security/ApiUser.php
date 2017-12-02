<?php
declare(strict_types=1);

namespace App\Security;


use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiUser implements UserInterface, EquatableInterface
{
    private $token;
    private $username;

    public function __construct(array $data)
    {
        $this->token = $data['token'];
        $this->username = $data['username'];
    }

    public function getTokenKey(): string
    {
        return $this->token;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return '';
    }

    public function getSalt(): string
    {
        return '';
    }

    public function getRoles(): array
    {
        return ['ROLE_USER', 'ROLE_API_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $user instanceof ApiUser && $user->getUsername() === $this->getUsername();
    }
}
