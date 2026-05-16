@extends('layouts.app_nasabah_operator')
@php $mode = 'operator'; $title = 'Manajemen Harga Sampah'; $subtitle = 'Atur kategori dan harga per kilogram'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ route('operator.dashboard') }}">Dashboard</a> <span>/</span> Harga Sampah</div>
  <h1>Manajemen Kategori & Harga Sampah</h1>
  <p>Atur kategori sampah dan perbarui harga beli/jual secara real-time.</p>
</div>

@if(session('success'))
  <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
@endif

<div class="grid grid-2-1">
  {{-- TABEL KATEGORI --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">Daftar Kategori & Harga</div>
      <span class="badge badge-green">🕐 Update: {{ now()->format('d M Y, H:i') }}</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Kategori</th>
            <th>Harga Beli/kg</th>
            <th>Harga Jual/kg</th>
            <th>Stok Gudang</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($kategori as $kat)
          <tr>
            <td>
              <div class="flex items-center gap-8">
                <span style="font-size:22px;">{{ $kat->ikon }}</span>
                <div>
                  <div class="td-name">{{ $kat->nama }}</div>
                  <div class="text-xs text-muted">{{ $kat->deskripsi }}</div>
                </div>
              </div>
            </td>
            <td><span class="fw-700 text-green">Rp {{ number_format($kat->harga_per_satuan, 0, ',', '.') }}</span></td>
            <td><span class="fw-600">Rp {{ number_format($kat->harga_jual, 0, ',', '.') }}</span></td>
            <td>
              <div>{{ number_format($kat->inventaris->stok ?? 0, 1) }} kg</div>
              @php $stok = $kat->inventaris->stok ?? 0; $pct = min(100, $stok / 2); @endphp
              <div class="progress mt-4" style="width:80px;"><div class="progress-bar green" style="width:{{ $pct }}%"></div></div>
            </td>
            <td>
              <button class="btn btn-secondary btn-sm"
                onclick="editKategori({{ $kat->id }}, '{{ addslashes($kat->nama) }}', {{ $kat->harga_per_satuan }}, {{ $kat->harga_jual }}, '{{ addslashes($kat->deskripsi ?? '') }}', '{{ $kat->ikon }}')">
                ✏️ Edit
              </button>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="empty-state"><div class="empty-icon">🏷️</div><p>Belum ada kategori.</p></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- FORM TAMBAH/EDIT --}}
  <div style="display:flex; flex-direction:column; gap:16px;">
    <div class="card" id="formKategori">
      <div class="card-title mb-16" id="formTitle">➕ Tambah Kategori Baru</div>

      @if($errors->any())
        <div class="alert alert-danger">⚠️ {{ $errors->first() }}</div>
      @endif

      <form method="POST" id="kategoriForm" action="{{ route('operator.manajemen-harga.store') }}">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST" />
        <input type="hidden" name="kategori_id" id="kategoriId" />

        <div class="form-group">
          <label class="form-label">Nama Kategori <span style="color:var(--red)">*</span></label>
          <input type="text" class="form-control" name="nama" id="inputNama" value="{{ old('nama') }}" placeholder="Contoh: Plastik PET" required />
        </div>
        <div class="form-group">
          <label class="form-label">Deskripsi</label>
          <input type="text" class="form-control" name="deskripsi" id="inputDeskripsi" value="{{ old('deskripsi') }}" placeholder="Jenis sampah yang termasuk..." />
        </div>
        <div class="form-group">
          <label class="form-label">Harga Beli per kg (Rp) <span style="color:var(--red)">*</span></label>
          <div class="input-group">
            <span class="input-addon">Rp</span>
            <input type="number" class="form-control" name="harga_per_satuan" id="inputBeli" value="{{ old('harga_per_satuan') }}" placeholder="0" min="1" required style="border-radius:0 var(--radius-sm) var(--radius-sm) 0; border-left:none;" />
          </div>
          <div class="form-hint">Harga yang dibayarkan ke nasabah saat setoran.</div>
        </div>
        <div class="form-group">
          <label class="form-label">Harga Jual per kg (Rp) <span style="color:var(--red)">*</span></label>
          <div class="input-group">
            <span class="input-addon">Rp</span>
            <input type="number" class="form-control" name="harga_jual" id="inputJual" value="{{ old('harga_jual') }}" placeholder="0" min="1" required style="border-radius:0 var(--radius-sm) var(--radius-sm) 0; border-left:none;" />
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Ikon Emoji</label>
          <input type="text" class="form-control" name="ikon" id="inputIkon" value="{{ old('ikon') }}" placeholder="🧴" maxlength="10" />
        </div>
        <div class="flex gap-8">
          <button type="submit" class="btn btn-primary" style="flex:1;">✓ Simpan</button>
          <button type="button" class="btn btn-secondary" onclick="resetKategoriForm()">Reset</button>
        </div>
      </form>
    </div>

    {{-- Margin Info --}}
    <div class="card">
      <div class="card-title mb-16">📈 Margin Keuntungan</div>
      <div style="display:flex; flex-direction:column; gap:10px;">
        @foreach($kategori->where('is_active', true)->take(6) as $kat)
        @php
          $margin = $kat->harga_jual - $kat->harga_per_satuan;
          $pct    = $kat->harga_per_satuan > 0
                    ? round($margin / $kat->harga_per_satuan * 100)
                    : 0;
        @endphp
        <div>
          <div class="flex items-center justify-between mb-4">
            <span class="text-sm">{{ $kat->ikon }} {{ $kat->nama }}</span>
            <span class="fw-600 text-green">
              +Rp {{ number_format($margin, 0, ',', '.') }}/kg ({{ $pct }}%)
            </span>
          </div>
          <div class="progress">
            <div class="progress-bar green" style="width:{{ min(100, max(0, $pct)) }}%"></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function editKategori(id, nama, beli, jual, deskripsi, ikon) {
  document.getElementById('formTitle').textContent = '✏️ Edit Kategori: ' + nama;
  document.getElementById('kategoriId').value      = id;
  document.getElementById('inputNama').value       = nama;
  document.getElementById('inputBeli').value       = beli;
  document.getElementById('inputJual').value       = jual;
  document.getElementById('inputDeskripsi').value  = deskripsi;
  document.getElementById('inputIkon').value       = ikon;
  document.getElementById('formMethod').value      = 'PUT';

  // Update form action ke route update
  document.getElementById('kategoriForm').action = '/operator/manajemen-harga/' + id;
  document.getElementById('formKategori').scrollIntoView({ behavior: 'smooth' });
}

function resetKategoriForm() {
  document.getElementById('formTitle').textContent = '➕ Tambah Kategori Baru';
  document.getElementById('kategoriForm').reset();
  document.getElementById('kategoriId').value = '';
  document.getElementById('formMethod').value = 'POST';
  document.getElementById('kategoriForm').action = '{{ route("operator.manajemen-harga.store") }}';
}
</script>
@endpush
