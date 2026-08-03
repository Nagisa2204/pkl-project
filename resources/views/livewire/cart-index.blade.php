<div class="max-w-[1250px] mx-auto px-5 my-10 font-sans">
    
    <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 25px;">Keranjang Belanja</h1>

    @if($cart && $cart->cartItems->count() > 0)
        <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start;">
            
            <div style="flex: 2; min-width: 60%;">
                <div style="background: #ffffff; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;">
                    
                    @foreach($cart->cartItems as $item)
                        @php 
                            $subtotal = $item->product->price * $item->quantity;
                            $isDisabled = $selectedCategoryId !== null && $item->product->category_id !== $selectedCategoryId;
                        @endphp
                        
                        <div style="display: flex; align-items: center; padding: 20px; border-bottom: 1px solid #f1f5f9; gap: 20px; flex-wrap: wrap; transition: opacity 0.3s; opacity: {{ $isDisabled ? '0.4' : '1' }}; background: {{ $isDisabled ? '#f8fafc' : 'transparent' }};">
                            
                            <div style="display: flex; align-items: center; justify-content: center; padding-right: 5px;">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" @if($isDisabled) disabled title="Hanya bisa checkout produk dari kategori yang sama" @endif style="width: 20px; height: 20px; cursor: {{ $isDisabled ? 'not-allowed' : 'pointer' }}; accent-color: #4f46e5;">
                            </div>

                            <div style="width: 100px; height: 100px; background: #f8fafc; border-radius: 12px; overflow: hidden; flex-shrink: 0;">
                                @if($item->product->images && $item->product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="{{ $item->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 12px;">No Image</div>
                                @endif
                            </div>

                            <div style="flex: 1; min-width: 150px;">
                                <div style="color: #64748b; font-size: 12px; font-weight: 700; margin-bottom: 4px;">{{ $item->product->category->name ?? 'Uncategorized' }}</div>
                                <h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; color: #1e293b;">{{ $item->product->name }}</h3>
                                <div style="font-weight: 800; color: #4f46e5;">Rp {{ number_format($item->product->price, 0, ',', '.') }}</div>
                            </div>

                            <div style="display: flex; align-items: center; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
                                <button wire:click="decrementQuantity({{ $item->id }})" style="padding: 8px 12px; border: none; background: transparent; cursor: pointer; font-size: 16px; font-weight: bold; color: #64748b;">-</button>
                                <div style="padding: 8px 12px; font-weight: 700; font-size: 14px; color: #0f172a; min-width: 25px; text-align: center; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0;">{{ $item->quantity }}</div>
                                <button wire:click="incrementQuantity({{ $item->id }})" style="padding: 8px 12px; border: none; background: transparent; cursor: pointer; font-size: 16px; font-weight: bold; color: #64748b;">+</button>
                            </div>

                            <div style="display: flex; flex-direction: column; align-items: flex-end; min-width: 100px; gap: 10px;">
                                <div style="font-weight: 800; font-size: 16px; color: #0f172a;">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </div>
                                <button wire:click="removeItem({{ $item->id }})" style="background: none; border: none; color: #ef4444; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='none'">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    Hapus
                                </button>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <div style="flex: 1; min-width: 300px;">
                <div style="background: #ffffff; padding: 25px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); position: sticky; top: 20px;">
                    <h3 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 800; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">Ringkasan Belanja</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #64748b; font-size: 15px;">
                        <span>Total Harga ({{ $totalItems }} Barang)</span>
                        <span style="font-weight: 700; color: #0f172a;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>

                    <div style="border-top: 2px dashed #f1f5f9; margin: 20px 0;"></div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 25px; font-size: 18px;">
                        <span style="font-weight: 800; color: #0f172a;">Total Tagihan</span>
                        <span style="font-weight: 800; color: #4f46e5;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>

                    @if($totalItems > 0)
                        <a href="{{ route('checkout') }}" style="display: block; width: 100%; text-align: center; padding: 15px; background: #0f172a; color: #ffffff; text-decoration: none; font-weight: 700; border-radius: 10px; transition: transform 0.2s, box-shadow 0.2s; box-sizing: border-box;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                            Lanjut ke Pembayaran
                        </a>
                    @else
                        <button disabled style="display: block; width: 100%; text-align: center; padding: 15px; background: #cbd5e1; color: #ffffff; border: none; font-weight: 700; border-radius: 10px; cursor: not-allowed; box-sizing: border-box;">
                            Pilih Item Dulu
                        </button>
                    @endif
                </div>
            </div>

        </div>
    @else
        <div style="background: #ffffff; padding: 60px 20px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
            <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; color: #94a3b8;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            </div>
            <h2 style="margin: 0 0 10px 0; font-size: 22px; font-weight: 800; color: #0f172a;">Keranjang Anda masih kosong</h2>
            <p style="color: #64748b; margin-bottom: 25px;">Yuk, temukan produk menarik dan tambahkan ke keranjangmu!</p>
            <a href="/products" style="display: inline-block; padding: 12px 30px; background: #4f46e5; color: white; text-decoration: none; font-weight: 700; border-radius: 8px;">Mulai Belanja</a>
        </div>
    @endif
</div>