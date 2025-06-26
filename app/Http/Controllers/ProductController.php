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
        $search = $request->query('search');

        $products = Produk::with([
            'perangkats' => function ($query) {
                $query->where('is_verified_perangkat', 'diverifikasi')
                    ->where('tampil_ekatalog', true) // Filter tambahan
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
            }, 
            'capacities' => function ($query) {
                $query->where('is_verified_kapasitas', 'diverifikasi')
                    ->where('tampil_ekatalog', true) // Filter tambahan
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
            }, 
            'faqs' => function ($query) {
                $query->where('tampil_ekatalog', true); // Filter untuk FAQ
            }
        ]);

        if ($search) {
            $products->where('nama_produk', 'like', '%' . $search . '%');
        }

        $products = $products->get();

        return view('list-produk', compact('products', 'search'));
    }

    // Method untuk menampilkan halaman detail produk (web)
    public function showDetail($id)
    {
        $product = Produk::with([
            'perangkats' => function ($query) {
                $query->where('is_verified_perangkat', 'diverifikasi')
                    ->where('tampil_ekatalog', true)
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
            }, 
            'capacities' => function ($query) {
                $query->where('is_verified_kapasitas', 'diverifikasi')
                    ->where('tampil_ekatalog', true)
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
            }, 
            'faqs' => function ($query) {
                $query->where('tampil_ekatalog', true);
            }
        ])->find($id);

        if (!$product) {
            abort(404);
        }

        return view('detail-produk', compact('product'));
    }

    // Method API untuk mengambil semua produk
    public function getAllProducts(Request $request)
    {
        $search = $request->query('search');

        $query = Produk::with([
            'perangkats' => function ($query) {
                $query->where('is_verified_perangkat', 'diverifikasi')
                    ->where('tampil_ekatalog', true)
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
            }, 
            'capacities' => function ($query) {
                $query->where('is_verified_kapasitas', 'diverifikasi')
                    ->where('tampil_ekatalog', true)
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
            }, 
            'faqs' => function ($query) {
                $query->where('tampil_ekatalog', true);
            }
        ]);

        if ($search) {
            $query->where('nama_produk', 'like', '%' . $search . '%');
        }

        $products = $query->get();

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
        ]);
    }

    // Method API untuk mengambil detail produk berdasarkan ID
    public function getProductDetail($id)
    {
        $product = Produk::with([
            'perangkats' => function ($query) {
                $query->where('is_verified_perangkat', 'diverifikasi')
                    ->where('tampil_ekatalog', true)
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
            }, 
            'capacities' => function ($query) {
                $query->where('is_verified_kapasitas', 'diverifikasi')
                    ->where('tampil_ekatalog', true)
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
            }, 
            'faqs' => function ($query) {
                $query->where('tampil_ekatalog', true);
            }
        ])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }
}