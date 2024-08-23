<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $bookingDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $valutaDate = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $creditDebit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bookingText = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descriptionRaw = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bankCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accountNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    private ?string $checksum = null;

    /**
     * @var Collection<int, SplitTransaction>
     */
    #[ORM\OneToMany(targetEntity: SplitTransaction::class, mappedBy: 'transactionNew', orphanRemoval: true)]
    private Collection $splitTransactions;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?SubAccount $subAccount = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?PayPalTransaction $payPalTransaction = null;

    public function __construct()
    {
        $this->splitTransactions = new ArrayCollection();
    }

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

    public function getValutaDate(): ?\DateTimeInterface
    {
        return $this->valutaDate;
    }

    public function setValutaDate(\DateTimeInterface $valutaDate): static
    {
        $this->valutaDate = $valutaDate;

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

    public function getCreditDebit(): ?string
    {
        return $this->creditDebit;
    }

    public function setCreditDebit(?string $creditDebit): static
    {
        $this->creditDebit = $creditDebit;

        return $this;
    }

    public function isCredit(): bool
    {
        return $this->getCreditDebit() === 'credit';
    }

    public function getBookingText(): ?string
    {
        return $this->bookingText;
    }

    public function setBookingText(?string $bookingText): static
    {
        $this->bookingText = $bookingText;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescriptionRaw(): ?string
    {
        return $this->descriptionRaw;
    }

    public function setDescriptionRaw(?string $descriptionRaw): static
    {
        $this->descriptionRaw = $descriptionRaw;

        return $this;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(?string $bankCode): static
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getChecksum(): ?string
    {
        return $this->checksum;
    }

    public function setChecksum(string $checksum): static
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * @return Collection<int, SplitTransaction>
     */
    public function getSplitTransactions(): Collection
    {
        return $this->splitTransactions;
    }

    public function addSplitTransaction(SplitTransaction $splitTransaction): static
    {
        if (!$this->splitTransactions->contains($splitTransaction)) {
            $this->splitTransactions->add($splitTransaction);
            $splitTransaction->setTransactionNew($this);
        }

        return $this;
    }

    public function removeSplitTransaction(SplitTransaction $splitTransaction): static
    {
        if ($this->splitTransactions->removeElement($splitTransaction)) {
            // set the owning side to null (unless already changed)
            if ($splitTransaction->getTransactionNew() === $this) {
                $splitTransaction->setTransactionNew(null);
            }
        }

        return $this;
    }

    public function getSubAccount(): ?SubAccount
    {
        return $this->subAccount;
    }

    public function setSubAccount(?SubAccount $subAccount): static
    {
        $this->subAccount = $subAccount;

        return $this;
    }

    public function getPayPalTransaction(): ?PayPalTransaction
    {
        return $this->payPalTransaction;
    }

    public function setPayPalTransaction(?PayPalTransaction $payPalTransaction): static
    {
        $this->payPalTransaction = $payPalTransaction;

        return $this;
    }
}
