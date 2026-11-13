<?php

namespace App\_Backoffice\Document;

use App\Modules\Product\ViewModels\BackOffice\ProductAuctionSessionBoViewModel;
use App\Http\Controllers\Controller;
use App\Modules\Product\Repository\DocumentRepository;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersInput;
use Illuminate\Http\Request;
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

    public function index(ProductAuctionSessionRepository $sessions): View
    {
        return view('document::index', [
            'documents' => self::DOCUMENTS,
            'sessions' => ProductAuctionSessionBoViewModel::collection($sessions->all()),
            'current' => ($current = $sessions->current()) ? ProductAuctionSessionBoViewModel::make($current) : null,
        ]);
    }

    public function show(Request $request, int $numero, DocumentRepository $documents, ProductAuctionSessionRepository $sessions, ListMembersAction $listMembers): View
    {
        abort_unless(isset(self::DOCUMENTS[$numero]), 404);
        $session = $sessions->find($request->integer('session')) ?? $sessions->current() ?? abort(404);

        $members = $listMembers->execute(new ListMembersInput)->keyBy('id');

        $rows = match ($numero) {
            1 => $documents->lots($session->id),
            2 => $documents->adjudications($session->id, null, $members),
            3 => $documents->enchereEnCours($session->id, null, $members),
            4 => $documents->repartition($session->id, null),
            5 => $documents->parPremium($session->id, null, $members),
        };

        return view('document::show', [
            'numero' => $numero,
            'titre' => self::DOCUMENTS[$numero],
            'session' => ProductAuctionSessionBoViewModel::make($session),
            'rows' => $rows,
            'columns' => $rows->isNotEmpty() ? array_keys($rows->first()) : [],
        ]);
    }
}
