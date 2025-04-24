<x-layout>
    {{-- Sidebar --}}
    <div class="flex w-full">
        <aside class="w-1/4">
            <h2 class="text-lg font-bold mb-4">Kategori</h2>
            <ul>
                <li class="mb-2">
                    <a 
                        id="semua" 
                        class="block rounded transition-all duration-200 mr-4 hover:p-2 focus:p-2 hover:bg-gray-100 focus:bg-gray-100" 
                        href="/" 
                        onclick="setActiveLink(event, 'semua', '/')"
                    >
                        Semua
                    </a>
                </li>
                <li>
                    <a 
                        id="css" 
                        class="block rounded transition-all duration-200 mr-4 hover:p-2 focus:p-2 hover:bg-gray-100 focus:bg-gray-100" 
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
            <form id="searchForm" class="mb-4 relative">
                <input 
                    id="searchInput"
                    name="search" 
                    class="w-full p-2 pl-4 pr-10 rounded bg-gray-100 text-[#252525] placeholder-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-300" 
                    placeholder="Cari produk mu disini" 
                    type="text"
                    value="{{ request('search') ?? '' }}" 
                />
                <!-- Search Icon -->
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/s">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>

            {{-- Product List --}}
            <hr class="border-gray-700 mb-4" />
            <h2 class="text-xl font-medium mb-4">Produk (<span id="productCount">0</span>)</h2>
            <div id="productList" class="space-y-4">
                <!-- Products will be loaded here via JavaScript -->
            </div>

            {{-- Loading Indicator --}}
            <div id="loadingIndicator" class="text-center py-8 hidden">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500 mx-auto"></div>
                <p class="mt-2 text-gray-400">Memuat produk...</p>
            </div>
        </section>
    </div>

    <script>
        // Fungsi untuk mengambil data produk dari API
        async function fetchProducts(search = '') {
            try {
                // Show loading indicator
                document.getElementById('loadingIndicator').classList.remove('hidden');
                document.getElementById('productList').classList.add('opacity-50');
                
                const response = await fetch(`/api/products?search=${encodeURIComponent(search)}`);
                const data = await response.json();

                if (data.success) {
                    renderProducts(data.data);
                } else {
                    console.error('Gagal mengambil data produk');
                    showEmptyState();
                }
            } catch (error) {
                console.error('Error:', error);
                showEmptyState();
            } finally {
                // Hide loading indicator
                document.getElementById('loadingIndicator').classList.add('hidden');
                document.getElementById('productList').classList.remove('opacity-50');
            }
        }

        // Fungsi untuk menampilkan data produk
        function renderProducts(products) {
            const productList = document.getElementById('productList');
            const productCount = document.getElementById('productCount');

            // Kosongkan daftar produk
            productList.innerHTML = '';

            // Update jumlah produk
            productCount.textContent = products.length;

            if (products.length === 0) {
                showEmptyState();
                return;
            }

            // Loop melalui setiap produk dan buat card
            products.forEach(product => {
                // Ambil gambar pertama dari perangkat (jika ada)
                const productImage = product.devices && product.devices.length > 0 
                    ? product.devices[0].deviceImage 
                    : null;

                const productCard = `
                    <div class="flex items-center rounded mb-8 hover:bg-gray-750 transition-colors">
                        ${productImage ? `
                            <img 
                                alt="Product image" 
                                class="w-24 h-24 mr-4 object-cover rounded-sm"
                                src="${productImage}" 
                                onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=No+Image';"
                            />
                        ` : `
                            <div class="w-24 h-24 mr-4 bg-gray-700 flex items-center justify-center rounded-sm">
                                <span class="text-white text-xs">No Image</span>
                            </div>
                        `}

                        <div class="flex-1 mr-8">
                            <h3 class="text-2xl font-bold text-[#252525]">${product.productName}</h3>
                            <div class="mt-2">
                                <p class="text-sm font-semibold text-gray-800">Fitur Utama:</p>
                                <p class="text-sm mt-1 text-gray-600">${product.productDescription}</p>
                            </div>
                        </div>

                        <button 
                            onclick="window.location.href='/detail/${product.productId}'" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded transition-colors"
                        >
                            Beli Sekarang
                        </button>
                    </div>
                `;

                productList.innerHTML += productCard;
            });
        }

        // Fungsi untuk menampilkan state ketika tidak ada produk
        function showEmptyState() {
            const productList = document.getElementById('productList');
            productList.innerHTML = `
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-200">Produk tidak ditemukan</h3>
                    <p class="mt-1 text-gray-400">Coba kata kunci pencarian yang berbeda</p>
                </div>
            `;
        }

        // Event listener untuk form pencarian
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const searchQuery = document.getElementById('searchInput').value.trim();
            fetchProducts(searchQuery);
        });

        // Ambil data produk saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            // Ambil nilai search dari URL jika ada
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('search') || '';
            
            // Set nilai input search
            document.getElementById('searchInput').value = searchQuery;
            
            // Load products
            fetchProducts(searchQuery);
        });

        // Fungsi untuk menandai link yang aktif
        function setActiveLink(event, linkId, targetUrl) {
            event.preventDefault();
            document.querySelectorAll('aside a').forEach(link => {
                link.classList.remove('font-bold', 'bg-gray-700');
            });
            document.getElementById(linkId).classList.add('font-bold', 'bg-gray-700');
            window.location.href = targetUrl + (document.getElementById('searchInput').value ? `?search=${encodeURIComponent(document.getElementById('searchInput').value)}` : '');
        }
    </script>
</x-layout>