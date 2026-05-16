@extends('layouts.app_nasabah_operator')
@php $mode = 'nasabah'; $title = 'Beranda Nasabah'; $subtitle = 'Selamat datang di portal nasabah'; @endphp

@section('content')
<div class="page-header">
  <h1>Selamat Datang, {{ $nasabah->nama }} 👋</h1>
  <p>No. Rekening: <strong>{{ $nasabah->no_rekening }}</strong> · Bergabung sejak {{ $nasabah->created_at->translatedFormat('F Y') }}</p>
</div>

{{-- SALDO UTAMA --}}
<div style="background: linear-gradient(135deg, #0f2d1e, #22874f); border-radius: 20px; padding: 28px 32px; margin-bottom: 20px; color: #fff; position: relative; overflow: hidden;">
  <div style="position:absolute; right:-20px; top:-20px; width:160px; height:160px; background:rgba(255,255,255,.05); border-radius:50%;"></div>
  <div style="position:absolute; right:40px; bottom:-40px; width:120px; height:120px; background:rgba(255,255,255,.04); border-radius:50%;"></div>
  <div class="flex items-center justify-between" style="flex-wrap:wrap; gap:16px;">
    <div>
      <div style="font-size:13px; color:rgba(255,255,255,.7); font-weight:500; text-transform:uppercase; letter-spacing:.5px;">Saldo Tabungan Sampah</div>
      <div style="font-size:42px; font-weight:900; margin-top:6px; line-height:1;">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</div>
      <div style="font-size:13px; color:rgba(255,255,255,.65); margin-top:8px;">Total setoran: <strong style="color:#fff;">{{ number_format($totalBerat, 2) }} kg</strong> sampah</div>
    </div>
    <div style="display:flex; flex-direction:column; gap:10px;">
      <a href="{{ route('nasabah.saldo') }}" style="background:rgba(255,255,255,.15); border:1.5px solid rgba(255,255,255,.3); color:#fff; padding:10px 20px; border-radius:10px; font-size:13.5px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
        💸 Tarik Saldo
      </a>
      <a href="{{ route('nasabah.histori-setoran') }}" style="background:rgba(255,255,255,.08); border:1.5px solid rgba(255,255,255,.15); color:rgba(255,255,255,.85); padding:10px 20px; border-radius:10px; font-size:13.5px; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
        📜 Lihat Histori
      </a>
    </div>
  </div>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-4 mb-20">
  <div class="stat-card">
    <div class="stat-icon green">⚖️</div>
    <div>
      <div class="stat-label">Total Berat Setoran</div>
      <div class="stat-value">{{ number_format($totalBerat, 1) }} kg</div>
      @if($beratBulanIni > 0)
        <div class="stat-change up">↑ {{ number_format($beratBulanIni, 1) }} kg bulan ini</div>
      @endif
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">📦</div>
    <div>
      <div class="stat-label">Jumlah Setoran</div>
      <div class="stat-value">{{ $jumlahSetor }} kali</div>
      @if($jumlahBulanIni > 0)
        <div class="stat-change up">↑ {{ $jumlahBulanIni }} kali bulan ini</div>
      @endif
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber">💰</div>
    <div>
      <div class="stat-label">Total Nilai Setoran</div>
      <div class="stat-value">Rp {{ number_format($totalNilai / 1000, 1) }} Rb</div>
      @if($nilaibulanIni > 0)
        <div class="stat-change up">↑ Rp {{ number_format($nilaibulanIni, 0, ',', '.') }} bulan ini</div>
      @endif
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red">💸</div>
    <div>
      <div class="stat-label">Total Dicairkan</div>
      <div class="stat-value">Rp {{ number_format($totalCair / 1000, 1) }} Rb</div>
      <div class="stat-change">Sejak bergabung</div>
    </div>
  </div>
</div>

{{-- CHART + KOMPOSISI --}}
<div class="grid grid-2-1 mb-20">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Grafik Setoran Saya</div>
      <div class="card-subtitle">6 bulan terakhir (kg)</div>
    </div>
    <div class="chart-container" style="height:240px;">
      <canvas id="chartNasabah"></canvas>
    </div>
  </div>

  <div class="card">
    <div class="card-title mb-16">🏷️ Komposisi Setoran Saya</div>
    @if($komposisi->isNotEmpty())
    <div class="chart-container" style="height:160px;">
      <canvas id="chartKomposisi"></canvas>
    </div>
    @php $totalKomposisi = $komposisi->sum('total_berat'); @endphp
    <div style="margin-top:12px; display:flex; flex-direction:column; gap:6px;">
      @foreach($komposisi->take(4) as $k)
      @php $pct = $totalKomposisi > 0 ? round($k->total_berat / $totalKomposisi * 100) : 0; @endphp
      <div class="flex items-center justify-between text-sm">
        <span>{{ $k->ikon }} {{ $k->nama }}</span>
        <span class="fw-600">{{ number_format($k->total_berat, 1) }} kg ({{ $pct }}%)</span>
      </div>
      @endforeach
    </div>
    @else
    <div class="empty-state"><div class="empty-icon">♻️</div><p>Belum ada data setoran.</p></div>
    @endif
  </div>
</div>

{{-- HISTORI TERBARU --}}
<div class="card">
  <div class="card-header">
    <div class="card-title">Setoran Terbaru</div>
    <a href="{{ route('nasabah.histori-setoran') }}" class="btn btn-secondary btn-sm">Lihat Semua →</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Tanggal</th><th>Kode</th><th>Berat</th><th>Nilai</th></tr>
      </thead>
      <tbody>
        @forelse($setoranTerbaru as $s)
        <tr>
          <td class="text-muted text-sm">{{ $s->created_at->format('d M Y') }}</td>
          <td class="fw-600 text-green text-sm">{{ $s->kode_setoran }}</td>
          <td class="fw-600">{{ number_format($s->total_berat, 2) }} kg</td>
          <td class="fw-700 text-green">+Rp {{ number_format($s->total_nilai, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="empty-state"><div class="empty-icon">📋</div><p>Belum ada setoran.</p></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
Chart.defaults.color = '#6b7280';

new Chart(document.getElementById('chartNasabah'), {
  type: 'bar',
  data: {
    labels: @json($chartLabels),
    datasets: [{
      label: 'Berat Setoran (kg)',
      data: @json($chartBerat),
      backgroundColor: 'rgba(34,135,79,.8)',
      borderRadius: 6
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false }, border: { display: false } },
      y: { grid: { color: '#f3f4f6' }, border: { display: false }, ticks: { callback: v => v + ' kg' } }
    }
  }
});

@if($komposisi->isNotEmpty())
const colors = ['#22874f','#3b82f6','#f59e0b','#ef4444','#8b5cf6'];
new Chart(document.getElementById('chartKomposisi'), {
  type: 'doughnut',
  data: {
    labels: @json($komposisi->pluck('nama')),
    datasets: [{
      data: @json($komposisi->pluck('total_berat')),
      backgroundColor: colors.slice(0, {{ $komposisi->count() }}),
      borderWidth: 0, hoverOffset: 4
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    cutout: '65%',
    plugins: { legend: { display: false } }
  }
});
@endif
</script>
@endpush
