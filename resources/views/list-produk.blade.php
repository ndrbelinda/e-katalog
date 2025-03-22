<x-layout>
    {{-- Sidebar --}}
    <div class="flex w-full">
        <aside class="w-1/4">
            <h2 class="text-lg font-bold mb-4">Kategori</h2>
            <ul>
                <li class="mb-2">
                    <a 
                        id="semua" 
                        class="block rounded transition-all duration-200 mr-4 hover:p-2 focus:p-2 hover:bg-gray-700 focus:bg-gray-700" 
                        href="/" 
                        onclick="setActiveLink(event, 'semua', '/')"
                    >
                        Semua
                    </a>
                </li>
                <li>
                    <a 
                        id="css" 
                        class="block rounded transition-all duration-200 mr-4 hover:p-2 focus:p-2 hover:bg-gray-700 focus:bg-gray-700" 
                        href="/css" 
                        onclick="setActiveLink(event, 'css', '/')"
                    >
                        CSS
                    </a>
                </li>
            </ul>
        </aside>

        {{-- Main Content --}}
        <section class="w-full pl-4 border-l border-gray-700">
            {{-- Search Bar --}}
            <form action="{{ url()->current() }}" method="GET" class="mb-4 relative">
                <input 
                    name="search" 
                    class="w-full p-2 pl-4 pr-10 rounded bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200" 
                    placeholder="Cari produk mu disini" 
                    type="text" 
                    value="{{ request('search') }}" 
                />
                <!-- Ikon Pencarian -->
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg 
                        class="w-5 h-5 text-gray-400" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24" 
                        xmlns="http://www.w3.org/2000/s"
                    >
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" 
                        />
                    </svg>
                </div>
            </form>

            {{-- Product List --}}
            <hr class="border-gray-700 mb-4" />
            <h2 class="text-xl font-medium mb-4">Produk ({{ $products->count() }})</h2>
            <div class="space-y-4">
                @foreach ($products as $product)
                    {{-- Product Card --}}
                    <div class="flex items-center rounded mb-8">
                        {{-- Product Image --}}
                        @if ($product->perangkats->isNotEmpty())
                            <img 
                                alt="Product image" 
                                class="w-24 h-24 mr-4 object-cover rounded-sm" 
                                src="{{ $product->perangkats->first()->gambar_perangkat }}" 
                            />
                        @else
                            <div class="w-24 h-24 mr-4 bg-gray-700 flex items-center justify-center">
                                <span class="text-white">No Image</span>
                            </div>
                        @endif

                        {{-- Product Details --}}
                        <div class="flex-1 mr-8">
                            <h3 class="text-2xl font-bold">{{ $product->nama_produk }}</h3>
                            <div class="mt-4">
                                <p class="text-sm font-semibold">Fitur Utama:</p>
                                <p class="text-sm mt-1">{{ $product->deskripsi_produk }}</p>
                            </div>
                        </div>

                        {{-- Buy Button --}}
                        <button 
                            onclick="window.location.href='/detail/{{ $product->id }}'" 
                            class="bg-purple-600 text-white px-4 py-2 rounded"
                        >
                            Beli Sekarang
                        </button>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <script>
        // Fungsi untuk menandai link yang aktif dan mengarahkan ke halaman yang sesuai
        function setActiveLink(event, linkId, targetUrl) {
            // Hentikan event default dari tag <a>
            event.preventDefault();

            // Hapus class 'font-bold' dari semua link
            document.querySelectorAll('aside a').forEach(link => {
                link.classList.remove('font-bold');
            });

            // Tambahkan class 'font-bold' ke link yang diklik
            document.getElementById(linkId).classList.add('font-bold');

            // Arahkan ke halaman yang sesuai
            window.location.href = targetUrl;
        }

        // Set link aktif berdasarkan halaman saat ini (opsional)
        document.addEventListener('DOMContentLoaded', () => {
            const currentUrl = window.location.pathname;

            // Hapus class 'font-bold' dari semua link terlebih dahulu
            document.querySelectorAll('aside a').forEach(link => {
                link.classList.remove('block rounded bg-gray-700');
            });

            // Tentukan link yang aktif berdasarkan URL saat ini
            if (currentUrl === '/') {
                document.getElementById('css').classList.add('block rounded bg-gray-700');
            } else {
                document.getElementById('semua').classList.add('block rounded bg-gray-700');
            }
        });
    </script>
</x-layout>