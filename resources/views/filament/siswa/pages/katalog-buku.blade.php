<x-filament-panels::page>
    <div>
        <div style="display:flex; gap:1rem; margin-bottom:2rem; flex-wrap:wrap;">
            <div style="flex:1; min-width:240px;">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari judul atau penulis..."
                    style="width:100%; padding:0.7rem 1rem; border:1.5px solid #e0e0e0; border-radius:0.65rem; font-size:0.9rem; font-family:inherit; background:#fafafa; outline:none; transition:all 0.2s;"
                    onfocus="this.style.borderColor='#333'; this.style.background='#fff'; this.style.boxShadow='0 0 0 3px rgba(0,0,0,0.06)'"
                    onblur="this.style.borderColor='#e0e0e0'; this.style.background='#fafafa'; this.style.boxShadow='none'"
                />
            </div>
            <div style="min-width:180px;">
                <select
                    wire:model.live="kategoriFilter"
                    style="width:100%; padding:0.7rem 1rem; border:1.5px solid #e0e0e0; border-radius:0.65rem; font-size:0.9rem; font-family:inherit; background:#fafafa; outline:none; cursor:pointer;"
                >
                    <option value="">Semua Kategori</option>
                    @foreach($this->getKategoris() as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:1.5rem;">
            @forelse($this->getBooks() as $buku)
                <div style="background:#fff; border:1px solid #e8e8e8; border-radius:1rem; overflow:hidden; transition:all 0.25s; cursor:default;"
                     onmouseover="this.style.boxShadow='0 12px 40px -10px rgba(0,0,0,0.12)'; this.style.transform='translateY(-4px)'; this.style.borderColor='#d0d0d0'"
                     onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)'; this.style.borderColor='#e8e8e8'"
                >
                    <div style="width:100%; aspect-ratio:3/4; background:#f3f3f3; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                        @if($buku->cover_image)
                            <img src="{{ asset('storage/' . $buku->cover_image) }}" alt="{{ $buku->judul }}"
                                 style="width:100%; height:100%; object-fit:cover;" />
                        @else
                            <div style="text-align:center; color:#bbb; padding:2rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/>
                                </svg>
                                <p style="font-size:0.75rem; margin-top:0.5rem;">No Cover</p>
                            </div>
                        @endif
                    </div>

                    <div style="padding:1rem 1.15rem 1.25rem;">
                        <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:#999; margin-bottom:0.35rem;">
                            {{ $buku->kategori?->nama ?? 'Umum' }}
                        </p>
                        <h3 style="font-size:0.95rem; font-weight:700; line-height:1.3; margin-bottom:0.3rem; letter-spacing:-0.01em; color:#111; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                            {{ $buku->judul }}
                        </h3>
                        <p style="font-size:0.82rem; color:#777; margin-bottom:0.75rem;">
                            {{ $buku->penulis }}
                        </p>

                        <div style="display:flex; align-items:center; justify-content:space-between;">
                            <span style="font-size:0.78rem; font-weight:600; padding:0.25rem 0.65rem; border-radius:999px;
                                {{ $buku->stok > 0 ? 'background:#f0fdf4; color:#16a34a;' : 'background:#fef2f2; color:#dc2626;' }}">
                                {{ $buku->stok > 0 ? "Stok: {$buku->stok}" : 'Habis' }}
                            </span>

                            @if($buku->stok > 0)
                                <button
                                    wire:click="bukaFormPinjam({{ $buku->id }})"
                                    style="padding:0.42rem 1rem; background:#111; color:#fff; border:none; border-radius:0.5rem; font-size:0.78rem; font-weight:700; letter-spacing:0.01em; cursor:pointer; font-family:inherit; transition:all 0.2s;"
                                    onmouseover="this.style.background='#333'; this.style.transform='scale(1.03)'"
                                    onmouseout="this.style.background='#111'; this.style.transform='scale(1)'"
                                >
                                    Ajukan
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1; text-align:center; padding:4rem 2rem; color:#aaa;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 1rem;">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    <p style="font-size:1rem; font-weight:600;">Tidak ada buku ditemukan</p>
                    <p style="font-size:0.85rem; margin-top:0.5rem;">Coba ubah kata kunci pencarian atau filter kategori.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>