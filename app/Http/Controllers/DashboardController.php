<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return match (true) {
            $user->isAdmin() => view('dashboard.admin'),
            $user->isSarpras() => view('dashboard.sarpras'),
            default => view('dashboard.unit'),
        };
    }
}
