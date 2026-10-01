@extends('layouts.app')

@section('title', 'Data Peserta')
@section('heading', 'Data Peserta')
@section('subheading', 'Kelola seluruh peserta sertifikasi')

@section('actions')
    <a href="{{ route('peserta.create') }}" class="btn btn-primary">@include('partials.icon', ['name' => 'plus', 'size' => 16]) Tambah Peserta</a>
@endsection

@section('content')
<div class="card">
    <form method="GET" action="{{ route('peserta.index') }}" class="filters">
        <div class="search">
            @include('partials.icon', ['name' => 'search', 'size' => 16])
            <input type="text" name="search" class="input" placeholder="Cari nama, NIK, atau email" value="{{ request('search') }}">
        </div>

        <select name="skema_id" class="input">
            <option value="">Semua skema</option>
            @foreach ($skemaList as $s)
                <option value="{{ $s['id'] }}" @selected(request('skema_id') == $s['id'])>{{ $s['nama_skema'] }}</option>
            @endforeach
        </select>

        <select name="status" class="input">
            <option value="">Semua status</option>
            <option value="terdaftar" @selected(request('status') === 'terdaftar')>Terdaftar</option>
            <option value="kompeten" @selected(request('status') === 'kompeten')>Kompeten</option>
            <option value="belum_kompeten" @selected(request('status') === 'belum_kompeten')>Belum Kompeten</option>
        </select>

        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        <a href="{{ route('peserta.index') }}" class="btn btn-ghost btn-sm">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>No</th><th>Peserta</th><th>NIK</th><th>Skema</th><th>Status</th><th class="col-actions">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse ($items as $p)
                <tr>
                    <td class="num">{{ (($pagination['page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + $loop->iteration }}</td>
                    <td>
                        <div class="cell-title">{{ $p['nama'] }}</div>
                        <div class="cell-sub">{{ $p['email'] }}</div>
                    </td>
                    <td class="num">{{ $p['nik'] }}</td>
                    <td><span class="badge badge-gray">{{ $p['skema']['kode_skema'] ?? '-' }}</span></td>
                    <td>@include('partials.status', ['s' => $p['status']])</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('peserta.show', $p['id']) }}" class="icon-btn" title="Detail">@include('partials.icon', ['name' => 'eye', 'size' => 17])</a>
                            <a href="{{ route('peserta.edit', $p['id']) }}" class="icon-btn" title="Ubah">@include('partials.icon', ['name' => 'edit', 'size' => 17])</a>
                            <form method="POST" action="{{ route('peserta.destroy', $p['id']) }}"
                                  data-confirm="Hapus data peserta {{ $p['nama'] }}? Tindakan ini tidak dapat dibatalkan.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-btn danger" title="Hapus">@include('partials.icon', ['name' => 'trash', 'size' => 17])</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Data peserta tidak ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($pagination && $pagination['total_pages'] > 1)
        @php
            $cur   = $pagination['page'];
            $last  = $pagination['total_pages'];
            $start = max(1, $cur - 2);
            $end   = min($last, $cur + 2);
        @endphp
        <div class="pager">
            <span>Halaman {{ $cur }} dari {{ $last }}, total {{ $pagination['total'] }} peserta</span>
            <div class="pages">
                @if ($cur > 1)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $cur - 1]) }}">@include('partials.icon', ['name' => 'chevron-left', 'size' => 16])</a>
                @else
                    <span class="disabled">@include('partials.icon', ['name' => 'chevron-left', 'size' => 16])</span>
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i === $cur)
                        <span class="current">{{ $i }}</span>
                    @else
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                    @endif
                @endfor

                @if ($cur < $last)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $cur + 1]) }}">@include('partials.icon', ['name' => 'chevron-right', 'size' => 16])</a>
                @else
                    <span class="disabled">@include('partials.icon', ['name' => 'chevron-right', 'size' => 16])</span>
                @endif
            </div>
        </div>
    @elseif ($pagination)
        <div class="pager"><span>Total {{ $pagination['total'] }} peserta</span></div>
    @endif
</div>
@endsection
