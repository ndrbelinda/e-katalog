<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Produk;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Method untuk menampilkan halaman list produk (web)
    public function index(Request $request)
    {
        // Ambil parameter pencarian dari request
        $search = $request->query('search');

        // Query untuk mengambil data produk beserta relasinya
        $products = Produk::with(['perangkats' => function ($query) {
            $query->where('is_verified_perangkat', 'diverifikasi')
                ->leftJoin('riwayat_pricing_perangkat', function ($join) {
                    $join->on('perangkats.id', '=', 'riwayat_pricing_perangkat.perangkat_id')
                        ->whereRaw('riwayat_pricing_perangkat.id = (
                            SELECT id FROM riwayat_pricing_perangkat 
                            WHERE perangkat_id = perangkats.id 
                            ORDER BY created_at DESC 
                            LIMIT 1
                        )');
                })
                ->select('perangkats.*', DB::raw('COALESCE(riwayat_pricing_perangkat.pricing, perangkats.tarif_perangkat) as harga_terbaru'))
                ->orderBy('harga_terbaru', 'asc');
        }, 'capacities' => function ($query) {
            $query->where('is_verified_kapasitas', 'diverifikasi')
                ->leftJoin('riwayat_pricing_kapasitas', function ($join) {
                    $join->on('capacities.id', '=', 'riwayat_pricing_kapasitas.kapasitas_id')
                        ->whereRaw('riwayat_pricing_kapasitas.id = (
                            SELECT id FROM riwayat_pricing_kapasitas 
                            WHERE kapasitas_id = capacities.id 
                            ORDER BY created_at DESC 
                            LIMIT 1
                        )');
                })
                ->select('capacities.*', DB::raw('COALESCE(riwayat_pricing_kapasitas.pricing, capacities.tarif_kapasitas) as harga_terbaru'))
                ->orderBy('harga_terbaru', 'asc');
        }, 'faqs']);

        // Jika ada parameter pencarian, filter produk berdasarkan nama
        if ($search) {
            $products->where('nama_produk', 'like', '%' . $search . '%');
        }

        // Ambil data produk
        $products = $products->get();

        // Tampilkan view list-produk dengan data produk
        return view('list-produk', compact('products', 'search'));
    }

    // Method untuk menampilkan halaman detail produk (web)
    public function showDetail($id)
    {
        // Query untuk mengambil detail produk berdasarkan ID
        $product = Produk::with(['perangkats' => function ($query) {
            $query->where('is_verified_perangkat', 'diverifikasi')
                  ->leftJoin('riwayat_pricing_perangkat', function ($join) {
                      $join->on('perangkats.id', '=', 'riwayat_pricing_perangkat.perangkat_id')
                           ->whereRaw('riwayat_pricing_perangkat.id = (
                               SELECT id FROM riwayat_pricing_perangkat 
                               WHERE perangkat_id = perangkats.id 
                               ORDER BY created_at DESC 
                               LIMIT 1
                           )');
                  })
                  ->select('perangkats.*', DB::raw('COALESCE(riwayat_pricing_perangkat.pricing, perangkats.tarif_perangkat) as harga_terbaru'))
                  ->orderBy('harga_terbaru', 'asc');
        }, 'capacities' => function ($query) {
            $query->where('is_verified_kapasitas', 'diverifikasi')
                  ->leftJoin('riwayat_pricing_kapasitas', function ($join) {
                      $join->on('capacities.id', '=', 'riwayat_pricing_kapasitas.kapasitas_id')
                           ->whereRaw('riwayat_pricing_kapasitas.id = (
                               SELECT id FROM riwayat_pricing_kapasitas 
                               WHERE kapasitas_id = capacities.id 
                               ORDER BY created_at DESC 
                               LIMIT 1
                           )');
                  })
                  ->select('capacities.*', DB::raw('COALESCE(riwayat_pricing_kapasitas.pricing, capacities.tarif_kapasitas) as harga_terbaru'))
                  ->orderBy('harga_terbaru', 'asc');
        }, 'faqs'])->find($id);

        // Jika produk tidak ditemukan, redirect ke halaman 404
        if (!$product) {
            abort(404);
        }

        // Tampilkan view detail-produk dengan data produk
        return view('detail-produk', compact('product'));
    }

    // Method API untuk mengambil semua produk (dengan fitur pencarian)
    public function getAllProducts(Request $request)
    {
        // Ambil parameter pencarian
        $search = $request->query('search');

        // Query dasar dengan relasi
        $query = Produk::with(['perangkats' => function ($query) {
            $query->where('is_verified_perangkat', 'diverifikasi')
                ->leftJoin('riwayat_pricing_perangkat', function ($join) {
                    $join->on('perangkats.id', '=', 'riwayat_pricing_perangkat.perangkat_id')
                        ->whereRaw('riwayat_pricing_perangkat.id = (
                            SELECT id FROM riwayat_pricing_perangkat 
                            WHERE perangkat_id = perangkats.id 
                            ORDER BY created_at DESC 
                            LIMIT 1
                        )');
                })
                ->select('perangkats.*', DB::raw('COALESCE(riwayat_pricing_perangkat.pricing, perangkats.tarif_perangkat) as harga_terbaru'))
                ->orderBy('harga_terbaru', 'asc');
        }, 'capacities' => function ($query) {
            $query->where('is_verified_kapasitas', 'diverifikasi')
                ->leftJoin('riwayat_pricing_kapasitas', function ($join) {
                    $join->on('capacities.id', '=', 'riwayat_pricing_kapasitas.kapasitas_id')
                        ->whereRaw('riwayat_pricing_kapasitas.id = (
                            SELECT id FROM riwayat_pricing_kapasitas 
                            WHERE kapasitas_id = capacities.id 
                            ORDER BY created_at DESC 
                            LIMIT 1
                        )');
                })
                ->select('capacities.*', DB::raw('COALESCE(riwayat_pricing_kapasitas.pricing, capacities.tarif_kapasitas) as harga_terbaru'))
                ->orderBy('harga_terbaru', 'asc');
        }, 'faqs']);

        // Tambahkan filter pencarian jika ada
        if ($search) {
            $query->where('nama_produk', 'like', '%' . $search . '%');
        }

        // Eksekusi query
        $products = $query->get();

        // Kembalikan response JSON menggunakan ProductResource
        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
        ]);
    }

    // Method API untuk mengambil detail produk berdasarkan ID
    public function getProductDetail($id)
    {
        // Query untuk mengambil detail produk berdasarkan ID
        $product = Produk::with(['perangkats' => function ($query) {
            $query->where('is_verified_perangkat', 'diverifikasi')
                  ->leftJoin('riwayat_pricing_perangkat', function ($join) {
                      $join->on('perangkats.id', '=', 'riwayat_pricing_perangkat.perangkat_id')
                           ->whereRaw('riwayat_pricing_perangkat.id = (
                               SELECT id FROM riwayat_pricing_perangkat 
                               WHERE perangkat_id = perangkats.id 
                               ORDER BY created_at DESC 
                               LIMIT 1
                           )');
                  })
                  ->select('perangkats.*', DB::raw('COALESCE(riwayat_pricing_perangkat.pricing, perangkats.tarif_perangkat) as harga_terbaru'))
                  ->orderBy('harga_terbaru', 'asc');
        }, 'capacities' => function ($query) {
            $query->where('is_verified_kapasitas', 'diverifikasi')
                  ->leftJoin('riwayat_pricing_kapasitas', function ($join) {
                      $join->on('capacities.id', '=', 'riwayat_pricing_kapasitas.kapasitas_id')
                           ->whereRaw('riwayat_pricing_kapasitas.id = (
                               SELECT id FROM riwayat_pricing_kapasitas 
                               WHERE kapasitas_id = capacities.id 
                               ORDER BY created_at DESC 
                               LIMIT 1
                           )');
                  })
                  ->select('capacities.*', DB::raw('COALESCE(riwayat_pricing_kapasitas.pricing, capacities.tarif_kapasitas) as harga_terbaru'))
                  ->orderBy('harga_terbaru', 'asc');
        }, 'faqs'])->find($id);

        // Jika produk tidak ditemukan, kembalikan response error
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        // Kembalikan response JSON menggunakan ProductResource
        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }
}