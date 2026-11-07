<x-layouts::app title="Style guide">
    <x-slot:actions>
        <x-ui.button variant="ghost" size="sm" icon="user" label="Profile" />
    </x-slot:actions>

    <div class="stack stack--lg">
        <div class="stack">
            <h1>Style guide</h1>
            <p class="muted">Every shared component, in the active theme. Local only.</p>
        </div>

        <x-ui.card title="Buttons">
            <div class="cluster">
                <x-ui.button icon="plus">Add bill</x-ui.button>
                <x-ui.button variant="secondary">Secondary</x-ui.button>
                <x-ui.button variant="ghost" icon="reset">Reset</x-ui.button>
                <x-ui.button variant="danger" icon="trash">Delete</x-ui.button>
                <x-ui.button size="sm">Small</x-ui.button>
                <x-ui.button variant="secondary" icon="pencil" label="Edit" />
                <x-ui.button disabled>Disabled</x-ui.button>
                <x-ui.button variant="secondary" href="#main">Link</x-ui.button>
            </div>
        </x-ui.card>

        <div class="grid-tiles">
            <x-ui.stat-tile label="Annual Income" value="$100,000.00" />
            <x-ui.stat-tile label="Monthly Income" value="$8,333.33" />
            <x-ui.stat-tile label="Bi-Weekly Income" value="$3,846.15" />
            <x-ui.stat-tile label="Weekly Income" value="$1,923.08" />
            <x-ui.stat-tile label="Monthly Remainder" value="$4,120.33" tone="positive" />
            <x-ui.stat-tile label="Bills vs Income" value="50.56%" />
        </div>

        <div class="grid-split">
            <x-ui.card>
                <x-ui.chart-bar
                    title="Income vs Bills"
                    :bars="[
                        ['label' => 'Income', 'value' => 8333.33, 'display' => '$8,333.33', 'tone' => 'positive'],
                        ['label' => 'Bills', 'value' => 4213, 'display' => '$4,213.00', 'tone' => 'negative'],
                    ]"
                />
            </x-ui.card>
            <x-ui.card>
                <x-ui.chart-bar
                    title="Top Bill Categories"
                    :bars="[
                        ['label' => 'Housing', 'value' => 1800, 'display' => '$1,800'],
                        ['label' => 'Debt', 'value' => 950, 'display' => '$950'],
                        ['label' => 'Insurance', 'value' => 640, 'display' => '$640'],
                        ['label' => 'Utilities', 'value' => 410, 'display' => '$410'],
                        ['label' => 'Medical/Health', 'value' => 220, 'display' => '$220'],
                    ]"
                />
            </x-ui.card>
        </div>

        <x-ui.card title="Form fields">
            <div class="stack">
                <x-ui.field name="sg-name" label="Name" required placeholder="Rent" hint="Up to 64 characters." />
                <x-ui.field name="sg-amount" label="Amount" type="number" required />
                <x-ui.field name="sg-category" label="Category" as="select" placeholder="Select a category" :options="['housing' => 'Housing', 'food' => 'Food']" />
                <x-ui.field name="sg-description" label="Description" as="textarea" />
                <x-ui.checkbox name="sg-january" label="January" />
            </div>
        </x-ui.card>

        <x-ui.card title="Chips and feedback">
            <div class="stack">
                <div class="cluster">
                    <x-ui.chip>Housing</x-ui.chip>
                    <x-ui.chip tone="accent">June</x-ui.chip>
                </div>
                <div class="cluster" x-data>
                    <x-ui.button variant="secondary" x-on:click="$dispatch('open-modal', { name: 'sg-modal' })">Open modal</x-ui.button>
                    <x-ui.button variant="secondary" x-on:click="$dispatch('toast', { type: 'success', message: 'Bill saved.' })">Success toast</x-ui.button>
                    <x-ui.button variant="secondary" x-on:click="$dispatch('toast', { type: 'error', message: 'Something went wrong.' })">Error toast</x-ui.button>
                </div>
            </div>
        </x-ui.card>
    </div>

    <x-ui.modal name="sg-modal" title="Edit bill">
        <x-ui.field name="sg-modal-name" label="Name" />
        <div class="cluster">
            <x-ui.button>Save</x-ui.button>
            <x-ui.button variant="ghost" x-on:click="$dispatch('close-modal', { name: 'sg-modal' })">Cancel</x-ui.button>
        </div>
    </x-ui.modal>
</x-layouts::app>
