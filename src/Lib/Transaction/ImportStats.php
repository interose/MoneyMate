<?php

namespace App\Lib\Transaction;

class ImportStats
{
    public function __construct(
        public int $iNew = 0,
        public int $iAssigned = 0,
        public int $iTransactions = 0,
    ) {
    }
}
