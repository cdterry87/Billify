<?php

use App\Enums\PayFrequency;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Ada',
        'email' => 'ada@example.com',
        'password' => 'current-password',
        'income_cents' => 250_050,
        'pay_frequency' => PayFrequency::BiWeekly,
        'timezone' => 'Europe/London',
    ]);
    $this->actingAs($this->user);
});

function profile(): Testable
{
    return Livewire::test('profile.editor')->dispatch('edit-profile');
}

it('opens from the header on every signed-in page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('aria-label="My profile"', false)
        ->assertSeeLivewire('profile.editor');
});

it('is not rendered for guests', function () {
    auth()->logout();

    $this->get('/styleguide')->assertDontSeeLivewire('profile.editor');
});

it('fills the form with the current profile', function () {
    profile()
        ->assertDispatched('open-modal', name: 'profile')
        ->assertSet('form.name', 'Ada')
        ->assertSet('form.email', 'ada@example.com')
        ->assertSet('form.income', '2500.50')
        ->assertSet('form.pay_frequency', '26')
        ->assertSet('form.timezone', 'Europe/London')
        ->assertSee('<option value="America/Chicago">', false);
});

it('updates the profile without a password when the email is unchanged', function () {
    profile()
        ->set('form.name', 'Ada Lovelace')
        ->set('form.income', '100000')
        ->set('form.pay_frequency', '1')
        ->set('form.timezone', 'America/Chicago')
        ->call('saveProfile')
        ->assertHasNoErrors()
        ->assertDispatched('profile-updated')
        ->assertDispatched('toast', type: 'success', message: 'Your profile has been updated.');

    expect($this->user->fresh())
        ->name->toBe('Ada Lovelace')
        ->income_cents->toBe(10_000_000)
        ->pay_frequency->toBe(PayFrequency::Yearly)
        ->timezone->toBe('America/Chicago');
});

it('accepts zero income', function () {
    profile()->set('form.income', '0')->call('saveProfile')->assertHasNoErrors();

    expect($this->user->fresh()->income_cents)->toBe(0);
});

it('requires the current password to change the email', function () {
    $component = profile()
        ->set('form.email', 'new@example.com')
        ->assertSee('Required to change your email.')
        ->call('saveProfile')
        ->assertHasErrors(['form.current_password' => 'Enter your current password to change your email.']);

    $component->set('form.current_password', 'wrong-password')
        ->call('saveProfile')
        ->assertHasErrors(['form.current_password' => 'The password is incorrect.'])
        ->assertSet('form.current_password', '');

    expect($this->user->fresh()->email)->toBe('ada@example.com');

    $component->set('form.current_password', 'current-password')
        ->call('saveProfile')
        ->assertHasNoErrors()
        ->assertSet('form.current_password', '');

    expect($this->user->fresh()->email)->toBe('new@example.com');
});

it('treats a change of email case as unchanged', function () {
    profile()->set('form.email', 'ADA@example.com')->call('saveProfile')->assertHasNoErrors();
});

it('rejects an email used by someone else', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    profile()
        ->set('form.email', 'taken@example.com')
        ->set('form.current_password', 'current-password')
        ->call('saveProfile')
        ->assertHasErrors('form.email');
});

it('validates the profile', function (array $input, string $field) {
    profile()->set($input)->call('saveProfile')->assertHasErrors($field)->assertNotDispatched('profile-updated');

    expect($this->user->fresh()->name)->toBe('Ada');
})->with([
    'missing name' => [['form.name' => ''], 'form.name'],
    'long name' => [['form.name' => str_repeat('a', 256)], 'form.name'],
    'invalid email' => [['form.email' => 'ada', 'form.current_password' => 'current-password'], 'form.email'],
    'missing income' => [['form.income' => ''], 'form.income'],
    'negative income' => [['form.income' => '-1'], 'form.income'],
    'three decimals' => [['form.income' => '1.234'], 'form.income'],
    'income too large' => [['form.income' => '1000000000'], 'form.income'],
    'unknown frequency' => [['form.pay_frequency' => '3'], 'form.pay_frequency'],
    'missing timezone' => [['form.timezone' => ''], 'form.timezone'],
    'unknown timezone' => [['form.timezone' => 'Mars/Olympus_Mons'], 'form.timezone'],
]);

it('changes the password with the current password', function () {
    $this->user->forceFill(['remember_token' => 'old'])->save();

    profile()
        ->set('current_password', 'current-password')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertSet('password', '')
        ->assertSet('current_password', '')
        ->assertDispatched('toast', type: 'success', message: 'Your password has been changed.');

    $user = $this->user->fresh();

    // This session keeps the new hash, so AuthenticateSession signs out only the others.
    expect(session('password_hash_web'))->toBe($user->password);
    $this->get('/')->assertOk();

    expect(Hash::check('brand-new-password', $user->password))->toBeTrue()
        ->and($user->remember_token)->not->toBe('old');
});

