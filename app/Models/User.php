<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'income',
        'frequency'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function getFrequencyOptions(): array
    {
        return [
            '1' => 'Yearly (Annual Salary)',
            '12' => 'Monthly (Every Month)',
            '26' => 'Bi-weekly (Every Two Weeks)',
            '52' => 'Weekly (Every Week)'
        ];
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function getYearlyIncome(): string
    {
        $income = $this->income * $this->frequency;
        return '$' . number_format($income, 2);
    }

    public function getMonthlyIncome(): string
    {
        $income = $this->income * $this->frequency / 12;
        return '$' . number_format($income, 2);
    }

    public function getBiWeeklyIncome(): string
    {
        $income = $this->income * $this->frequency / 26;
        return '$' . number_format($income, 2);
    }

    public function getWeeklyIncome(): string
    {
        $income = $this->income * $this->frequency / 52;
        return '$' . number_format($income, 2);
    }
}
