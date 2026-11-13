<x-bo.layout :title="$numero.'. '.$titre">
    @php
        $session = $session->toArray();
        $labels = [
            'date_enchere' => "Date de l'enchère", 'nom' => 'Nom de la plante', 'lot' => 'Lot', 'description_1' => 'Description 1',
            'prix_vente_existant' => 'Prix de vente existant', 'prix_base' => 'Prix de base', 'prix_achat_immediat' => "Prix d'achat immédiat",
            'prix_adjudication' => "Prix d'adjudication", 'adjudicataire' => 'Adjudicataire', 'montant_enchere' => "Montant de l'enchère",
            'encherisseur' => 'Enchérisseur', 'le' => 'Le', 'part_premium' => 'Part membre premium', 'part_exploitant' => "Part de l'exploitant",
            'part_proprietaire' => 'Part propriétaire pépinière', 'dividende_total_proprietaire' => 'Dividende total propriétaire',
            'membre_premium' => 'Membre premium', 'montant' => 'Montant',
        ];
        $numeric = ['prix_vente_existant', 'prix_base', 'prix_achat_immediat', 'prix_adjudication', 'montant_enchere', 'part_premium', 'part_exploitant', 'part_proprietaire', 'dividende_total_proprietaire', 'montant'];
    @endphp
    <div class="flex items-center justify-between print:hidden">
        <a href="{{ route('bo.documents.index', ['session' => $session['id']]) }}" class="link text-sm">← Documents</a>
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
        @if ($rows->isEmpty())
            <p class="text-sm text-stone-500">Aucune donnée pour cette session.</p>
        @else
            <table class="table">
                <thead><tr>@foreach ($columns as $c)<th @class(['text-right' => in_array($c, $numeric, true)])>{{ $labels[$c] ?? $c }}</th>@endforeach</tr></thead>
                <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($columns as $c)
                            <td @class(['text-right tabular-nums' => in_array($c, $numeric, true)])>
                                @if (in_array($c, $numeric, true)) @money($row[$c]) @elseif ($row[$c] instanceof \Carbon\CarbonInterface) @kst($row[$c]) @else {{ $row[$c] ?? '—' }} @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
            <p class="mt-3 text-right text-xs text-stone-500">{{ $rows->count() }} ligne(s) · imprimé le {{ now()->tz(config('caudexbid.display_timezone'))->format('Y-m-d H:i') }} KST</p>
        @endif
    </div>
</x-bo.layout>
