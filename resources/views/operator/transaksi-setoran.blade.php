@extends('layouts.app_nasabah_operator')
@php $mode = 'operator'; $title = 'Input Transaksi Setoran'; $subtitle = 'Catat setoran sampah nasabah'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ route('operator.dashboard') }}">Dashboard</a> <span>/</span> Input Setoran</div>
  <h1>Input Transaksi Setoran</h1>
  <p>Catat setoran sampah dari nasabah dan konversi otomatis ke nilai saldo.</p>
</div>

@if(session('success'))
  <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
@endif

<div class="grid grid-2-1">
  {{-- FORM INPUT --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">Form Setoran Sampah</div>
      <span class="badge badge-green">⚖️ Timbang & Catat</span>
    </div>

    <form method="POST" action="{{ route('operator.transaksi-setoran.store') }}" id="formSetoran">
      @csrf

      <div class="grid grid-2" style="gap:14px;">
        <div class="form-group">
          <label class="form-label">Nama / No. Rekening Nasabah <span style="color:var(--red)">*</span></label>
          <input type="text" class="form-control" placeholder="Ketik nama atau nomor rekening..." id="inputNasabahText" autocomplete="off" />
          <input type="hidden" name="nasabah_id" id="nasabahId" />
          <div id="nasabahSuggestions" style="position:relative;"></div>
          @error('nasabah_id') <div class="form-hint" style="color:var(--red)">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Setoran <span style="color:var(--red)">*</span></label>
          <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required />
        </div>
      </div>

      {{-- Info nasabah terpilih --}}
      <div id="nasabahInfo" style="display:none; background:var(--green-50); border:1px solid var(--green-200); border-radius:8px; padding:12px 14px; margin-bottom:14px; font-size:13px; color:var(--green-700);">
        👤 <strong id="nasabahNama"></strong> — No. Rek: <strong id="nasabahRek"></strong> — Saldo: <strong id="nasabahSaldo"></strong>
      </div>

      <div class="divider"></div>
      <div style="font-size:13px; font-weight:700; color:var(--gray-700); margin-bottom:14px;">Detail Sampah</div>

      <div id="itemList">
        <div class="item-row" style="background:var(--gray-50); border:1px solid var(--gray-200); border-radius:10px; padding:14px; margin-bottom:10px;">
          <div class="grid grid-3" style="gap:10px; align-items:end;">
            <div class="form-group" style="margin:0;">
              <label class="form-label">Kategori Sampah</label>
              <select class="form-control kategori-select" name="items[0][kategori_id]" onchange="hitungTotal(this)" required>
                <option value="">Pilih kategori...</option>
                @foreach($kategori as $kat)
                  <option value="{{ $kat->id }}" data-harga="{{ $kat->harga_per_satuan }}">
                    {{ $kat->ikon }} {{ $kat->nama }} — Rp {{ number_format($kat->harga_per_satuan, 0, ',', '.') }}/kg
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group" style="margin:0;">
              <label class="form-label">Berat (kg)</label>
              <input type="number" class="form-control berat-input" name="items[0][berat]" placeholder="0.00" step="0.01" min="0.01" oninput="hitungTotal(this)" required />
            </div>
            <div class="form-group" style="margin:0;">
              <label class="form-label">Nilai (Rp)</label>
              <input type="text" class="form-control nilai-output" placeholder="Rp 0" readonly style="background:var(--green-50); color:var(--green-700); font-weight:700;" />
            </div>
          </div>
        </div>
      </div>

      <button type="button" onclick="tambahItem()" class="btn btn-secondary btn-sm" style="margin-bottom:16px;">
        + Tambah Kategori Lain
      </button>

      <div class="divider"></div>

      <div style="background:var(--green-50); border:1px solid var(--green-200); border-radius:10px; padding:16px; margin-bottom:16px;">
        <div class="flex items-center justify-between">
          <div>
            <div style="font-size:12px; color:var(--green-600); font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Total Nilai Setoran</div>
            <div id="grandTotal" style="font-size:28px; font-weight:800; color:var(--green-700); margin-top:4px;">Rp 0</div>
          </div>
          <div style="font-size:36px;">💰</div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Catatan (opsional)</label>
        <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan tambahan..."></textarea>
      </div>

      <div class="flex gap-12">
        <button type="submit" class="btn btn-primary btn-lg" style="flex:1;">
          ✓ Simpan Transaksi
        </button>
        <button type="button" class="btn btn-secondary btn-lg" onclick="resetForm()">Reset</button>
      </div>
    </form>
  </div>

  {{-- SIDEBAR INFO --}}
  <div style="display:flex; flex-direction:column; gap:16px;">
    <div class="card">
      <div class="card-title mb-16">🏷️ Harga Terkini</div>
      <div style="display:flex; flex-direction:column; gap:8px;">
        @foreach($kategori as $kat)
        <div class="flex items-center justify-between" style="padding:8px 10px; background:var(--gray-50); border-radius:8px;">
          <span style="font-size:13px;">{{ $kat->ikon }} {{ $kat->nama }}</span>
          <span class="fw-700 text-green">Rp {{ number_format($kat->harga_per_satuan, 0, ',', '.') }}/kg</span>
        </div>
        @endforeach
      </div>
    </div>

    <div class="card">
      <div class="card-title mb-16">📊 Statistik Hari Ini</div>
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div>
          <div class="flex items-center justify-between mb-4">
            <span class="text-sm text-muted">Transaksi</span>
            <span class="fw-700">{{ $statsHariIni['jumlah'] }} setoran</span>
          </div>
        </div>
        <div>
          <div class="flex items-center justify-between mb-4">
            <span class="text-sm text-muted">Total Berat</span>
            <span class="fw-700">{{ number_format($statsHariIni['berat'], 2) }} kg</span>
          </div>
        </div>
        <div>
          <div class="flex items-center justify-between mb-4">
            <span class="text-sm text-muted">Nilai Setoran</span>
            <span class="fw-700">Rp {{ number_format($statsHariIni['nilai'], 0, ',', '.') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- TABEL TRANSAKSI --}}
<div class="card mt-20">
  <div class="card-header">
    <div class="card-title">Transaksi Setoran Hari Ini</div>
    <form method="GET" action="{{ route('operator.transaksi-setoran') }}" class="flex gap-8">
      <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', date('Y-m-d')) }}" style="width:auto; padding:7px 12px; font-size:13px;" />
      <input type="text" name="search" class="form-control" placeholder="Cari nasabah..." value="{{ request('search') }}" style="width:180px; padding:7px 12px; font-size:13px;" />
      <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
    </form>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Kode</th>
          <th>Nasabah</th>
          <th>No. Rekening</th>
          <th>Berat (kg)</th>
          <th>Total</th>
          <th>Waktu</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($setoran as $s)
        <tr>
          <td class="text-muted text-sm fw-600">{{ $s->kode_setoran }}</td>
          <td>
            <div class="flex items-center gap-8">
              <div class="avatar-sm">{{ strtoupper(substr($s->nasabah->nama, 0, 2)) }}</div>
              <span class="td-name">{{ $s->nasabah->nama }}</span>
            </div>
          </td>
          <td class="text-muted">{{ $s->nasabah->no_rekening }}</td>
          <td class="fw-600">{{ number_format($s->total_berat, 2) }}</td>
          <td class="fw-700 text-green">Rp {{ number_format($s->total_nilai, 0, ',', '.') }}</td>
          <td class="text-muted text-sm">{{ $s->created_at->format('H:i') }}</td>
          <td>
            <form method="POST" action="{{ route('operator.transaksi-setoran.destroy', $s) }}" onsubmit="return confirm('Hapus transaksi ini? Saldo nasabah akan dikurangi.')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="empty-state"><div class="empty-icon">📋</div><p>Belum ada transaksi hari ini.</p></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-16">{{ $setoran->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
// ── Autocomplete Nasabah ──────────────────────────────────────────────────────
const inputText = document.getElementById('inputNasabahText');
const inputId   = document.getElementById('nasabahId');
const suggestEl = document.getElementById('nasabahSuggestions');

inputText.addEventListener('input', function () {
  const q = this.value.trim();
  if (q.length < 2) { suggestEl.innerHTML = ''; return; }

  fetch(`{{ route('operator.api.nasabah.search') }}?q=${encodeURIComponent(q)}`)
    .then(r => r.json())
    .then(data => {
      if (!data.length) { suggestEl.innerHTML = ''; return; }
      suggestEl.innerHTML = `<div style="position:absolute;z-index:999;width:100%;background:#fff;border:1px solid var(--gray-200);border-radius:8px;box-shadow:var(--shadow);max-height:200px;overflow-y:auto;">
        ${data.map(n => `<div onclick="pilihNasabah(${n.id},'${n.nama}','${n.no_rekening}',${n.saldo})"
          style="padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--gray-100);"
          onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='#fff'">
          <strong>${n.nama}</strong> <span style="color:var(--gray-500);">${n.no_rekening}</span>
        </div>`).join('')}
      </div>`;
    });
});

function pilihNasabah(id, nama, rek, saldo) {
  inputId.value   = id;
  inputText.value = nama + ' (' + rek + ')';
  suggestEl.innerHTML = '';
  document.getElementById('nasabahInfo').style.display = 'block';
  document.getElementById('nasabahNama').textContent   = nama;
  document.getElementById('nasabahRek').textContent    = rek;
  document.getElementById('nasabahSaldo').textContent  = 'Rp ' + parseInt(saldo).toLocaleString('id-ID');
}

document.addEventListener('click', e => {
  if (!suggestEl.contains(e.target) && e.target !== inputText) suggestEl.innerHTML = '';
});

// ── Hitung Total ──────────────────────────────────────────────────────────────
function formatRupiah(n) { return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }

function hitungTotal(el) {
  const row   = el.closest('.item-row');
  const sel   = row.querySelector('.kategori-select');
  const harga = parseFloat(sel.options[sel.selectedIndex]?.dataset?.harga || 0);
  const berat = parseFloat(row.querySelector('.berat-input').value) || 0;
  row.querySelector('.nilai-output').value = formatRupiah(harga * berat);
  updateGrandTotal();
}

function updateGrandTotal() {
  let total = 0;
  document.querySelectorAll('.nilai-output').forEach(el => {
    total += parseInt(el.value.replace(/[^0-9]/g, '')) || 0;
  });
  document.getElementById('grandTotal').textContent = formatRupiah(total);
}

// ── Tambah Item ───────────────────────────────────────────────────────────────
let itemIdx = 1;
function tambahItem() {
  const i = itemIdx++;
  const html = `
    <div class="item-row" style="background:var(--gray-50); border:1px solid var(--gray-200); border-radius:10px; padding:14px; margin-bottom:10px; position:relative;">
      <button type="button" onclick="this.closest('.item-row').remove(); updateGrandTotal();"
        style="position:absolute;top:8px;right:8px;background:none;border:none;cursor:pointer;font-size:16px;color:var(--gray-400);">✕</button>
      <div class="grid grid-3" style="gap:10px; align-items:end;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Kategori Sampah</label>
          <select class="form-control kategori-select" name="items[${i}][kategori_id]" onchange="hitungTotal(this)" required>
            <option value="">Pilih kategori...</option>
            @foreach($kategori as $kat)
            <option value="{{ $kat->id }}" data-harga="{{ $kat->harga_per_satuan }}">
              {{ $kat->ikon }} {{ $kat->nama }} — Rp {{ number_format($kat->harga_per_satuan, 0, ',', '.') }}/kg
            </option>
            @endforeach
          </select>
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Berat (kg)</label>
          <input type="number" class="form-control berat-input" name="items[${i}][berat]" placeholder="0.00" step="0.01" min="0.01" oninput="hitungTotal(this)" required />
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Nilai (Rp)</label>
          <input type="text" class="form-control nilai-output" placeholder="Rp 0" readonly style="background:var(--green-50); color:var(--green-700); font-weight:700;" />
        </div>
      </div>
    </div>`;
  document.getElementById('itemList').insertAdjacentHTML('beforeend', html);
}

function resetForm() {
  document.getElementById('formSetoran').reset();
  document.getElementById('nasabahId').value = '';
  document.getElementById('nasabahInfo').style.display = 'none';
  document.getElementById('grandTotal').textContent = 'Rp 0';
  itemIdx = 1;
  document.getElementById('itemList').innerHTML = document.getElementById('itemList').children[0].outerHTML;
  document.querySelectorAll('.nilai-output').forEach(el => el.value = '');
}
</script>
@endpush
