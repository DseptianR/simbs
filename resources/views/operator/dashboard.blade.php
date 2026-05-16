@extends('layouts.app_nasabah_operator')
@php $mode = 'operator'; $title = 'Dashboard Operator'; $subtitle = 'Ringkasan aktivitas bank sampah'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ url('/') }}">Beranda</a> <span>/</span> Dashboard</div>
  <h1>Dashboard Operator</h1>
  <p>Selamat datang, <strong>{{ auth()->user()->name }}</strong>! Berikut ringkasan aktivitas bank sampah.</p>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-4 mb-20">
  <div class="stat-card">
    <div class="stat-icon green">👥</div>
    <div>
      <div class="stat-label">Total Nasabah</div>
      <div class="stat-value">{{ number_format($totalNasabah) }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber">⚖️</div>
    <div>
      <div class="stat-label">Setoran Bulan Ini</div>
      <div class="stat-value">{{ number_format($setoranBulanIni, 1) }} kg</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">💰</div>
    <div>
      <div class="stat-label">Total Saldo Nasabah</div>
      <div class="stat-value">Rp {{ number_format($totalSaldo / 1000, 1) }} Rb</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red">💸</div>
    <div>
      <div class="stat-label">Penarikan Pending</div>
      <div class="stat-value">{{ $pendingPenarikan }}</div>
      @if($pendingPenarikan > 0)
        <div class="stat-change down">Perlu divalidasi segera</div>
      @endif
    </div>
  </div>
</div>

{{-- CHARTS ROW --}}
<div class="grid grid-2-1 mb-20">
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">Volume Setoran Sampah</div>
        <div class="card-subtitle">6 bulan terakhir (dalam kg)</div>
      </div>
    </div>
    <div class="chart-container" style="height:260px;">
      <canvas id="chartSetoran"></canvas>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">Komposisi Sampah</div>
        <div class="card-subtitle">Berdasarkan kategori</div>
      </div>
    </div>
    @php $totalKomposisi = $komposisi->sum('total'); @endphp
    <div class="chart-container" style="height:180px;">
      <canvas id="chartKategori"></canvas>
    </div>
    <div style="margin-top:12px; display:flex; flex-direction:column; gap:6px;">
      @forelse($komposisi->take(4) as $k)
        @php $pct = $totalKomposisi > 0 ? round($k->total / $totalKomposisi * 100) : 0; @endphp
        <div class="flex items-center justify-between text-sm">
          <span>{{ $k->nama }}</span>
          <span class="fw-600">{{ $pct }}%</span>
        </div>
      @empty
        <div class="text-muted text-sm">Belum ada data setoran.</div>
      @endforelse
    </div>
  </div>
</div>

{{-- HARGA TERKINI + TRANSAKSI TERBARU --}}
<div class="grid grid-2 mb-20">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Transaksi Setoran Terbaru</div>
      <a href="{{ route('operator.transaksi-setoran') }}" class="btn btn-primary btn-sm">+ Input Baru</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Nasabah</th><th>Berat</th><th>Nilai</th><th>Waktu</th></tr>
        </thead>
        <tbody>
          @forelse($setoranTerbaru as $s)
          <tr>
            <td>
              <div class="flex items-center gap-8">
                <div class="avatar-sm">{{ strtoupper(substr($s->nasabah->nama, 0, 2)) }}</div>
                <div>
                  <div class="td-name">{{ $s->nasabah->nama }}</div>
                  <div class="text-xs text-muted">{{ $s->nasabah->no_rekening }}</div>
                </div>
              </div>
            </td>
            <td class="fw-600">{{ number_format($s->total_berat, 2) }} kg</td>
            <td class="text-green fw-600">Rp {{ number_format($s->total_nilai, 0, ',', '.') }}</td>
            <td class="text-muted text-sm">{{ $s->created_at->diffForHumans() }}</td>
          </tr>
          @empty
          <tr><td colspan="4" class="empty-state">Belum ada transaksi.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="card-title">Harga Sampah Terkini</div>
      <a href="{{ route('operator.manajemen-harga') }}" class="btn btn-secondary btn-sm">Edit Harga</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Kategori</th><th>Harga Beli/kg</th><th>Stok</th></tr>
        </thead>
        <tbody>
          @forelse($kategori as $kat)
          <tr>
            <td><span style="display:flex;align-items:center;gap:8px;"><span style="font-size:18px;">{{ $kat->ikon }}</span><span class="td-name">{{ $kat->nama }}</span></span></td>
            <td class="text-green fw-600">Rp {{ number_format($kat->harga_per_satuan, 0, ',', '.') }}</td>
            <td><span class="badge badge-green">{{ number_format($kat->inventaris->stok ?? 0, 1) }} kg</span></td>
          </tr>
          @empty
          <tr><td colspan="3" class="empty-state">Belum ada kategori.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- PENARIKAN PENDING --}}
