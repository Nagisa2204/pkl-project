<div style="max-width: 1100px; margin: 40px auto; padding: 0 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 25px;">Checkout Pesanan</h1>

    <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start;">
        
        <div style="flex: 2; min-width: 60%;">
            <form wire:submit.prevent="placeOrder" id="checkout-form">
                
                <div style="background: #ffffff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 20px;">
                    <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-top: 0; margin-bottom: 20px;">
                        Informasi Kontak
                    </h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px;">Nama Lengkap</label>
                            <input type="text" wire:model="buyer_name" required 
                                   style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; outline: none; box-sizing: border-box;">
                            @error('buyer_name') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px;">Nomor WhatsApp</label>
                            <input type="text" wire:model="buyer_whatsapp" required 
                                   style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; outline: none; box-sizing: border-box;">
                            @error('buyer_whatsapp') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div style="background: #ffffff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 20px;">
                    <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-top: 0; margin-bottom: 20px;">
                        Alamat Pengiriman
                    </h2>
                    
                    @if($has_address)
                        <div style="padding: 15px; border: 1px solid #4f46e5; background-color: #eef2ff; border-radius: 8px;">
                            <span style="font-weight: 700; color: #4f46e5; font-size: 15px;">Alamat Utama Tersimpan</span>
                            <p style="margin: 5px 0 0 0; color: #475569; font-size: 14px;">Total Berat Pengiriman: <strong>{{ number_format($total_weight, 0, ',', '.') }} Gram</strong></p>
                        </div>
                    @else
                        <div style="padding: 25px 20px; border: 2px dashed #ef4444; background-color: #fef2f2; border-radius: 8px; text-align: center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <h3 style="margin: 0 0 10px 0; color: #991b1b; font-size: 18px; font-weight: 700;">Alamat Pengiriman Belum Diatur</h3>
                            <p style="margin: 0 0 20px 0; color: #b91c1c; font-size: 14px;">Sistem membutuhkan alamat Anda untuk menghitung ongkos kirim.</p>
                            <a href="{{ route('profile.show') }}" style="display: inline-block; padding: 12px 25px; background-color: #ef4444; color: white; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 15px; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">
                                Isi Alamat Sekarang
                            </a>
                        </div>
                    @endif
                </div>

                @if($has_address)
                    
                    <div style="background: #ffffff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 20px;">
                        <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-top: 0; margin-bottom: 20px;">
                            Metode Pembayaran
                        </h2>
                        
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px;">Pilih Bank / E-Wallet</label>
                            <select wire:model.live="selected_bank" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; outline: none; box-sizing: border-box; background: white;">
                                <option value="">-- Pilih Pembayaran --</option>
                                <option value="bca_va">BCA Virtual Account</option>
                                <option value="mandiri_clickpay">Mandiri</option>
                                <option value="bni_va">BNI Virtual Account</option>
                                <option value="bri_va">BRI Virtual Account</option>
                                <option value="gopay">GoPay</option>
                                <option value="shopeepay">ShopeePay</option>
                                <option value="all">Tampilkan Semua Opsi Midtrans</option>
                            </select>
                            @error('selected_bank') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div style="background: #ffffff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-top: 0; margin-bottom: 20px;">
                            Metode Pengiriman
                        </h2>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px;">Pilih Kurir</label>
                            <select wire:model.live="courier" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; outline: none; box-sizing: border-box; background: white;">
                                <option value="">-- Pilih Ekspedisi --</option>
                                <option value="jne">JNE (Jalur Nugraha Ekakurir)</option>
                                <option value="pos">POS Indonesia</option>
                                <option value="tiki">TIKI</option>
                            </select>
                            @error('courier') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                            
                            <div wire:loading wire:target="courier" style="color: #4f46e5; font-size: 13px; margin-top: 8px; font-weight: 600;">
                                Memuat daftar layanan dan harga...
                            </div>
                        </div>

                        @if(count($services) > 0)
                            <div style="margin-top: 15px;">
                                <label style="display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 10px;">Pilih Layanan</label>
                                
                                @foreach($services as $svc)
                                    <label style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px; cursor: pointer; transition: background 0.2s; {{ $shipping_service === $svc['service'] ? 'background-color: #eef2ff; border-color: #4f46e5;' : 'background-color: white;' }}">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <input type="radio" wire:model.live="shipping_service" value="{{ $svc['service'] }}" required style="accent-color: #4f46e5; width: 16px; height: 16px;">
                                            <div>
                                                <div style="font-weight: 700; color: #1e293b; font-size: 15px;">{{ $svc['service'] }} <span style="font-size: 12px; color: #64748b; font-weight: normal;">({{ $svc['description'] }})</span></div>
                                                <div style="font-size: 13px; color: #64748b; margin-top: 2px;">Estimasi Tiba: {{ $svc['cost'][0]['etd'] }} Hari</div>
                                            </div>
                                        </div>
                                        <div style="font-weight: 800; color: #4f46e5; font-size: 16px;">
                                            Rp {{ number_format($svc['cost'][0]['value'], 0, ',', '.') }}
                                        </div>
                                    </label>
                                @endforeach
                                @error('shipping_service') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                @endif
            </form>
        </div>

        <div style="flex: 1; min-width: 300px;">
            <div style="background: #ffffff; padding: 25px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); position: sticky; top: 20px;">
                <h3 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 800; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">Ringkasan Pesanan</h3>
                
                @php $subtotalProduk = 0; @endphp

                <div style="margin-bottom: 20px;">
                    @if($cart && $cart->cartItems)
                        @foreach($cart->cartItems as $item)
                            @php 
                                $lineSubtotal = $item->product->price * $item->quantity;
                                $subtotalProduk += $lineSubtotal;
                            @endphp
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                                <div style="color: #475569; padding-right: 15px;">
                                    <span style="font-weight: 600; color: #1e293b;">{{ $item->product->name }}</span>
                                    <div style="font-size: 12px; margin-top: 2px;">{{ $item->quantity }} x Rp {{ number_format($item->product->price, 0, ',', '.') }}</div>
                                </div>
                                <div style="font-weight: 600; color: #0f172a;">
                                    Rp {{ number_format($lineSubtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div style="border-top: 2px dashed #f1f5f9; margin: 20px 0;"></div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #64748b; font-size: 14px;">
                    <span>Subtotal Produk</span>
                    <span style="font-weight: 600; color: #0f172a;">Rp {{ number_format($subtotalProduk, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; color: #64748b; font-size: 14px;">
                    <span>Biaya Pengiriman</span>
                    <span style="font-weight: 600; color: {{ $shipping_cost > 0 ? '#0f172a' : '#10b981' }};">
                        {{ $shipping_cost > 0 ? 'Rp ' . number_format($shipping_cost, 0, ',', '.') : 'Belum dihitung' }}
                    </span>
                </div>

                <div style="border-top: 2px solid #f1f5f9; margin: 20px 0;"></div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 25px; font-size: 18px;">
                    <span style="font-weight: 800; color: #0f172a;">Total Pembayaran</span>
                    <span style="font-weight: 800; color: #4f46e5;">Rp {{ number_format($subtotalProduk + $shipping_cost, 0, ',', '.') }}</span>
                </div>

                @php
                    $isReadyToSubmit = $has_address && !empty($selected_bank) && $shipping_cost > 0;
                @endphp

                <button type="submit" form="checkout-form" 
                        @if(!$isReadyToSubmit) disabled @endif
                        style="display: flex; justify-content: center; align-items: center; gap: 8px; width: 100%; padding: 16px; border: none; font-weight: 700; font-size: 16px; border-radius: 10px; cursor: {{ $isReadyToSubmit ? 'pointer' : 'not-allowed' }}; background: {{ $isReadyToSubmit ? '#0f172a' : '#94a3b8' }}; color: #ffffff; transition: all 0.2s;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    Selesaikan Pesanan
                </button>
                
                @if(!$has_address)
                    <div style="text-align: center; color: #ef4444; font-size: 13px; font-weight: 600; margin-top: 10px;">
                        Alamat pengiriman belum diatur.
                    </div>
                @elseif(empty($selected_bank))
                    <div style="text-align: center; color: #ef4444; font-size: 13px; font-weight: 600; margin-top: 10px;">
                        Silakan pilih bank/metode pembayaran.
                    </div>
                @elseif($shipping_cost <= 0)
                    <div style="text-align: center; color: #ef4444; font-size: 13px; font-weight: 600; margin-top: 10px;">
                        Pilih kurir dan layanan pengiriman terlebih dahulu.
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>