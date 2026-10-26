<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan form register.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Memvalidasi data, membuat akun baru, lalu langsung login.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // Harus sama dengan password_confirmation
        ]);

        $user = User::create($validated); // Password otomatis di-hash oleh cast 'hashed' pada model User

        Auth::login($user);
        $request->session()->regenerate(); // Mencegah session fixation

        return redirect()->route('kontaks.index')->with('success', 'Registrasi berhasil, selamat datang ' . $user->name . '.');
    }

    /**
     * Menampilkan form login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Memeriksa email dan password, lalu membuat session login.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('kontaks.index'))->with('success', 'Login berhasil.');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    /**
     * Mengakhiri session login.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate(); // Hapus seluruh data session
        $request->session()->regenerateToken(); // Buat token CSRF baru

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}