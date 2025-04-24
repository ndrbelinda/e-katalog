<x-layout>
    <div class="flex flex-col space-y-6 w-full">  
        {{-- Path --}}
        <div class="flex items-center space-x-2 text-gray-400">
            <i onclick="window.location.href='/'" class="fas fa-arrow-left"></i>
            <span>Produk</span>
            <span>/</span>
            <span>CSS</span>
            <span>/</span>
            <span class="text-blue-500" id="productName"></span>
        </div>

        {{-- Product Detail --}}
        <div class="flex flex-col lg:flex-row mt-8">
            {{-- Product Image --}}
            <div class="w-full lg:w-auto flex justify-center lg:justify-start">
                <img 
                    id="productImage"
                    alt="Product image" 
                    class="w-[380px] h-[380px] object-cover rounded-lg" 
                    src="" 
                    onerror="this.onerror=null; this.src='https://via.placeholder.com/380';"
                />
            </div>

            {{-- Detail Varian --}}
            <div class="w-full lg:w-auto mt-8 lg:mt-0 lg:ml-[60px]">
                <h1 class="text-3xl font-bold mb-4" id="productNameTitle"></h1>
                <p class="text-sm mb-8" id="productDescription"></p>

                {{-- Varian Perangkat --}}
                <div class="mt-4">
                    <h2 class="text-xl font-bold">Pilih Perangkat</h2>
                    <div id="deviceList" class="flex space-x-4 mt-2">
                        <!-- Data perangkat akan dimuat di sini menggunakan JavaScript -->
                    </div>
                </div>

                {{-- Varian Kapasitas --}}
                <div class="mt-4">
                    <h2 class="text-xl font-bold">Pilih Kapasitas</h2>
                    <div id="capacityList" class="flex space-x-4 mt-2">
                        <!-- Data kapasitas akan dimuat di sini menggunakan JavaScript -->
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
            <div id="faqList" class="mt-4">
                <!-- Data FAQ akan dimuat di sini menggunakan JavaScript -->
            </div>
        </section>

        <script>
            let selectedDevicePrice = 0;
            let selectedCapacityPrice = 0;
            let selectedDeviceButton = null;
            let selectedCapacityButton = null;

            // Fungsi untuk mengambil detail produk dari API
            async function fetchProductDetail(productId) {
                try {
                    const response = await fetch(`/api/products/${productId}`);
                    const data = await response.json();

                    if (data.success) {
                        renderProductDetail(data.data);
                    } else {
                        console.error('Gagal mengambil detail produk');
                    }
                } catch (error) {
                    console.error('Terjadi kesalahan:', error);
                }
            }

            // Fungsi untuk menampilkan detail produk
            function renderProductDetail(product) {
                // Tampilkan nama dan deskripsi produk
                document.getElementById('productName').textContent = product.productName;
                document.getElementById('productNameTitle').textContent = product.productName;
                document.getElementById('productDescription').textContent = product.productDescription;

                // Ambil gambar pertama dari perangkat (jika ada)
                const productImage = product.devices.length > 0 ? product.devices[0].deviceImage : null;

                // Set gambar produk
                const productImageElement = document.getElementById('productImage');
                if (productImage) {
                    productImageElement.src = productImage;
                    productImageElement.onerror = function() {
                        this.src = 'https://via.placeholder.com/380'; // Gambar placeholder jika gambar gagal dimuat
                    };
                } else {
                    productImageElement.src = 'https://via.placeholder.com/380'; // Gambar placeholder jika tidak ada gambar
                }

                // Render perangkat (jika ada)
                const deviceList = document.getElementById('deviceList');
                if (product.devices && product.devices.length > 0) {
                    deviceList.innerHTML = product.devices.map(device => `
                        <button 
                            class="bg-gray-100 text-[#252525] py-2 px-4 rounded"
                            onclick="selectDevice(${device.deviceId}, ${device.price}, this, '${device.deviceImage}')"
                            data-image="${device.deviceImage}"
                        >
                            ${device.deviceName} <br/> 
                            Rp ${parseFloat(device.price).toLocaleString()}
                        </button>
                    `).join('');
                } else {
                    deviceList.innerHTML = '<p class="text-gray-400">Tidak ada perangkat tersedia.</p>';
                }

                // Render kapasitas (jika ada)
                const capacityList = document.getElementById('capacityList');
                if (product.capacities && product.capacities.length > 0) {
                    capacityList.innerHTML = product.capacities.map(capacity => `
                        <button 
                            class="bg-gray-100 text-[#252525] py-2 px-4 rounded"
                            onclick="selectCapacity(${capacity.capacityId}, ${capacity.price}, this)"
                        >
                            ${capacity.capacitySize} GB <br/> 
                            Rp ${parseFloat(capacity.price).toLocaleString()}
                        </button>
                    `).join('');
                } else {
                    capacityList.innerHTML = '<p class="text-gray-400">Tidak ada kapasitas tersedia.</p>';
                }

                // Render FAQ (jika ada)
                const faqList = document.getElementById('faqList');
                if (product.faqs && product.faqs.length > 0) {
                    faqList.innerHTML = product.faqs.map(faq => `
                        <div class="py-2">
                            <div class="flex justify-between items-center cursor-pointer" onclick="toggleFAQAnswer(${faq.faqId})">
                                <span>${faq.question}</span>
                                <i id="faq-icon-${faq.faqId}" class="fas fa-plus"></i>
                            </div>
                            <div id="faq-answer-${faq.faqId}" class="hidden mt-2 text-[#252525]">
                                ${faq.answer}
                            </div>
                            <div class="border-b border-gray-700 mt-2"></div>
                        </div>
                    `).join('');
                } else {
                    faqList.innerHTML = '<p class="text-gray-400">Tidak ada FAQ tersedia.</p>';
                }
            }

            // Fungsi untuk memilih perangkat
            function selectDevice(deviceId, price, button, imageUrl) {
                // Hapus class aktif dari tombol perangkat sebelumnya (jika ada)
                if (selectedDeviceButton) {
                    selectedDeviceButton.classList.remove('border-gray-100', 'text-purple-500', 'border-2', 'bg-gray-100');
                    selectedDeviceButton.classList.add('bg-gray-100', 'text-[#252525]');
                }

                // Jika tombol yang sama diklik lagi, kembalikan ke tampilan semula
                if (selectedDeviceButton === button) {
                    selectedDeviceButton = null;
                    selectedDevicePrice = 0;
                } else {
                    // Tambahkan class aktif ke tombol yang diklik
                    button.classList.remove('bg-gray-700', 'text-white');
                    button.classList.add('border-purple-500', 'text-purple-500', 'border-2', 'bg-gray-100');
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
                    selectedCapacityButton.classList.remove('border-purple-500', 'text-purple-500', 'border-2', 'bg-gray-100');
                    selectedCapacityButton.classList.add('bg-gray-100', 'text-[#252525]');
                }

                // Jika tombol yang sama diklik lagi, kembalikan ke tampilan semula
                if (selectedCapacityButton === button) {
                    selectedCapacityButton = null;
                    selectedCapacityPrice = 0;
                } else {
                    // Tambahkan class aktif ke tombol yang diklik
                    button.classList.remove('bg-gray-100', 'text-white');
                    button.classList.add('border-purple-500', 'text-purple-500', 'border-2', 'bg-gray-100');
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

            // Ambil detail produk saat halaman dimuat
            document.addEventListener('DOMContentLoaded', () => {
                const productId = window.location.pathname.split('/').pop();
                fetchProductDetail(productId);
            });
        </script>
    </div>
</x-layout>