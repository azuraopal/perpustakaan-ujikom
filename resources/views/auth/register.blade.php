<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa — Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: #fafafa; color: #111;
            min-height: 100vh; display: flex; align-items: center;
            justify-content: center; padding: 2rem;
        }

        .register-container { width: 100%; max-width: 520px; }

        .back-link {
            display: flex; align-items: center; gap: .35rem;
            text-decoration: none; color: #888; font-size: .85rem;
            font-weight: 500; margin-bottom: 2rem; transition: color .2s;
        }
        .back-link:hover { color: #111; }
        .back-link svg { width: 16px; height: 16px; }

        .register-header { text-align: center; margin-bottom: 2rem; }
        .register-icon {
            width: 56px; height: 56px; background: #111; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .register-icon svg { width: 26px; height: 26px; stroke: #fff; }
        .register-header h1 { font-size: 1.75rem; font-weight: 800; letter-spacing: -.03em; margin-bottom: .35rem; }
        .register-header p { font-size: .9rem; color: #888; }
        .register-header p a { color: #111; font-weight: 600; text-decoration: none; }
        .register-header p a:hover { text-decoration: underline; }

        /* ─── STEPPER ─── */
        .stepper {
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 2.5rem; gap: 0;
        }
        .step {
            display: flex; align-items: center; gap: .6rem;
            cursor: pointer; transition: all .2s;
        }
        .step-num {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .85rem;
            border: 2px solid #ddd; color: #aaa; background: #fff;
            transition: all .3s; flex-shrink: 0;
        }
        .step-label {
            font-size: .82rem; font-weight: 600; color: #aaa;
            transition: all .2s; white-space: nowrap;
        }
        .step.active .step-num { background: #111; color: #fff; border-color: #111; }
        .step.active .step-label { color: #111; }
        .step.done .step-num { background: #111; color: #fff; border-color: #111; }
        .step.done .step-label { color: #111; }
        .step-line {
            width: 40px; height: 2px; background: #ddd;
            margin: 0 .5rem; transition: background .3s; flex-shrink: 0;
        }
        .step-line.active { background: #111; }

        /* ─── CARD ─── */
        .register-card {
            background: #fff; border: 1px solid #e5e5e5; border-radius: 1.25rem;
            padding: 2.25rem; box-shadow: 0 20px 60px -15px rgba(0,0,0,.06);
        }

        .step-content { display: none; }
        .step-content.active { display: block; animation: fadeIn .3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        .step-title { font-size: 1.1rem; font-weight: 700; margin-bottom: .3rem; }
        .step-desc { font-size: .85rem; color: #888; margin-bottom: 1.5rem; }

        .form-group { margin-bottom: 1.15rem; }
        .form-group label {
            display: block; font-size: .82rem; font-weight: 600;
            color: #333; margin-bottom: .4rem;
        }
        .form-group input, .form-group textarea {
            width: 100%; padding: .75rem 1rem;
            border: 1.5px solid #e5e5e5; border-radius: .75rem;
            font-size: .9rem; font-family: inherit;
            background: #fafafa; color: #111;
            transition: all .2s; outline: none; resize: vertical;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: #111; background: #fff;
            box-shadow: 0 0 0 3px rgba(17,17,17,.08);
        }
        .form-group input::placeholder, .form-group textarea::placeholder { color: #bbb; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

        .form-actions {
            display: flex; gap: .75rem; margin-top: 1.5rem;
        }
        .btn {
            flex: 1; padding: .8rem; border-radius: .75rem;
            font-size: .9rem; font-weight: 700; font-family: inherit;
            cursor: pointer; transition: all .2s; border: none;
        }
        .btn-next { background: #111; color: #fff; }
        .btn-next:hover { background: #333; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,.15); }
        .btn-back { background: #fff; color: #111; border: 1.5px solid #e5e5e5; }
        .btn-back:hover { border-color: #111; background: #fafafa; }
        .btn-submit { background: #111; color: #fff; }
        .btn-submit:hover { background: #333; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,.15); }

        .error-box {
            background: #fef2f2; border: 1px solid #fecaca; border-radius: .75rem;
            padding: .85rem 1rem; margin-bottom: 1.25rem; font-size: .84rem;
            color: #b91c1c; line-height: 1.5;
        }

        .field-error { font-size: .78rem; color: #dc2626; margin-top: .35rem; }

        @media (max-width: 520px) {
            .form-row { grid-template-columns: 1fr; }
            .step-label { display: none; }
            .stepper { gap: 0; }
        }
    </style>
</head>
<body>

<div class="register-container">
    <a href="/login" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Kembali ke Login
    </a>

    <div class="register-header">
        <div class="register-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
        </div>
        <h1>Daftar Siswa</h1>
        <p>atau <a href="/login">masuk ke akun Anda</a></p>
    </div>

    <!-- STEPPER -->
    <div class="stepper">
        <div class="step active" data-step="1">
            <div class="step-num">1</div>
            <span class="step-label">Data Diri</span>
        </div>
        <div class="step-line"></div>
        <div class="step" data-step="2">
            <div class="step-num">2</div>
            <span class="step-label">Kontak</span>
        </div>
        <div class="step-line"></div>
        <div class="step" data-step="3">
            <div class="step-num">3</div>
            <span class="step-label">Keamanan</span>
        </div>
    </div>

    <div class="register-card">
        @if($errors->any())
            <div class="error-box">
                <strong>Terdapat kesalahan:</strong><br>
                @foreach($errors->all() as $error)
                    • {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form action="/register" method="POST" id="registerForm">
            @csrf

            <!-- STEP 1: Data Diri -->
            <div class="step-content active" data-step="1">
                <p class="step-title">Data Diri</p>
                <p class="step-desc">Masukkan informasi identitas Anda sebagai siswa.</p>

                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap *</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Masukkan nama lengkap">
                    @error('nama_lengkap') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nomor_induk">Nomor Induk (NIS)</label>
                        <input type="text" id="nomor_induk" name="nomor_induk" value="{{ old('nomor_induk') }}" placeholder="Contoh: 24100123">
                        @error('nomor_induk') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="kelas">Kelas</label>
                        <input type="text" id="kelas" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: XII RPL 1">
                        @error('kelas') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-next" onclick="goStep(2)">Lanjut</button>
                </div>
            </div>

            <!-- STEP 2: Kontak -->
            <div class="step-content" data-step="2">
                <p class="step-title">Informasi Kontak</p>
                <p class="step-desc">Data kontak untuk akses login dan komunikasi.</p>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com">
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="no_telepon">No. Telepon</label>
                    <input type="tel" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" placeholder="08xxxxxxxxxx">
                    @error('no_telepon') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap Anda">{{ old('alamat') }}</textarea>
                    @error('alamat') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-back" onclick="goStep(1)">Kembali</button>
                    <button type="button" class="btn btn-next" onclick="goStep(3)">Lanjut</button>
                </div>
            </div>

            <!-- STEP 3: Keamanan -->
            <div class="step-content" data-step="3">
                <p class="step-title">Keamanan Akun</p>
                <p class="step-desc">Buat password yang kuat untuk mengamankan akun Anda.</p>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter">
                    @error('password') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password Anda">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-back" onclick="goStep(2)">Kembali</button>
                    <button type="submit" class="btn btn-submit">Daftar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function goStep(step) {
        // Validate current step before proceeding
        const currentStep = document.querySelector('.step-content.active');
        const currentStepNum = parseInt(currentStep.dataset.step);

        if (step > currentStepNum) {
            const requiredFields = currentStep.querySelectorAll('input[required], textarea[required]');
            for (const field of requiredFields) {
                if (!field.value.trim()) {
                    field.focus();
                    field.style.borderColor = '#dc2626';
                    field.style.boxShadow = '0 0 0 3px rgba(220,38,38,.1)';
                    setTimeout(() => {
                        field.style.borderColor = '#e5e5e5';
                        field.style.boxShadow = 'none';
                    }, 2000);
                    return;
                }
            }
        }

        // Hide all steps
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.stepper .step').forEach(el => el.classList.remove('active', 'done'));
        document.querySelectorAll('.step-line').forEach(el => el.classList.remove('active'));

        // Show target step
        document.querySelector(`.step-content[data-step="${step}"]`).classList.add('active');

        // Update stepper indicators
        document.querySelectorAll('.stepper .step').forEach(el => {
            const s = parseInt(el.dataset.step);
            if (s < step) el.classList.add('done');
            if (s === step) el.classList.add('active');
        });

        // Update step lines
        const lines = document.querySelectorAll('.step-line');
        lines.forEach((line, i) => {
            if (i < step - 1) line.classList.add('active');
        });
    }
</script>

</body>
</html>
