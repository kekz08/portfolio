<?php

namespace App\Http\Controllers;

use App\Models\HomeContent;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Home', [
            'content' => HomeContent::resolvedContent(),
        ]);
    }

    public function blogs(): RedirectResponse
    {
        return redirect()->to(route('home').'#blogs');
    }

    public function blogDetails(): RedirectResponse
    {
        return redirect()->to(route('home').'#blogs');
    }
}
