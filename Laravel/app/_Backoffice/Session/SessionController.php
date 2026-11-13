<?php

namespace App\_Backoffice\Session;

use App\Modules\Product\ViewModels\BackOffice\ProductAuctionSessionBoViewModel;
use App\Http\Controllers\Controller;
use App\Modules\Product\Actions\CreateAuctionSession\CreateAuctionSessionAction;
use App\Modules\Product\Actions\CreateAuctionSession\CreateAuctionSessionValidation;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(ProductAuctionSessionRepository $sessions): View
    {
        return view('session::index', ['sessions' => ProductAuctionSessionBoViewModel::collection($sessions->all())]);
    }

    public function store(Request $request, CreateAuctionSessionValidation $validation, CreateAuctionSessionAction $action): RedirectResponse
    {
        $action->execute($validation->validate($request->all(), $request->user()));

        return back()->with('status', 'Session créée.');
    }
}
