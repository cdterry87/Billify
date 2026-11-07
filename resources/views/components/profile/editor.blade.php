<?php

use App\Enums\PayFrequency;
use App\Livewire\Forms\ProfileForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The My Profile modal: profile details, password change, and account deletion.
 */
new class extends Component
{
    public ProfileForm $form;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $delete_password = '';

    #[On('edit-profile')]
    public function open(): void
    {
        $this->resetErrorBag();
        $this->reset('current_password', 'password', 'password_confirmation', 'delete_password');
        $this->form->fillFrom(auth()->user());

        $this->dispatch('open-modal', name: 'profile');
    }

    public function saveProfile(): void
    {
        $this->form->save(auth()->user());

        $this->dispatch('profile-updated');
        $this->dispatch('toast', type: 'success', message: 'Your profile has been updated.');
    }

    public function updatePassword(): void
    {
        if ($this->blockDemo("The demo account's password can't be changed.")) {
            return;
        }

        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', 'confirmed', Password::defaults()],
            ], [
                'current_password.current_password' => 'The password is incorrect.',
            ]);
        } finally {
            // Never send passwords back to the browser.
            $this->reset('current_password', 'password', 'password_confirmation');
        }

        // A new remember token signs out "remember me" sessions on other devices.
        auth()->user()->forceFill([
            'password' => $validated['password'],
            'remember_token' => Str::random(60),
        ])->save();

        session()->regenerate();

        $this->dispatch('toast', type: 'success', message: 'Your password has been changed.');
    }

    public function deleteAccount(): void
    {
        if ($this->blockDemo("The demo account can't be deleted.")) {
            return;
        }

        try {
            $this->validate(
                ['delete_password' => ['required', 'string', 'current_password']],
                ['delete_password.current_password' => 'The password is incorrect.'],
                ['delete_password' => 'password'],
            );
        } finally {
            $this->reset('delete_password');
        }

        $user = auth()->user();

        Auth::logout();
        $user->delete();

        session()->invalidate();
        session()->regenerateToken();
        session()->flash('toast', ['type' => 'success', 'message' => 'Your account and bills have been deleted.']);

        $this->redirectRoute('login');
    }

    /**
     * The demo account is shared, so nobody may lock others out of it. Its controls are
     * hidden, and this enforces the rule for requests sent without the UI.
     */
    private function blockDemo(string $message): bool
    {
        if (! auth()->user()->is_demo) {
            return false;
        }

        $this->reset('current_password', 'password', 'password_confirmation', 'delete_password');
        $this->dispatch('toast', type: 'error', message: $message);

        return true;
    }
};
?>

<div>
    @php($demo = auth()->user()->is_demo)

    <x-ui.modal name="profile" title="My profile">
        <div class="stack" x-data="{ view: 'profile' }" x-on:open-modal.window="$event.detail.name === 'profile' && (view = 'profile')">
            @if ($demo)
                <p class="notice notice--info">This is the shared demo account, so its email and password can't be changed, and it can't be deleted.</p>
            @else
                <div class="tabs" role="tablist" aria-label="Profile sections">
                    @foreach (['profile' => 'Profile', 'password' => 'Password', 'delete' => 'Delete account'] as $key => $label)
                        <button
                            type="button"
                            class="tabs__tab"
                            role="tab"
                            id="profile-tab-{{ $key }}"
                            aria-controls="profile-panel-{{ $key }}"
                            x-bind:aria-selected="view === @js($key)"
                            x-bind:tabindex="view === @js($key) ? 0 : -1"
                            x-on:click="view = @js($key)"
                            x-on:keydown.right.prevent="$el.nextElementSibling?.click(); $el.nextElementSibling?.focus()"
                            x-on:keydown.left.prevent="$el.previousElementSibling?.click(); $el.previousElementSibling?.focus()"
                        >{{ $label }}</button>
                    @endforeach
                </div>
            @endif

            <form id="profile-panel-profile" @unless ($demo) role="tabpanel" aria-labelledby="profile-tab-profile" @endunless x-show="view === 'profile'" class="stack" wire:submit="saveProfile" novalidate>
                <x-ui.field name="form.name" label="Name" autocomplete="name" required wire:model="form.name" />
                @if ($demo)
                    <x-ui.field name="form.email" label="Email" type="email" readonly wire:model="form.email" />
                @else
                    <x-ui.field name="form.email" label="Email" type="email" autocomplete="email" required wire:model.live.blur="form.email" />
                    @if (mb_strtolower(trim($form->email)) !== mb_strtolower(auth()->user()->email))
                        <x-ui.field name="form.current_password" label="Current password" type="password" autocomplete="current-password" hint="Required to change your email." required wire:model="form.current_password" />
                    @endif
                @endif
                <div class="form-row">
                    <x-ui.field name="form.income" label="Income after taxes ($)" inputmode="decimal" hint="Per pay period." required wire:model="form.income" />
                    <x-ui.field name="form.pay_frequency" label="Paid" as="select" :options="PayFrequency::options()" required wire:model="form.pay_frequency" />
                </div>
                <x-ui.field name="form.timezone" label="Timezone" list="timezones" autocomplete="off" spellcheck="false" hint="Decides your today and current month. Start typing a city, such as Chicago." required wire:model="form.timezone" />
                <datalist id="timezones">
                    @foreach (timezone_identifiers_list() as $timezone)
                        <option value="{{ $timezone }}"></option>
                    @endforeach
                </datalist>
                <div class="modal__actions">
                    <x-ui.button type="submit" wire:loading.attr="disabled">Save profile</x-ui.button>
                </div>
            </form>

            @unless ($demo)
                <form id="profile-panel-password" role="tabpanel" aria-labelledby="profile-tab-password" x-show="view === 'password'" x-cloak class="stack" wire:submit="updatePassword" novalidate>
                    <x-ui.field name="current_password" label="Current password" type="password" autocomplete="current-password" required wire:model="current_password" />
                    <x-ui.field name="password" label="New password" type="password" autocomplete="new-password" hint="At least 8 characters." required wire:model="password" />
                    <x-ui.field name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" required wire:model="password_confirmation" />
                    <div class="modal__actions">
                        <x-ui.button type="submit" wire:loading.attr="disabled">Change password</x-ui.button>
                    </div>
                </form>

                <form id="profile-panel-delete" role="tabpanel" aria-labelledby="profile-tab-delete" x-show="view === 'delete'" x-cloak class="stack" wire:submit="deleteAccount" novalidate>
                    <p>This permanently deletes your account and all of your bills. It cannot be undone.</p>
                    <x-ui.field name="delete_password" label="Enter your password to confirm" type="password" autocomplete="current-password" required wire:model="delete_password" />
                    <div class="modal__actions">
                        <x-ui.button type="submit" variant="danger" icon="trash" wire:loading.attr="disabled">Delete my account</x-ui.button>
                    </div>
                </form>
            @endunless
        </div>
    </x-ui.modal>
</div>
