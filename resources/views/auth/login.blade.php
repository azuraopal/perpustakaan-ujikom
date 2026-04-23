<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: #fafafa;
            color: #111;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container { width: 100%; max-width: 420px; }

        .login-header { text-align: center; margin-bottom: 2.5rem; }

        .login-icon {
            width: 56px; height: 56px; background: #111; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .login-icon svg { width: 26px; height: 26px; stroke: #fff; }

        .login-header h1 { font-size: 1.75rem; font-weight: 800; letter-spacing: -.03em; margin-bottom: .35rem; }
        .login-header p { font-size: .9rem; color: #888; }

        .login-card {
            background: #fff; border: 1px solid #e5e5e5; border-radius: 1.25rem;
            padding: 2.25rem; box-shadow: 0 20px 60px -15px rgba(0,0,0,.06);
        }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block; font-size: .82rem; font-weight: 600;
            color: #333; margin-bottom: .45rem; letter-spacing: .01em;
        }
        .form-group input {
            width: 100%; padding: .75rem 1rem;
            border: 1.5px solid #e5e5e5; border-radius: .75rem;
            font-size: .9rem; font-family: inherit;
            background: #fafafa; color: #111;
            transition: all .2s; outline: none;
        }
        .form-group input:focus {
            border-color: #111; background: #fff;
            box-shadow: 0 0 0 3px rgba(17,17,17,.08);
        }
        .form-group input::placeholder { color: #bbb; }

        .btn-login {
            width: 100%; padding: .85rem; background: #111; color: #fff;
            border: none; border-radius: .75rem; font-size: .9rem;
            font-weight: 700; font-family: inherit; cursor: pointer;
            transition: all .2s; margin-top: .5rem; letter-spacing: .01em;
        }
        .btn-login:hover { background: #333; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,.15); }

        .error-box {
            background: #fef2f2; border: 1px solid #fecaca; border-radius: .75rem;
            padding: .85rem 1rem; margin-bottom: 1.25rem; font-size: .84rem;
            color: #b91c1c; line-height: 1.5;
        }

        .success-box {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: .75rem;
            padding: .85rem 1rem; margin-bottom: 1.25rem; font-size: .84rem;
            color: #15803d; line-height: 1.5;
        }

        .login-footer { text-align: center; margin-top: 1.75rem; font-size: .85rem; color: #999; }
        .login-footer a { color: #111; font-weight: 600; text-decoration: none; }
        .login-footer a:hover { text-decoration: underline; }

        .back-link {
            display: flex; align-items: center; gap: .35rem;
            text-decoration: none; color: #888; font-size: .85rem;
            font-weight: 500; margin-bottom: 2rem; transition: color .2s;
        }
        .back-link:hover { color: #111; }
        .back-link svg { width: 16px; height: 16px; }

        .btn-register {
            display: block; width: 100%; padding: .75rem; margin-top: .75rem;
            background: #fff; color: #111; border: 1.5px solid #e5e5e5;
            border-radius: .75rem; font-size: .9rem; font-weight: 600;
            font-family: inherit; cursor: pointer; text-align: center;
            text-decoration: none; transition: all .2s;
        }
        .btn-register:hover { border-color: #111; background: #fafafa; }
    </style>
</head>
<body>

<div class="login-container">
    <a href="/" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Kembali ke Beranda
    </a>

    <div class="login-header">
        <div class="login-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/>
            </svg>
        </div>
        <h1>Masuk</h1>
        <p>Masukkan kredensial akun Anda</p>
    </div>

    <div class="login-card">
        @if(session('success'))
            <div class="success-box">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="error-box">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <a href="/register" class="btn-register">Daftar sebagai Siswa</a>
    </div>
</div>

</body>
</html>
