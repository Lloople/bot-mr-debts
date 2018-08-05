<?php

namespace App\Traits;

trait HasCurrency
{

    protected $currency_symbols = [
        'eur' => '€',
        'usd' => '$',
        'gbp' => '£'
    ];

    public function getCurrencySymbolAttribute()
    {
        return $this->currency_symbols[$this->currency ?? 'eur'];
    }
}