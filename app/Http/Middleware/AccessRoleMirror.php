<?php

namespace App\Http\Middleware;

use App\Models\Access;
use Closure;
use Illuminate\Http\Request;

class AccessRoleMirror
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user instanceof Access) {
            return response()->json(['ok' => false, 'error' => 'Wrong object type'], 403);
        }

        if ($user->role !== 'mirror') {
            return response()->json(['ok' => false, 'error' => 'Wrong access role'], 403);
        }

        return $next($request);
    }
}
