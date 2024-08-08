@props(['label', 'value'])

<div class="bg-white p-2 sm:p-4 rounded-lg border border-zinc-200 flex flex-col sm:flex-row items-center gap-1 sm:gap-3">
    <div
        class="text-zinc-600 uppercase text-xs w-full sm:w-2/5 border-b sm:border-r sm:border-b-0 border-zinc-200 sm:tracking-wider text-center sm:text-left sm:pr-2 pb-1 sm:pb-0">
        {{ $label }}
    </div>
    <div class="text-zinc-800 font-bold text-lg sm:text-2xl w-full sm:w-3/5 text-center">
        {{ $value }}
    </div>
</div>
