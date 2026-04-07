<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('month_abbr', [$this, 'monthAbbr']),
        ];
    }

    public function monthAbbr(int $month): string
    {
        return (new \DateTime())->setDate(2000, $month, 1)->format('M');
    }
}
