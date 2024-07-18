<?php

namespace App\Entity;

use App\Repository\CategoryAssignmentRuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryAssignmentRuleRepository::class)]
class CategoryAssignmentRule
{
    const TYPE_SIMPLE = 0;
    const TYPE_REGEX = 1;
    const AVAILABLE_TYPES = [
        self::TYPE_SIMPLE => 'Simple',
        self::TYPE_REGEX => 'Regex',
    ];
    const AVAILABLE_TYPE_CHOICES = [
        'Simple' => self::TYPE_SIMPLE,
        'Regex' => self::TYPE_REGEX,
    ];

    const TRANSACTION_FIELD_NAME = 0;
    const TRANSACTION_FIELD_DESCRIPTION = 1;
    const AVAILABLE_TRANSACTION_FIELDS = [
        self::TRANSACTION_FIELD_NAME => 'Name',
        self::TRANSACTION_FIELD_DESCRIPTION => 'Description',
    ];
    const AVAILABLE_TRANSACTION_FIELD_CHOICE = [
        'Name' => self::TRANSACTION_FIELD_NAME,
        'Description' => self::TRANSACTION_FIELD_DESCRIPTION,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $rule = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\Column]
    private ?int $transactionField = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getTypeName(): string
    {
        return self::AVAILABLE_TYPES[$this->type] ?? 'not defined';
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTransactionField(): ?int
    {
        return $this->transactionField;
    }

    public function getTransactionFieldName(): string
    {
        return self::AVAILABLE_TRANSACTION_FIELDS[$this->transactionField] ?? 'not defined';
    }

    public function setTransactionField(int $transactionField): static
    {
        $this->transactionField = $transactionField;

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

    public function getRule(): ?string
    {
        return $this->rule;
    }

    public function setRule(string $rule): static
    {
        $this->rule = $rule;

        return $this;
    }
}
