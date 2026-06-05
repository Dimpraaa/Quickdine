<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtpMail;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login utama (Admin, Staff, Customer).
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Memproses percobaan masuk (authentication).
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // CLAIM: Ambil alih pesanan guest ke akun yang baru login
            $guestOrderIds = session('guest_order_ids', []);
            if (!empty($guestOrderIds)) {
                Order::whereIn('id', $guestOrderIds)
                    ->whereNull('user_id')
                    ->update(['user_id' => Auth::id()]);
                session()->forget('guest_order_ids');
            }

            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email atau Kata Sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan halaman pendaftaran untuk Pelanggan (Customer).
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran user baru khusus sebagai Customer.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Buat akun dengan role 'customer'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        // Langsung login setelah daftar
        Auth::login($user);

        // CLAIM: Ambil alih pesanan guest ke akun baru
        $guestOrderIds = session('guest_order_ids', []);
        if (!empty($guestOrderIds)) {
            Order::whereIn('id', $guestOrderIds)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
            session()->forget('guest_order_ids');
        }

        // Ambil nomor meja dari sesi (disimpan dari MenuController), fallback ke 1 jika kosong
        $tableNumber = session('current_table', 1);
        return redirect()->route('menu.index', ['table_number' => $tableNumber]);
    }

    /**
     * Fungsi pembantu untuk mengalihkan user berdasarkan role.
     */
    private function redirectBasedOnRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->intended('/admin');
        } elseif ($user->role === 'staff') { // Pastikan menggunakan 'staff' sesuai database
            return redirect()->intended('/kitchen');
        }

        // Untuk Customer: Ambil nomor meja dari sesi, kembali ke meja tempat mereka berasal
        $tableNumber = session('current_table', 1);
        return redirect()->intended('/menu/' . $tableNumber);
    }

    /**
     * Proses keluar (logout).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Jika keluar, arahkan kembali ke halaman login utama
        return redirect()->route('login');
    }

    // --- FUNGSI BARU UNTUK SIMULASI LUPA PASSWORD ---

    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function simulateForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email ini tidak ditemukan di sistem kami.'
        ]);

        // Generate 4 digit OTP
        $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Kirim email
        Mail::to($request->email)->send(new ResetPasswordOtpMail($otp));

        // Simpan ke session
        session([
            'reset_email' => $request->email,
            'reset_otp' => $otp,
            'otp_verified' => false
        ]);

        return response()->json(['success' => true]);
    }

    public function showOtpForm()
    {
        if (!session()->has('reset_email') || !session()->has('reset_otp')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi kedaluwarsa. Silakan ulangi proses reset.']);
        }

        return view('auth.verify-otp', [
            'email' => session('reset_email')
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:4'
        ]);

        if ($request->otp === session('reset_otp')) {
            session(['otp_verified' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode OTP salah. Silakan periksa kembali.'
        ], 400);
    }

    public function resendOtp()
    {
        if (!session()->has('reset_email')) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid.'], 400);
        }

        // Generate OTP baru
        $newOtp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        // Kirim email ulang
        Mail::to(session('reset_email'))->send(new ResetPasswordOtpMail($newOtp));

        session([
            'reset_otp' => $newOtp,
            'otp_verified' => false
        ]);

        return response()->json(['success' => true, 'message' => 'OTP berhasil dikirim ulang.']);
    }

    public function resetPasswordForm()
    {
        // Harus lolos verifikasi OTP
        if (!session()->has('reset_email') || session('otp_verified') !== true) {
            return redirect()->route('password.request')->withErrors(['email' => 'Akses ditolak. Anda harus memverifikasi OTP terlebih dahulu.']);
        }

        return view('auth.reset-password', ['email' => session('reset_email')]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed', // Pastikan menggunakan konfirmasi password
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru harus memiliki minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok. Pastikan Anda mengetik ulang password yang sama.'
        ]);

        // Update password di database
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus sesi email reset
        session()->forget('reset_email');

        return redirect()->route('login')->with('success', 'Password berhasil diperbarui! Silakan login dengan password baru.');
    }
}
