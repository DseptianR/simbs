@extends('layouts.app_nasabah_operator')
@php $mode = 'operator'; $title = 'Inventaris & Penjualan'; $subtitle = 'Stok gudang dan penjualan ke pengepul'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ route('operator.dashboard') }}">Dashboard</a> <span>/</span> Inventaris & Penjualan</div>
  <h1>Inventaris & Penjualan ke Pengepul</h1>
  <p>Pantau stok gudang dan catat penjualan sampah ke pengepul besar.</p>
</div>

@if(session('success'))
  <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
@endif

{{-- STOK GUDANG --}}
<div class="card mb-20">
  <div class="card-header">
    <div>
      <div class="card-title">📦 Stok Gudang Saat Ini</div>
      <div class="card-subtitle">Total sampah yang belum dijual ke pengepul</div>
    </div>
    <span class="badge badge-blue">{{ number_format($inventaris->sum('stok'), 1) }} kg total</span>
  </div>
  <div class="grid grid-4" style="gap:12px; margin-top:4px;">
    @forelse($inventaris as $inv)
    @php
      $maxStok = 200;
      $pct = min(100, ($inv->stok / $maxStok) * 100);
      $colors = ['green' => '#22874f', 'blue' => '#3b82f6', 'amber' => '#f59e0b', 'red' => '#ef4444', 'purple' => '#8b5cf6'];
      $colorKeys = array_keys($colors);
      $colorKey = $colorKeys[$loop->index % count($colorKeys)];
      $colorHex = $colors[$colorKey];
    @endphp
    <div style="background:var(--gray-50); border:1px solid var(--gray-200); border-radius:10px; padding:16px; text-align:center;">
      <div style="font-size:28px; margin-bottom:6px;">{{ $inv->kategori->ikon }}</div>
      <div class="fw-700" style="font-size:20px; color:{{ $colorHex }};">{{ number_format($inv->stok, 1) }} kg</div>
      <div class="text-sm text-muted">{{ $inv->kategori->nama }}</div>
      <div class="progress mt-8"><div class="progress-bar {{ $colorKey }}" style="width:{{ $pct }}%"></div></div>
    </div>
    @empty
    <div class="col-span-4 empty-state"><p>Belum ada data inventaris.</p></div>
    @endforelse
  </div>
</div>

