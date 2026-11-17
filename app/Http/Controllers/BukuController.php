<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index()
    {
        $bukus = Buku::with('kategori')->latest()->get();

        return view('buku.index', compact('bukus'));
    }

    public function create()
    {
        $kategori = Kategori::all();

        return view('buku.create', compact('kategori'));
    }

    // Soal 4: Validasi Input Ketat pada Penambahan Buku
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validatedBookData($request);

        // Menyimpan hanya data yang sudah terverifikasi (Mencegah Mass Assignment)
        Buku::create($validatedData);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show($id)
    {
        $buku = Buku::with('kategori')->findOrFail($id);

        return view('buku.show', compact('buku'));
    }

    public function edit($id)
    {
        $buku = Buku::findOrFail($id);
        $kategori = Kategori::all();

        return view('buku.edit', compact('buku', 'kategori'));
    }

    // Soal 4: Validasi Input Ketat pada Perubahan Buku
    public function update(Request $request, int $id): RedirectResponse
    {
        $buku = Buku::findOrFail($id);
        $validatedData = $this->validatedBookData($request);

        $buku->update($validatedData);

        return redirect()->route('buku.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }

    /**
     * @return array{judul: string, penulis: string, kategori_id: int|string, tahun_terbit: int|string}
     */
    private function validatedBookData(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'integer', 'exists:kategoris,id'],
            'tahun_terbit' => ['required', 'integer', 'min:0', 'max:9999'],
        ], [
            'judul.required' => 'Judul buku wajib diisi.',
            'penulis.required' => 'Nama penulis wajib diisi.',
            'kategori_id.required' => 'Kategori buku wajib dipilih.',
            'kategori_id.integer' => 'Kategori yang dipilih tidak valid.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka bulat.',
            'tahun_terbit.min' => 'Tahun terbit tidak boleh kurang dari 0.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh lebih dari 9999.',
        ]);
    }
}
