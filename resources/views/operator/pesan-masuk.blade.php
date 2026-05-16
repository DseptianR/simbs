@extends('layouts.app_nasabah_operator')
@php $mode = 'operator'; $title = 'Pesan Masuk'; $subtitle = 'Pesan dari halaman kontak'; @endphp

@section('content')
<div class="page-header">
  <div class="breadcrumb"><a href="{{ route('operator.dashboard') }}">Dashboard</a> <span>/</span> Pesan Masuk</div>
  <h1>✉️ Pesan Masuk</h1>
  <p>Pesan yang dikirim melalui halaman kontak website.</p>
</div>

@if(session('success'))
  <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif

<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title">Semua Pesan</div>
      <div class="card-subtitle">{{ $pesan->total() }} total · {{ $belumDibaca }} belum dibaca</div>
    </div>
  </div>

  @if($pesan->isEmpty())
    <div class="empty-state">
      <div class="empty-icon">✉️</div>
      <p>Belum ada pesan masuk.</p>
    </div>
  @else
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Pengirim</th>
          <th>Subjek</th>
          <th>Pesan</th>
          <th>Waktu</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pesan as $p)
        <tr style="{{ !$p->sudah_dibaca ? 'background:var(--green-50);' : '' }}">
          <td>
            <div class="fw-600">{{ $p->nama }}</div>
            <div class="text-xs text-muted">{{ $p->email }}</div>
          </td>
          <td class="fw-600">{{ $p->subjek }}</td>
          <td style="max-width:300px;">
            <div style="overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; font-size:13px; color:var(--gray-600);">
              {{ $p->pesan }}
            </div>
          </td>
          <td class="text-muted text-sm">{{ $p->created_at->diffForHumans() }}</td>
          <td>
            @if($p->sudah_dibaca)
              <span class="badge badge-gray">Dibaca</span>
            @else
              <span class="badge badge-green">Baru</span>
            @endif
          </td>
          <td>
            <div class="flex gap-8">
              {{-- Modal detail --}}
              <button class="btn btn-secondary btn-sm"
                onclick="lihatPesan('{{ addslashes($p->nama) }}','{{ addslashes($p->email) }}','{{ addslashes($p->subjek) }}',`{{ addslashes($p->pesan) }}`)">
                👁 Lihat
              </button>
              @if(!$p->sudah_dibaca)
              <form method="POST" action="{{ route('operator.pesan-masuk.baca', $p) }}">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">✓ Baca</button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-16">{{ $pesan->links() }}</div>
  @endif
</div>

{{-- Modal Detail Pesan --}}
<div id="modalPesan" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:16px; padding:32px; max-width:520px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,.3);">
    <div class="flex items-center justify-between mb-16">
      <div class="fw-700" style="font-size:16px;">Detail Pesan</div>
      <button onclick="tutupModal()" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--gray-400);">✕</button>
    </div>
    <div style="display:flex; flex-direction:column; gap:12px;">
      <div style="background:var(--gray-50); border-radius:8px; padding:12px 14px;">
        <div class="text-xs text-muted">Dari</div>
        <div class="fw-600" id="mNama"></div>
        <div class="text-sm text-muted" id="mEmail"></div>
      </div>
      <div style="background:var(--gray-50); border-radius:8px; padding:12px 14px;">
        <div class="text-xs text-muted">Subjek</div>
        <div class="fw-600" id="mSubjek"></div>
      </div>
      <div style="background:var(--gray-50); border-radius:8px; padding:12px 14px;">
        <div class="text-xs text-muted mb-4">Pesan</div>
        <div id="mPesan" style="font-size:14px; line-height:1.7; white-space:pre-wrap;"></div>
      </div>
    </div>
    <div class="mt-16 flex justify-end">
      <button onclick="tutupModal()" class="btn btn-secondary">Tutup</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function lihatPesan(nama, email, subjek, pesan) {
  document.getElementById('mNama').textContent   = nama;
  document.getElementById('mEmail').textContent  = email;
  document.getElementById('mSubjek').textContent = subjek;
  document.getElementById('mPesan').textContent  = pesan;
  document.getElementById('modalPesan').style.display = 'flex';
}
function tutupModal() {
  document.getElementById('modalPesan').style.display = 'none';
}
document.getElementById('modalPesan').addEventListener('click', function(e) {
  if (e.target === this) tutupModal();
});
</script>
@endpush
