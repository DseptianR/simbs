@extends('layouts.app_nasabah_operator')
@php $mode = 'nasabah'; $title = 'Saldo Saya'; $subtitle = 'Kelola saldo tabungan sampah'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ route('nasabah.dashboard') }}">Beranda</a> <span>/</span> Saldo Saya</div>
  <h1>Saldo Tabungan Sampah</h1>
  <p>Kelola saldo tabungan dan ajukan penarikan.</p>
</div>

@if(session('success'))
  <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
@endif

<div class="grid grid-2-1">
  {{-- SALDO & MUTASI --}}
  <div style="display:flex; flex-direction:column; gap:16px;">

    {{-- Saldo Card --}}
    <div style="background: linear-gradient(135deg, #0f2d1e, #22874f); border-radius: 20px; padding: 28px 32px; color: #fff;">
      <div style="font-size:12px; color:rgba(255,255,255,.7); text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px;">Saldo Tersedia</div>
      <div style="font-size:44px; font-weight:900; line-height:1;">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</div>
      <div style="margin-top:16px; padding-top:16px; border-top:1px solid rgba(255,255,255,.15); display:flex; gap:24px;">
        <div>
          <div style="font-size:11px; color:rgba(255,255,255,.6);">Total Masuk</div>
          <div style="font-size:16px; font-weight:700; color:#4dc97e;">+Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
        </div>
        <div>
          <div style="font-size:11px; color:rgba(255,255,255,.6);">Total Keluar</div>
          <div style="font-size:16px; font-weight:700; color:#fca5a5;">-Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
        </div>
      </div>
    </div>

    {{-- Riwayat Mutasi --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">Riwayat Mutasi Saldo</div>
      </div>
      <div style="display:flex; flex-direction:column; gap:2px;">
        @forelse($mutasi as $m)
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--gray-100);">
          <div class="flex items-center gap-12">
            <div style="width:36px;height:36px;background:{{ $m['tipe'] === 'masuk' ? 'var(--green-100)' : ($m['tipe'] === 'keluar' ? 'var(--red-light)' : 'var(--accent-light)') }};border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;">
              {{ $m['ikon'] }}
            </div>
            <div>
              <div class="fw-600" style="font-size:13.5px;">{{ $m['keterangan'] }}</div>
              <div class="text-xs text-muted">{{ $m['detail'] }} · {{ \Carbon\Carbon::parse($m['tanggal'])->format('d M Y, H:i') }}</div>
            </div>
          </div>
          @if($m['tipe'] === 'masuk')
            <span class="fw-700 text-green">+Rp {{ number_format($m['jumlah'], 0, ',', '.') }}</span>
          @elseif($m['tipe'] === 'keluar')
            <span class="fw-700 text-red">-Rp {{ number_format($m['jumlah'], 0, ',', '.') }}</span>
          @else
            <span class="fw-600 text-muted">Rp {{ number_format($m['jumlah'], 0, ',', '.') }} <span class="badge badge-amber" style="font-size:10px;">Pending</span></span>
          @endif
        </div>
        @empty
        <div class="empty-state"><div class="empty-icon">📋</div><p>Belum ada riwayat mutasi.</p></div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- FORM PENARIKAN --}}
  <div style="display:flex; flex-direction:column; gap:16px;">
    <div class="card">
      <div class="card-title mb-16">💸 Ajukan Penarikan Saldo</div>

      @if($errors->any())
        <div class="alert alert-danger">⚠️ {{ $errors->first() }}</div>
      @endif

      @if($penarikanPending)
        <div class="alert alert-warning">
          ⏳ Anda memiliki permintaan penarikan <strong>Rp {{ number_format($penarikanPending->jumlah, 0, ',', '.') }}</strong> yang sedang diproses. Tunggu hingga selesai sebelum mengajukan yang baru.
        </div>
      @else

      <div style="background:var(--green-50); border:1px solid var(--green-200); border-radius:8px; padding:12px 14px; margin-bottom:16px;">
        <div class="text-xs text-muted">Saldo Tersedia</div>
        <div class="fw-800" style="font-size:20px; color:var(--green-700);">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</div>
      </div>

      <form method="POST" action="{{ route('nasabah.saldo.tarik') }}">
        @csrf

        <div class="form-group">
          <label class="form-label">Jumlah Penarikan (Rp) <span style="color:var(--red)">*</span></label>
          <div class="input-group">
            <span class="input-addon">Rp</span>
            <input type="number" class="form-control" name="jumlah" id="jumlahTarik"
              value="{{ old('jumlah') }}"
              placeholder="0" min="10000" max="{{ $nasabah->saldo }}" step="1000"
              oninput="cekSaldo()"
              style="border-radius:0 var(--radius-sm) var(--radius-sm) 0; border-left:none;"
              required />
          </div>
          <div class="form-hint" id="sisaSaldo">Minimal penarikan Rp 10.000</div>
        </div>

        <div class="form-group">
          <label class="form-label">Catatan (opsional)</label>
          <input type="text" class="form-control" name="catatan" value="{{ old('catatan') }}" placeholder="Keperluan penarikan..." />
        </div>

        <div class="form-group">
          <label class="form-label">PIN Konfirmasi <span style="color:var(--red)">*</span></label>
          <input type="password" class="form-control" name="pin" placeholder="••••••" maxlength="6"
            inputmode="numeric"
            style="letter-spacing:6px; text-align:center; font-size:18px;"
            required />
          @error('pin') <div class="form-hint" style="color:var(--red)">{{ $message }}</div> @enderror
        </div>

        <div style="background:var(--accent-light); border:1px solid #fcd34d; border-radius:8px; padding:12px 14px; margin-bottom:16px; font-size:12.5px; color:#92400e;">
          ⚠️ Penarikan akan diproses oleh operator dalam 1×24 jam kerja.
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">✓ Ajukan Penarikan</button>
      </form>
      @endif
    </div>

    {{-- Status Penarikan Terakhir --}}
    @if($penarikanPending)
    <div class="card">
      <div class="card-title mb-16">📋 Status Penarikan</div>
      <div style="padding:14px; background:var(--accent-light); border:1px solid #fcd34d; border-radius:10px;">
        <div class="flex items-center justify-between mb-8">
          <span class="fw-600" style="font-size:13.5px;">Penarikan Saldo</span>
          <span class="badge badge-amber">⏳ Menunggu</span>
        </div>
        <div class="fw-800" style="font-size:20px; color:var(--gray-900);">Rp {{ number_format($penarikanPending->jumlah, 0, ',', '.') }}</div>
        <div class="text-xs text-muted mt-4">Diajukan: {{ $penarikanPending->created_at->format('d M Y, H:i') }}</div>
        @if($penarikanPending->catatan_nasabah)
          <div class="text-xs text-muted mt-4">Catatan: {{ $penarikanPending->catatan_nasabah }}</div>
        @endif
      </div>
    </div>
    @endif

    {{-- Riwayat penarikan selesai --}}
    @php
      $riwayatPenarikan = $nasabah->penarikan()->whereIn('status',['disetujui','ditolak'])->latest()->take(5)->get();
    @endphp
    @if($riwayatPenarikan->isNotEmpty())
    <div class="card">
      <div class="card-title mb-16">📜 Riwayat Penarikan</div>
      <div style="display:flex; flex-direction:column; gap:8px;">
        @foreach($riwayatPenarikan as $p)
        <div class="flex items-center justify-between" style="padding:10px 12px; background:var(--gray-50); border-radius:8px;">
          <div>
            <div class="fw-600 text-sm">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</div>
            <div class="text-xs text-muted">{{ $p->created_at->format('d M Y') }}</div>
          </div>
          @if($p->status === 'disetujui')
            <span class="badge badge-green">✓ Cair</span>
          @else
            <span class="badge badge-red">✗ Ditolak</span>
          @endif
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
const saldoTersedia = {{ $nasabah->saldo }};

function cekSaldo() {
  const jumlah = parseInt(document.getElementById('jumlahTarik').value) || 0;
  const sisa   = saldoTersedia - jumlah;
  const hint   = document.getElementById('sisaSaldo');
  if (jumlah > saldoTersedia) {
    hint.textContent = '⚠️ Jumlah melebihi saldo tersedia!';
    hint.style.color = 'var(--red)';
  } else if (jumlah >= 10000) {
    hint.textContent = 'Sisa saldo setelah penarikan: Rp ' + sisa.toLocaleString('id-ID');
    hint.style.color = 'var(--green-600)';
  } else {
    hint.textContent = 'Minimal penarikan Rp 10.000';
    hint.style.color = '';
  }
}
</script>
@endpush
