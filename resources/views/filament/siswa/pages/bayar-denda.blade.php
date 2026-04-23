<x-filament-panels::page>
    <style>
        .denda-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.25rem; }
        .denda-card {
            background: #fff; border: 1px solid #e5e5e5; border-radius: 1rem;
            padding: 1.5rem; position: relative; overflow: hidden;
            transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        .denda-card:hover { box-shadow: 0 16px 48px -12px rgba(0,0,0,0.1); transform: translateY(-3px); border-color: #d0d0d0; }
        .denda-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, #dc2626, #ef4444, #f87171);
        }
        .denda-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.7rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.03em; text-transform: uppercase; }
        .badge-danger { background: #fef2f2; color: #dc2626; }
        .badge-type { background: #f4f4f5; color: #52525b; }
        .denda-nominal { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.02em; color: #18181b; margin: 0.75rem 0 0.5rem; }
        .denda-info { font-size: 0.82rem; color: #71717a; margin-bottom: 0.2rem; display: flex; align-items: center; gap: 0.35rem; }
        .denda-actions { display: flex; gap: 0.6rem; margin-top: 1.25rem; flex-wrap: wrap; }
        .btn-pay {
            flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
            padding: 0.65rem 1.2rem; border: none; border-radius: 0.65rem;
            font-size: 0.82rem; font-weight: 700; cursor: pointer; font-family: inherit;
            transition: all 200ms ease; min-width: 140px;
        }
        .btn-midtrans { background: #18181b; color: #fff; }
        .btn-midtrans:hover { background: #333; transform: translateY(-1px); box-shadow: 0 6px 20px -4px rgba(0,0,0,0.2); }
        .empty-state { text-align: center; padding: 4rem 2rem; }
        .empty-icon { width: 64px; height: 64px; margin: 0 auto 1rem; color: #d4d4d8; }
        .empty-title { font-size: 1.1rem; font-weight: 700; color: #52525b; margin-bottom: 0.35rem; }
        .empty-desc { font-size: 0.88rem; color: #a1a1aa; }

        .payment-success-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px);
            z-index: 9999; display: none; align-items: center; justify-content: center;
        }
        .payment-success-overlay.active { display: flex; animation: fadeInOverlay 300ms ease; }
        .payment-success-card {
            background: #fff; border-radius: 1.5rem; padding: 3rem 2.5rem; text-align: center;
            max-width: 380px; width: 90%; animation: bounceIn 500ms cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }
        .checkmark-circle { width: 80px; height: 80px; margin: 0 auto 1.5rem; position: relative; }
        .checkmark-circle svg { width: 80px; height: 80px; }
        .checkmark-circle .circle { stroke: #22c55e; stroke-width: 2; fill: none; stroke-dasharray: 252; stroke-dashoffset: 252; animation: drawCircle 600ms ease forwards 200ms; }
        .checkmark-circle .check { stroke: #22c55e; stroke-width: 3; fill: none; stroke-dasharray: 50; stroke-dashoffset: 50; animation: drawCheck 400ms ease forwards 700ms; }
        .success-title { font-size: 1.3rem; font-weight: 800; color: #18181b; margin-bottom: 0.4rem; }
        .success-desc { font-size: 0.9rem; color: #71717a; margin-bottom: 1.5rem; }
        .btn-close-success { padding: 0.6rem 2rem; background: #18181b; color: #fff; border: none; border-radius: 0.65rem; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 200ms; }
        .btn-close-success:hover { background: #333; }

        @keyframes fadeInOverlay { from { opacity: 0; } to { opacity: 1; } }
        @keyframes bounceIn { 0% { transform: scale(0.3); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        @keyframes drawCircle { to { stroke-dashoffset: 0; } }
        @keyframes drawCheck { to { stroke-dashoffset: 0; } }

        .denda-card { animation: cardSlideIn 400ms cubic-bezier(0.4, 0, 0.2, 1) both; }
        .denda-card:nth-child(1) { animation-delay: 0ms; }
        .denda-card:nth-child(2) { animation-delay: 80ms; }
        .denda-card:nth-child(3) { animation-delay: 160ms; }
        .denda-card:nth-child(4) { animation-delay: 240ms; }
        @keyframes cardSlideIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

        .dark .denda-card { background: #18181b; border-color: rgba(255,255,255,0.1); }
        .dark .denda-card:hover { border-color: rgba(255,255,255,0.2); box-shadow: 0 16px 48px -12px rgba(0,0,0,0.4); }
        .dark .denda-nominal { color: #f4f4f5; }
        .dark .badge-type { background: #27272a; color: #a1a1aa; }
        .dark .badge-danger { background: rgba(220,38,38,0.15); }
        .dark .btn-midtrans { background: #f4f4f5; color: #18181b; }
        .dark .btn-midtrans:hover { background: #e5e5e5; }
        .dark .payment-success-card { background: #1c1c1e; }
        .dark .success-title { color: #f4f4f5; }
    </style>

    @php $dendas = $this->getDendas(); @endphp

    @if($dendas->isEmpty())
        <div class="empty-state">
            <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <p class="empty-title">Tidak Ada Denda</p>
            <p class="empty-desc">Semua tagihan denda Anda sudah lunas. Pertahankan!</p>
        </div>
    @else
        <div class="denda-grid">
            @foreach($dendas as $denda)
                <div class="denda-card" wire:key="denda-{{ $denda->id }}">
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="denda-badge badge-danger">Belum Lunas</span>
                        <span class="denda-badge badge-type">{{ \App\Models\Denda::jenisDendaLabel($denda->jenis_denda) }}</span>
                    </div>

                    <p class="denda-nominal">Rp {{ number_format($denda->nominal, 0, ',', '.') }}</p>

                    <p class="denda-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        {{ $denda->peminjaman->kode_peminjaman ?? '-' }}
                    </p>

                    @if($denda->jumlah_hari)
                    <p class="denda-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        Terlambat {{ $denda->jumlah_hari }} hari
                    </p>
                    @endif

                    <div class="denda-actions">
                        <button class="btn-pay btn-midtrans" onclick="handleBayar({{ $denda->id }})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                            Bayar Online
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1.5rem; padding: 1rem 1.25rem; background: #f4f4f5; border-radius: 0.75rem; font-size: 0.82rem; color: #71717a; display: flex; align-items: flex-start; gap: 0.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
            <span>Untuk pembayaran <strong>tunai (cash)</strong>, silakan hubungi petugas perpustakaan. Admin akan mengkonfirmasi pembayaran Anda secara manual.</span>
        </div>
    @endif

    {{-- Success overlay --}}
    <div class="payment-success-overlay" id="successOverlay">
        <div class="payment-success-card">
            <div class="checkmark-circle">
                <svg viewBox="0 0 100 100">
                    <circle class="circle" cx="50" cy="50" r="40" />
                    <path class="check" d="M30 52 l13 13 l27 -27" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <p class="success-title">Pembayaran Berhasil!</p>
            <p class="success-desc">Denda Anda telah berhasil dilunasi secara otomatis.</p>
            <button class="btn-close-success" onclick="closeSuccess()">Tutup</button>
        </div>
    </div>

    @script
    <script>
        const snapUrl = @js($this->getSnapUrl());
        const clientKey = @js($this->getClientKey());

        if (!document.querySelector('script[src*="snap.js"]')) {
            const script = document.createElement('script');
            script.src = snapUrl;
            script.setAttribute('data-client-key', clientKey);
            document.head.appendChild(script);
        }

        window.handleBayar = function(dendaId) {
            $wire.bayarMidtrans(dendaId);
        };

        $wire.on('open-snap', (data) => {
            const params = Array.isArray(data) ? data[0] : data;
            const { snapToken, dendaId } = params;

            const trySnap = () => {
                if (window.snap) {
                    window.snap.pay(snapToken, {
                        onSuccess: function(result) {
                            document.getElementById('successOverlay').classList.add('active');
                            $wire.tandaiLunas(dendaId);
                        },
                        onPending: function(result) {
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal. Silakan coba lagi.');
                        },
                        onClose: function() {
                        }
                    });
                } else {
                    setTimeout(trySnap, 300);
                }
            };
            trySnap();
        });

        window.closeSuccess = function() {
            document.getElementById('successOverlay').classList.remove('active');
            location.reload();
        };
    </script>
    @endscript
</x-filament-panels::page>
