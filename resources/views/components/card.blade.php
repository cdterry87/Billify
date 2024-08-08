@props(['title' => null, 'primaryAction' => null])

<div {{ $attributes->merge([
    'class' => 'p-4 bg-white rounded-lg border border-zinc-200',
]) }}>
    <div class="flex flex-col gap-4">
        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
            <div>
                @if ($title)
                    <h2 class="text-3xl font-bold">{{ $title }}</h2>
                @endif
            </div>
            <div>
                @if ($primaryAction)
                    {{ $primaryAction }}
                @endif
            </div>
        </div>
        <div>
            {{ $slot }}
        </div>
    </div>
</div>
