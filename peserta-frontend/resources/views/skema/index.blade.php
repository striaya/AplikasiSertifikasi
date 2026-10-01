@extends('layouts.app')

@section('title', 'Data Skema')
@section('heading', 'Skema Sertifikasi')
@section('subheading', 'Kelola skema KKNI, Okupasi, dan Klaster')

@section('actions')
    <a href="{{ route('skema.create') }}" class="btn btn-primary">@include('partials.icon', ['name' => 'plus', 'size' => 16]) Tambah Skema</a>
@endsection

@section('content')
<div class="card">
    <form method="GET" action="{{ route('skema.index') }}" class="filters">
        <div class="search">
            @include('partials.icon', ['name' => 'search', 'size' => 16])
            <input type="text" name="search" class="input" placeholder="Cari kode atau nama skema" value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        <a href="{{ route('skema.index') }}" class="btn btn-ghost btn-sm">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama Skema</th><th>Jenis</th><th>Deskripsi</th><th class="col-actions">Aksi</th></tr></thead>
            <tbody>
            @forelse ($items as $s)
                <tr>
                    <td><span class="badge badge-blue">{{ $s['kode_skema'] }}</span></td>
                    <td class="cell-title">{{ $s['nama_skema'] }}</td>
                    <td>{{ $s['jenis'] }}</td>
                    <td class="cell-sub">{{ \Illuminate\Support\Str::limit($s['deskripsi'] ?? '', 60) ?: '-' }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('skema.edit', $s['id']) }}" class="icon-btn" title="Ubah">@include('partials.icon', ['name' => 'edit', 'size' => 17])</a>
                            <form method="POST" action="{{ route('skema.destroy', $s['id']) }}"
                                  data-confirm="Hapus skema {{ $s['nama_skema'] }}? Skema yang masih dipakai peserta tidak dapat dihapus.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-btn danger" title="Hapus">@include('partials.icon', ['name' => 'trash', 'size' => 17])</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Data skema tidak ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
