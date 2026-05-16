@extends('layouts.app_nasabah_operator')
@php $mode = 'operator'; $title = 'Laporan Keuangan'; $subtitle = 'Laporan volume sampah dan keuangan'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ route('operator.dashboard') }}">Dashboard</a> <span>/</span> Laporan</div>
  <h1>Laporan Keuangan & Operasional</h1>
  <p>Ringkasan laporan volume sampah, transaksi, dan keuangan bank sampah.</p>
</div>

{{-- FILTER --}}
<div class="card mb-20">
  <form method="GET" action="{{ route('operator.laporan') }}" class="flex items-center gap-12" style="flex-wrap:wrap;">
    <div class="form-group" style="margin:0; flex:1; min-width:150px;">
      <label class="form-label">Dari Tanggal</label>
      <input type="date" name="dari" class="form-control" value="{{ $dari }}" />
    </div>
    <div class="form-group" style="margin:0; flex:1; min-width:150px;">
      <label class="form-label">Sampai Tanggal</label>
      <input type="date" name="sampai" class="form-control" value="{{ $sampai }}" />
    </div>
    <div style="padding-top:22px;">
      <button type="submit" class="btn btn-primary">🔍 Tampilkan</button>
    </div>
  </form>
</div>

{{-- RINGKASAN --}}
<div class="grid grid-4 mb-20">
  <div class="stat-card">
    <div class="stat-icon green">⚖️</div>
    <div><div class="stat-label">Total Sampah Masuk</div><div class="stat-value">{{ number_format($totalMasuk, 2) }} kg</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">🏭</div>
    <div><div class="stat-label">Sampah Terjual</div><div class="stat-value">{{ number_format($totalTerjual, 2) }} kg</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber">💰</div>
    <div><div class="stat-label">Pendapatan Penjualan</div><div class="stat-value">Rp {{ number_format($pendapatan / 1000, 1) }} Rb</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red">💸</div>
    <div><div class="stat-label">Dibayar ke Nasabah</div><div class="stat-value">Rp {{ number_format($dibayarNasabah / 1000, 1) }} Rb</div></div>
  </div>
</div>

{{-- CHARTS --}}
<div class="grid grid-2 mb-20">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Volume Sampah Masuk vs Keluar</div>
      <div class="card-subtitle">Per minggu (kg)</div>
    </div>
    <div class="chart-container" style="height:260px;">
      <canvas id="chartMasukKeluar"></canvas>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <div class="card-title">Arus Keuangan</div>
      <div class="card-subtitle">Pendapatan vs Pengeluaran (Rp)</div>
    </div>
    <div class="chart-container" style="height:260px;">
      <canvas id="chartKeuangan"></canvas>
    </div>
  </div>
</div>

{{-- REKAP PER KATEGORI --}}
<div class="grid grid-2 mb-20">
  <div class="card">
    <div class="card-title mb-16">📊 Rekap Setoran per Kategori</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Kategori</th><th>Total Berat</th><th>Total Nilai</th></tr></thead>
        <tbody>
          @forelse($rekapKategori as $r)
          <tr>
            <td><span style="font-size:18px;">{{ $r->ikon }}</span> {{ $r->nama }}</td>
            <td class="fw-600">{{ number_format($r->total_berat, 2) }} kg</td>
            <td class="fw-700 text-green">Rp {{ number_format($r->total_nilai, 0, ',', '.') }}</td>
          </tr>
          @empty
          <tr><td colspan="3" class="empty-state">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-title mb-16">🏭 Rekap Penjualan per Kategori</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Kategori</th><th>Total Berat</th><th>Total Pendapatan</th></tr></thead>
        <tbody>
          @forelse($rekapPenjualan as $r)
          <tr>
            <td><span style="font-size:18px;">{{ $r->ikon }}</span> {{ $r->nama }}</td>
            <td class="fw-600">{{ number_format($r->total_berat, 2) }} kg</td>
            <td class="fw-700 text-green">Rp {{ number_format($r->total_pendapatan, 0, ',', '.') }}</td>
          </tr>
          @empty
          <tr><td colspan="3" class="empty-state">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- TABEL TRANSAKSI --}}
<div class="card">
  <div class="card-header">
    <div class="card-title">Rekap Transaksi Setoran</div>
    <div class="card-subtitle">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} – {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</div>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Tanggal</th><th>Kode</th><th>Nasabah</th><th>Berat (kg)</th><th>Nilai (Rp)</th></tr>
      </thead>
      <tbody>
        @forelse($setoranList as $s)
        <tr>
          <td class="text-muted text-sm">{{ $s->created_at->format('d M Y') }}</td>
          <td class="fw-600 text-green">{{ $s->kode_setoran }}</td>
          <td class="td-name">{{ $s->nasabah->nama }}</td>
          <td class="fw-600">{{ number_format($s->total_berat, 2) }}</td>
          <td class="fw-700 text-green">Rp {{ number_format($s->total_nilai, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty-state">Tidak ada transaksi dalam periode ini.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-16">{{ $setoranList->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
Chart.defaults.color = '#6b7280';

new Chart(document.getElementById('chartMasukKeluar'), {
  type: 'bar',
  data: {
    labels: @json($chartLabels),
    datasets: [
      { label: 'Masuk (kg)', data: @json($chartMasuk), backgroundColor: 'rgba(34,135,79,.8)', borderRadius: 6 },
      { label: 'Keluar (kg)', data: @json($chartKeluar), backgroundColor: 'rgba(59,130,246,.8)', borderRadius: 6 }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'top', labels: { boxWidth: 10, padding: 16, font: { size: 12 } } } },
    scales: {
      x: { grid: { display: false }, border: { display: false } },
      y: { grid: { color: '#f3f4f6' }, border: { display: false }, ticks: { callback: v => v + ' kg' } }
    }
  }
});

new Chart(document.getElementById('chartKeuangan'), {
  type: 'line',
  data: {
    labels: @json($chartLabels),
    datasets: [
      { label: 'Pendapatan', data: @json($chartPemasukan), borderColor: '#22874f', backgroundColor: 'rgba(34,135,79,.08)', borderWidth: 2.5, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#22874f' },
      { label: 'Pengeluaran', data: @json($chartPengeluaran), borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,.06)', borderWidth: 2.5, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#ef4444' }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'top', labels: { boxWidth: 10, padding: 16, font: { size: 12 } } } },
    scales: {
      x: { grid: { display: false }, border: { display: false } },
      y: { grid: { color: '#f3f4f6' }, border: { display: false }, ticks: { callback: v => 'Rp ' + (v/1000) + 'rb' } }
    }
  }
});
</script>
@endpush
