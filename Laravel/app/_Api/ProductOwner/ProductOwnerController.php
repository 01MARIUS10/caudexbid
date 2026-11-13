<?php

namespace App\_Api\ProductOwner;

use App\Http\Controllers\Controller;
use App\Modules\Product\Actions\CreateProductOwner\CreateProductOwnerAction;
use App\Modules\Product\Actions\CreateProductOwner\CreateProductOwnerValidation;
use App\_Api\ProductOwner\ViewModels\ProductOwnerViewModel;
use App\Modules\Product\Repository\ProductOwnerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Shared\ViewModels\ViewModelCollection;

class ProductOwnerController extends Controller
{
    public function index(ProductOwnerRepository $owners): ViewModelCollection
    {
        return ProductOwnerViewModel::collection($owners->all());
    }

    public function store(Request $request, CreateProductOwnerValidation $validation, CreateProductOwnerAction $action): ProductOwnerViewModel
    {
        $owner = $action->execute($validation->validate($request->all(), $request->user()));

        return (new ProductOwnerViewModel($owner))->created();
    }
}
