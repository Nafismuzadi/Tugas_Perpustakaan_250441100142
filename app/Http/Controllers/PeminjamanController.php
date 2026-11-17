<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PeminjamanController extends Controller
{
    public function index(): View
    {
        $peminjamans = Peminjaman::with(['user', 'buku'])->latest()->get();

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function store(Buku $buku): RedirectResponse
    {
        Peminjaman::create([
            'user_id' => Auth::id(),
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now(),
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil.');
    }

    // Soal 3: Method show dengan Otorisasi Gate
    public function show(int $id): View
    {
        $peminjaman = Peminjaman::with(['user', 'buku'])->findOrFail($id);

        // Memanggil Gate sentralisasi (Return 403 jika Siswa mengakses peminjaman siswa lain)
        Gate::authorize('view-peminjaman', $peminjaman);

        return view('peminjaman.show', compact('peminjaman'));
    }

    public function riwayatSaya(): View
    {
        $peminjamans = Peminjaman::where('user_id', Auth::id())->with('buku')->latest()->get();

        return view('peminjaman.riwayat', compact('peminjamans'));
    }

    public function setujui(int $id): RedirectResponse
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update(['status' => 'dipinjam']);

        return redirect()->back()->with('success', 'Peminjaman disetujui.');
    }

    public function tolak(int $id): RedirectResponse
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update(['status' => 'ditolak']);

        return redirect()->back()->with('success', 'Peminjaman ditolak.');
    }

    public function kembalikan(int $id): RedirectResponse
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_kembali' => now(),
        ]);

        return redirect()->back()->with('success', 'Buku telah dikembalikan.');
    }
}
