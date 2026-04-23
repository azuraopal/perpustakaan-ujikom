<x-filament-widgets::widget>
    <div style="
        background: linear-gradient(135deg, #18181b 0%, #27272a 50%, #3f3f46 100%);
        border-radius: 1.25rem;
        padding: 2rem 2.25rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    ">
        <div style="position:absolute; top:-30px; right:-30px; width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,0.04);"></div>
        <div style="position:absolute; bottom:-20px; right:60px; width:80px; height:80px; border-radius:50%; background:rgba(255,255,255,0.03);"></div>

        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            <div>
                <p style="font-size:0.82rem; color:rgba(255,255,255,0.55); font-weight:500; margin-bottom:0.25rem;">
                    {{ $this->getDateString() }}
                </p>
                <h2 style="font-size:1.5rem; font-weight:800; letter-spacing:-0.02em; margin:0 0 0.35rem 0; line-height:1.2;">
                    {{ $this->getGreeting() }}, {{ $this->getUserName() }}
                </h2>
                <p style="font-size:0.88rem; color:rgba(255,255,255,0.6); margin:0;">
                    Kuota pinjam tersisa <strong style="color:#fff;">{{ $this->getKuota() }} buku</strong>
                </p>
            </div>
            <div style="display:flex; gap:0.6rem; flex-wrap:wrap;">
                <a href="{{ \App\Filament\Siswa\Pages\KatalogBuku::getUrl(panel: 'siswa') }}"
                   style="
                       display:inline-flex; align-items:center; gap:0.4rem;
                       padding:0.55rem 1.1rem;
                       background:rgba(255,255,255,0.12);
                       border:1px solid rgba(255,255,255,0.15);
                       border-radius:0.65rem;
                       color:#fff; font-size:0.82rem; font-weight:600;
                       text-decoration:none;
                       transition:all 0.2s;
                   "
                   onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.background='rgba(255,255,255,0.12)'; this.style.transform='translateY(0)'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Jelajahi Katalog
                </a>
                <a href="{{ \App\Filament\Siswa\Resources\PeminjamanSiswaResource::getUrl('create', panel: 'siswa') }}"
                   style="
                       display:inline-flex; align-items:center; gap:0.4rem;
                       padding:0.55rem 1.1rem;
                       background:#fff;
                       border:1px solid #fff;
                       border-radius:0.65rem;
                       color:#18181b; font-size:0.82rem; font-weight:700;
                       text-decoration:none;
                       transition:all 0.2s;
                   "
                   onmouseover="this.style.background='#e4e4e7'; this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.background='#fff'; this.style.transform='translateY(0)'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Pinjam Buku
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
