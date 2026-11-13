<?php

namespace App\_Backoffice\Owner;

use App\Modules\Product\ViewModels\BackOffice\ProductOwnerBoViewModel;
use App\Http\Controllers\Controller;
use App\Modules\Product\Actions\CreateProductOwner\CreateProductOwnerAction;
use App\Modules\Product\Actions\CreateProductOwner\CreateProductOwnerValidation;
use App\Modules\Product\Repository\ProductOwnerRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerController extends Controller
{
    public function index(ProductOwnerRepository $owners): View
    {
        return view('owner::index', ['owners' => ProductOwnerBoViewModel::collection($owners->all()->loadCount('products'))]);
    }

    public function store(Request $request, CreateProductOwnerValidation $validation, CreateProductOwnerAction $action): RedirectResponse
    {
        $action->execute($validation->validate($request->all(), $request->user()));

        return back()->with('status', 'Propriétaire ajouté.');
    }
}