@if($penarikanPending->count() > 0)
<div class="card">
  <div class="card-header">
    <div class="card-title">Penarikan Menunggu Validasi</div>
    <a href="{{ route('operator.validasi-penarikan') }}" class="btn btn-amber btn-sm">Lihat Semua</a>
  </div>
  <div style="display:flex; flex-direction:column; gap:12px;">
    @foreach($penarikanPending as $p)
    <div style="padding:14px; background:var(--gray-50); border-radius:10px; border:1px solid var(--gray-200);">
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-8">
          <div class="avatar-sm">{{ strtoupper(substr($p->nasabah->nama, 0, 2)) }}</div>
          <div>
            <div class="fw-600" style="font-size:13.5px;">{{ $p->nasabah->nama }}</div>
            <div class="text-muted text-xs">No. Rek: {{ $p->nasabah->no_rekening }}</div>
          </div>
        </div>
        <span class="badge badge-amber">Pending</span>
      </div>
      <div class="flex items-center justify-between">
        <span class="fw-700" style="font-size:16px; color:var(--gray-900);">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</span>
        <div class="flex gap-8">
          <form method="POST" action="{{ route('operator.validasi-penarikan.setujui', $p) }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">✓ Setujui</button>
          </form>
          <form method="POST" action="{{ route('operator.validasi-penarikan.tolak', $p) }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">✗ Tolak</button>
          </form>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection

@push('scripts')
<script>
Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
Chart.defaults.color = '#6b7280';

const chartLabels = @json($chartLabels);
const chartData   = @json($chartData);

const colors = ['#22874f','#3b82f6','#f59e0b','#ef4444','#8b5cf6'];
const datasets = chartData.map((d, i) => ({
  label: d.label + ' (kg)',
  data: d.data,
  borderColor: colors[i] || '#999',
  backgroundColor: (colors[i] || '#999') + '14',
  borderWidth: 2.5,
  pointRadius: 4,
  pointBackgroundColor: colors[i] || '#999',
  fill: true,
  tension: 0.4
}));

new Chart(document.getElementById('chartSetoran'), {
  type: 'line',
  data: { labels: chartLabels, datasets },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'top', labels: { boxWidth: 10, padding: 16, font: { size: 12 } } } },
    scales: {
      x: { grid: { display: false }, border: { display: false } },
      y: { grid: { color: '#f3f4f6' }, border: { display: false }, ticks: { callback: v => v + ' kg' } }
    }
  }
});

const komposisi = @json($komposisi);
if (komposisi.length > 0) {
  new Chart(document.getElementById('chartKategori'), {
    type: 'doughnut',
    data: {
      labels: komposisi.map(k => k.nama),
      datasets: [{
        data: komposisi.map(k => parseFloat(k.total)),
        backgroundColor: colors,
        borderWidth: 0,
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      cutout: '68%',
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.raw.toFixed(1) + ' kg' } }
      }
    }
  });
}
</script>
@endpush
