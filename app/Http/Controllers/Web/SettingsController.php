<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        $business = auth()->user()->business;
        $users = User::where('business_id', $business->id)->get(['id', 'name', 'email', 'role', 'is_active']);

        return Inertia::render('Settings/Index', [
            'business' => $business,
            'users'    => $users,
        ]);
    }
}
