@extends('layouts.app_nasabah_operator')
@php $mode = 'nasabah'; $title = 'Profil Saya'; $subtitle = 'Informasi akun nasabah'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ route('nasabah.dashboard') }}">Beranda</a> <span>/</span> Profil Saya</div>
  <h1>Profil Nasabah</h1>
  <p>Kelola informasi pribadi dan keamanan akun Anda.</p>
</div>

@if(session('success'))
  <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('success_pin'))
  <div class="alert alert-success">🔐 {{ session('success_pin') }}</div>
@endif

<div class="grid grid-2">
  {{-- PROFIL --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">Informasi Pribadi</div>
      <button class="btn btn-secondary btn-sm" id="btnEdit" onclick="toggleEdit()">✏️ Edit</button>
    </div>

    <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding:16px; background:var(--green-50); border-radius:12px; border:1px solid var(--green-200);">
      <div style="width:64px; height:64px; background:var(--green-600); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800; color:#fff; flex-shrink:0;">
        {{ strtoupper(substr($nasabah->nama, 0, 2)) }}
      </div>
      <div>
        <div style="font-size:18px; font-weight:800; color:var(--gray-900);">{{ $nasabah->nama }}</div>
        <div style="font-size:13px; color:var(--green-600); font-weight:600;">No. Rek: {{ $nasabah->no_rekening }}</div>
        <div style="font-size:12px; color:var(--gray-500); margin-top:2px;">Bergabung: {{ $nasabah->created_at->translatedFormat('F Y') }}</div>
      </div>
    </div>

    @if($errors->has('nama') || $errors->has('no_hp'))
      <div class="alert alert-danger">⚠️ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('nasabah.profil.update') }}" id="formProfil">
      @csrf

      <div class="form-group">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" name="nama" id="fNama"
          value="{{ old('nama', $nasabah->nama) }}" readonly />
      </div>
      <div class="form-group">
        <label class="form-label">Nomor Rekening</label>
        <input type="text" class="form-control" value="{{ $nasabah->no_rekening }}" readonly
          style="background:var(--gray-50); color:var(--gray-500);" />
        <div class="form-hint">Nomor rekening tidak dapat diubah.</div>
      </div>
      <div class="form-group">
        <label class="form-label">Nomor HP</label>
        <input type="tel" class="form-control" name="no_hp" id="fHp"
          value="{{ old('no_hp', $nasabah->no_hp) }}" readonly />
      </div>
      <div class="form-group">
        <label class="form-label">NIK (KTP)</label>
        <input type="text" class="form-control" value="{{ $nasabah->nik ?? '-' }}" readonly
          style="background:var(--gray-50); color:var(--gray-500);" />
        <div class="form-hint">NIK hanya dapat diubah oleh operator.</div>
      </div>
      <div class="form-group">
        <label class="form-label">Alamat</label>
        <textarea class="form-control" name="alamat" id="fAlamat" rows="3" readonly>{{ old('alamat', $nasabah->alamat) }}</textarea>
      </div>

      <div id="editActions" style="display:none;">
        <div class="flex gap-8">
          <button type="submit" class="btn btn-primary" style="flex:1;">✓ Simpan Perubahan</button>
          <button type="button" class="btn btn-secondary" onclick="batalEdit()">Batal</button>
        </div>
      </div>
    </form>
  </div>

  {{-- KEAMANAN + STATISTIK --}}
  <div style="display:flex; flex-direction:column; gap:16px;">

    {{-- Ganti PIN --}}
    <div class="card">
      <div class="card-title mb-16">🔐 Ganti PIN</div>

      @if($errors->has('pin_lama') || $errors->has('pin_baru') || $errors->has('pin_konfirm'))
        <div class="alert alert-danger">⚠️ {{ $errors->first('pin_lama') ?? $errors->first('pin_baru') ?? $errors->first('pin_konfirm') }}</div>
      @endif

      <form method="POST" action="{{ route('nasabah.profil.ganti-pin') }}">
        @csrf
        <div class="form-group">
          <label class="form-label">PIN Lama <span style="color:var(--red)">*</span></label>
          <input type="password" class="form-control" name="pin_lama" placeholder="••••••"
            maxlength="6" inputmode="numeric"
            style="letter-spacing:6px; text-align:center; font-size:18px;" required />
        </div>
        <div class="form-group">
          <label class="form-label">PIN Baru <span style="color:var(--red)">*</span></label>
          <input type="password" class="form-control" name="pin_baru" placeholder="••••••"
            maxlength="6" inputmode="numeric"
            style="letter-spacing:6px; text-align:center; font-size:18px;" required />
        </div>
        <div class="form-group">
          <label class="form-label">Konfirmasi PIN Baru <span style="color:var(--red)">*</span></label>
          <input type="password" class="form-control" name="pin_konfirm" placeholder="••••••"
            maxlength="6" inputmode="numeric"
            style="letter-spacing:6px; text-align:center; font-size:18px;" required />
        </div>
        <button type="submit" class="btn btn-primary btn-block">🔐 Ganti PIN</button>
      </form>
    </div>

    {{-- Statistik Akun --}}
    <div class="card">
      <div class="card-title mb-16">📊 Statistik Akun</div>
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div class="flex items-center justify-between" style="padding:10px 14px; background:var(--gray-50); border-radius:8px;">
          <span class="text-sm">Status Akun</span>
          @if($nasabah->is_active)
            <span class="badge badge-green">✓ Aktif</span>
          @else
            <span class="badge badge-amber">Tidak Aktif</span>
          @endif
        </div>
        <div class="flex items-center justify-between" style="padding:10px 14px; background:var(--gray-50); border-radius:8px;">
          <span class="text-sm">Saldo Saat Ini</span>
          <span class="fw-700 text-green">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between" style="padding:10px 14px; background:var(--gray-50); border-radius:8px;">
          <span class="text-sm">Total Setoran</span>
          <span class="fw-700">{{ $jumlahSetor }} kali</span>
        </div>
        <div class="flex items-center justify-between" style="padding:10px 14px; background:var(--gray-50); border-radius:8px;">
          <span class="text-sm">Total Berat</span>
          <span class="fw-700">{{ number_format($totalBerat, 2) }} kg</span>
        </div>
        <div class="flex items-center justify-between" style="padding:10px 14px; background:var(--gray-50); border-radius:8px;">
          <span class="text-sm">Bergabung Sejak</span>
          <span class="fw-600">{{ $nasabah->created_at->translatedFormat('F Y') }}</span>
        </div>
        <div class="flex items-center justify-between" style="padding:10px 14px; background:var(--gray-50); border-radius:8px;">
          <span class="text-sm">Setoran Terakhir</span>
          <span class="fw-600">{{ $setoranTerakhir ? $setoranTerakhir->created_at->format('d M Y') : '-' }}</span>
        </div>
      </div>
    </div>

    {{-- Logout --}}
    <form method="POST" action="{{ route('nasabah.logout') }}">
      @csrf
      <button type="submit" class="btn btn-danger btn-block">🚪 Keluar dari Akun</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
const editableFields = ['fNama', 'fHp', 'fAlamat'];

function toggleEdit() {
  editableFields.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.removeAttribute('readonly');
      el.style.borderColor = 'var(--green-500)';
      el.style.boxShadow   = '0 0 0 3px rgba(34,135,79,.12)';
    }
  });
  document.getElementById('editActions').style.display = 'block';
  document.getElementById('btnEdit').style.display = 'none';
}

function batalEdit() {
  editableFields.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.setAttribute('readonly', '');
      el.style.borderColor = '';
      el.style.boxShadow   = '';
    }
  });
  document.getElementById('editActions').style.display = 'none';
  document.getElementById('btnEdit').style.display = '';
}
</script>
@endpush
