<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bill;
use App\Models\User;
use App\Livewire\Home;
use Livewire\Livewire;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_logged_in()
    {
        $this->get(route('home'))
            ->assertRedirect(route('login'));
    }

    public function test_renders_correctly_with_notifications_and_charts(): void
    {
        $user = User::factory()->create([
            'income' => '100000',
            'frequency' => '1',
        ]);

        $this->actingAs($user);

        Livewire::test(Home::class)
            ->assertSee('welcome--alert')
            ->assertSee('income-summary--container')
            ->assertSee('$100,000.00')
            ->assertSee('$8,333.33')
            ->assertSee('$3,846.15')
            ->assertSee('$1,923.08')
            ->assertSee('charts--container')
            ->assertSee('bills--card')
            ->assertDontSee('notification--alert')
            ->assertDontSee('bill-list-item--container');

        Bill::factory(5)->create([
            'user_id' => $user->id,
        ]);

        $billDueSoon = Bill::factory()->create([
            'user_id' => $user->id,
            'day' => Carbon::now()->addDays(2)->format('j'),
            'january' => false,
            'february' => false,
            'march' => false,
            'april' => false,
            'may' => false,
            'june' => false,
            'july' => false,
            'august' => false,
            'september' => false,
            'october' => false,
            'november' => false,
            'december' => false,
        ]);

        Livewire::test(Home::class)
            ->assertDontSee('welcome--alert') // Don't see welcome message because notification is present
            ->assertSee('notification--alert') // Should see notification since a bill is due soon
            ->assertSee('income-summary--container')
            ->assertSee('charts--container')
            ->assertSee('bills--card')
            ->assertSee($billDueSoon->name)
            ->assertSee('bill-list-item--container');
    }
}
