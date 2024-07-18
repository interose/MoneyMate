<?php

namespace App\Entity;

use App\Repository\PayPalTransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PayPalTransactionRepository::class)]
class PayPalTransaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $bookingDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $bookingTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $recipient = null;

    #[ORM\Column(length: 255)]
    private ?string $transactionCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $articleDescription = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $articleNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $associatedTransactionCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $invoiceNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookingDate(): ?\DateTimeInterface
    {
        return $this->bookingDate;
    }

    public function setBookingDate(\DateTimeInterface $bookingDate): static
    {
        $this->bookingDate = $bookingDate;

        return $this;
    }

    public function getBookingTime(): ?\DateTimeInterface
    {
        return $this->bookingTime;
    }

    public function setBookingTime(\DateTimeInterface $bookingTime): static
    {
        $this->bookingTime = $bookingTime;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

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

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getTransactionCode(): ?string
    {
        return $this->transactionCode;
    }

    public function setTransactionCode(string $transactionCode): static
    {
        $this->transactionCode = $transactionCode;

        return $this;
    }

    public function getArticleDescription(): ?string
    {
        return $this->articleDescription;
    }

    public function setArticleDescription(?string $articleDescription): static
    {
        $this->articleDescription = $articleDescription;

        return $this;
    }

    public function getArticleNumber(): ?string
    {
        return $this->articleNumber;
    }

    public function setArticleNumber(?string $articleNumber): static
    {
        $this->articleNumber = $articleNumber;

        return $this;
    }

    public function getAssociatedTransactionCode(): ?string
    {
        return $this->associatedTransactionCode;
    }

    public function setAssociatedTransactionCode(?string $associatedTransactionCode): static
    {
        $this->associatedTransactionCode = $associatedTransactionCode;

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): static
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }
}
