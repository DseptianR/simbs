@extends('layouts.app_nasabah_operator')
@php $mode = 'operator'; $title = 'Validasi Penarikan'; $subtitle = 'Proses permintaan pencairan saldo'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ route('operator.dashboard') }}">Dashboard</a> <span>/</span> Validasi Penarikan</div>
  <h1>Validasi Penarikan Saldo</h1>
  <p>Proses permintaan pencairan saldo tabungan sampah dari nasabah.</p>
</div>

@if(session('success'))
  <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
@endif

{{-- STATS --}}
<div class="grid grid-4 mb-20">
  <div class="stat-card">
    <div class="stat-icon amber">⏳</div>
    <div><div class="stat-label">Menunggu Validasi</div><div class="stat-value">{{ $stats['pending'] }}</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">✅</div>
    <div><div class="stat-label">Disetujui Bulan Ini</div><div class="stat-value">{{ $stats['disetujui'] }}</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">💸</div>
    <div><div class="stat-label">Total Dicairkan</div><div class="stat-value">Rp {{ number_format($stats['total_cair'] / 1000, 1) }} Rb</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red">❌</div>
    <div><div class="stat-label">Ditolak</div><div class="stat-value">{{ $stats['ditolak'] }}</div></div>
  </div>
</div>

{{-- PENDING --}}
<div class="card mb-20">
  <div class="card-header">
    <div>
      <div class="card-title">Permintaan Menunggu Validasi</div>
      <div class="card-subtitle">Perlu diproses segera</div>
    </div>
    <span class="badge badge-amber">{{ $pending->count() }} Pending</span>
  </div>

  @if($pending->isEmpty())
    <div class="empty-state">
      <div class="empty-icon">✅</div>
      <p>Tidak ada penarikan yang menunggu validasi.</p>
    </div>
  @else
  <div style="display:flex; flex-direction:column; gap:12px;">
    @foreach($pending as $p)
    <div style="border:1.5px solid #fcd34d; background:#fffbeb; border-radius:12px; padding:18px;">
      <div class="flex items-center justify-between" style="flex-wrap:wrap; gap:12px;">
        <div class="flex items-center gap-12">
          <div class="avatar-sm" style="width:44px;height:44px;font-size:16px;">{{ strtoupper(substr($p->nasabah->nama, 0, 2)) }}</div>
          <div>
            <div class="fw-700" style="font-size:15px;">{{ $p->nasabah->nama }}</div>
            <div class="text-muted text-sm">No. Rek: {{ $p->nasabah->no_rekening }} · Saldo: Rp {{ number_format($p->nasabah->saldo, 0, ',', '.') }}</div>
            <div class="text-xs text-muted mt-4">Diajukan: {{ $p->created_at->format('d M Y, H:i') }}</div>
            @if($p->catatan_nasabah)
              <div class="text-xs text-muted mt-4">Catatan: {{ $p->catatan_nasabah }}</div>
            @endif
          </div>
        </div>
        <div style="text-align:right;">
          <div class="text-xs text-muted">Jumlah Penarikan</div>
          <div style="font-size:22px; font-weight:800; color:var(--gray-900);">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</div>
          <div class="text-xs text-muted">Kode: {{ $p->kode_penarikan }}</div>
        </div>
        <div class="flex gap-8" style="flex-direction:column; min-width:160px;">
          <form method="POST" action="{{ route('operator.validasi-penarikan.setujui', $p) }}">
            @csrf
            <input type="text" name="catatan_operator" class="form-control" placeholder="Catatan (opsional)" style="font-size:12px; padding:6px 10px; margin-bottom:6px;" />
            <button type="submit" class="btn btn-success btn-block">✓ Setujui</button>
          </form>
          <form method="POST" action="{{ route('operator.validasi-penarikan.tolak', $p) }}">
            @csrf
            <input type="text" name="catatan_operator" class="form-control" placeholder="Alasan penolakan..." style="font-size:12px; padding:6px 10px; margin-bottom:6px;" />
            <button type="submit" class="btn btn-danger btn-block">✗ Tolak</button>
          </form>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endif
</div>

{{-- RIWAYAT --}}
<div class="card">
  <div class="card-header">
    <div class="card-title">Riwayat Penarikan</div>
    <form method="GET" action="{{ route('operator.validasi-penarikan') }}" class="flex gap-8">
      <select name="status" class="form-control" style="width:auto; padding:7px 12px; font-size:13px;">
        <option value="semua" {{ request('status','semua') === 'semua' ? 'selected' : '' }}>Semua Status</option>
        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </form>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Tanggal</th><th>Nasabah</th><th>No. Rek</th><th>Jumlah</th><th>Status</th><th>Catatan</th><th>Diproses Oleh</th></tr>
      </thead>
      <tbody>
        @forelse($riwayat as $r)
        <tr>
          <td class="text-muted text-sm">{{ $r->divalidasi_at?->format('d M Y') ?? $r->created_at->format('d M Y') }}</td>
          <td class="td-name">{{ $r->nasabah->nama }}</td>
          <td class="text-muted">{{ $r->nasabah->no_rekening }}</td>
          <td class="fw-700">Rp {{ number_format($r->jumlah, 0, ',', '.') }}</td>
          <td>
            @if($r->status === 'disetujui')
              <span class="badge badge-green">✓ Disetujui</span>
            @else
              <span class="badge badge-red">✗ Ditolak</span>
            @endif
          </td>
          <td class="text-muted text-sm">{{ $r->catatan_operator ?? '-' }}</td>
          <td class="text-muted text-sm">{{ $r->validator?->name ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="empty-state"><div class="empty-icon">📋</div><p>Belum ada riwayat penarikan.</p></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-16">{{ $riwayat->links() }}</div>
</div>
@endsection
