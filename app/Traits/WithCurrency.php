<?php

namespace App\Traits;

trait WithCurrency
{
    public function formatCurrency($amount): string
    {
        return '$' . number_format($amount, 2);
    }
}
