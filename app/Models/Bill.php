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
}
