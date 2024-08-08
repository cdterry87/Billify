<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCategoryValue()
    {
        return $this->getCategoryOptions()[$this->category];
    }

    public function getAmountAsCurrency()
    {
        return '$' . number_format($this->amount, 2);
    }
}
