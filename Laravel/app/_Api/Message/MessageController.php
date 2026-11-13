<?php

namespace App\_Api\Message;

use App\Http\Controllers\Controller;
use App\Modules\Message\Actions\MarkMessageRead\MarkMessageReadAction;
use App\Modules\Message\Actions\MarkMessageRead\MarkMessageReadInput;
use App\Modules\Message\Actions\SendMessage\SendMessageAction;
use App\Modules\Message\Actions\SendMessage\SendMessageInput;
use App\_Api\Message\ViewModels\MessageViewModel;
use App\Modules\Message\Repository\MessageRepository;
use App\Modules\User\_UserAuth\ViewModels\UserAuthViewModel;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Shared\Support\MemberDirectory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Shared\ViewModels\ViewModelCollection;

/**
 * Menus « Messages reçus », « Messages envoyés » et « Envoyer un message ».
 */
class MessageController extends Controller
{
    public function inbox(Request $request, MessageRepository $messages, ListMembersAction $members): ViewModelCollection
    {
        return $this->collection($messages->inbox($request->user()->id), $members);
    }

    public function outbox(Request $request, MessageRepository $messages, ListMembersAction $members): ViewModelCollection
    {
        return $this->collection($messages->outbox($request->user()->id), $members);
    }

    public function store(Request $request, SendMessageAction $action, ListMembersAction $members): MessageViewModel
    {
        $message = $action->execute(SendMessageInput::fromArray($request->all(), $request->user()));

        return (new MessageViewModel($message, MemberDirectory::forColumns($members, [$message], 'expediteur_id', 'destinataire_id')))->created();
    }

    public function markRead(Request $request, int $message, MarkMessageReadAction $action, ListMembersAction $members): MessageViewModel
    {
        $input = MarkMessageReadInput::fromArray(['message_id' => $message], $request->user()->id);
        $read = $action->execute($input);

        return new MessageViewModel($read, MemberDirectory::forColumns($members, [$read], 'expediteur_id', 'destinataire_id'));
    }

    private function collection(iterable $page, ListMembersAction $members): ViewModelCollection
    {
        $directory = MemberDirectory::forColumns($members, $page, 'expediteur_id', 'destinataire_id');

        return MessageViewModel::collection($page, fn ($m) => new MessageViewModel($m, $directory));
    }

    /** Liste des membres premium (destinataires possibles d'une demande de statut). */
    public function premiums(UserAuthRepository $users): ViewModelCollection
    {
        return UserAuthViewModel::collection($users->premiums());
    }
}
