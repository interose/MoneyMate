<?php

namespace App\Entity;

use App\Repository\TransferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransferRepository::class)]
class Transfer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'transfer.info.not_blank')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'transfer.info.min_length',
        maxMessage: 'transfer.info.max_length'
    )]
    private ?string $info = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'transfer.name.not_blank')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'transfer.name.min_length',
        maxMessage: 'transfer.name.max_length'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'transfer.iban.not_blank')]
    #[Assert\Iban(message: 'transfer.iban.valid')]
    private ?string $iban = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'transfer.bic.not_blank')]
    #[Assert\Bic(message: 'transfer.bic.valid')]
    private ?string $bic = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'transfer.bankname.not_blank')]
    private ?string $bankName = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'transfer.amount.not_blank')]
    #[Assert\GreaterThan(
        value: 0,
        message: 'transfer.amount.greater_than',
    )]
    private ?int $amount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $executionDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(string $info): static
    {
        $this->info = $info;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): static
    {
        $this->iban = $iban;

        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(string $bic): static
    {
        $this->bic = $bic;

        return $this;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): static
    {
        $this->bankName = $bankName;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getExecutionDate(): ?\DateTimeInterface
    {
        return $this->executionDate;
    }

    public function setExecutionDate(\DateTimeInterface $executionDate): static
    {
        $this->executionDate = $executionDate;

        return $this;
    }
}
