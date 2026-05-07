<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::where('business_id', auth()->user()->business_id)
            ->latest()
            ->get(['id', 'name', 'email', 'role', 'is_active', 'last_login_at']);

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }
}
