<?php

namespace App\Config;

enum StockTrend: string
{
    case DOWNWARDTREND = 'Abwärtstrend';
    case UPWARDTREND = 'Aufwärtstrend';
    case BUYSIGNAL = 'Kaufsignal';
    case TRENDBREAK = 'Trend-Bruch';
}
