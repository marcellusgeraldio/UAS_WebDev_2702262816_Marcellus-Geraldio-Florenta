<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use Illuminate\Validation\ValidationException;

class MahasiswaController extends Controller
{
    // 1. GET LIST (Pagination, Search, Filter, Sort)
    public function index(Request $request)
    {
        $query = Mahasiswa::query();

        // A. PENCARIAN (Search q = nama/nim/email)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // B. FILTERING
        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        // C. SORTING (Default: created_at desc)
        $sortBy = $request->input('sortBy', 'created_at');
        $sortDir = $request->input('sortDir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // D. PAGINATION (Per page 10)
        return response()->json($query->paginate(10));
    }

    // 2. CREATE (POST)
    public function store(Request $request)
    {
        // Validasi Input
        $validated = $request->validate([
            'nim' => 'required|unique:mahasiswas',
            'nama' => 'required',
            'email' => 'required|email|unique:mahasiswas',
            'prodi' => 'required',
            'angkatan' => 'required|numeric|digits:4',
            'status' => 'required|in:aktif,cuti,lulus,dropout'
        ]);

        $mhs = Mahasiswa::create($validated);

        return response()->json([
            'message' => 'Data berhasil ditambahkan',
            'data' => $mhs
        ], 201);
    }

    // 3. SHOW DETAIL (GET /{id})
    public function show($id)
    {
        $mhs = Mahasiswa::find($id);
        if (!$mhs) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        return response()->json($mhs);
    }

    // 4. UPDATE (PUT/PATCH /{id})
    public function update(Request $request, $id)
    {
        $mhs = Mahasiswa::find($id);
        if (!$mhs) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $validated = $request->validate([
            'nim' => 'required|unique:mahasiswas,nim,'.$id, // Ignore current ID
            'nama' => 'required',
            'email' => 'required|email|unique:mahasiswas,email,'.$id,
            'prodi' => 'required',
            'angkatan' => 'required|numeric|digits:4',
            'status' => 'required|in:aktif,cuti,lulus,dropout'
        ]);

        $mhs->update($validated);

        return response()->json([
            'message' => 'Data berhasil diupdate',
            'data' => $mhs
        ]);
    }

    // 5. DELETE (Soft Delete)
    public function destroy($id)
    {
        $mhs = Mahasiswa::find($id);
        if (!$mhs) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $mhs->delete(); // Soft delete

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}