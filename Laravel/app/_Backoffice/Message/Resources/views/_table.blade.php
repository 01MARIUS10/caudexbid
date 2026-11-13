@php($rows = $messages->toArray())
<table class="table">
    <thead><tr><th>Envoyé le</th><th>Type</th><th>De</th><th>À</th><th>Mémo</th><th>Lu</th></tr></thead>
    <tbody>
    @forelse ($rows as $msg)
        <tr>
            <td class="whitespace-nowrap">@kst($msg['envoye_le'])</td>
            <td><x-bo.status :status="$msg['type']" /></td>
            <td><a class="link" href="{{ route('bo.members.show', $msg['expediteur_id']) }}">{{ $msg['expediteur'] }}</a></td>
            <td><a class="link" href="{{ route('bo.members.show', $msg['destinataire_id']) }}">{{ $msg['destinataire'] }}</a></td>
            <td class="max-w-md">{{ $msg['memo'] }}</td>
            <td class="whitespace-nowrap">
                @if ($msg['lu_le'])
                    <span class="text-xs text-stone-500">@kst($msg['lu_le'])</span>
                @elseif ($msg['destinataire_id'] === auth()->id())
                    <form method="POST" action="{{ route('bo.messages.read', $msg['id']) }}">@csrf @method('PATCH')<button class="link text-xs">Marquer lu</button></form>
                @else
                    <span class="text-xs text-amber-700">non lu</span>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-stone-500">Aucun message.</td></tr>
    @endforelse
    </tbody>
</table>
@if ($paginate ?? true)<div class="mt-4">{{ $messages->links() }}</div>@endif
