<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WilayahAdministratif;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WilayahController extends Controller
{
    /**
     * Kolom eksplisit yang diambil dari database.
     *
     * @var array<int, string>
     */
    protected array $selectColumns = [
        'id',
        'kode_wilayah',
        'nama',
        'tingkat',
        'status_batas',
    ];

    /**
     * Format entitas wilayah ke bentuk representasi response API.
     *
     * @param  WilayahAdministratif  $wilayah
     * @return array<string, mixed>
     */
    protected function formatWilayah(WilayahAdministratif $wilayah): array
    {
        return [
            'id' => (int) $wilayah->id,
            'kode_wilayah' => (string) $wilayah->kode_wilayah,
            'kode' => (string) $wilayah->kode_wilayah,
            'nama' => (string) $wilayah->nama,
            'tingkat' => (string) $wilayah->tingkat,
            'status_batas' => $wilayah->status_batas !== null ? (string) $wilayah->status_batas : null,
        ];
    }

    /**
     * Mengambil daftar seluruh provinsi.
     *
     * GET /api/wilayah/provinsi
     */
    public function provinsi(): JsonResponse
    {
        $provinsi = WilayahAdministratif::query()
            ->select($this->selectColumns)
            ->where('tingkat', 'provinsi')
            ->orderBy('kode_wilayah')
            ->get();

        $data = $provinsi->map(fn (WilayahAdministratif $item) => $this->formatWilayah($item))->values();

        return response()->json([
            'success' => true,
            'message' => 'Data wilayah berhasil diambil.',
            'data' => $data,
        ], 200);
    }

    /**
     * Mengambil daftar kabupaten/kota berdasarkan ID provinsi parent.
     *
     * GET /api/wilayah/{id}/kabupaten
     */
    public function kabupaten(mixed $id): JsonResponse
    {
        if (!is_numeric($id) || (int) $id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ], 404);
        }

        $parent = WilayahAdministratif::find($id);

        if (!$parent) {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ], 404);
        }

        if ($parent->tingkat !== 'provinsi') {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah parent harus bertingkat provinsi.',
            ], 422);
        }

        $kabupaten = WilayahAdministratif::query()
            ->select($this->selectColumns)
            ->where('parent_id', $parent->id)
            ->where('tingkat', 'kabupaten')
            ->orderBy('kode_wilayah')
            ->get();

        $data = $kabupaten->map(fn (WilayahAdministratif $item) => $this->formatWilayah($item))->values();

        return response()->json([
            'success' => true,
            'message' => 'Data wilayah berhasil diambil.',
            'data' => $data,
        ], 200);
    }

    /**
     * Mengambil daftar kecamatan berdasarkan ID kabupaten/kota parent.
     *
     * GET /api/wilayah/{id}/kecamatan
     */
    public function kecamatan(mixed $id): JsonResponse
    {
        if (!is_numeric($id) || (int) $id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ], 404);
        }

        $parent = WilayahAdministratif::find($id);

        if (!$parent) {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ], 404);
        }

        if ($parent->tingkat !== 'kabupaten') {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah parent harus bertingkat kabupaten.',
            ], 422);
        }

        $kecamatan = WilayahAdministratif::query()
            ->select($this->selectColumns)
            ->where('parent_id', $parent->id)
            ->where('tingkat', 'kecamatan')
            ->orderBy('kode_wilayah')
            ->get();

        $data = $kecamatan->map(fn (WilayahAdministratif $item) => $this->formatWilayah($item))->values();

        return response()->json([
            'success' => true,
            'message' => 'Data wilayah berhasil diambil.',
            'data' => $data,
        ], 200);
    }

    /**
     * Mengambil daftar desa/kelurahan berdasarkan ID kecamatan parent dengan pagination.
     *
     * GET /api/wilayah/{id}/desa
     */
    public function desa(Request $request, mixed $id): JsonResponse
    {
        if (!is_numeric($id) || (int) $id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ], 404);
        }

        $parent = WilayahAdministratif::find($id);

        if (!$parent) {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ], 404);
        }

        if ($parent->tingkat !== 'kecamatan') {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah parent harus bertingkat kecamatan.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ], [
            'page.integer' => 'Parameter page harus berupa bilangan bulat.',
            'page.min' => 'Parameter page minimal bernilai 1.',
            'per_page.integer' => 'Parameter per_page harus berupa bilangan bulat.',
            'per_page.min' => 'Parameter per_page minimal bernilai 1.',
            'per_page.max' => 'Parameter per_page tidak boleh lebih dari 100.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter pagination tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $perPage = $request->filled('per_page') ? (int) $request->input('per_page') : 50;
        $page = $request->filled('page') ? (int) $request->input('page') : 1;

        $paginator = WilayahAdministratif::query()
            ->select($this->selectColumns)
            ->where('parent_id', $parent->id)
            ->where('tingkat', 'desa')
            ->orderBy('kode_wilayah')
            ->paginate(
                perPage: $perPage,
                columns: $this->selectColumns,
                pageName: 'page',
                page: $page
            );

        $items = collect($paginator->items())
            ->map(fn (WilayahAdministratif $item) => $this->formatWilayah($item))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Data wilayah berhasil diambil.',
            'data' => $items,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ], 200);
    }
}
