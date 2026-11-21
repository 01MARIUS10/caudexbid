<x-bo.layout title="Barème des grades">
    <x-bo.card title="Barème des grades" subtitle="Le grade actuel d'un membre est sa dernière attribution (table user_grades)">
        <table class="table">
            <thead><tr><th>Code</th><th>Nom</th><th>Niveau</th><th>Peut enchérir</th><th>Peut changer un grade</th><th class="text-right">Membres</th></tr></thead>
            <tbody>
            @foreach ($grades->toArray() as $g)
                <tr>
                    <td><x-bo.grade :code="$g['code']" /></td>
                    <td class="font-medium">{{ $g['nom'] }}</td>
                    <td class="tabular-nums">{{ $g['niveau'] }}</td>
                    <td>{{ $g['peut_encherir'] ? 'Oui' : 'Non' }}</td>
                    <td>{{ $g['peut_changer_grade'] ? 'Oui' : 'Non' }}</td>
                    <td class="text-right"><a class="link" href="{{ route('bo.members.index', ['grade' => $g['code']]) }}">{{ $counts[$g['code']] ?? 0 }}</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </x-bo.card>
</x-bo.layout>
