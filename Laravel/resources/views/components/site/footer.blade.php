<footer class="border-t border-stone-200 bg-white">
    @if (! $slot->isEmpty())
        <div class="border-b border-stone-100 px-5 py-6 md:px-10">{{ $slot }}</div>
    @endif
    <div class="flex flex-col gap-6 px-5 py-8 text-sm md:flex-row md:items-start md:justify-between md:px-10">
        <div class="space-y-1.5">
            <p class="font-medium text-stone-900">{{ __('home.policy') }}</p>
            <p class="text-stone-500">© {{ now()->year }} {{ config('caudexbid.exploitant.site') }}</p>
        </div>
        {{-- Informations sur l'administrateur : nom du site et e-mail uniquement, pas de téléphone. --}}
        <div class="space-y-1.5 md:text-right">
            <p class="font-medium text-stone-900">{{ __('home.operator') }}</p>
            <p class="text-stone-500">
                {{ config('caudexbid.exploitant.site') }}
                <span class="mx-1.5 text-stone-300">·</span>
                <a class="link" href="mailto:{{ config('caudexbid.exploitant.email') }}">{{ config('caudexbid.exploitant.email') }}</a>
            </p>
        </div>
    </div>
</footer>