it('rejects a password change with the wrong current password', function () {
    profile()
        ->set('current_password', 'wrong-password')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('updatePassword')
        ->assertHasErrors(['current_password' => 'The password is incorrect.'])
        ->assertSet('password', '');

    expect(Hash::check('current-password', $this->user->fresh()->password))->toBeTrue();
});

it('validates the new password', function (array $input, string $field) {
    profile()
        ->set(['current_password' => 'current-password', 'password' => 'brand-new-password', 'password_confirmation' => 'brand-new-password', ...$input])
        ->call('updatePassword')
        ->assertHasErrors($field);
})->with([
    'too short' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
    'mismatched' => [['password_confirmation' => 'different-password'], 'password'],
    'missing current' => [['current_password' => ''], 'current_password'],
]);

it('deletes the account and its bills with the password', function () {
    Bill::factory()->for($this->user)->count(3)->create();
    $other = Bill::factory()->create();

    profile()
        ->set('delete_password', 'current-password')
        ->call('deleteAccount')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    $this->assertGuest();

    expect(User::find($this->user->id))->toBeNull()
        ->and(Bill::pluck('id')->all())->toBe([$other->id])
        ->and(session('toast.message'))->toBe('Your account and bills have been deleted.');
});

it('keeps the account when the delete password is wrong', function () {
    profile()
        ->set('delete_password', 'wrong-password')
        ->call('deleteAccount')
        ->assertHasErrors(['delete_password' => 'The password is incorrect.'])
        ->assertSet('delete_password', '')
        ->assertNoRedirect();

    $this->assertAuthenticatedAs($this->user);
});

it('requires a password to delete the account', function () {
    profile()->call('deleteAccount')->assertHasErrors('delete_password');

    expect(User::count())->toBe(1);
});

describe('for the demo user', function () {
    beforeEach(function () {
        $this->user->forceFill(['is_demo' => true])->save();
    });

    it('hides the email, password, and delete controls', function () {
        profile()
            ->assertSee('This is the shared demo account')
            ->assertSeeHtml('readonly')
            ->assertDontSee('Change password')
            ->assertDontSee('Delete my account')
            ->assertDontSeeHtml('role="tablist"');
    });

    it('still saves the name, income, frequency, and timezone', function () {
        profile()
            ->set('form.name', 'Visitor')
            ->set('form.income', '1234.56')
            ->set('form.pay_frequency', (string) PayFrequency::Weekly->value)
            ->set('form.timezone', 'America/Chicago')
            ->call('saveProfile')
            ->assertHasNoErrors();

        expect($this->user->fresh())
            ->name->toBe('Visitor')
            ->income_cents->toBe(123_456)
            ->pay_frequency->toBe(PayFrequency::Weekly)
            ->timezone->toBe('America/Chicago');
    });

    it('refuses an email change', function () {
        profile()
            ->set('form.email', 'someone@example.com')
            ->set('form.current_password', 'current-password')
            ->call('saveProfile')
            ->assertHasErrors(['form.email'])
            ->assertSee("The demo account's email can't be changed.")
            ->assertHasNoErrors(['form.current_password']);

        expect($this->user->fresh()->email)->toBe('ada@example.com');
    });

    it('refuses a password change', function () {
        profile()
            ->set('current_password', 'current-password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('updatePassword')
            ->assertDispatched('toast', type: 'error', message: "The demo account's password can't be changed.")
            ->assertSet('current_password', '')
            ->assertSet('password', '');

        expect(Hash::check('current-password', $this->user->fresh()->password))->toBeTrue();
    });

    it('refuses account deletion', function () {
        profile()
            ->set('delete_password', 'current-password')
            ->call('deleteAccount')
            ->assertDispatched('toast', type: 'error', message: "The demo account can't be deleted.")
            ->assertNoRedirect();

        expect($this->user->fresh())->not->toBeNull();
        $this->assertAuthenticatedAs($this->user);
    });
});

it('limits wrong current-password answers across the whole modal', function () {
    $this->freezeTime();

    foreach (range(1, 3) as $attempt) {
        profile()->set(['current_password' => 'wrong', 'password' => 'new-password-1', 'password_confirmation' => 'new-password-1'])
            ->call('updatePassword')
            ->assertHasErrors('current_password');
    }

    profile()->set('delete_password', 'wrong')->call('deleteAccount')->assertHasErrors('delete_password');
    profile()->set(['form.email' => 'new@example.com', 'form.current_password' => 'wrong'])->call('saveProfile')->assertHasErrors('form.current_password');

    profile()->set(['current_password' => 'current-password', 'password' => 'new-password-1', 'password_confirmation' => 'new-password-1'])
        ->call('updatePassword')
        ->assertHasErrors(['current_password' => 'Too many attempts. Please try again in 300 seconds.']);

    expect(Hash::check('current-password', $this->user->fresh()->password))->toBeTrue();
});

it('does not count other validation errors as password guesses', function () {
    foreach (range(1, 6) as $attempt) {
        profile()->set(['current_password' => 'current-password', 'password' => 'short', 'password_confirmation' => 'short'])
            ->call('updatePassword')
            ->assertHasErrors('password')
            ->assertHasNoErrors('current_password');
    }
});
