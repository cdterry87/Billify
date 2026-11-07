<?php

namespace App\Enums;

enum BillCategory: string
{
    case Housing = 'housing';
    case Utilities = 'utilities';
    case Food = 'food';
    case Transportation = 'transportation';
    case Clothing = 'clothing';
    case MedicalHealth = 'medical_health';
    case Insurance = 'insurance';
    case Personal = 'personal';
    case Debt = 'debt';
    case Retirement = 'retirement';
    case Education = 'education';
    case Savings = 'savings';
    case GiftsDonations = 'gifts_donations';
    case Entertainment = 'entertainment';
    case Miscellaneous = 'miscellaneous';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MedicalHealth => 'Medical/Health',
            self::GiftsDonations => 'Gifts/Donations',
            default => ucfirst($this->value),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
