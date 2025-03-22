<x-layout>
    <div class="flex flex-col space-y-6 w-full">  
        {{-- Path --}}
        <div class="flex items-center space-x-2 text-gray-400">
            <i onclick="window.location.href='/'" class="fas fa-arrow-left"></i>
            <span>Produk</span>
            <span>/</span>
            <span>CSS</span>
            <span>/</span>
            <span class="text-blue-500">{{ $product->nama_produk }}</span>
        </div>

        {{-- Product Detail --}}
        <div class="flex flex-col lg:flex-row mt-8">
            {{-- Product Image --}}
            <div class="w-full lg:w-auto flex justify-center lg:justify-start">
                @if ($product->perangkats->isNotEmpty())
                    <img 
                        id="productImage"
                        alt="Product image" 
                        class="w-[380px] h-[380px] object-cover rounded-lg" 
                        src="{{ $product->perangkats->first()->gambar_perangkat }}" 
                    />
                @else
                    <div class="w-[380px] h-[380px] bg-gray-700 flex items-center justify-center">
                        <span class="text-white">No Image</span>
                    </div>
                @endif
            </div>

            {{-- Detail Varian --}}
            <div class="w-full lg:w-auto mt-8 lg:mt-0 lg:ml-[60px]">
                <h1 class="text-3xl font-bold mb-4">{{ $product->nama_produk }}</h1>
                <p class="text-sm mb-8">{{ $product->deskripsi_produk }}</p>
                {{-- Varian Perangkat --}}
                <div class="mt-4">
                    <h2 class="text-xl font-bold">Pilih Perangkat</h2>
                    <div class="flex space-x-4 mt-2">
                        @if ($product->perangkats->isNotEmpty())
                            @foreach ($product->perangkats as $perangkat)
                                <button 
                                    class="bg-gray-700 text-white py-2 px-4 rounded"
                                    onclick="selectDevice({{ $perangkat->id }}, {{ $perangkat->harga_terbaru }}, this, '{{ $perangkat->gambar_perangkat }}')"
                                    data-image="{{ $perangkat->gambar_perangkat }}"
                                >
                                    {{ $perangkat->jenis_perangkat }} <br/> 
                                    Rp {{ number_format($perangkat->harga_terbaru, 0, ',', '.') }}
                                </button>
                            @endforeach
                        @else
                            <p class="text-gray-400">Tidak ada perangkat tersedia.</p>
                        @endif
                    </div>
                </div>

                {{-- Varian Kapasitas --}}
                <div class="mt-4">
                    <h2 class="text-xl font-bold">Pilih Kapasitas</h2>
                    <div class="flex space-x-4 mt-2">
                        @if ($product->capacities->isNotEmpty())
                            @foreach ($product->capacities as $capacity)
                                <button 
                                    class="bg-gray-700 text-white py-2 px-4 rounded"
                                    onclick="selectCapacity({{ $capacity->id }}, {{ $capacity->harga_terbaru }}, this)"
                                >
                                    {{ $capacity->besar_kapasitas }} GB <br/> 
                                    Rp {{ number_format($capacity->harga_terbaru, 0, ',', '.') }}
                                </button>
                            @endforeach
                        @else
                            <p class="text-gray-400">Tidak ada kapasitas tersedia.</p>
                        @endif
                    </div>
                </div>

                {{-- Button --}}
                <div class="mt-4 flex space-x-4 mt-12">
                    <button class="bg-purple-700 text-white py-2 px-4 rounded"> Hubungi AM </button>
                    <button id="continuePayment" class="bg-gray-500 text-white py-2 px-4 rounded" disabled>
                        Lanjutkan Pembayaran (Rp 0)
                    </button>
                </div>
            </div>
        </div>

        {{-- FAQ --}}
        <section class="mt-12 w-full">
            <h2 class="text-2xl font-bold">Frequently Asked Questions</h2>
            <div class="mt-4">
                @if ($product->faqs->isNotEmpty())
                    @foreach ($product->faqs as $faq)
                        <div class="py-2">
                            <div class="flex justify-between items-center cursor-pointer" onclick="toggleFAQAnswer({{ $faq->id }})">
                                <span>{{ $faq->pertanyaan }}</span>
                                <i id="faq-icon-{{ $faq->id }}" class="fas fa-plus"></i>
                            </div>
                            <div id="faq-answer-{{ $faq->id }}" class="hidden mt-2 text-white"> <!-- Warna jawaban putih -->
                                {{ $faq->jawaban }}
                            </div>
                            <div class="border-b border-gray-700 mt-2"></div> <!-- Garis pemisah di bawah jawaban -->
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-400">Tidak ada FAQ tersedia.</p>
                @endif
            </div>
        </section>

        <script>
            let selectedDevicePrice = 0;
            let selectedCapacityPrice = 0;
            let selectedDeviceButton = null; // Untuk menyimpan tombol perangkat yang aktif
            let selectedCapacityButton = null; // Untuk menyimpan tombol kapasitas yang aktif

            // Fungsi untuk memilih perangkat
            function selectDevice(deviceId, price, button, imageUrl) {
                // Hapus class aktif dari tombol perangkat sebelumnya (jika ada)
                if (selectedDeviceButton) {
                    selectedDeviceButton.classList.remove('border-purple-700', 'text-purple-700', 'border-2', 'bg-gray-900');
                    selectedDeviceButton.classList.add('bg-gray-700', 'text-white');
                }

                // Jika tombol yang sama diklik lagi, kembalikan ke tampilan semula
                if (selectedDeviceButton === button) {
                    selectedDeviceButton = null;
                    selectedDevicePrice = 0;
                } else {
                    // Tambahkan class aktif ke tombol yang diklik
                    button.classList.remove('bg-gray-700', 'text-white');
                    button.classList.add('border-purple-500', 'text-purple-500', 'border-2', 'bg-gray-900');
                    selectedDeviceButton = button;
                    selectedDevicePrice = price;

                    // Update gambar produk
                    document.getElementById('productImage').src = imageUrl;
                }

                updatePaymentButton();
            }

            // Fungsi untuk memilih kapasitas
            function selectCapacity(capacityId, price, button) {
                // Hapus class aktif dari tombol kapasitas sebelumnya (jika ada)
                if (selectedCapacityButton) {
                    selectedCapacityButton.classList.remove('border-purple-700', 'text-purple-700', 'border-2', 'bg-gray-900');
                    selectedCapacityButton.classList.add('bg-gray-700', 'text-white');
                }

                // Jika tombol yang sama diklik lagi, kembalikan ke tampilan semula
                if (selectedCapacityButton === button) {
                    selectedCapacityButton = null;
                    selectedCapacityPrice = 0;
                } else {
                    // Tambahkan class aktif ke tombol yang diklik
                    button.classList.remove('bg-gray-700', 'text-white');
                    button.classList.add('border-purple-500', 'text-purple-500', 'border-2', 'bg-gray-900');
                    selectedCapacityButton = button;
                    selectedCapacityPrice = price;
                }

                updatePaymentButton();
            }

            // Fungsi untuk mengupdate tombol pembayaran
            function updatePaymentButton() {
                const totalPrice = selectedDevicePrice + selectedCapacityPrice;
                const continuePaymentButton = document.getElementById('continuePayment');

                // Tombol hanya aktif jika kedua harga (perangkat dan kapasitas) dipilih
                if (selectedDevicePrice > 0 && selectedCapacityPrice > 0) {
                    continuePaymentButton.disabled = false;
                    continuePaymentButton.classList.remove('bg-gray-500');
                    continuePaymentButton.classList.add('bg-blue-500');
                    continuePaymentButton.textContent = `Lanjutkan Pembayaran (Rp ${totalPrice.toLocaleString()})`;
                } else {
                    continuePaymentButton.disabled = true;
                    continuePaymentButton.classList.remove('bg-blue-500');
                    continuePaymentButton.classList.add('bg-gray-500');
                    continuePaymentButton.textContent = 'Lanjutkan Pembayaran (Rp 0)';
                }
            }

            // Fungsi untuk menampilkan/sembunyikan jawaban FAQ
            function toggleFAQAnswer(faqId) {
                const answerElement = document.getElementById(`faq-answer-${faqId}`);
                const iconElement = document.getElementById(`faq-icon-${faqId}`);

                if (answerElement.classList.contains('hidden')) {
                    answerElement.classList.remove('hidden');
                    iconElement.classList.remove('fa-plus');
                    iconElement.classList.add('fa-minus');
                } else {
                    answerElement.classList.add('hidden');
                    iconElement.classList.remove('fa-minus');
                    iconElement.classList.add('fa-plus');
                }
            }
        </script>
    </div>
</x-layout>