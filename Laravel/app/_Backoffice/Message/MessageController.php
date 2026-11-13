<?php

namespace App\_Backoffice\Message;

use App\Modules\Message\ViewModels\BackOffice\MessageBoViewModel;
use App\Http\Controllers\Controller;
use App\Modules\Message\Actions\MarkMessageRead\MarkMessageReadAction;
use App\Modules\Message\Actions\MarkMessageRead\MarkMessageReadInput;
use App\Modules\Message\Actions\SendMessage\SendMessageAction;
use App\Modules\Message\Actions\SendMessage\SendMessageInput;
use App\Modules\Message\Models\Enums\MessageType;
use App\Modules\Message\Repository\MessageRepository;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use Illuminate\Http\RedirectResponse;
use App\Shared\Support\MemberDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Tous les messages du site + envoi d'un message au nom de l'administrateur.
 */
class MessageController extends Controller
{
    public function index(Request $request, MessageRepository $messages, UserAuthRepository $users, ListMembersAction $listMembers): View
    {
        $page = $messages->search(
            MessageType::tryFrom((string) $request->query('type')),
            $request->integer('membre') ?: null,
        );
        $directory = MemberDirectory::forColumns($listMembers, $page, 'expediteur_id', 'destinataire_id');

        return view('message::index', [
            'messages' => MessageBoViewModel::collection($page, fn ($m) => new MessageBoViewModel($m, $directory)),
            'types' => MessageType::cases(),
            'members' => $users->options(),
        ]);
    }

    public function create(Request $request, UserAuthRepository $users): View
    {
        return view('message::create', [
            'expediteur' => $request->user()->identifiant,
            'members' => $users->options()->except($request->user()->id),
            'types' => MessageType::cases(),
        ]);
    }

    public function store(Request $request, SendMessageAction $action): RedirectResponse
    {
        $action->execute(SendMessageInput::fromArray($request->all(), $request->user()));

        return redirect()->route('bo.messages.index')->with('status', 'Message envoyé.');
    }

    public function markRead(Request $request, int $message, MarkMessageReadAction $action): RedirectResponse
    {
        $action->execute(MarkMessageReadInput::fromArray(['message_id' => $message], $request->user()->id));

        return back()->with('status', 'Message marqué comme lu.');
    }
}
