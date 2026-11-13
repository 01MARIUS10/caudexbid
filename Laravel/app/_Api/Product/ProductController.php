<?php

namespace App\_Api\Product;

use App\Http\Controllers\Controller;
use App\Modules\Product\Actions\CreateProduct\CreateProductAction;
use App\Modules\Product\Actions\CreateProduct\CreateProductValidation;
use App\Modules\Product\Actions\SettleProduct\SettleProductAction;
use App\Modules\Product\Actions\SettleProduct\SettleProductValidation;
use App\_Api\Product\ViewModels\ProductCardViewModel;
use App\Modules\Product\ViewModels\ProductDetailViewModel;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\_ProductBid\Repository\ProductBidRepository;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Modules\User\_UserProfil\Repository\UserProfileRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Shared\Exceptions\BusinessRuleException;
use App\Shared\Support\MemberDirectory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Shared\ViewModels\ViewModelCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /** Menu « Enchères de cette session ». */
    public function index(Request $request, ProductRepository $products, ProductAuctionSessionRepository $sessions, ListMembersAction $members): ViewModelCollection
    {
        $sessionId = $request->integer('session_id') ?: $sessions->current()?->id;

        $items = $sessionId ? $products->forSession($sessionId) : collect();
        $directory = $this->winners($items, $members);

        return ProductCardViewModel::collection($items, fn ($p) => new ProductCardViewModel($p, $request->user(), $directory));
    }

    /**
     * Menu « Enchères non soldées » : lots adjugés ou achetés pas encore réglés.
     * M : les siens ; P : les siens + ceux de ses filleuls ; A : tous.
     */
    public function unsettled(Request $request, ProductRepository $products, UserProfileRepository $profiles, ListMembersAction $members): ViewModelCollection
    {
        $user = $request->user();

        $scope = match ($user->gradeCode()) {
            GradeCode::Admin => null,
            GradeCode::Premium => [$user->id, ...$profiles->sponsoredMemberIds($user->id)],
            default => [$user->id],
        };

        $items = $products->unsettled($scope);
        $directory = $this->winners($items, $members);

        return ProductCardViewModel::collection($items, fn ($p) => new ProductCardViewModel($p, $user, $directory));
    }

    public function show(Request $request, int $product, ProductRepository $products, ProductBidRepository $bids, ListMembersAction $members): ProductDetailViewModel
    {
        $model = $products->find($product) ?? throw new BusinessRuleException('Produit introuvable.', 404);
        $viewer = $request->user();

        return new ProductDetailViewModel($model, $viewer, $this->winners([$model], $members), $viewer ? $bids->proxyOf($model->id, $viewer->id) : null);
    }

    public function store(Request $request, CreateProductValidation $validation, CreateProductAction $action, ProductRepository $products, ListMembersAction $members): ProductDetailViewModel
    {
        $product = $action->execute($validation->validate($request->all(), $request->user()));

        $created = $products->find($product->id);

        return (new ProductDetailViewModel($created, $request->user(), $this->winners([$created], $members)))->created();
    }

    /** Annuaire des membres dont l'écran a besoin : l'adjudicataire de chaque lot (et son parrain). */
    private function winners(iterable $products, ListMembersAction $members): MemberDirectory
    {
        $ids = [];
        foreach ($products as $p) {
            $ids[] = $p->adjudicataireId();
        }

        return MemberDirectory::load($members, $ids);
    }

    /** Règlement terminé : suppression du produit et de ses enchères. */
    public function settle(Request $request, int $product, SettleProductValidation $validation, SettleProductAction $action): Response
    {
        $action->execute($validation->validate(['produit_id' => $product], $request->user()));

        return response()->noContent();
    }
}
