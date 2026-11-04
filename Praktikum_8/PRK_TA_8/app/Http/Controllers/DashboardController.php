<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Buku\Models\Buku;
use Modules\Peminjaman\Models\Peminjaman;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with record counts per module.
     */
    public function __invoke(Request $request): View
    {
        return view('dashboard', [
            'totalBuku' => Buku::count(),
            'totalDipinjam' => Peminjaman::where('status', 'Dipinjam')->count(),
            'totalPeminjaman' => Peminjaman::count(),
            'totalUsers' => $request->user()->isAdmin() ? User::count() : null,
        ]);
    }
}
