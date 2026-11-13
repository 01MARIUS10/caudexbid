<?php

namespace App\_Backoffice\Member;

use App\Modules\Message\ViewModels\BackOffice\MessageBoViewModel;
use App\Modules\Product\ViewModels\BackOffice\ProductBoViewModel;
use App\_Backoffice\Member\ViewModels\SponsoredMemberBoViewModel;
use App\_Backoffice\Member\ViewModels\UserGradeBoViewModel;
use App\Modules\User\_UserGrad\ViewModels\GradeViewModel;
use App\Modules\User\_UserAuth\ViewModels\BackOffice\MemberBoViewModel;
use App\Http\Controllers\Controller;
use App\Modules\Message\Repository\MessageRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\User\_UserAuth\Actions\CreateMember\CreateMemberAction;
use App\Modules\User\_UserAuth\Actions\CreateMember\CreateMemberInput;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Modules\User\_UserGrad\Repository\GradeRepository;
use App\Modules\User\_UserGrad\Repository\UserGradeRepository;
use App\Modules\User\_UserProfil\Repository\UserProfileRepository;
use Illuminate\Http\RedirectResponse;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Shared\Support\MemberDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Membres : liste, fiche, création.
 */
class MemberController extends Controller
{
    public function index(Request $request, UserAuthRepository $users, GradeRepository $grades): View
    {
        $grade = GradeCode::tryFrom((string) $request->query('grade'));

        return view('member::index', [
            'members' => MemberBoViewModel::collection($users->search($grade, $request->query('q'))),
            'grades' => GradeViewModel::collection($grades->all()),
        ]);
    }

    public function create(UserAuthRepository $users, GradeRepository $grades): View
    {
        return view('member::create', [
            'grades' => GradeViewModel::collection($grades->all()),
            'premiums' => $users->options(GradeCode::Premium),
        ]);
    }

    public function store(Request $request, CreateMemberAction $action): RedirectResponse
    {
        $member = $action->execute(CreateMemberInput::fromArray($request->all(), $request->user()));

        return redirect()->route('bo.members.show', $member->id)->with('status', 'Membre créé.');
    }

    public function show(
        int $member,
        UserAuthRepository $users,
        UserProfileRepository $profiles,
        UserGradeRepository $userGrades,
        GradeRepository $grades,
        ProductRepository $products,
        MessageRepository $messages,
        ListMembersAction $listMembers,
    ): View {
        $user = $users->findById($member) ?? abort(404);
        $user->load('profile.parrain', 'preference');
        $unsettled = $products->unsettled([$user->id]);
        $page = $messages->search(membreId: $user->id, perPage: 10);
        $winners = MemberDirectory::load($listMembers, $unsettled->flatMap(fn ($p) => [$p->auteur_prix_actuel_id, $p->achat?->acheteur_id]));
        $correspondents = MemberDirectory::forColumns($listMembers, $page, 'expediteur_id', 'destinataire_id');

        return view('member::show', [
            'member' => MemberBoViewModel::make($user),
            'history' => UserGradeBoViewModel::collection($userGrades->history($user->id)),
            'grades' => GradeViewModel::collection($grades->all()),
            'filleuls' => SponsoredMemberBoViewModel::collection($profiles->sponsored($user->id)),
            'nonSoldes' => ProductBoViewModel::collection($unsettled, fn ($p) => new ProductBoViewModel($p, $winners)),
            'messages' => MessageBoViewModel::collection($page, fn ($m) => new MessageBoViewModel($m, $correspondents)),
        ]);
    }
}
