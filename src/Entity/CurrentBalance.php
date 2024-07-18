<?php

namespace App\Entity;

use App\Repository\CurrentBalanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrentBalanceRepository::class)]
class CurrentBalance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $balance = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?SubAccount $subAccount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getSubAccount(): ?SubAccount
    {
        return $this->subAccount;
    }

    public function setSubAccount(SubAccount $subAccount): static
    {
        $this->subAccount = $subAccount;

        return $this;
    }
}
