<div class="max-w-[1250px] mx-auto px-5 my-10 font-sans">
    
    <h1 class="text-[28px] font-extrabold text-slate-900 mb-6">Keranjang Belanja</h1>

    @if($cart && $cart->cartItems->count() > 0)
        <div class="flex flex-wrap gap-[30px] items-start">
            
            <div class="flex-[2] min-w-[60%]">
                <div class="bg-white rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] overflow-hidden">
                    
                    @foreach($cart->cartItems as $item)
                        @php 
                            $subtotal = $item->product->price * $item->quantity;
                            $isDisabled = $selectedCategoryId !== null && $item->product->category_id !== $selectedCategoryId;
                        @endphp
                        
                        <div class="flex items-center p-5 border-b border-slate-100 gap-5 flex-wrap transition-opacity duration-300 {{ $isDisabled ? 'opacity-40 bg-slate-50' : 'opacity-100 bg-transparent' }}">
                            
                            <div class="flex items-center justify-center pr-1">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" 
                                       @if($isDisabled) disabled title="Hanya bisa checkout produk dari kategori yang sama" @endif 
                                       class="w-5 h-5 accent-indigo-600 {{ $isDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                            </div>

                            <div class="w-[100px] h-[100px] bg-slate-50 rounded-xl overflow-hidden flex-shrink-0">
                                @if($item->product->images && $item->product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300 text-xs">No Image</div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-[150px]">
                                <div class="text-slate-500 text-xs font-bold mb-1">{{ $item->product->category->name ?? 'Uncategorized' }}</div>
                                <h3 class="m-0 mb-2 text-base font-bold text-slate-800">{{ $item->product->name }}</h3>
                                <div class="font-extrabold text-indigo-600">Rp {{ number_format($item->product->price, 0, ',', '.') }}</div>
                            </div>

                            <div class="flex items-center bg-slate-50 rounded-lg border border-slate-200 overflow-hidden">
                                <button wire:click="decrementQuantity({{ $item->id }})" class="py-2 px-3 border-none bg-transparent cursor-pointer text-base font-bold text-slate-500 hover:bg-slate-200 transition-colors">-</button>
                                <div class="py-2 px-3 font-bold text-sm text-slate-900 min-w-[25px] text-center border-l border-r border-slate-200">{{ $item->quantity }}</div>
                                <button wire:click="incrementQuantity({{ $item->id }})" class="py-2 px-3 border-none bg-transparent cursor-pointer text-base font-bold text-slate-500 hover:bg-slate-200 transition-colors">+</button>
                            </div>

                            <div class="flex flex-col items-end min-w-[100px] gap-2.5">
                                <div class="font-extrabold text-base text-slate-900">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </div>
                                <button wire:click="removeItem({{ $item->id }})" class="bg-transparent border-none text-red-500 text-[13px] font-bold cursor-pointer flex items-center gap-1 py-1 px-2 rounded-md hover:bg-red-100 transition-colors duration-200">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    Hapus
                                </button>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex-1 min-w-[300px]">
                <div class="bg-white p-[25px] rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] sticky top-5">
                    <h3 class="m-0 mb-5 text-lg font-extrabold text-slate-900 border-b-2 border-slate-100 pb-4">Ringkasan Belanja</h3>
                    
                    <div class="flex justify-between mb-4 text-slate-500 text-[15px]">
                        <span>Total Harga ({{ $totalItems }} Barang)</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="border-t-2 border-dashed border-slate-100 my-5"></div>

                    <div class="flex justify-between mb-6 text-lg">
                        <span class="font-extrabold text-slate-900">Total Tagihan</span>
                        <span class="font-extrabold text-indigo-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>

                    @if($totalItems > 0)
                        <a href="{{ route('checkout') }}" class="block w-full text-center p-4 bg-slate-900 text-white no-underline font-bold rounded-lg box-border hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                            Lanjut ke Pembayaran
                        </a>
                    @else
                        <button disabled class="block w-full text-center p-4 bg-slate-300 text-white border-none font-bold rounded-lg cursor-not-allowed box-border">
                            Pilih Item Dulu
                        </button>
                    @endif
                </div>
            </div>

        </div>
    @else
        <div class="bg-white py-[60px] px-5 rounded-2xl shadow-[0_4px_15px_rgba(0,0,0,0.03)] text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5 text-slate-400">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            </div>
            <h2 class="m-0 mb-2.5 text-[22px] font-extrabold text-slate-900">Keranjang Anda masih kosong</h2>
            <p class="text-slate-500 mb-6">Yuk, temukan produk menarik dan tambahkan ke keranjangmu!</p>
            <a href="/products" class="inline-block py-3 px-8 bg-indigo-600 hover:bg-indigo-700 transition-colors text-white no-underline font-bold rounded-lg">Mulai Belanja</a>
        </div>
    @endif
</div>