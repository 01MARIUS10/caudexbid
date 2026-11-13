<?php

namespace App\_Api\ProductMedia;

use App\Http\Controllers\Controller;
use App\Modules\Product\_ProductMedia\Actions\AddProductMedia\AddProductMediaAction;
use App\Modules\Product\_ProductMedia\Actions\AddProductMedia\AddProductMediaValidation;
use App\Modules\Product\_ProductMedia\Actions\DeleteProductMedia\DeleteProductMediaAction;
use App\Modules\Product\_ProductMedia\Actions\DeleteProductMedia\DeleteProductMediaValidation;
use App\Modules\Product\_ProductMedia\ViewModels\ProductMediaViewModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductMediaController extends Controller
{
    public function store(Request $request, int $product, AddProductMediaValidation $validation, AddProductMediaAction $action): ProductMediaViewModel
    {
        $input = $validation->validate(['produit_id' => $product] + $request->all(), $request->user());

        return (new ProductMediaViewModel($action->execute($input)))->created();
    }

    public function destroy(Request $request, int $media, DeleteProductMediaValidation $validation, DeleteProductMediaAction $action): Response
    {
        $action->execute($validation->validate(['media_id' => $media], $request->user()));

        return response()->noContent();
    }
}
