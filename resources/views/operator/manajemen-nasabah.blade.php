@extends('layouts.app_nasabah_operator')
@php $mode = 'operator'; $title = 'Manajemen Nasabah'; $subtitle = 'Kelola data anggota bank sampah'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ route('operator.dashboard') }}">Dashboard</a> <span>/</span> Manajemen Nasabah</div>
  <h1>Manajemen Nasabah</h1>
  <p>Kelola data nasabah, pendaftaran anggota baru, dan profil warga.</p>
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
    <div class="stat-icon green">👥</div>
    <div><div class="stat-label">Total Nasabah</div><div class="stat-value">{{ $stats['total'] }}</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">✅</div>
    <div><div class="stat-label">Nasabah Aktif</div><div class="stat-value">{{ $stats['aktif'] }}</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber">🆕</div>
    <div><div class="stat-label">Baru Bulan Ini</div><div class="stat-value">{{ $stats['baru'] }}</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red">⏸️</div>
    <div><div class="stat-label">Tidak Aktif</div><div class="stat-value">{{ $stats['nonaktif'] }}</div></div>
  </div>
</div>

<div class="grid grid-2-1">
  {{-- TABEL NASABAH --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">Daftar Nasabah</div>
      <form method="GET" action="{{ route('operator.manajemen-nasabah') }}" class="flex gap-8">
        <input type="text" name="search" class="form-control" placeholder="🔍 Cari nasabah..." value="{{ request('search') }}" style="width:200px; padding:7px 12px; font-size:13px;" />
        <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
      </form>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No. Rek</th>
            <th>Nama Nasabah</th>
            <th>No. HP</th>
            <th>Saldo</th>
            <th>Status</th>
            <th>Bergabung</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($nasabah as $n)
          <tr>
            <td class="fw-600 text-green">{{ $n->no_rekening }}</td>
            <td>
              <div class="flex items-center gap-8">
                <div class="avatar-sm">{{ strtoupper(substr($n->nama, 0, 2)) }}</div>
                <span class="td-name">{{ $n->nama }}</span>
              </div>
            </td>
            <td class="text-muted">{{ $n->no_hp ?? '-' }}</td>
            <td class="fw-700">Rp {{ number_format($n->saldo, 0, ',', '.') }}</td>
            <td>
              @if($n->is_active)
                <span class="badge badge-green">Aktif</span>
              @else
                <span class="badge badge-amber">Tidak Aktif</span>
              @endif
            </td>
            <td class="text-muted text-sm">{{ $n->created_at->format('M Y') }}</td>
            <td>
              <div class="flex gap-8">
                <a href="{{ route('operator.manajemen-nasabah.show', $n) }}" class="btn btn-secondary btn-sm">Detail</a>
                <form method="POST" action="{{ route('operator.manajemen-nasabah.toggle', $n) }}" style="display:inline;">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn btn-sm {{ $n->is_active ? 'btn-amber' : 'btn-success' }}">
                    {{ $n->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                  </button>
                </form>
                <form method="POST" action="{{ route('operator.manajemen-nasabah.destroy', $n) }}" style="display:inline;" onsubmit="return confirm('Hapus nasabah {{ $n->nama }}?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="empty-state"><div class="empty-icon">👥</div><p>Belum ada nasabah terdaftar.</p></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="flex items-center justify-between mt-16" style="font-size:13px; color:var(--gray-500);">
      <span>Menampilkan {{ $nasabah->firstItem() }}–{{ $nasabah->lastItem() }} dari {{ $nasabah->total() }} nasabah</span>
      <div>{{ $nasabah->links() }}</div>
    </div>
  </div>

  {{-- FORM TAMBAH --}}
  <div class="card">
    <div class="card-title mb-16">➕ Daftarkan Nasabah Baru</div>

    @if($errors->any())
      <div class="alert alert-danger">⚠️ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('operator.manajemen-nasabah.store') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Nama Lengkap <span style="color:var(--red)">*</span></label>
        <input type="text" class="form-control" name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap nasabah..." required />
      </div>
      <div class="form-group">
        <label class="form-label">Nomor HP <span style="color:var(--red)">*</span></label>
        <input type="tel" class="form-control" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xx-xxxx-xxxx" required />
      </div>
      <div class="form-group">
        <label class="form-label">NIK (KTP)</label>
        <input type="text" class="form-control" name="nik" value="{{ old('nik') }}" placeholder="16 digit NIK..." maxlength="16" />
      </div>
      <div class="form-group">
        <label class="form-label">Alamat Lengkap</label>
        <textarea class="form-control" name="alamat" rows="3" placeholder="Jl. ...">{{ old('alamat') }}</textarea>
      </div>
      <div class="form-group">
        <label class="form-label">PIN Nasabah (6 digit) <span style="color:var(--red)">*</span></label>
        <input type="password" class="form-control" name="pin" placeholder="••••••" maxlength="6" inputmode="numeric" required />
        <div class="form-hint">PIN digunakan nasabah untuk login ke portal.</div>
      </div>
      <div class="divider"></div>
      <div style="background:var(--green-50); border:1px solid var(--green-200); border-radius:8px; padding:12px; margin-bottom:16px; font-size:13px; color:var(--green-700);">
        📋 Nomor rekening akan digenerate otomatis setelah pendaftaran.
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg">✓ Daftarkan Nasabah</button>
    </form>
  </div>
</div>
@endsection
