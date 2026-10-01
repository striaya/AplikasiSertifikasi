@extends('layouts.app')

@php $v = fn ($key) => old($key, $peserta[$key] ?? ''); @endphp

@section('title', $title)
@section('heading', $title)
@section('subheading', 'Lengkapi data peserta dengan benar')

@section('actions')
    <a href="{{ route('peserta.index') }}" class="btn btn-ghost">@include('partials.icon', ['name' => 'arrow-left', 'size' => 16]) Kembali</a>
@endsection

@section('content')
<div class="card card-pad">
    <form method="POST" action="{{ $action }}" novalidate>
        @csrf
        @if ($method !== 'POST') @method($method) @endif

        <div class="form-grid">
            <div class="field">
                <label for="nik">NIK</label>
                <input id="nik" name="nik" type="text" inputmode="numeric" maxlength="16" placeholder="16 digit angka"
                       class="input @error('nik') invalid @enderror" value="{{ $v('nik') }}">
                @error('nik') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="nama">Nama Lengkap</label>
                <input id="nama" name="nama" type="text" class="input @error('nama') invalid @enderror" value="{{ $v('nama') }}">
                @error('nama') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" class="input @error('email') invalid @enderror" value="{{ $v('email') }}">
                @error('email') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="no_telepon">No. Telepon</label>
                <input id="no_telepon" name="no_telepon" type="text" inputmode="numeric" class="input @error('no_telepon') invalid @enderror" value="{{ $v('no_telepon') }}">
                @error('no_telepon') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin" class="input @error('jenis_kelamin') invalid @enderror">
                    <option value="">Pilih jenis kelamin</option>
                    <option value="L" @selected($v('jenis_kelamin') === 'L')>Laki-laki</option>
                    <option value="P" @selected($v('jenis_kelamin') === 'P')>Perempuan</option>
                </select>
                @error('jenis_kelamin') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input id="tanggal_lahir" name="tanggal_lahir" type="date" class="input @error('tanggal_lahir') invalid @enderror" value="{{ $v('tanggal_lahir') }}">
                @error('tanggal_lahir') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="skema_id">Skema Sertifikasi</label>
                <select id="skema_id" name="skema_id" class="input @error('skema_id') invalid @enderror">
                    <option value="">Pilih skema</option>
                    @foreach ($skemaList as $s)
                        <option value="{{ $s['id'] }}" @selected((string) $v('skema_id') === (string) $s['id'])>{{ $s['kode_skema'] }} - {{ $s['nama_skema'] }}</option>
                    @endforeach
                </select>
                @error('skema_id') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status" class="input @error('status') invalid @enderror">
                    @php $st = $v('status') ?: 'terdaftar'; @endphp
                    <option value="terdaftar" @selected($st === 'terdaftar')>Terdaftar</option>
                    <option value="kompeten" @selected($st === 'kompeten')>Kompeten</option>
                    <option value="belum_kompeten" @selected($st === 'belum_kompeten')>Belum Kompeten</option>
                </select>
                @error('status') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field full">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" class="input @error('alamat') invalid @enderror">{{ $v('alamat') }}</textarea>
                @error('alamat') <span class="err">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check', 'size' => 16]) Simpan</button>
            <a href="{{ route('peserta.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>
</div>
@endsection
