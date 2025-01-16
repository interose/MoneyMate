<?php

namespace App\Entity;

use App\Repository\SubAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Fhp\Model\SEPAAccount;

#[ORM\Entity(repositoryClass: SubAccountRepository::class)]
class SubAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $iban = null;

    #[ORM\Column(length: 255)]
    private ?string $accountNumber = null;

    #[ORM\ManyToOne(inversedBy: 'subAccounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'subAccount')]
    private Collection $transactions;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\OneToOne(mappedBy: 'subaccount', cascade: ['persist', 'remove'])]
    private ?CurrentBalance $currentBalance = null;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;

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

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;

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

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setSubAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getSubAccount() === $this) {
                $transaction->setSubAccount(null);
            }
        }

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Transforms the database record to a SEPAAccount object.
     */
    public function getSEPAAcount(): SEPAAccount
    {
        $sepaAccount = new SEPAAccount();

        $sepaAccount
            ->setIban($this->getIban())
            ->setBic($this->getAccount()->getBic())
            ->setBlz($this->getAccount()->getBankCode())
            ->setAccountNumber($this->getAccountNumber())
        ;

        return $sepaAccount;
    }

    public function getCurrentBalance(): ?CurrentBalance
    {
        return $this->currentBalance;
    }

    public function setCurrentBalance(CurrentBalance $currentBalance): static
    {
        // set the owning side of the relation if necessary
        if ($currentBalance->getSubaccount() !== $this) {
            $currentBalance->setSubaccount($this);
        }

        $this->currentBalance = $currentBalance;

        return $this;
    }
}
