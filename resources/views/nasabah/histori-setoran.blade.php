@extends('layouts.app_nasabah_operator')
@php $mode = 'nasabah'; $title = 'Histori Setoran'; $subtitle = 'Riwayat setoran sampah Anda'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ route('nasabah.dashboard') }}">Beranda</a> <span>/</span> Histori Setoran</div>
  <h1>Histori Setoran Sampah</h1>
  <p>Riwayat lengkap setoran sampah Anda.</p>
</div>

{{-- RINGKASAN --}}
<div class="grid grid-4 mb-20">
  <div class="stat-card">
    <div class="stat-icon green">⚖️</div>
    <div><div class="stat-label">Total Berat</div><div class="stat-value">{{ number_format($totalBerat, 1) }} kg</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">📦</div>
    <div><div class="stat-label">Jumlah Setoran</div><div class="stat-value">{{ $jumlahSetor }} kali</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber">💰</div>
    <div><div class="stat-label">Total Nilai</div><div class="stat-value">Rp {{ number_format($totalNilai / 1000, 1) }} Rb</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">🏆</div>
    <div>
      <div class="stat-label">Terbanyak</div>
      <div class="stat-value">{{ $terbanyak->nama ?? '-' }}</div>
      @if($terbanyak)
        <div class="stat-change">{{ number_format($terbanyak->total, 1) }} kg</div>
      @endif
    </div>
  </div>
</div>

{{-- CHART --}}
<div class="card mb-20">
  <div class="card-header">
    <div class="card-title">Grafik Setoran per Bulan</div>
    <div class="card-subtitle">Berat (kg) per kategori</div>
  </div>
  <div class="chart-container" style="height:220px;">
    <canvas id="chartHistori"></canvas>
  </div>
</div>

{{-- TABEL --}}
<div class="card">
  <div class="card-header">
    <div class="card-title">Daftar Setoran</div>
    <form method="GET" action="{{ route('nasabah.histori-setoran') }}" class="flex gap-8" style="flex-wrap:wrap;">
      <select name="kategori_id" class="form-control" style="width:auto; padding:7px 12px; font-size:13px;">
        <option value="">Semua Kategori</option>
        @foreach($kategoriList as $kat)
          <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
            {{ $kat->ikon }} {{ $kat->nama }}
          </option>
        @endforeach
      </select>
      <input type="month" name="bulan" class="form-control" value="{{ request('bulan', date('Y-m')) }}" style="width:auto; padding:7px 12px; font-size:13px;" />
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      @if(request()->hasAny(['bulan','kategori_id']))
        <a href="{{ route('nasabah.histori-setoran') }}" class="btn btn-secondary btn-sm">Reset</a>
      @endif
    </form>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>#</th><th>Tanggal</th><th>Kode</th><th>Kategori</th><th>Berat (kg)</th><th>Nilai</th></tr>
      </thead>
      <tbody>
        @forelse($setoran as $s)
        <tr>
          <td class="text-muted text-sm">{{ $setoran->firstItem() + $loop->index }}</td>
          <td class="text-muted text-sm">{{ $s->created_at->format('d M Y') }}</td>
          <td class="fw-600 text-green text-sm">{{ $s->kode_setoran }}</td>
          <td>
            @foreach($s->detail as $d)
              <span class="badge badge-green" style="margin-right:2px;">{{ $d->kategori->ikon }} {{ $d->kategori->nama }}</span>
            @endforeach
          </td>
          <td class="fw-600">{{ number_format($s->total_berat, 2) }}</td>
          <td class="fw-700 text-green">+Rp {{ number_format($s->total_nilai, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="empty-state"><div class="empty-icon">📋</div><p>Belum ada setoran pada periode ini.</p></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="flex items-center justify-between mt-16" style="font-size:13px; color:var(--gray-500);">
    <span>Menampilkan {{ $setoran->firstItem() ?? 0 }}–{{ $setoran->lastItem() ?? 0 }} dari {{ $setoran->total() }} setoran</span>
    <div>{{ $setoran->links() }}</div>
  </div>
</div>
@endsection

@push('scripts')
<script>
Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
Chart.defaults.color = '#6b7280';

new Chart(document.getElementById('chartHistori'), {
  type: 'bar',
  data: {
    labels: @json($chartLabels),
    datasets: @json($chartDatasets)
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'top', labels: { boxWidth: 10, padding: 16, font: { size: 12 } } } },
    scales: {
      x: { grid: { display: false }, border: { display: false }, stacked: true },
      y: { grid: { color: '#f3f4f6' }, border: { display: false }, stacked: true, ticks: { callback: v => v + ' kg' } }
    }
  }
});
</script>
@endpush
