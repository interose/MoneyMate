<?php

namespace App\Config;

enum StockComment: string
{
    case HOLD = 'Halten';
    case HOLDALLTIMEHIGH = 'Halten, ATH';
    case BUY = 'Kaufen';
    case NOBUY = 'Keine Käufe';
    case REBUY = 'Nachkauf';
}