<div class="grid grid-2-1">
  {{-- RIWAYAT PENJUALAN --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">Riwayat Penjualan ke Pengepul</div>
      <form method="GET" action="{{ route('operator.inventaris-penjualan') }}" class="flex gap-8">
        <input type="month" name="bulan" class="form-control" value="{{ request('bulan', date('Y-m')) }}" style="width:auto; padding:7px 12px; font-size:13px;" />
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      </form>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Tanggal</th><th>Pengepul</th><th>Kategori</th><th>Berat</th><th>Harga/kg</th><th>Total</th></tr>
        </thead>
        <tbody>
          @forelse($penjualan as $p)
          <tr>
            <td class="text-muted text-sm">{{ $p->created_at->format('d M Y') }}</td>
            <td class="td-name">{{ $p->nama_pengepul }}</td>
            <td><span class="badge badge-green">{{ $p->kategori->ikon }} {{ $p->kategori->nama }}</span></td>
            <td class="fw-600">{{ number_format($p->berat, 1) }} kg</td>
            <td>Rp {{ number_format($p->harga_jual_per_kg, 0, ',', '.') }}</td>
            <td class="fw-700 text-green">Rp {{ number_format($p->total_pendapatan, 0, ',', '.') }}</td>
          </tr>
          @empty
          <tr><td colspan="6" class="empty-state"><div class="empty-icon">🏭</div><p>Belum ada penjualan bulan ini.</p></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="flex items-center justify-between mt-12" style="padding:12px 0 0;">
      <div>{{ $penjualan->links() }}</div>
      <div style="background:var(--green-50); border:1px solid var(--green-200); border-radius:8px; padding:10px 16px; display:flex; justify-content:space-between; align-items:center; gap:20px;">
        <span class="fw-600" style="font-size:13.5px;">Total Pendapatan Bulan Ini</span>
        <span class="fw-800" style="font-size:18px; color:var(--green-700);">Rp {{ number_format($totalPendapatanBulanIni, 0, ',', '.') }}</span>
      </div>
    </div>
  </div>

  {{-- FORM CATAT PENJUALAN --}}
  <div class="card">
    <div class="card-title mb-16">🏭 Catat Penjualan Baru</div>

    @if($errors->any())
      <div class="alert alert-danger">⚠️ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('operator.inventaris-penjualan.store') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Nama Pengepul <span style="color:var(--red)">*</span></label>
        <input type="text" class="form-control" name="nama_pengepul" value="{{ old('nama_pengepul') }}" placeholder="Nama perusahaan/pengepul..." required />
      </div>
      <div class="form-group">
        <label class="form-label">Tanggal Penjualan</label>
        <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required />
      </div>
      <div class="form-group">
        <label class="form-label">Kategori Sampah <span style="color:var(--red)">*</span></label>
        <select class="form-control" name="kategori_id" id="selKategori" onchange="updateStok()" required>
          <option value="">Pilih kategori...</option>
          @foreach($kategori as $kat)
          <option value="{{ $kat->id }}"
            data-stok="{{ $kat->inventaris->stok ?? 0 }}"
            data-harga="{{ $kat->harga_per_satuan }}"
            {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
            {{ $kat->ikon }} {{ $kat->nama }} (Stok: {{ number_format($kat->inventaris->stok ?? 0, 1) }} kg)
          </option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Stok Tersedia</label>
        <input type="text" class="form-control" id="stokInfo" placeholder="Pilih kategori dulu..." readonly />
      </div>
      <div class="form-group">
        <label class="form-label">Berat Terjual (kg) <span style="color:var(--red)">*</span></label>
        <input type="number" class="form-control" name="berat" id="beratJual" value="{{ old('berat') }}" placeholder="0.00" step="0.01" min="0.01" oninput="hitungPenjualan()" required />
      </div>
      <div class="form-group">
        <label class="form-label">Harga Jual per kg (Rp) <span style="color:var(--red)">*</span></label>
        <input type="number" class="form-control" name="harga_jual_per_kg" id="hargaJual" value="{{ old('harga_jual_per_kg') }}" placeholder="0" min="1" oninput="hitungPenjualan()" required />
      </div>
      <div class="form-group">
        <label class="form-label">Catatan</label>
        <input type="text" class="form-control" name="catatan" value="{{ old('catatan') }}" placeholder="Opsional..." />
      </div>
      <div style="background:var(--green-50); border:1px solid var(--green-200); border-radius:8px; padding:12px 16px; margin-bottom:16px;">
        <div class="text-xs text-muted">Total Pendapatan</div>
        <div id="totalJual" class="fw-800" style="font-size:20px; color:var(--green-700);">Rp 0</div>
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg">✓ Simpan Penjualan</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function updateStok() {
  const sel  = document.getElementById('selKategori');
  const opt  = sel.options[sel.selectedIndex];
  const stok = opt.dataset.stok || 0;
  const harga= opt.dataset.harga || 0;
  document.getElementById('stokInfo').value  = stok ? parseFloat(stok).toFixed(1) + ' kg tersedia' : '';
  document.getElementById('hargaJual').value = harga || '';
  hitungPenjualan();
}
function hitungPenjualan() {
  const berat = parseFloat(document.getElementById('beratJual').value) || 0;
  const harga = parseFloat(document.getElementById('hargaJual').value) || 0;
  document.getElementById('totalJual').textContent = 'Rp ' + (berat * harga).toLocaleString('id-ID');
}
</script>
@endpush
