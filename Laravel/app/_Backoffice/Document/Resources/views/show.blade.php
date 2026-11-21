<x-bo.layout :title="$numero.'. '.$titre">
    @php($session = $session->toArray())
    <div class="flex items-center justify-between print:hidden">
        <a href="{{ route('bo.documents.index', ['session' => $session['id']]) }}" class="link text-sm">← Rapports par session</a>
        <button onclick="window.print()" class="btn">Imprimer (A4 paysage)</button>
    </div>

    <div class="rounded-xl border border-stone-200 bg-white p-6 print:border-0 print:p-0">
        <div class="mb-4 flex items-end justify-between border-b border-stone-200 pb-3">
            <div>
                <p class="text-xs tracking-wide text-stone-500 uppercase">caudexbid.com</p>
                <h2 class="text-lg font-semibold text-stone-900">{{ $titre }}</h2>
            </div>
            <p class="text-sm text-stone-600">{{ $session['titre'] }} · clôture @kst($session['cloture_le']) KST</p>
        </div>
        @include('document::_table', ['rows' => $rows])
        @if ($rows->isNotEmpty())
            <p class="mt-3 text-right text-xs text-stone-500">{{ $rows->count() }} ligne(s) · imprimé le {{ now()->tz(config('caudexbid.display_timezone'))->format('Y-m-d H:i') }} KST</p>
        @endif
    </div>
</x-bo.layout>
