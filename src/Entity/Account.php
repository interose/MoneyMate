<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use App\Service\EncryptionService;
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
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $bic = null;

    #[ORM\Column(length: 255)]
    private ?string $bankCode = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tanMediaName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tanMechanism = null;

    #[ORM\Column(length: 255)]
    private $username;

    #[ORM\Column(length: 255)]
    private $password;

    /**
     * @var Collection<int, SubAccount>
     */
    #[ORM\OneToMany(targetEntity: SubAccount::class, mappedBy: 'account', orphanRemoval: true)]
    private Collection $subAccounts;

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

    public function getTanMediaName(EncryptionService $crypto): ?string
    {
        return $crypto->decrypt($this->tanMediaName);
    }

    public function setTanMediaName(string $plainTanMediaName, EncryptionService $crypto): static
    {
        $this->tanMediaName = $crypto->encrypt($plainTanMediaName);

        return $this;
    }

    public function getTanMechanism(EncryptionService $crypto): ?string
    {
        return $crypto->decrypt($this->tanMechanism);
    }

    public function setTanMechanism(string $plainTanMechanism, EncryptionService $crypto): static
    {
        $this->tanMechanism = $crypto->encrypt($plainTanMechanism);

        return $this;
    }

    public function getUsername(EncryptionService $crypto): ?string
    {
        return $crypto->decrypt($this->username);
    }

    public function setUsername(string $plainUsername, EncryptionService $crypto): static
    {
        $this->username = $crypto->encrypt($plainUsername);

        return $this;
    }

    public function getPassword(EncryptionService $crypto): ?string
    {
        return $crypto->decrypt($this->password);
    }

    public function setPassword(string $plainPassword, EncryptionService $crypto): static
    {
        $this->password = $crypto->encrypt($plainPassword);

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
}
