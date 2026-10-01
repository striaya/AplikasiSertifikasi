@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan data peserta dan skema sertifikasi')

@section('content')
@if ($data)
    @php
        $status = collect($data['peserta_per_status'] ?? []);
        $jumlah = fn ($s) => $status->firstWhere('status', $s)['jumlah'] ?? 0;
        $total  = max((int) $data['total_peserta'], 0);
    @endphp

    <div class="stats">
        <div class="card stat">
            <span class="stat-icon">@include('partials.icon', ['name' => 'users', 'size' => 20])</span>
            <div><div class="stat-label">Total Peserta</div><div class="stat-value">{{ $data['total_peserta'] }}</div></div>
        </div>
        <div class="card stat">
            <span class="stat-icon">@include('partials.icon', ['name' => 'layers', 'size' => 20])</span>
            <div><div class="stat-label">Skema</div><div class="stat-value">{{ $data['total_skema'] }}</div></div>
        </div>
        <div class="card stat">
            <span class="stat-icon">@include('partials.icon', ['name' => 'clock', 'size' => 20])</span>
            <div><div class="stat-label">Terdaftar</div><div class="stat-value">{{ $jumlah('terdaftar') }}</div></div>
        </div>
        <div class="card stat">
            <span class="stat-icon">@include('partials.icon', ['name' => 'check-circle', 'size' => 20])</span>
            <div><div class="stat-label">Kompeten</div><div class="stat-value">{{ $jumlah('kompeten') }}</div></div>
        </div>
    </div>

    <div class="grid-2">
        {{-- <div class="card">
            <div class="card-head"><h3>Peserta per Skema</h3></div>
            @forelse ($data['peserta_per_skema'] ?? [] as $row)
                <div class="bar-row">
                    <div class="bar-top">
                        <span>{{ $row['nama_skema'] }} <span class="cell-sub">{{ $row['kode_skema'] }}</span></span>
                        <b>{{ $row['jumlah_peserta'] }}</b>
                    </div>
                    <div class="bar"><i style="width: {{ $total > 0 ? round($row['jumlah_peserta'] / $total * 100) : 0 }}%"></i></div>
                </div>
            @empty
                <div class="empty">Belum ada skema.</div>
            @endforelse
        </div> --}}
{{-- 
        <div class="card">
            <div class="card-head"><h3>Status Peserta</h3></div>
            @forelse ($status as $row)
                <div class="bar-row">
                    <div class="bar-top">
                        <span>@include('partials.status', ['s' => $row['status']])</span>
                        <b>{{ $row['jumlah'] }}</b>
                    </div>
                    <div class="bar"><i style="width: {{ $total > 0 ? round($row['jumlah'] / $total * 100) : 0 }}%"></i></div>
                </div>
            @empty
                <div class="empty">Belum ada data.</div>
            @endforelse
        </div> --}}
    </div>

    <div class="card">
        <div class="card-head">
            <h3>Peserta Terbaru</h3>
            <a href="{{ route('peserta.index') }}" class="btn btn-ghost btn-sm">Lihat semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nama</th><th>NIK</th><th>Skema</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($data['peserta_terbaru'] ?? [] as $p)
                    <tr>
                        <td>
                            <a href="{{ route('peserta.show', $p['id']) }}" class="cell-title">{{ $p['nama'] }}</a>
                            <div class="cell-sub">{{ $p['email'] }}</div>
                        </td>
                        <td class="num">{{ $p['nik'] }}</td>
                        <td>{{ $p['skema']['nama_skema'] ?? '-' }}</td>
                        <td>@include('partials.status', ['s' => $p['status']])</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Belum ada peserta.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
