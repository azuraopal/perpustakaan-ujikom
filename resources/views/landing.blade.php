<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital — Sistem Informasi Perpustakaan Sekolah</title>
    <meta name="description" content="Sistem Informasi Perpustakaan Sekolah Digital. Kelola peminjaman, pengembalian, dan katalog buku dengan mudah.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
        :root{--bg:#f5f5f5;--white:#fff;--black:#111;--gray-1:#1a1a1a;--gray-2:#333;--gray-3:#555;--gray-4:#888;--gray-5:#bbb;--gray-6:#ddd;--gray-7:#eee;--gray-8:#f9f9f9;--radius:1.25rem;--radius-sm:.75rem}
        html{scroll-behavior:smooth}
        body{font-family:'Inter',-apple-system,sans-serif;background:var(--bg);color:var(--black);line-height:1.6;overflow-x:hidden}

        /* ANIMATIONS */
        .anim{opacity:0;transform:translateY(40px);transition:opacity .7s ease,transform .7s ease}
        .anim.show{opacity:1;transform:translateY(0)}
        .anim-left{opacity:0;transform:translateX(-40px);transition:opacity .7s ease,transform .7s ease}
        .anim-left.show{opacity:1;transform:translateX(0)}
        .anim-right{opacity:0;transform:translateX(40px);transition:opacity .7s ease,transform .7s ease}
        .anim-right.show{opacity:1;transform:translateX(0)}
        .anim-scale{opacity:0;transform:scale(.92);transition:opacity .6s ease,transform .6s ease}
        .anim-scale.show{opacity:1;transform:scale(1)}
        .delay-1{transition-delay:.1s}.delay-2{transition-delay:.2s}.delay-3{transition-delay:.3s}.delay-4{transition-delay:.4s}.delay-5{transition-delay:.5s}

        /* NAV */
        nav{position:fixed;top:0;left:0;right:0;z-index:100;backdrop-filter:blur(20px);background:rgba(245,245,245,.8);border-bottom:1px solid var(--gray-6);transition:all .3s}
        nav.scrolled{box-shadow:0 2px 30px rgba(0,0,0,.06)}
        .nav-inner{max-width:1200px;margin:0 auto;padding:0 2rem;height:72px;display:flex;align-items:center;justify-content:space-between}
        .nav-brand{display:flex;align-items:center;gap:.75rem;text-decoration:none;color:var(--black);font-weight:800;font-size:1.1rem}
        .nav-brand-icon{width:36px;height:36px;background:var(--black);border-radius:.6rem;display:flex;align-items:center;justify-content:center}
        .nav-brand-icon svg{width:18px;height:18px;stroke:#fff}
        .nav-links{display:flex;gap:2rem;align-items:center}
        .nav-links a{text-decoration:none;color:var(--gray-3);font-weight:500;font-size:.9rem;transition:color .2s}
        .nav-links a:hover{color:var(--black)}
        .btn{display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.4rem;border-radius:var(--radius-sm);font-weight:600;font-size:.875rem;text-decoration:none;transition:all .25s;cursor:pointer;border:1.5px solid var(--black)}
        .btn-dark{background:var(--black);color:#fff;border-color:var(--black)}
        .btn-dark:hover{background:var(--gray-2);transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.2)}
        .btn-light{background:transparent;color:var(--black)}
        .btn-light:hover{background:var(--black);color:#fff}

        /* HERO */
        .hero{min-height:100vh;display:flex;align-items:center;padding:7rem 2rem 5rem}
        .hero-inner{max-width:1200px;margin:0 auto;width:100%;display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center}
        .hero-tag{display:inline-flex;align-items:center;gap:.5rem;padding:.4rem 1rem;background:var(--white);border:1px solid var(--gray-6);border-radius:999px;font-size:.8rem;font-weight:600;color:var(--gray-3);margin-bottom:1.5rem}
        .hero-tag-dot{width:8px;height:8px;border-radius:50%;background:var(--black);animation:blink 2s infinite}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
        .hero h1{font-size:3.5rem;font-weight:900;line-height:1.08;letter-spacing:-.045em;margin-bottom:1.5rem}
        .hero h1 em{font-style:normal;color:var(--gray-4);font-weight:400;font-size:1.25rem;display:block;letter-spacing:-.01em;margin-top:.6rem;line-height:1.5}
        .hero p{font-size:1.05rem;color:var(--gray-3);max-width:460px;margin-bottom:2.5rem;line-height:1.75}
        .hero-actions{display:flex;gap:1rem;flex-wrap:wrap}

        .hero-visual{position:relative}
        .hero-card{background:var(--white);border:1px solid var(--gray-6);border-radius:var(--radius);padding:2rem;box-shadow:0 30px 60px -15px rgba(0,0,0,.07);position:relative}
        .hero-card-dark{background:var(--gray-1);border-color:var(--gray-2);color:#fff;border-radius:var(--radius);padding:1.75rem;position:absolute;bottom:-30px;right:-20px;width:220px;box-shadow:0 20px 40px rgba(0,0,0,.15)}
        .hero-card-dark .hcd-num{font-size:2rem;font-weight:800;letter-spacing:-.03em}
        .hero-card-dark .hcd-label{font-size:.78rem;color:var(--gray-5);margin-top:.2rem}
        .stat-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem}
        .stat-item{background:var(--gray-8);border:1px solid var(--gray-7);border-radius:var(--radius-sm);padding:1.15rem;text-align:center}
        .stat-item .num{font-size:1.75rem;font-weight:800;letter-spacing:-.03em;display:block}
        .stat-item .label{font-size:.72rem;color:var(--gray-4);margin-top:.15rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em}
        .hero-card-footer{display:flex;align-items:center;gap:.6rem;padding-top:1rem;border-top:1px solid var(--gray-7)}
        .hero-card-footer span{font-size:.82rem;color:var(--gray-4)}
        .status-dot{width:8px;height:8px;border-radius:50%;background:#22c55e;animation:blink 2s infinite}

        /* MARQUEE */
        .marquee-section{padding:3rem 0;border-top:1px solid var(--gray-6);border-bottom:1px solid var(--gray-6);overflow:hidden;background:var(--white)}
        .marquee-track{display:flex;gap:4rem;animation:marquee 20s linear infinite;width:max-content}
        .marquee-track span{font-size:1.1rem;font-weight:700;color:var(--gray-5);white-space:nowrap;letter-spacing:.02em}
        @keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

        /* FEATURES */
        .features{padding:7rem 2rem;max-width:1200px;margin:0 auto}
        .section-label{display:inline-block;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--gray-4);margin-bottom:.75rem;padding:.35rem 1rem;background:var(--white);border:1px solid var(--gray-6);border-radius:999px}
        .section-title{font-size:2.4rem;font-weight:800;letter-spacing:-.035em;margin-bottom:1rem}
        .section-desc{font-size:1.05rem;color:var(--gray-3);max-width:520px;margin-bottom:3.5rem;line-height:1.7}
        .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem}
        .feature-card{background:var(--white);border:1px solid var(--gray-6);border-radius:var(--radius);padding:2rem;transition:all .3s;position:relative;overflow:hidden}
        .feature-card:hover{border-color:var(--gray-5);box-shadow:0 20px 50px -15px rgba(0,0,0,.08);transform:translateY(-4px)}
        .feature-card:nth-child(odd){background:var(--gray-1);border-color:var(--gray-2);color:#fff}
        .feature-card:nth-child(odd) p{color:var(--gray-5)}
        .feature-card:nth-child(odd) .feature-icon{background:var(--gray-2);border-color:var(--gray-2)}
        .feature-card:nth-child(odd) .feature-icon svg{stroke:#fff}
        .feature-icon{width:44px;height:44px;border-radius:var(--radius-sm);background:var(--gray-8);border:1px solid var(--gray-7);display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem}
        .feature-icon svg{width:20px;height:20px;stroke:var(--black)}
        .feature-card h3{font-size:1.05rem;font-weight:700;margin-bottom:.5rem}
        .feature-card p{font-size:.88rem;color:var(--gray-3);line-height:1.65}

        /* HOW IT WORKS */
        .how-section{padding:7rem 2rem;background:var(--white);border-top:1px solid var(--gray-6)}
        .how-inner{max-width:1200px;margin:0 auto}
        .steps-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:2rem;margin-top:.5rem}
        .step-card{text-align:center;padding:1.5rem}
        .step-num{width:52px;height:52px;border-radius:50%;background:var(--black);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.15rem;margin:0 auto 1.25rem;transition:transform .3s}
        .step-card:hover .step-num{transform:scale(1.1)}
        .step-card h3{font-size:1rem;font-weight:700;margin-bottom:.5rem}
        .step-card p{font-size:.85rem;color:var(--gray-3);line-height:1.6}

        /* CTA */
        .cta{padding:7rem 2rem}
        .cta-box{max-width:1000px;margin:0 auto;background:var(--gray-1);border-radius:1.5rem;padding:4rem;display:flex;align-items:center;justify-content:space-between;gap:3rem;position:relative;overflow:hidden}
        .cta-box::after{content:'';position:absolute;top:-50%;right:-100px;width:400px;height:400px;border-radius:50%;border:1px solid var(--gray-2);pointer-events:none}
        .cta-text h2{font-size:2.2rem;font-weight:800;color:#fff;letter-spacing:-.03em;margin-bottom:.75rem}
        .cta-text p{font-size:1rem;color:var(--gray-5);max-width:420px;line-height:1.7}
        .cta-actions{display:flex;gap:1rem;flex-shrink:0}
        .btn-white{background:#fff;color:var(--black);border:1.5px solid #fff;font-weight:700;padding:.7rem 1.6rem;border-radius:var(--radius-sm);font-size:.9rem;text-decoration:none;transition:all .25s;display:inline-flex;align-items:center;gap:.5rem}
        .btn-white:hover{background:var(--gray-8);transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.3)}

        /* FOOTER */
        footer{border-top:1px solid var(--gray-6);padding:3rem 2rem}
        .footer-inner{max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;align-items:center}
        .footer-brand{font-weight:700;font-size:.95rem}
        footer p{font-size:.85rem;color:var(--gray-4)}

        /* RESPONSIVE */
        @media(max-width:900px){
            .hero-inner{grid-template-columns:1fr;text-align:center}
            .hero p{margin-left:auto;margin-right:auto}
            .hero-actions{justify-content:center}
            .hero-visual{margin-top:2rem}
            .hero h1{font-size:2.4rem}
            .features-grid{grid-template-columns:1fr}
            .steps-grid{grid-template-columns:1fr 1fr}
            .cta-box{flex-direction:column;text-align:center;padding:3rem 2rem}
            .cta-text p{margin:0 auto}
            .footer-inner{flex-direction:column;gap:1rem;text-align:center}
        }
        @media(max-width:600px){
            .hero h1{font-size:2rem}
            .hero-card-dark{position:relative;bottom:0;right:0;width:100%;margin-top:1rem}
            .section-title{font-size:1.8rem}
            .steps-grid{grid-template-columns:1fr}
            .nav-links{gap:1rem}
            .nav-links a:not(.btn){display:none}
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav id="navbar">
    <div class="nav-inner">
        <a href="/" class="nav-brand">
            <div class="nav-brand-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
            </div>
            Perpustakaan
        </a>
        <div class="nav-links">
            <a href="#fitur">Fitur</a>
            <a href="#cara-kerja">Cara Kerja</a>
            <a href="/login" class="btn btn-dark">Masuk</a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <div class="hero-tag anim"><span class="hero-tag-dot"></span> Sistem aktif &bull; Siap digunakan</div>
            <h1 class="anim delay-1">
                Kelola perpustakaan sekolah secara digital.
                <em>Sistem informasi modern untuk peminjaman, katalog, dan manajemen buku.</em>
            </h1>
            <p class="anim delay-2">Digitalisasi seluruh proses perpustakaan — dari katalog buku, peminjaman, pengembalian, hingga denda. Semua dalam satu platform.</p>
            <div class="hero-actions anim delay-3">
                <a href="/login" class="btn btn-dark">
                    Mulai Sekarang
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
                <a href="#fitur" class="btn btn-light">Pelajari Fitur</a>
            </div>
        </div>
        <div class="hero-visual anim-right delay-2">
            <div class="hero-card">
                <div class="stat-grid">
                    <div class="stat-item"><span class="num">2,847</span><span class="label">Total Buku</span></div>
                    <div class="stat-item"><span class="num">156</span><span class="label">Dipinjam</span></div>
                    <div class="stat-item"><span class="num">1,204</span><span class="label">Anggota</span></div>
                    <div class="stat-item"><span class="num">98%</span><span class="label">Tepat Waktu</span></div>
                </div>
                <div class="hero-card-footer"><span class="status-dot"></span><span>Sistem berjalan normal</span></div>
            </div>
            <div class="hero-card-dark">
                <div class="hcd-num">+24</div>
                <div class="hcd-label">Buku baru bulan ini</div>
            </div>
        </div>
    </div>
</section>

<!-- MARQUEE -->
<div class="marquee-section">
    <div class="marquee-track">
        <span>Katalog Digital</span><span>&bull;</span><span>Peminjaman Otomatis</span><span>&bull;</span><span>Manajemen Denda</span><span>&bull;</span>
        <span>Keamanan Berlapis</span><span>&bull;</span><span>Multi-Role Dashboard</span><span>&bull;</span><span>Konfigurasi Fleksibel</span><span>&bull;</span>
        <span>Katalog Digital</span><span>&bull;</span><span>Peminjaman Otomatis</span><span>&bull;</span><span>Manajemen Denda</span><span>&bull;</span>
        <span>Keamanan Berlapis</span><span>&bull;</span><span>Multi-Role Dashboard</span><span>&bull;</span><span>Konfigurasi Fleksibel</span><span>&bull;</span>
    </div>
</div>

<!-- FEATURES -->
<section class="features" id="fitur">
    <p class="section-label anim">Fitur Unggulan</p>
    <h2 class="section-title anim delay-1">Semua yang Anda butuhkan.</h2>
    <p class="section-desc anim delay-2">Dirancang khusus untuk kebutuhan perpustakaan sekolah dengan fitur lengkap.</p>
    <div class="features-grid">
        <div class="feature-card anim delay-1">
            <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg></div>
            <h3>Katalog Buku Digital</h3>
            <p>Kelola ribuan koleksi buku dengan kategori, rak, dan pencarian cepat. Lengkap dengan cover dan detail.</p>
        </div>
        <div class="feature-card anim delay-2">
            <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
            <h3>Peminjaman Otomatis</h3>
            <p>Siswa dapat meminjam buku secara mandiri. Sistem otomatis menghitung tenggat dan mengelola stok.</p>
        </div>
        <div class="feature-card anim delay-3">
            <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <h3>Manajemen Denda</h3>
            <p>Perhitungan denda keterlambatan otomatis. Tracking pembayaran dan laporan yang transparan.</p>
        </div>
        <div class="feature-card anim delay-1">
            <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <h3>Multi-Role Dashboard</h3>
            <p>Dashboard terpisah untuk Admin dan Siswa. Masing-masing memiliki fitur sesuai perannya.</p>
        </div>
        <div class="feature-card anim delay-2">
            <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
            <h3>Keamanan Berlapis</h3>
            <p>Sistem approval pendaftaran siswa baru. Kontrol akses ketat berbasis role dan status akun.</p>
        </div>
        <div class="feature-card anim delay-3">
            <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
            <h3>Pengaturan Fleksibel</h3>
            <p>Konfigurasi durasi peminjaman, batas buku, dan tarif denda sesuai kebijakan sekolah.</p>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-section" id="cara-kerja">
    <div class="how-inner">
        <p class="section-label anim">Cara Kerja</p>
        <h2 class="section-title anim delay-1">Empat langkah sederhana.</h2>
        <p class="section-desc anim delay-2">Proses yang dirancang sesederhana mungkin agar mudah digunakan siapa saja.</p>
        <div class="steps-grid">
            <div class="step-card anim delay-1"><div class="step-num">1</div><h3>Daftar Akun</h3><p>Siswa mendaftar melalui halaman registrasi dan menunggu approval Admin.</p></div>
            <div class="step-card anim delay-2"><div class="step-num">2</div><h3>Cari Buku</h3><p>Jelajahi katalog buku digital, filter berdasarkan kategori atau pencarian.</p></div>
            <div class="step-card anim delay-3"><div class="step-num">3</div><h3>Pinjam Buku</h3><p>Pilih buku yang tersedia dan ajukan peminjaman secara otomatis.</p></div>
            <div class="step-card anim delay-4"><div class="step-num">4</div><h3>Kembalikan</h3><p>Kembalikan buku tepat waktu. Terlambat? Denda dihitung otomatis.</p></div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="cta-box anim-scale">
        <div class="cta-text">
            <h2>Siap memulai?</h2>
            <p>Bergabunglah dan rasakan kemudahan mengelola perpustakaan sekolah secara digital.</p>
        </div>
        <div class="cta-actions">
            <a href="/login" class="btn-white">
                Masuk ke Dashboard
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <span class="footer-brand">Perpustakaan Digital</span>
        <p>&copy; {{ date('Y') }} Sistem Informasi Perpustakaan Sekolah</p>
    </div>
</footer>

<script>
    // Navbar scroll
    const nav = document.getElementById('navbar');
    window.addEventListener('scroll', () => nav.classList.toggle('scrolled', window.scrollY > 20));

    // Intersection Observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.anim, .anim-left, .anim-right, .anim-scale').forEach(el => observer.observe(el));

    // Counter animation for stat numbers
    function animateCounter(el, target, suffix = '') {
        let current = 0;
        const increment = target / 40;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) { current = target; clearInterval(timer); }
            el.textContent = Math.floor(current).toLocaleString() + suffix;
        }, 30);
    }
    const statObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                entry.target.dataset.animated = 'true';
                const text = entry.target.textContent;
                const num = parseInt(text.replace(/[^0-9]/g, ''));
                const suffix = text.includes('%') ? '%' : '';
                if (num) animateCounter(entry.target, num, suffix);
            }
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('.stat-item .num, .hcd-num').forEach(el => statObserver.observe(el));
</script>

</body>
</html>