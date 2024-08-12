<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['new', 'edit'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['new', 'edit'])]
    private ?string $accountHolder = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['new', 'edit'])]
    #[Assert\Iban(groups: ['new', 'edit'])]
    private ?string $iban = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['new', 'edit'])]
    #[Assert\Bic(groups: ['new', 'edit'])]
    private ?string $bic = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['new', 'edit'])]
    private ?string $bankCode = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['new', 'edit'])]
    #[Assert\Url(groups: ['new', 'edit'])]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(groups: ['new', 'edit_credentials'])]
    private ?string $tanMediaName = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(groups: ['new', 'edit_credentials'])]
    #[Assert\Positive(groups: ['new', 'edit_credentials'], message: 'Please choose a valid number.')]
    private ?int $tanMechanism = null;

    #[ORM\Column(type: Types::BINARY, nullable: true)]
    #[Assert\NotBlank(groups: ['new', 'edit_credentials'])]
    private $username = null;

    #[ORM\Column(type: Types::BINARY, nullable: true)]
    #[Assert\NotBlank(groups: ['new', 'edit_credentials'])]
    private $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Image(groups: ['new', 'edit'], maxSize: '2048K')]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $backgroundColor = null;

    /**
     * @var Collection<int, SubAccount>
     */
    #[ORM\OneToMany(targetEntity: SubAccount::class, mappedBy: 'account', orphanRemoval: true)]
    private Collection $subAccounts;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foregroundColor = null;

    public function __construct()
    {
        $this->subAccounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAccountHolder(): ?string
    {
        return $this->accountHolder;
    }

    public function setAccountHolder(string $accountHolder): static
    {
        $this->accountHolder = $accountHolder;

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

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): static
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getTanMediaName(): ?string
    {
        return $this->tanMediaName;
    }

    public function setTanMediaName(string $tanMediaName): static
    {
        $this->tanMediaName = $tanMediaName;

        return $this;
    }

    public function getTanMechanism(): ?int
    {
        return $this->tanMechanism;
    }

    public function setTanMechanism(?int $tanMechanism): static
    {
        $this->tanMechanism = $tanMechanism;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): static
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    /**
     * @return Collection<int, SubAccount>
     */
    public function getSubAccounts(): Collection
    {
        return $this->subAccounts;
    }

    public function addSubAccount(SubAccount $subAccount): static
    {
        if (!$this->subAccounts->contains($subAccount)) {
            $this->subAccounts->add($subAccount);
            $subAccount->setAccount($this);
        }

        return $this;
    }

    public function removeSubAccount(SubAccount $subAccount): static
    {
        if ($this->subAccounts->removeElement($subAccount)) {
            // set the owning side to null (unless already changed)
            if ($subAccount->getAccount() === $this) {
                $subAccount->setAccount(null);
            }
        }

        return $this;
    }

    public function getForegroundColor(): ?string
    {
        return $this->foregroundColor;
    }

    public function setForegroundColor(?string $foregroundColor): static
    {
        $this->foregroundColor = $foregroundColor;

        return $this;
    }
}
