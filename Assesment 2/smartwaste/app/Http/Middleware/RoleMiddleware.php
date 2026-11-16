<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Membatasi akses route berdasarkan role user.
     *
     * Contoh pemakaian: Route::middleware('role:admin,petugas')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Belum login -> lempar ke halaman login
        if (! $user) {
            return redirect()->route('login');
        }

        // Role tidak sesuai -> 403 Forbidden (ditangani errors/403.blade.php)
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Role "' . $user->role . '" tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
