<?php

namespace App\_Backoffice\Product;

use App\Modules\User\_UserAuth\ViewModels\BackOffice\MemberBoViewModel;
use App\_Backoffice\Product\ViewModels\ProductProxyBoViewModel;
use App\Modules\Product\_ProductBid\ViewModels\BackOffice\ProductBidBoViewModel;
use App\Modules\Product\ViewModels\BackOffice\ProductOwnerBoViewModel;
use App\Modules\Product\ViewModels\BackOffice\ProductAuctionSessionBoViewModel;
use App\Modules\Product\ViewModels\BackOffice\ProductBoViewModel;
use App\Http\Controllers\Controller;
use App\Modules\Product\Actions\CloseExpiredAuctions\CloseExpiredAuctionsAction;
use App\Modules\Product\Actions\CloseExpiredAuctions\CloseExpiredAuctionsValidation;
use App\Modules\Product\Actions\CreateProduct\CreateProductAction;
use App\Modules\Product\Actions\CreateProduct\CreateProductValidation;
use App\Modules\Product\Actions\SettleProduct\SettleProductAction;
use App\Modules\Product\Actions\SettleProduct\SettleProductValidation;
use App\Modules\Product\Models\Enums\AuctionSessionStatus;
use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\_ProductBid\Repository\ProductBidRepository;
use App\Modules\Product\Repository\DocumentRepository;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\Product\Repository\ProductOwnerRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use Illuminate\Http\RedirectResponse;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Shared\Support\MemberDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Produits (lots) : liste, création, fiche complète, règlement, clôture manuelle.
 */
class ProductController extends Controller
{
    public function index(Request $request, ProductRepository $products, ProductAuctionSessionRepository $sessions, ListMembersAction $members): View
    {
        $page = $products->search(
            $request->integer('session') ?: null,
            ProductStatus::tryFrom((string) $request->query('statut')),
            $request->query('q'),
        );
        $directory = MemberDirectory::load($members, collect($page->items())->flatMap(fn ($p) => [$p->auteur_prix_actuel_id, $p->achat?->acheteur_id]));

        return view('product::index', [
            'products' => ProductBoViewModel::collection($page, fn ($p) => new ProductBoViewModel($p, $directory)),
            'sessions' => ProductAuctionSessionBoViewModel::collection($sessions->all()),
            'statuts' => ProductStatus::cases(),
        ]);
    }

    public function create(ProductAuctionSessionRepository $sessions, ProductOwnerRepository $owners): View
    {
        return view('product::create', [
            'sessions' => ProductAuctionSessionBoViewModel::collection($sessions->all()->whereIn('statut', [AuctionSessionStatus::Scheduled, AuctionSessionStatus::Open])->values()),
            'owners' => ProductOwnerBoViewModel::collection($owners->all()),
        ]);
    }

    public function store(Request $request, CreateProductValidation $validation, CreateProductAction $action): RedirectResponse
    {
        $data = $request->except('_token');
        $data['publier'] = $request->boolean('publier');
        $product = $action->execute($validation->validate(array_filter($data, fn ($v) => $v !== null), $request->user()));

        return redirect()->route('bo.products.show', $product->id)->with('status', 'Lot créé. Ajoutez maintenant les photos.');
    }

    public function show(
        int $product,
        ProductRepository $products,
        ProductBidRepository $bids,
        DocumentRepository $documents,
        UserAuthRepository $users,
        ListMembersAction $members,
    ): View {
        $model = $products->find($product) ?? abort(404);
        $model->load('proprietaire');
        $winnerId = $model->adjudicataireId();
        $history = $bids->history($model->id);
        $proxies = $bids->proxiesOf($model->id);
        $directory = MemberDirectory::load($members, [
            $model->auteur_prix_actuel_id, $model->achat?->acheteur_id,
            ...$history->pluck('membre_id')->all(), ...$proxies->pluck('membre_id')->all(),
        ]);

        return view('product::show', [
            'product' => ProductBoViewModel::make($model, $directory),
            'bids' => ProductBidBoViewModel::collection($history, fn ($b) => new ProductBidBoViewModel($b, $directory)),
            'proxies' => ProductProxyBoViewModel::collection($proxies, fn ($px) => new ProductProxyBoViewModel($px, $directory)),
            'shares' => $model->statut->isUnsettledSale() ? $documents->shares($model) : null,
            'winner' => ($winner = $winnerId ? $users->findById($winnerId)?->load('profile.parrain') : null) ? MemberBoViewModel::make($winner) : null,
        ]);
    }

    /** Règlement terminé : suppression du lot et de ses enchères. */
    public function settle(Request $request, int $product, SettleProductValidation $validation, SettleProductAction $action): RedirectResponse
    {
        $action->execute($validation->validate(['produit_id' => $product], $request->user()));

        return redirect()->route('bo.products.index')->with('status', 'Lot réglé et supprimé.');
    }

    /** Lance la clôture tout de suite (sinon le scheduler le fait chaque minute). */
    public function closeAuctions(CloseExpiredAuctionsValidation $validation, CloseExpiredAuctionsAction $action): RedirectResponse
    {
        $r = $action->execute($validation->validate());

        return back()->with('status', sprintf(
            'Clôture : %d session(s) ouverte(s), %d lot(s) adjugé(s), %d non vendu(s), %d session(s) clôturée(s).',
            $r['sessions_ouvertes'], $r['adjuges'], $r['non_vendus'], $r['sessions_cloturees'],
        ));
    }
}
