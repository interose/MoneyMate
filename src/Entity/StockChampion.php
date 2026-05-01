<?php

namespace App\Entity;

use App\Repository\StockChampionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockChampionRepository::class)]
class StockChampion
{
    public const COMMENT_COLOR_PILL = [
        'Kaufen' => 'bg-green/10 text-green',
        'Nachkauf' => 'bg-teal-400/10 text-teal-400',
        'Halten' => 'bg-white/[.07] text-white/50',
        'Halten ATH' => 'bg-violet-400/10 text-violet-400',
        'Keine Käufe' => 'bg-pink/10 text-pink/70',
    ];

    public const TREND_PILL = [
        'Aufwärtstrend' => [
            'pill' => 'bg-green/10 text-green',
            'icon' => '▲',
            'dot' => 'bg-green',
        ],
        'Kaufsignal' => [
            'pill' => 'bg-accent/10 text-accent/80',
            'icon' => '●',
            'dot' => 'bg-accent',
        ],
        'Trend-Bruch' => [
            'pill' => 'bg-orange-400/10 text-orange-400',
            'icon' => '◆',
            'dot' => 'bg-orange-400',
        ],
        'Abwärtstrend' => [
            'pill' => 'bg-pink/10 text-pink',
            'icon' => '▼',
            'dot' => 'bg-pink',
        ],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $wkn = null;

    #[ORM\Column(length: 255)]
    private ?string $industry = null;

    #[ORM\Column]
    private ?float $geoPak10 = null;

    #[ORM\Column]
    private ?int $profitConsistency = null;

    #[ORM\Column]
    private ?float $lossRatio = null;

    #[ORM\Column]
    private ?float $dividendYield = null;

    #[ORM\Column]
    private ?float $sharePrice = null;

    #[ORM\Column]
    private ?float $gd200 = null;

    #[ORM\Column(length: 255)]
    private ?string $trend = null;

    #[ORM\Column(length: 255)]
    private ?string $comment = null;

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

    public function getWkn(): ?string
    {
        return $this->wkn;
    }

    public function setWkn(string $wkn): static
    {
        $this->wkn = $wkn;

        return $this;
    }

    public function getIndustry(): ?string
    {
        return $this->industry;
    }

    public function setIndustry(string $industry): static
    {
        $this->industry = $industry;

        return $this;
    }

    public function getGeoPak10(): ?float
    {
        return $this->geoPak10;
    }

    public function setGeoPak10(float $geoPak10): static
    {
        $this->geoPak10 = $geoPak10;

        return $this;
    }

    public function getProfitConsistency(): ?int
    {
        return $this->profitConsistency;
    }

    public function setProfitConsistency(int $profitConsistency): static
    {
        $this->profitConsistency = $profitConsistency;

        return $this;
    }

    public function getLossRatio(): ?float
    {
        return $this->lossRatio;
    }

    public function setLossRatio(float $lossRatio): static
    {
        $this->lossRatio = $lossRatio;

        return $this;
    }

    public function getDividendYield(): ?float
    {
        return $this->dividendYield;
    }

    public function setDividendYield(float $dividendYield): static
    {
        $this->dividendYield = $dividendYield;

        return $this;
    }

    public function getSharePrice(): ?float
    {
        return $this->sharePrice;
    }

    public function setSharePrice(float $sharePrice): static
    {
        $this->sharePrice = $sharePrice;

        return $this;
    }

    public function getGd200(): ?float
    {
        return $this->gd200;
    }

    public function setGd200(float $gd200): static
    {
        $this->gd200 = $gd200;

        return $this;
    }

    public function getTrend(): ?string
    {
        return $this->trend;
    }

    public function setTrend(string $trend): static
    {
        $this->trend = $trend;

        return $this;
    }

    public function getTrendPill(): string
    {
        return self::TREND_PILL[$this->trend]['pill'] ?? 'bg-white/10 text-white/80';
    }

    public function getTrendIcon(): string
    {
        return self::TREND_PILL[$this->trend]['icon'] ?? '';
    }

    public function getTrendDot(): string
    {
        return self::TREND_PILL[$this->trend]['dot'] ?? 'bg-white';
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCommentPillColor(): string
    {
        return self::COMMENT_COLOR_PILL[$this->comment] ?? 'bg-white/[.07] text-white/50';
    }
}
