<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Buku\Models\Buku;
use Modules\Peminjaman\Models\Peminjaman;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with record counts per module.
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('dashboard', [
            'totalBuku' => Buku::count(),
            'totalPeminjaman' => Peminjaman::count(),
            'totalDipinjam' => Peminjaman::where('status', 'Dipinjam')->count(),
            'totalUsers' => $request->user()?->isAdmin() ? User::count() : null,
        ]);
    }
}
