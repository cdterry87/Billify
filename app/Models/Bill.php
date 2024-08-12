<?php

namespace App\Models;

use App\Traits\WithCurrency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends Model
{
    use HasFactory, WithCurrency;

    protected $table = 'bills';
    protected $guarded = [];

    public static function getCategoryOptions(): array
    {
        return [
            'housing' => 'Housing',
            'utilities' => 'Utilities',
            'food' => 'Food',
            'transportation' => 'Transportation',
            'clothing' => 'Clothing',
            'medical/health' => 'Medical/Health',
            'insurance' => 'Insurance',
            'personal' => 'Personal',
            'debt' => 'Debt',
            'retirement' => 'Retirement',
            'education' => 'Education',
            'savings' => 'Savings',
            'gifts/donations' => 'Gifts/Donations',
            'entertainment' => 'Entertainment',
            'miscellaneous' => 'Miscellaneous',
            'other' => 'Other'
        ];
    }

    public function getCategoryValue()
    {
        return $this->getCategoryOptions()[$this->category];
    }

    public function getAmountAsCurrency()
    {
        return $this->formatCurrency($this->amount);
    }
}
