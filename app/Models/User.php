<?php

namespace App\Models;

use App\Enums\PayFrequency;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'income_cents', 'pay_frequency', 'timezone'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'income_cents' => 0,
        'pay_frequency' => PayFrequency::Monthly->value,
        'timezone' => 'UTC',
        'is_demo' => false,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'income_cents' => 'integer',
            'pay_frequency' => PayFrequency::class,
            'is_demo' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Bill, $this>
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * The current time in the user's own timezone. Use this, never now(), for "today" and "this month".
     */
    public function now(): CarbonImmutable
    {
        return CarbonImmutable::now($this->timezone);
    }
}
