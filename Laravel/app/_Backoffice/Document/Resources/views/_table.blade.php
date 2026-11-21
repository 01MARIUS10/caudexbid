{{-- Tableau d'un document (partagé par la liste en accordéon et la version imprimable). Attend : $rows. --}}
@php
    $columns = $rows->isNotEmpty() ? array_keys($rows->first()) : [];
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
@if ($rows->isEmpty())
    <p class="text-sm text-stone-500">Aucune donnée pour cette session.</p>
@else
    <div class="overflow-x-auto">
        <table class="table">
            <thead><tr>@foreach ($columns as $c)<th @class(['text-right' => in_array($c, $numeric, true)])>{{ $labels[$c] ?? $c }}</th>@endforeach</tr></thead>
            <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($columns as $c)
                        <td @class(['text-right tabular-nums' => in_array($c, $numeric, true), 'whitespace-nowrap' => in_array($c, ['lot', 'date_enchere', 'le'], true)])>
                            @if (in_array($c, $numeric, true)) @money($row[$c]) @elseif ($row[$c] instanceof \Carbon\CarbonInterface) @kst($row[$c]) @else {{ $row[$c] ?? '—' }} @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
