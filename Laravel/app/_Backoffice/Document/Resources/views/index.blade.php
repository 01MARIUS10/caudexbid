<x-bo.layout title="Rapports par session">
    @php($session = $session?->toArray())

    <form method="GET" class="flex flex-wrap items-end gap-3">
        <x-bo.select name="session" label="Session" :value="$session['id'] ?? null" onchange="this.form.submit()"
            :options="collect($sessions->toArray())->mapWithKeys(fn ($s) => [$s['id'] => $s['titre'].' ('.$s['statut'].')'])" />
        <noscript><button class="btn-secondary">Choisir</button></noscript>
        @if ($session)
            <p class="pb-2 text-sm text-stone-500">Clôture @kst($session['cloture_le']) KST</p>
        @endif
    </form>

    @if (! $session)
        <p class="text-sm text-stone-500">Aucune session.</p>
    @else
        {{-- Un rapport par ligne : nombre de lignes affiché directement, chevron pour déplier le tableau. --}}
        <div class="divide-y divide-stone-100 overflow-hidden rounded-xl border border-stone-200 bg-white">
            @foreach ($documents as $numero => $doc)
                @php($count = $doc['rows']->count())
                <details class="group">
                    <summary class="flex cursor-pointer list-none items-center gap-4 px-5 py-3.5 transition select-none hover:bg-stone-50 [&::-webkit-details-marker]:hidden">
                        <span class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-stone-100 text-xs font-semibold text-stone-500">{{ $numero }}</span>
                        <span class="flex-1 text-sm font-medium text-stone-900">{{ $doc['titre'] }}</span>
                        <span @class([
                            'min-w-10 rounded-full px-2.5 py-0.5 text-center text-sm font-semibold tabular-nums',
                            'bg-brand-50 text-brand-900' => $count > 0,
                            'bg-stone-100 text-stone-400' => $count === 0,
                        ]) title="{{ $count }} ligne(s)">{{ $count }}</span>
                        <svg viewBox="0 0 24 24" class="size-4 shrink-0 text-stone-400 transition group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <div class="border-t border-stone-100 bg-stone-50/50 px-5 py-4">
                        @include('document::_table', ['rows' => $doc['rows']])
                        @if ($count > 0)
                            <div class="mt-3 flex justify-end">
                                <a href="{{ route('bo.documents.show', ['numero' => $numero, 'session' => $session['id']]) }}" class="btn-secondary text-xs">
                                    <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                    Version imprimable
                                </a>
                            </div>
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
    @endif
</x-bo.layout>
