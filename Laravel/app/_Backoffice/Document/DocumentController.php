<?php

namespace App\_Backoffice\Document;

use App\Modules\Product\ViewModels\BackOffice\ProductAuctionSessionBoViewModel;
use App\Http\Controllers\Controller;
use App\Modules\Product\Repository\DocumentRepository;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersInput;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Les 5 documents par session (écran + impression A4 paysage). Administrateur : toutes les données.
 */
class DocumentController extends Controller
{
    public const DOCUMENTS = [
        1 => 'Liste des lots par session',
        2 => 'Résultat des adjudications par session',
        3 => 'Enchères en cours par session',
        4 => 'Répartition par session',
        5 => 'Adjudications par membre premium',
    ];

    /** Les 5 documents de la session choisie, avec leurs lignes (affichés en accordéon : nombre de lignes + détail). */
    public function index(Request $request, DocumentRepository $documents, ProductAuctionSessionRepository $sessions, ListMembersAction $listMembers): View
    {
        $all = $sessions->all();
        $session = $sessions->find($request->integer('session')) ?? $sessions->current() ?? $all->first();
        $members = $session ? $this->members($listMembers) : collect();

        return view('document::index', [
            'sessions' => ProductAuctionSessionBoViewModel::collection($all),
            'session' => $session ? ProductAuctionSessionBoViewModel::make($session) : null,
            'documents' => collect(self::DOCUMENTS)->map(fn (string $titre, int $numero) => [
                'titre' => $titre,
                'rows' => $session ? $this->rows($numero, $session->id, $documents, $members) : collect(),
            ]),
        ]);
    }

    /** Un document seul : version imprimable (A4 paysage). */
    public function show(Request $request, int $numero, DocumentRepository $documents, ProductAuctionSessionRepository $sessions, ListMembersAction $listMembers): View
    {
        abort_unless(isset(self::DOCUMENTS[$numero]), 404);
        $session = $sessions->find($request->integer('session')) ?? $sessions->current() ?? abort(404);

        return view('document::show', [
            'numero' => $numero,
            'titre' => self::DOCUMENTS[$numero],
            'session' => ProductAuctionSessionBoViewModel::make($session),
            'rows' => $this->rows($numero, $session->id, $documents, $this->members($listMembers)),
        ]);
    }

    private function rows(int $numero, int $sessionId, DocumentRepository $documents, Collection $members): Collection
    {
        return match ($numero) {
            1 => $documents->lots($sessionId),
            2 => $documents->adjudications($sessionId, null, $members),
            3 => $documents->enchereEnCours($sessionId, null, $members),
            4 => $documents->repartition($sessionId, null),
            5 => $documents->parPremium($sessionId, null, $members),
        };
    }

    private function members(ListMembersAction $listMembers): Collection
    {
        return $listMembers->execute(new ListMembersInput)->keyBy('id');
    }
}
