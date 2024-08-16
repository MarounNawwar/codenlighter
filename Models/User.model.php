<?php

namespace Codenlighter\Models;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;

#[Entity]
#[Table(name: 'auth_users')]
class User
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private $id;

    #[Column(type: 'string', nullable: false)]
    private $name;

    #[Column(type: 'string', nullable: false)]
    private $password;

    #[Column(type: 'string', nullable: false)]
    private $mail;

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
        
    public function setMail(string $mail): void
    {
        $this->mail = $mail;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }
}