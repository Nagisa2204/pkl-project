<div style="max-width: 1100px; margin: 40px auto; padding: 0 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9fafb; min-height: 100vh; padding-top: 40px;">
    <div style="display: flex; flex-wrap: wrap; background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="flex: 1; min-width: 350px; background-color: #e0f2fe; padding: 20px; display: flex; align-items: center; justify-content: center;">
            @if($product->images && $product->images->count() > 0)
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                     alt="{{ $product->name }}" 
                     style="width: 100%; max-width: 450px; height: auto; aspect-ratio: 1/1; object-fit: cover; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            @else
                <div style="width: 100%; max-width: 450px; aspect-ratio: 1/1; background: #ffffff; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-weight: bold;">
                    Belum ada gambar
                </div>
            @endif
        </div>

        <div style="flex: 1; min-width: 350px; padding: 40px;">
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <span style="background: #eef2ff; color: #4f46e5; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                    {{ $product->category->name ?? 'Uncategorized' }}
                </span>
                <span style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                    Produk Fisik
                </span>
            </div>

            <h1 style="margin: 0 0 10px 0; font-size: 32px; font-weight: 800; color: #0f172a; line-height: 1.2;">
                {{ $product->name }}
            </h1>

            <div style="font-size: 32px; font-weight: 800; color: #4f46e5; margin-bottom: 25px;">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                <div style="background: #f8fafc; padding: 15px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Stok Tersedia</div>
                    <div style="color: {{ $product->stock_quantity > 0 ? '#0f172a' : '#dc2626' }}; font-size: 14px; font-weight: 700;">
                        {{ $product->stock_quantity > 0 ? $product->stock_quantity . ' pcs' : 'Habis' }}
                    </div>
                </div>
                <div style="background: #f8fafc; padding: 15px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Berat Produk</div>
                    <div style="color: #0f172a; font-size: 14px; font-weight: 700;">{{ $product->weight_grams ?? 0 }} Gram</div>
                </div>
                <div style="background: #f8fafc; padding: 15px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Min. Pembelian</div>
                    <div style="color: #0f172a; font-size: 14px; font-weight: 700;">1 Item</div>
                </div>
                <div style="background: #f8fafc; padding: 15px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">SKU</div>
                    <div style="color: #0f172a; font-size: 14px; font-weight: 700;">{{ $product->sku ?? '-' }}</div>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #f1f5f9; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 700; color: #334155;">Deskripsi Produk</h3>
                <div style="line-height: 1.7; color: #64748b; font-size: 14px;">
                    {{ $product->description ?? 'Deskripsi produk belum tersedia. Silakan hubungi admin untuk informasi lebih lanjut mengenai detail dan spesifikasi lengkap produk ini.' }}
                </div>
            </div>

            <button 
                wire:click="addToCart" 
                @if($product->stock_quantity <= 0) disabled @endif
                style="
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    padding: 16px 30px; 
                    font-size: 16px; 
                    font-weight: 700; 
                    border: none; 
                    border-radius: 12px; 
                    cursor: {{ $product->stock_quantity > 0 ? 'pointer' : 'not-allowed' }}; 
                    background-color: {{ $product->stock_quantity > 0 ? '#0f172a' : '#e2e8f0' }}; 
                    color: {{ $product->stock_quantity > 0 ? 'white' : '#94a3b8' }}; 
                    width: 100%; 
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                "
                onmouseover="this.style.transform='{{ $product->stock_quantity > 0 ? 'translateY(-2px)' : 'none' }}'; this.style.boxShadow='{{ $product->stock_quantity > 0 ? '0 10px 15px -3px rgba(0, 0, 0, 0.1)' : 'none' }}'"
                onmouseout="this.style.transform='none'; this.style.boxShadow='none'"
            >
                @if($product->stock_quantity > 0)
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    Tambahkan ke Keranjang
                @else
                    Produk Tidak Tersedia
                @endif
            </button>
            
        </div>
    </div>
</div>