<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Nasabah – SIM Bank Sampah</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green-700: #155230;
      --green-600: #1a6b3e;
      --green-500: #22874f;
      --green-200: #a3e6be;
      --green-100: #d4f5e3;
      --green-50:  #f0fdf6;
      --gray-900:  #111827;
      --gray-700:  #374151;
      --gray-600:  #4b5563;
      --gray-500:  #6b7280;
      --gray-300:  #d1d5db;
      --gray-200:  #e5e7eb;
      --gray-100:  #f3f4f6;
      --red:       #ef4444;
      --red-light: #fee2e2;
      --white:     #ffffff;
    }
    body {
      font-family: 'Inter', system-ui, sans-serif;
      background: linear-gradient(135deg, var(--green-700) 0%, #0a2218 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .login-card {
      background: var(--white);
      border-radius: 20px;
      padding: 40px 36px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0,0,0,.25);
    }
    .logo-wrap { text-align: center; margin-bottom: 28px; }
    .logo-icon {
      width: 72px; height: 72px;
      background: var(--green-100);
      border-radius: 18px;
      display: flex; align-items: center; justify-content: center;
      font-size: 36px;
      margin: 0 auto 14px;
    }
    .logo-title  { font-size: 20px; font-weight: 800; color: var(--gray-900); }
    .logo-sub    { font-size: 13px; color: var(--gray-500); margin-top: 4px; }
    .badge-role  {
      display: inline-block;
      background: var(--green-100); color: var(--green-700);
      font-size: 11px; font-weight: 700;
      padding: 3px 10px; border-radius: 20px;
      margin-top: 8px;
      text-transform: uppercase; letter-spacing: .5px;
    }
    .form-group  { margin-bottom: 18px; }
    .form-label  { display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 6px; }
    .form-control {
      width: 100%; padding: 11px 14px;
      border: 1.5px solid var(--gray-200);
      border-radius: 10px;
      font-size: 14px; color: var(--gray-900);
      font-family: inherit;
      transition: border-color .15s, box-shadow .15s;
    }
    .form-control:focus { outline: none; border-color: var(--green-500); box-shadow: 0 0 0 3px rgba(34,135,79,.12); }
    .form-control.is-invalid { border-color: var(--red); }
    .btn-login {
      width: 100%; padding: 13px;
      background: var(--green-700); color: #fff;
      border: none; border-radius: 10px;
      font-size: 15px; font-weight: 700;
      cursor: pointer; transition: background .15s;
      font-family: inherit;
    }
    .btn-login:hover { background: var(--green-600); }
    .alert-danger {
      background: var(--red-light); border: 1px solid #fca5a5; color: var(--red);
      padding: 11px 14px; border-radius: 10px;
      font-size: 13.5px; margin-bottom: 18px;
    }
    .divider { height: 1px; background: var(--gray-200); margin: 22px 0; }
    .links-row {
      display: flex; align-items: center; justify-content: space-between;
      font-size: 13px;
    }
    .links-row a { color: var(--green-600); font-weight: 600; text-decoration: none; }
    .links-row a:hover { text-decoration: underline; }
    .links-row .sep { color: var(--gray-300); }
    .operator-link {
      display: block; text-align: center; margin-top: 16px;
      font-size: 12.5px; color: var(--gray-500);
    }
    .operator-link a { color: var(--gray-600); font-weight: 600; text-decoration: none; }
    .operator-link a:hover { color: var(--green-700); }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="logo-wrap">
      <div class="logo-icon">♻️</div>
      <div class="logo-title">SIM Bank Sampah</div>
      <div class="logo-sub">Sistem Informasi Manajemen</div>
      <span class="badge-role">Portal Nasabah</span>
    </div>

    @if ($errors->any())
      <div class="alert-danger">⚠️ {{ $errors->first() }}</div>
    @endif
    @if (session('error'))
      <div class="alert-danger">⚠️ {{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('nasabah.login.post') }}">
      @csrf

      <div class="form-group">
        <label class="form-label" for="no_rekening">Nomor Rekening</label>
        <input
          type="text"
          class="form-control {{ $errors->has('no_rekening') ? 'is-invalid' : '' }}"
          id="no_rekening"
          name="no_rekening"
          placeholder="Contoh: BS-001"
          value="{{ old('no_rekening') }}"
          required
          autocomplete="username"
          autofocus
        />
      </div>

      <div class="form-group">
        <label class="form-label" for="pin">PIN (6 digit)</label>
        <input
          type="password"
          class="form-control {{ $errors->has('pin') ? 'is-invalid' : '' }}"
          id="pin"
          name="pin"
          placeholder="••••••"
          maxlength="6"
          inputmode="numeric"
          pattern="\d{6}"
          required
          autocomplete="current-password"
          style="letter-spacing:8px; text-align:center; font-size:18px; font-weight:700;"
        />
      </div>

      <button type="submit" class="btn-login" style="margin-top:4px;">
        Masuk ke Portal →
      </button>
    </form>

    <div class="divider"></div>

    <div class="links-row">
      <a href="{{ url('/') }}">← Kembali ke Beranda</a>
      <span class="sep">|</span>
      <a href="{{ url('/') }}#daftar">Belum punya akun? Daftar</a>
    </div>

    <div class="operator-link">
      Anda operator? <a href="{{ route('operator.login') }}">Login sebagai Operator →</a>
    </div>
  </div>
</body>
</html>
