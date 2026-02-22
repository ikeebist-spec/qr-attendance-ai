<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class EnsureOnlyOneAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // If an admin is already registered, block the register page
        if (User::count() >= 1) {
            return redirect()->route('login')->with('error', 'An admin account already exists. Please log in.');
        }
        return $next($request);
    }
}
