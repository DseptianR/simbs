<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Daftar Nasabah – SIM Bank Sampah</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green-700: #155230;
      --green-600: #1a6b3e;
      --green-500: #22874f;
      --green-100: #d4f5e3;
      --green-50:  #f0fdf6;
      --gray-900:  #111827;
      --gray-700:  #374151;
      --gray-600:  #4b5563;
      --gray-500:  #6b7280;
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
      padding: 32px 20px;
    }
    .card {
      background: var(--white);
      border-radius: 20px;
      padding: 40px 36px;
      width: 100%;
      max-width: 520px;
      box-shadow: 0 20px 60px rgba(0,0,0,.25);
    }
    .logo-wrap { text-align: center; margin-bottom: 28px; }
    .logo-icon {
      width: 64px; height: 64px;
      background: var(--green-100);
      border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
      font-size: 30px;
      margin: 0 auto 12px;
    }
    .logo-title { font-size: 19px; font-weight: 800; color: var(--gray-900); }
    .logo-sub   { font-size: 13px; color: var(--gray-500); margin-top: 3px; }
    .badge {
      display: inline-block;
      background: var(--green-100); color: var(--green-700);
      font-size: 11px; font-weight: 700;
      padding: 3px 10px; border-radius: 20px;
      margin-top: 8px;
      text-transform: uppercase; letter-spacing: .5px;
    }
    .section-title {
      font-size: 13px; font-weight: 700; color: var(--gray-500);
      text-transform: uppercase; letter-spacing: .5px;
      margin: 20px 0 12px;
      padding-bottom: 8px;
      border-bottom: 1px solid var(--gray-200);
    }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 5px; }
    .form-label span { color: var(--red); }
    .form-control {
      width: 100%; padding: 10px 14px;
      border: 1.5px solid var(--gray-200);
      border-radius: 10px;
      font-size: 14px; color: var(--gray-900);
      font-family: inherit;
      transition: border-color .15s, box-shadow .15s;
    }
    .form-control:focus { outline: none; border-color: var(--green-500); box-shadow: 0 0 0 3px rgba(34,135,79,.12); }
    .form-control.is-invalid { border-color: var(--red); }
    .form-hint { font-size: 12px; color: var(--gray-500); margin-top: 4px; }
    textarea.form-control { resize: vertical; min-height: 72px; }
    .pin-input {
      letter-spacing: 8px; text-align: center;
      font-size: 20px; font-weight: 700;
    }
    .btn-submit {
      width: 100%; padding: 13px;
      background: var(--green-700); color: #fff;
      border: none; border-radius: 10px;
      font-size: 15px; font-weight: 700;
      cursor: pointer; transition: background .15s;
      font-family: inherit; margin-top: 8px;
    }
    .btn-submit:hover { background: var(--green-600); }
    .alert-danger {
      background: var(--red-light); border: 1px solid #fca5a5; color: var(--red);
      padding: 11px 14px; border-radius: 10px;
      font-size: 13.5px; margin-bottom: 18px;
    }
    .divider { height: 1px; background: var(--gray-200); margin: 22px 0; }
    .links-row { text-align: center; font-size: 13px; color: var(--gray-500); }
    .links-row a { color: var(--green-600); font-weight: 600; text-decoration: none; }
    .links-row a:hover { text-decoration: underline; }
    .info-box {
      background: var(--green-50); border: 1px solid var(--green-100);
      border-radius: 10px; padding: 12px 14px;
      font-size: 12.5px; color: var(--green-700);
      margin-bottom: 20px;
    }
    @media (max-width: 480px) {
      .card { padding: 28px 20px; }
      .grid-2 { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo-wrap">
      <div class="logo-icon">♻️</div>
      <div class="logo-title">SIM Bank Sampah</div>
      <div class="logo-sub">Sistem Informasi Manajemen</div>
      <span class="badge">Daftar Nasabah Baru</span>
    </div>

    @if ($errors->any())
      <div class="alert-danger">⚠️ {{ $errors->first() }}</div>
    @endif

    <div class="info-box">
      📋 Setelah mendaftar, nomor rekening akan digenerate otomatis dan Anda langsung bisa masuk ke portal nasabah.
    </div>

    <form method="POST" action="{{ route('nasabah.register.post') }}">
      @csrf

      {{-- DATA DIRI --}}
      <div class="section-title">Data Diri</div>

      <div class="form-group">
        <label class="form-label" for="nama">Nama Lengkap <span>*</span></label>
        <input type="text" class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}"
          id="nama" name="nama" value="{{ old('nama') }}"
          placeholder="Nama sesuai KTP" required autofocus />
      </div>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label" for="no_hp">Nomor HP <span>*</span></label>
          <input type="tel" class="form-control {{ $errors->has('no_hp') ? 'is-invalid' : '' }}"
            id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
            placeholder="08xx-xxxx-xxxx" required />
        </div>
        <div class="form-group">
          <label class="form-label" for="nik">NIK (KTP)</label>
          <input type="text" class="form-control {{ $errors->has('nik') ? 'is-invalid' : '' }}"
            id="nik" name="nik" value="{{ old('nik') }}"
            placeholder="16 digit NIK" maxlength="16" inputmode="numeric" />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="alamat">Alamat Lengkap</label>
        <textarea class="form-control" id="alamat" name="alamat"
          placeholder="Jl. nama jalan, RT/RW, Kelurahan, Kecamatan...">{{ old('alamat') }}</textarea>
      </div>

      {{-- KEAMANAN --}}
      <div class="section-title">Keamanan Akun</div>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label" for="pin">PIN <span>*</span></label>
          <input type="password" class="form-control pin-input {{ $errors->has('pin') ? 'is-invalid' : '' }}"
            id="pin" name="pin"
            placeholder="••••••" maxlength="6" inputmode="numeric"
            pattern="\d{6}" required />
          <div class="form-hint">6 digit angka</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="pin_confirmation">Konfirmasi PIN <span>*</span></label>
          <input type="password" class="form-control pin-input {{ $errors->has('pin_confirmation') ? 'is-invalid' : '' }}"
            id="pin_confirmation" name="pin_confirmation"
            placeholder="••••••" maxlength="6" inputmode="numeric"
            pattern="\d{6}" required />
          <div class="form-hint" id="pinHint"></div>
        </div>
      </div>

      <button type="submit" class="btn-submit">
        ✓ Daftar Sekarang →
      </button>
    </form>

    <div class="divider"></div>

    <div class="links-row">
      Sudah punya akun? <a href="{{ route('nasabah.login') }}">Login di sini</a>
      &nbsp;·&nbsp;
      <a href="{{ url('/') }}">← Beranda</a>
    </div>
  </div>

  <script>
    // Validasi PIN match secara real-time
    const pinEl    = document.getElementById('pin');
    const konfEl   = document.getElementById('pin_confirmation');
    const hintEl   = document.getElementById('pinHint');

    function cekPin() {
      const p = pinEl.value;
      const k = konfEl.value;
      if (!k) { hintEl.textContent = ''; return; }
      if (p === k) {
        hintEl.textContent = '✓ PIN cocok';
        hintEl.style.color = '#22874f';
        konfEl.style.borderColor = '#22874f';
      } else {
        hintEl.textContent = '✗ PIN tidak cocok';
        hintEl.style.color = '#ef4444';
        konfEl.style.borderColor = '#ef4444';
      }
    }

    pinEl.addEventListener('input', cekPin);
    konfEl.addEventListener('input', cekPin);
  </script>
</body>
</html>
