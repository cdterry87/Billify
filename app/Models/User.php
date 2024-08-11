<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\WithCurrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, WithCurrency;

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

    public function currentMonthBills()
    {
        return $this->bills()
            ->where(function ($query) {
                $currentMonth = strtolower(now()->format('F'));

                $query->where($currentMonth, true)
                    ->orWhere(function ($query) {
                        $query->where('january', false)
                            ->where('february', false)
                            ->where('march', false)
                            ->where('april', false)
                            ->where('may', false)
                            ->where('june', false)
                            ->where('july', false)
                            ->where('august', false)
                            ->where('september', false)
                            ->where('october', false)
                            ->where('november', false)
                            ->where('december', false);
                    });
            });
    }

    public function getYearlyIncome(): string
    {
        $income = round($this->income * $this->frequency, 2);
        return $this->formatCurrency($income);
    }

    public function getMonthlyIncomeRaw(): float
    {
        return round($this->income * $this->frequency / 12, 2);
    }

    public function getMonthlyIncome(): string
    {
        $income = $this->getMonthlyIncomeRaw();
        return $this->formatCurrency($income);
    }

    public function getBiWeeklyIncome(): string
    {
        $income = round($this->income * $this->frequency / 26, 2);
        return $this->formatCurrency($income);
    }

    public function getWeeklyIncome(): string
    {
        $income = round($this->income * $this->frequency / 52, 2);
        return $this->formatCurrency($income);
    }
}
