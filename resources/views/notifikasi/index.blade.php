@extends('layouts.app_nasabah_operator')
@php
  $mode  = $guard === 'nasabah' ? 'nasabah' : 'operator';
  $title = 'Notifikasi';
  $subtitle = 'Pemberitahuan terbaru untuk Anda';
@endphp

@section('content')
<div class="page-header">
  <h1>🔔 Notifikasi</h1>
  <p>Semua pemberitahuan terbaru untuk Anda.</p>
</div>

@if(session('success'))
  <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif

<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title">Semua Notifikasi</div>
      <div class="card-subtitle">{{ $notifikasi->total() }} total notifikasi</div>
    </div>
    @if($notifikasi->total() > 0)
    <form method="POST" action="{{ $guard === 'nasabah' ? route('nasabah.notifikasi.baca-semua') : route('operator.notifikasi.baca-semua') }}">
      @csrf
      <button type="submit" class="btn btn-secondary btn-sm">✓ Tandai Semua Dibaca</button>
    </form>
    @endif
  </div>

  @if($notifikasi->isEmpty())
    <div class="empty-state">
      <div class="empty-icon">🔔</div>
      <p>Tidak ada notifikasi.</p>
    </div>
  @else
  <div style="display:flex; flex-direction:column; gap:2px;">
    @foreach($notifikasi as $n)
    <div style="display:flex; align-items:flex-start; gap:14px; padding:14px 12px; border-radius:10px; background:{{ $n->sudahDibaca() ? 'transparent' : 'var(--green-50)' }}; border-bottom:1px solid var(--gray-100); transition:background .15s;">
      {{-- Ikon --}}
      <div style="width:42px; height:42px; background:{{ $n->warnaTipe() }}; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
        {{ $n->ikon }}
      </div>

      {{-- Konten --}}
      <div style="flex:1; min-width:0;">
        <div class="flex items-center justify-between gap-8">
          <div class="fw-700" style="font-size:14px; color:{{ $n->sudahDibaca() ? 'var(--gray-600)' : 'var(--gray-900)' }};">
            {{ $n->judul }}
            @if(!$n->sudahDibaca())
              <span style="display:inline-block; width:8px; height:8px; background:var(--green-500); border-radius:50%; margin-left:6px; vertical-align:middle;"></span>
            @endif
          </div>
          <span class="text-xs text-muted" style="white-space:nowrap;">{{ $n->created_at->diffForHumans() }}</span>
        </div>
        <div class="text-sm text-muted mt-4">{{ $n->pesan }}</div>
      </div>

      {{-- Aksi --}}
      @if(!$n->sudahDibaca())
      <form method="POST" action="{{ $guard === 'nasabah' ? route('nasabah.notifikasi.baca', $n) : route('operator.notifikasi.baca', $n) }}" style="flex-shrink:0;">
        @csrf
        <button type="submit" class="btn btn-secondary btn-sm" title="Tandai dibaca">✓</button>
      </form>
      @else
      <span class="text-xs text-muted" style="flex-shrink:0; padding-top:4px;">Dibaca</span>
      @endif
    </div>
    @endforeach
  </div>

  <div class="mt-16">{{ $notifikasi->links() }}</div>
  @endif
</div>
@endsection
