@extends('layouts.app_nasabah_operator')
@php $mode = 'operator'; $title = 'Detail Nasabah'; $subtitle = $nasabah->nama; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb">
    <a href="{{ route('operator.dashboard') }}">Dashboard</a> <span>/</span>
    <a href="{{ route('operator.manajemen-nasabah') }}">Manajemen Nasabah</a> <span>/</span>
    Detail
  </div>
  <h1>Detail Nasabah</h1>
</div>

<div class="grid grid-2-1">
  {{-- INFO NASABAH --}}
  <div style="display:flex; flex-direction:column; gap:16px;">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Informasi Nasabah</div>
        @if($nasabah->is_active)
          <span class="badge badge-green">Aktif</span>
        @else
          <span class="badge badge-amber">Tidak Aktif</span>
        @endif
      </div>
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div class="flex items-center gap-12">
          <div class="avatar-sm" style="width:56px;height:56px;font-size:20px;border-radius:14px;">{{ strtoupper(substr($nasabah->nama, 0, 2)) }}</div>
          <div>
            <div style="font-size:18px; font-weight:800;">{{ $nasabah->nama }}</div>
            <div class="text-muted text-sm">No. Rekening: <strong class="text-green">{{ $nasabah->no_rekening }}</strong></div>
          </div>
        </div>
        <div class="divider"></div>
        <div class="grid grid-2" style="gap:12px;">
          <div><div class="text-xs text-muted">NIK</div><div class="fw-600">{{ $nasabah->nik ?? '-' }}</div></div>
          <div><div class="text-xs text-muted">No. HP</div><div class="fw-600">{{ $nasabah->no_hp ?? '-' }}</div></div>
          <div class="col-span-2"><div class="text-xs text-muted">Alamat</div><div class="fw-600">{{ $nasabah->alamat ?? '-' }}</div></div>
          <div><div class="text-xs text-muted">Bergabung</div><div class="fw-600">{{ $nasabah->created_at->format('d M Y') }}</div></div>
        </div>
      </div>
    </div>

    {{-- HISTORI SETORAN --}}
    <div class="card">
      <div class="card-title mb-16">📋 Histori Setoran</div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Kode</th><th>Berat</th><th>Nilai</th><th>Tanggal</th></tr></thead>
          <tbody>
            @forelse($nasabah->setoran->take(10) as $s)
            <tr>
              <td class="fw-600 text-green text-sm">{{ $s->kode_setoran }}</td>
              <td>{{ number_format($s->total_berat, 2) }} kg</td>
              <td class="fw-700 text-green">Rp {{ number_format($s->total_nilai, 0, ',', '.') }}</td>
              <td class="text-muted text-sm">{{ $s->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-state">Belum ada setoran.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- SALDO & PENARIKAN --}}
  <div style="display:flex; flex-direction:column; gap:16px;">
    <div class="card" style="text-align:center;">
      <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:.5px;">Saldo Saat Ini</div>
      <div style="font-size:36px; font-weight:800; color:var(--green-700); margin:8px 0;">
        Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}
      </div>
      <div class="text-sm text-muted">Total {{ $nasabah->setoran->count() }} transaksi setoran</div>
    </div>

    <div class="card">
      <div class="card-title mb-16">💸 Riwayat Penarikan</div>
      <div style="display:flex; flex-direction:column; gap:8px;">
        @forelse($nasabah->penarikan->take(5) as $p)
        <div class="flex items-center justify-between" style="padding:10px 12px; background:var(--gray-50); border-radius:8px;">
          <div>
            <div class="fw-600 text-sm">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</div>
            <div class="text-xs text-muted">{{ $p->created_at->format('d M Y') }}</div>
          </div>
          @if($p->status === 'disetujui')
            <span class="badge badge-green">✓ Cair</span>
          @elseif($p->status === 'ditolak')
            <span class="badge badge-red">✗ Ditolak</span>
          @else
            <span class="badge badge-amber">Pending</span>
          @endif
        </div>
        @empty
        <div class="text-muted text-sm">Belum ada penarikan.</div>
        @endforelse
      </div>
    </div>

    <a href="{{ route('operator.manajemen-nasabah') }}" class="btn btn-secondary btn-block">← Kembali</a>
  </div>
</div>
@endsection
