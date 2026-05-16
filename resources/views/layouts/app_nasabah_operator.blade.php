<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $title ?? 'SIM Bank Sampah' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --green-900: #0a2218;
      --green-800: #0f2d1e;
      --green-700: #155230;
      --green-600: #1a6b3e;
      --green-500: #22874f;
      --green-400: #2da862;
      --green-300: #4dc97e;
      --green-200: #a3e6be;
      --green-100: #d4f5e3;
      --green-50:  #f0fdf6;
      --accent:    #f59e0b;
      --accent-light: #fef3c7;
      --red:       #ef4444;
      --red-light: #fee2e2;
      --blue:      #3b82f6;
      --blue-light: #dbeafe;
      --gray-900:  #111827;
      --gray-800:  #1f2937;
      --gray-700:  #374151;
      --gray-600:  #4b5563;
      --gray-500:  #6b7280;
      --gray-400:  #9ca3af;
      --gray-300:  #d1d5db;
      --gray-200:  #e5e7eb;
      --gray-100:  #f3f4f6;
      --gray-50:   #f9fafb;
      --white:     #ffffff;
      --sidebar-w: 260px;
      --topbar-h:  64px;
      --radius-sm: 8px;
      --radius:    12px;
      --radius-lg: 16px;
      --radius-xl: 20px;
      --shadow-sm: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
      --shadow:    0 4px 12px rgba(0,0,0,.08), 0 2px 4px rgba(0,0,0,.04);
      --shadow-lg: 0 10px 30px rgba(0,0,0,.10), 0 4px 8px rgba(0,0,0,.06);
    }

    html { font-size: 15px; }
    body {
      font-family: 'Inter', system-ui, sans-serif;
      background: var(--gray-50);
      color: var(--gray-800);
      line-height: 1.6;
      min-height: 100vh;
    }

    a { color: inherit; text-decoration: none; }

    /* ── SIDEBAR ── */
    .sidebar {
      position: fixed; top: 0; left: 0;
      width: var(--sidebar-w); height: 100vh;
      background: var(--green-800);
      display: flex; flex-direction: column;
      z-index: 100;
      overflow-y: auto;
      transition: transform .3s ease;
    }
    .sidebar-logo {
      padding: 24px 20px 20px;
      border-bottom: 1px solid rgba(255,255,255,.08);
    }
    .sidebar-logo .logo-icon {
      width: 40px; height: 40px;
      background: var(--green-400);
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; margin-bottom: 10px;
    }
    .sidebar-logo .logo-title {
      font-size: 15px; font-weight: 700; color: #fff;
      line-height: 1.3;
    }
    .sidebar-logo .logo-sub {
      font-size: 11px; color: var(--green-200);
      margin-top: 2px;
    }
    .sidebar-section {
      padding: 16px 12px 4px;
      font-size: 10px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 1px;
      color: var(--green-300);
    }
    .sidebar-nav { padding: 0 8px; flex: 1; }
    .nav-item {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 12px;
      border-radius: var(--radius-sm);
      color: rgba(255,255,255,.7);
      font-size: 13.5px; font-weight: 500;
      cursor: pointer;
      transition: all .15s;
      margin-bottom: 2px;
    }
    .nav-item:hover { background: rgba(255,255,255,.08); color: #fff; }
    .nav-item.active { background: var(--green-600); color: #fff; }
    .nav-item .nav-icon { font-size: 16px; width: 20px; text-align: center; flex-shrink: 0; }
    .nav-item .nav-badge {
      margin-left: auto;
      background: var(--accent);
      color: #fff;
      font-size: 10px; font-weight: 700;
      padding: 2px 6px; border-radius: 20px;
    }
    .sidebar-footer {
      padding: 16px 12px;
      border-top: 1px solid rgba(255,255,255,.08);
    }
    .sidebar-user {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 12px;
      border-radius: var(--radius-sm);
      color: rgba(255,255,255,.8);
      font-size: 13px;
    }
    .sidebar-user .avatar {
      width: 34px; height: 34px;
      background: var(--green-500);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 13px; color: #fff;
      flex-shrink: 0;
    }
    .sidebar-user .user-name { font-weight: 600; font-size: 13px; color: #fff; }
    .sidebar-user .user-role { font-size: 11px; color: var(--green-200); }

    /* ── TOPBAR ── */
    .topbar {
      position: fixed; top: 0;
      left: var(--sidebar-w); right: 0;
      height: var(--topbar-h);
      background: var(--white);
      border-bottom: 1px solid var(--gray-200);
      display: flex; align-items: center;
      padding: 0 24px;
      gap: 16px;
      z-index: 90;
      box-shadow: var(--shadow-sm);
    }
    .topbar-title { font-size: 17px; font-weight: 700; color: var(--gray-900); flex: 1; }
    .topbar-subtitle { font-size: 12px; color: var(--gray-500); font-weight: 400; }
    .topbar-actions { display: flex; align-items: center; gap: 10px; }
    .topbar-btn {
      width: 38px; height: 38px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--gray-200);
      background: var(--white);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 16px;
      transition: all .15s;
      position: relative;
    }
    .topbar-btn:hover { background: var(--gray-50); border-color: var(--gray-300); }
    .topbar-btn .notif-dot {
      position: absolute; top: 6px; right: 6px;
      width: 8px; height: 8px;
      background: var(--red); border-radius: 50%;
      border: 2px solid #fff;
    }
    .topbar-date {
      font-size: 12px; color: var(--gray-500);
      background: var(--gray-100);
      padding: 6px 12px; border-radius: 20px;
    }

    /* ── MAIN CONTENT ── */
    .main-wrap {
      margin-left: var(--sidebar-w);
      padding-top: var(--topbar-h);
      min-height: 100vh;
    }
    .page-content {
      padding: 28px 28px;
      max-width: 1400px;
    }

    /* ── CARDS ── */
    .card {
      background: var(--white);
      border: 1px solid var(--gray-200);
      border-radius: var(--radius-lg);
      padding: 20px;
      box-shadow: var(--shadow-sm);
    }
    .card-header {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 16px;
    }
    .card-title { font-size: 15px; font-weight: 700; color: var(--gray-900); }
    .card-subtitle { font-size: 12px; color: var(--gray-500); margin-top: 2px; }

    /* ── STAT CARDS ── */
    .stat-card {
      background: var(--white);
      border: 1px solid var(--gray-200);
      border-radius: var(--radius-lg);
      padding: 20px 22px;
      box-shadow: var(--shadow-sm);
      display: flex; align-items: flex-start; gap: 14px;
      transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: var(--shadow); transform: translateY(-1px); }
    .stat-icon {
      width: 48px; height: 48px;
      border-radius: var(--radius);
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; flex-shrink: 0;
    }
    .stat-icon.green  { background: var(--green-100); }
    .stat-icon.amber  { background: var(--accent-light); }
    .stat-icon.blue   { background: var(--blue-light); }
    .stat-icon.red    { background: var(--red-light); }
    .stat-label { font-size: 12px; color: var(--gray-500); font-weight: 500; text-transform: uppercase; letter-spacing: .5px; }
    .stat-value { font-size: 26px; font-weight: 800; color: var(--gray-900); line-height: 1.2; margin-top: 4px; }
    .stat-change { font-size: 12px; margin-top: 4px; display: flex; align-items: center; gap: 4px; }
    .stat-change.up   { color: var(--green-500); }
    .stat-change.down { color: var(--red); }

    /* ── GRID ── */
    .grid { display: grid; gap: 20px; }
    .grid-4 { grid-template-columns: repeat(4, 1fr); }
    .grid-3 { grid-template-columns: repeat(3, 1fr); }
    .grid-2 { grid-template-columns: repeat(2, 1fr); }
    .grid-2-1 { grid-template-columns: 2fr 1fr; }
    .grid-1-2 { grid-template-columns: 1fr 2fr; }
    .col-span-2 { grid-column: span 2; }
    .col-span-3 { grid-column: span 3; }

    /* ── BUTTONS ── */
    .btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 6px;
      padding: 9px 16px;
      border-radius: var(--radius-sm);
      font-size: 13.5px; font-weight: 600;
      cursor: pointer; border: none;
      transition: all .15s;
      white-space: nowrap;
    }
    .btn-primary { background: var(--green-700); color: #fff; }
    .btn-primary:hover { background: var(--green-600); }
    .btn-secondary { background: var(--gray-100); color: var(--gray-700); border: 1px solid var(--gray-200); }
    .btn-secondary:hover { background: var(--gray-200); }
    .btn-danger { background: var(--red-light); color: var(--red); }
    .btn-danger:hover { background: #fca5a5; }
    .btn-success { background: var(--green-100); color: var(--green-700); }
    .btn-success:hover { background: var(--green-200); }
    .btn-amber { background: var(--accent-light); color: #92400e; }
    .btn-amber:hover { background: #fde68a; }
    .btn-sm { padding: 6px 12px; font-size: 12px; }
    .btn-lg { padding: 12px 24px; font-size: 15px; }
    .btn-block { width: 100%; }
    .btn-outline { background: transparent; border: 1.5px solid var(--green-700); color: var(--green-700); }
    .btn-outline:hover { background: var(--green-50); }

    /* ── FORMS ── */
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 6px; }
    .form-control {
      width: 100%; padding: 10px 14px;
      border: 1.5px solid var(--gray-200);
      border-radius: var(--radius-sm);
      font-size: 14px; color: var(--gray-800);
      background: var(--white);
      transition: border-color .15s, box-shadow .15s;
      font-family: inherit;
    }
    .form-control:focus { outline: none; border-color: var(--green-500); box-shadow: 0 0 0 3px rgba(34,135,79,.12); }
    .form-control[readonly] { background: var(--gray-50); color: var(--gray-600); }
    textarea.form-control { resize: vertical; min-height: 80px; }
    .form-hint { font-size: 12px; color: var(--gray-500); margin-top: 4px; }
    .input-group { display: flex; }
    .input-group .form-control { border-radius: var(--radius-sm) 0 0 var(--radius-sm); }
    .input-group .input-addon {
      padding: 10px 14px;
      background: var(--gray-100);
      border: 1.5px solid var(--gray-200);
      border-left: none;
      border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
      font-size: 13px; color: var(--gray-600); font-weight: 600;
      white-space: nowrap;
    }

    /* ── TABLES ── */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead th {
      padding: 10px 14px;
      font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .6px;
      color: var(--gray-500);
      background: var(--gray-50);
      border-bottom: 1px solid var(--gray-200);
      white-space: nowrap;
    }
    tbody td {
      padding: 12px 14px;
      font-size: 13.5px;
      border-bottom: 1px solid var(--gray-100);
      color: var(--gray-700);
    }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: var(--gray-50); }
    .td-name { font-weight: 600; color: var(--gray-900); }
    .empty-state { text-align: center; padding: 40px 20px; color: var(--gray-400); }
    .empty-state .empty-icon { font-size: 40px; margin-bottom: 10px; }
    .empty-state p { font-size: 14px; }

    /* ── BADGES ── */
    .badge {
      display: inline-flex; align-items: center; gap: 4px;
      padding: 3px 10px; border-radius: 20px;
      font-size: 11.5px; font-weight: 600;
    }
    .badge-green  { background: var(--green-100); color: var(--green-700); }
    .badge-amber  { background: var(--accent-light); color: #92400e; }
    .badge-red    { background: var(--red-light); color: var(--red); }
    .badge-blue   { background: var(--blue-light); color: #1d4ed8; }
    .badge-gray   { background: var(--gray-100); color: var(--gray-600); }

    /* ── CHART CONTAINER ── */
    .chart-container { position: relative; }

    /* ── PROGRESS BAR ── */
    .progress { height: 8px; background: var(--gray-100); border-radius: 20px; overflow: hidden; }
    .progress-bar { height: 100%; border-radius: 20px; transition: width .6s ease; }
    .progress-bar.green { background: var(--green-500); }
    .progress-bar.amber { background: var(--accent); }
    .progress-bar.blue  { background: var(--blue); }
    .progress-bar.red   { background: var(--red); }

    /* ── DIVIDER ── */
    .divider { height: 1px; background: var(--gray-200); margin: 16px 0; }

    /* ── ALERT ── */
    .alert {
      padding: 12px 16px; border-radius: var(--radius-sm);
      font-size: 13.5px; display: flex; align-items: flex-start; gap: 10px;
      margin-bottom: 16px;
    }
    .alert-success { background: var(--green-50); border: 1px solid var(--green-200); color: var(--green-700); }
    .alert-warning { background: var(--accent-light); border: 1px solid #fcd34d; color: #92400e; }
    .alert-danger  { background: var(--red-light); border: 1px solid #fca5a5; color: var(--red); }

    /* ── AVATAR ── */
    .avatar-sm {
      width: 32px; height: 32px; border-radius: 50%;
      background: var(--green-200); color: var(--green-800);
      display: inline-flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 700; flex-shrink: 0;
    }

    /* ── MISC ── */
    .text-muted { color: var(--gray-500); }
    .text-sm { font-size: 12.5px; }
    .text-xs { font-size: 11px; }
    .text-green { color: var(--green-600); }
    .text-red { color: var(--red); }
    .text-amber { color: #d97706; }
    .fw-600 { font-weight: 600; }
    .fw-700 { font-weight: 700; }
    .mt-4 { margin-top: 4px; }
    .mt-8 { margin-top: 8px; }
    .mt-16 { margin-top: 16px; }
    .mt-20 { margin-top: 20px; }
    .mb-4 { margin-bottom: 4px; }
    .mb-8 { margin-bottom: 8px; }
    .mb-16 { margin-bottom: 16px; }
    .mb-20 { margin-bottom: 20px; }
    .flex { display: flex; }
    .flex-col { flex-direction: column; }
    .items-center { align-items: center; }
    .items-start { align-items: flex-start; }
    .justify-between { justify-content: space-between; }
    .justify-end { justify-content: flex-end; }
    .gap-8 { gap: 8px; }
    .gap-12 { gap: 12px; }
    .gap-16 { gap: 16px; }
    .gap-20 { gap: 20px; }
    .w-full { width: 100%; }
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 22px; font-weight: 800; color: var(--gray-900); }
    .page-header p { font-size: 13.5px; color: var(--gray-500); margin-top: 4px; }
    .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--gray-500); margin-bottom: 8px; }
    .breadcrumb a { color: var(--green-600); }
    .breadcrumb span { color: var(--gray-400); }

    /* ── RESPONSIVE ── */
    @media (max-width: 1024px) {
      .grid-4 { grid-template-columns: repeat(2, 1fr); }
      .grid-3 { grid-template-columns: repeat(2, 1fr); }
      .grid-2-1, .grid-1-2 { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
      .main-wrap { margin-left: 0; }
      .topbar { left: 0; }
      .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
      .page-content { padding: 20px 16px; }
    }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--gray-400); }
  </style>
  @stack('styles')
</head>
<body>

  {{-- SIDEBAR --}}
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">♻️</div>
      <div class="logo-title">SIM Bank Sampah</div>
      <div class="logo-sub">Sistem Informasi Manajemen</div>
    </div>

    @if(($mode ?? 'operator') === 'operator')
    <div class="sidebar-nav">
      <div class="sidebar-section">Utama</div>
      <a href="{{ url('/operator/dashboard') }}" class="nav-item {{ request()->is('operator/dashboard') ? 'active' : '' }}">
        <span class="nav-icon">📊</span> Dashboard
      </a>

      <div class="sidebar-section">Transaksi</div>
      <a href="{{ url('/operator/transaksi-setoran') }}" class="nav-item {{ request()->is('operator/transaksi-setoran') ? 'active' : '' }}">
        <span class="nav-icon">⚖️</span> Input Setoran
      </a>
      <a href="{{ url('/operator/validasi-penarikan') }}" class="nav-item {{ request()->is('operator/validasi-penarikan') ? 'active' : '' }}">
        <span class="nav-icon">💸</span> Validasi Penarikan
        @php $pendingCount = \App\Models\Penarikan::where('status', 'pending')->count(); @endphp
        @if($pendingCount > 0)
          <span class="nav-badge">{{ $pendingCount }}</span>
        @endif
      </a>

      <div class="sidebar-section">Manajemen</div>
      <a href="{{ url('/operator/manajemen-nasabah') }}" class="nav-item {{ request()->is('operator/manajemen-nasabah') ? 'active' : '' }}">
        <span class="nav-icon">👥</span> Manajemen Nasabah
      </a>
      <a href="{{ url('/operator/manajemen-harga') }}" class="nav-item {{ request()->is('operator/manajemen-harga') ? 'active' : '' }}">
        <span class="nav-icon">🏷️</span> Harga Sampah
      </a>
      <a href="{{ url('/operator/inventaris-penjualan') }}" class="nav-item {{ request()->is('operator/inventaris-penjualan') ? 'active' : '' }}">
        <span class="nav-icon">🏭</span> Inventaris & Penjualan
      </a>

      <div class="sidebar-section">Laporan</div>
      <a href="{{ url('/operator/laporan') }}" class="nav-item {{ request()->is('operator/laporan') ? 'active' : '' }}">
        <span class="nav-icon">📋</span> Laporan Keuangan
      </a>
      <a href="{{ url('/operator/pesan-masuk') }}" class="nav-item {{ request()->is('operator/pesan-masuk') ? 'active' : '' }}">
        <span class="nav-icon">✉️</span> Pesan Masuk
        @php $pesanBaru = \App\Models\PesanKontak::where('sudah_dibaca', false)->count(); @endphp
        @if($pesanBaru > 0)
          <span class="nav-badge">{{ $pesanBaru }}</span>
        @endif
      </a>
    </div>
    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'O', 0, 1)) }}</div>
        <div>
          <div class="user-name">{{ auth()->user()->name ?? 'Operator' }}</div>
          <div class="user-role">Operator Bank Sampah</div>
        </div>
      </div>
      <form method="POST" action="{{ route('operator.logout') }}" style="margin-top:4px;">
        @csrf
        <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
          <span class="nav-icon">🚪</span> Keluar
        </button>
      </form>
    </div>

    @else
    <div class="sidebar-nav">
      <div class="sidebar-section">Portal Nasabah</div>
      <a href="{{ url('/nasabah/dashboard') }}" class="nav-item {{ request()->is('nasabah/dashboard') ? 'active' : '' }}">
        <span class="nav-icon">🏠</span> Beranda
      </a>
      <a href="{{ url('/nasabah/saldo') }}" class="nav-item {{ request()->is('nasabah/saldo') ? 'active' : '' }}">
        <span class="nav-icon">💰</span> Saldo Saya
      </a>
      <a href="{{ url('/nasabah/histori-setoran') }}" class="nav-item {{ request()->is('nasabah/histori-setoran') ? 'active' : '' }}">
        <span class="nav-icon">📜</span> Histori Setoran
      </a>
      <a href="{{ url('/nasabah/profil') }}" class="nav-item {{ request()->is('nasabah/profil') ? 'active' : '' }}">
        <span class="nav-icon">👤</span> Profil Saya
      </a>
    </div>
    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="avatar">{{ strtoupper(substr(auth('nasabah')->user()->nama ?? 'N', 0, 1)) }}</div>
        <div>
          <div class="user-name">{{ auth('nasabah')->user()->nama ?? 'Nasabah' }}</div>
          <div class="user-role">No. Rek: {{ auth('nasabah')->user()->no_rekening ?? '-' }}</div>
        </div>
      </div>
      <form method="POST" action="{{ route('nasabah.logout') }}" style="margin-top:4px;">
        @csrf
        <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
          <span class="nav-icon">🚪</span> Keluar
        </button>
      </form>
    </div>
    @endif
  </aside>

  {{-- TOPBAR --}}
  <div class="topbar">
    <div>
      <div class="topbar-title">{{ $title ?? 'SIM Bank Sampah' }}</div>
      @isset($subtitle)
      <div class="topbar-subtitle">{{ $subtitle }}</div>
      @endisset
    </div>
    <div class="topbar-actions">
      <div class="topbar-date">📅 {{ now()->translatedFormat('d F Y') }}</div>

      {{-- LONCENG NOTIFIKASI --}}
      @php
        $isNasabah = (($mode ?? 'operator') === 'nasabah');
        if ($isNasabah && auth('nasabah')->check()) {
            $notifCount = \App\Models\Notifikasi::untukNasabah(auth('nasabah')->id())->belumDibaca()->count();
            $notifTerbaru = \App\Models\Notifikasi::untukNasabah(auth('nasabah')->id())->latest()->take(5)->get();
            $notifUrl = route('nasabah.notifikasi');
            $notifBacaSemuaUrl = route('nasabah.notifikasi.baca-semua');
        } elseif (!$isNasabah && auth()->check()) {
            $notifCount = \App\Models\Notifikasi::untukOperator(auth()->id())->belumDibaca()->count();
            $notifTerbaru = \App\Models\Notifikasi::untukOperator(auth()->id())->latest()->take(5)->get();
            $notifUrl = route('operator.notifikasi');
            $notifBacaSemuaUrl = route('operator.notifikasi.baca-semua');
        } else {
            $notifCount = 0; $notifTerbaru = collect(); $notifUrl = '#'; $notifBacaSemuaUrl = '#';
        }
      @endphp

      <div style="position:relative;" id="notifWrapper">
        <button onclick="toggleNotif()" class="topbar-btn" id="notifBtn" style="position:relative;">
          🔔
          @if($notifCount > 0)
            <span class="notif-dot" style="background:var(--red);"></span>
          @endif
        </button>

        {{-- DROPDOWN --}}
        <div id="notifDropdown" style="display:none; position:absolute; right:0; top:calc(100% + 8px); width:340px; background:#fff; border:1px solid var(--gray-200); border-radius:14px; box-shadow:var(--shadow-lg); z-index:999; overflow:hidden;">
          {{-- Header --}}
          <div style="padding:14px 16px; border-bottom:1px solid var(--gray-100); display:flex; align-items:center; justify-content:space-between;">
            <div>
              <div style="font-size:14px; font-weight:700; color:var(--gray-900);">Notifikasi</div>
              @if($notifCount > 0)
                <div style="font-size:12px; color:var(--green-600);">{{ $notifCount }} belum dibaca</div>
              @else
                <div style="font-size:12px; color:var(--gray-500);">Semua sudah dibaca</div>
              @endif
            </div>
            @if($notifCount > 0)
            <form method="POST" action="{{ $notifBacaSemuaUrl }}">
              @csrf
              <button type="submit" style="font-size:12px; color:var(--green-600); font-weight:600; background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:6px;">
                Baca Semua
              </button>
            </form>
            @endif
          </div>

          {{-- List --}}
          <div style="max-height:320px; overflow-y:auto;">
            @forelse($notifTerbaru as $n)
            <div style="display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-bottom:1px solid var(--gray-50); background:{{ $n->sudahDibaca() ? '#fff' : 'var(--green-50)' }};">
              <div style="width:36px; height:36px; background:{{ $n->warnaTipe() }}; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                {{ $n->ikon }}
              </div>
              <div style="flex:1; min-width:0;">
                <div style="font-size:13px; font-weight:{{ $n->sudahDibaca() ? '500' : '700' }}; color:var(--gray-900); line-height:1.3;">
                  {{ $n->judul }}
                </div>
                <div style="font-size:12px; color:var(--gray-500); margin-top:2px; line-height:1.4; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                  {{ $n->pesan }}
                </div>
                <div style="font-size:11px; color:var(--gray-400); margin-top:4px;">{{ $n->created_at->diffForHumans() }}</div>
              </div>
              @if(!$n->sudahDibaca())
              <form method="POST" action="{{ $isNasabah ? route('nasabah.notifikasi.baca', $n) : route('operator.notifikasi.baca', $n) }}" style="flex-shrink:0;">
                @csrf
                <button type="submit" style="width:8px; height:8px; background:var(--green-500); border-radius:50%; border:none; cursor:pointer; margin-top:6px;" title="Tandai dibaca"></button>
              </form>
              @endif
            </div>
            @empty
            <div style="padding:32px 16px; text-align:center; color:var(--gray-400);">
              <div style="font-size:32px; margin-bottom:8px;">🔔</div>
              <div style="font-size:13px;">Tidak ada notifikasi</div>
            </div>
            @endforelse
          </div>

          {{-- Footer --}}
          <div style="padding:10px 16px; border-top:1px solid var(--gray-100); text-align:center;">
            <a href="{{ $notifUrl }}" style="font-size:13px; color:var(--green-600); font-weight:600;">
              Lihat Semua Notifikasi →
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- MAIN --}}
  <div class="main-wrap">
    <div class="page-content">
      @yield('content')
    </div>
  </div>

  <script>
    // Sidebar toggle for mobile
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', () => {
          if (window.innerWidth <= 768) sidebar.classList.remove('open');
        });
      });
    });

    // Notifikasi dropdown
    function toggleNotif() {
      const dd = document.getElementById('notifDropdown');
      dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
    }

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function(e) {
      const wrapper = document.getElementById('notifWrapper');
      if (wrapper && !wrapper.contains(e.target)) {
        const dd = document.getElementById('notifDropdown');
        if (dd) dd.style.display = 'none';
      }
    });
  </script>
@stack('scripts')
</body>
</html>



