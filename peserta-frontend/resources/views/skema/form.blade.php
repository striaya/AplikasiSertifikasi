@extends('layouts.app')

@php $v = fn ($key) => old($key, $skema[$key] ?? ''); @endphp

@section('title', $title)
@section('heading', $title)
@section('subheading', 'Isi data skema sertifikasi')

@section('actions')
    <a href="{{ route('skema.index') }}" class="btn btn-ghost">@include('partials.icon', ['name' => 'arrow-left', 'size' => 16]) Kembali</a>
@endsection

@section('content')
<div class="card card-pad">
    <form method="POST" action="{{ $action }}" novalidate>
        @csrf
        @if ($method !== 'POST') @method($method) @endif

        <div class="form-grid">
            <div class="field">
                <label for="kode_skema">Kode Skema</label>
                <input id="kode_skema" name="kode_skema" type="text" placeholder="contoh: SKM-JWD"
                       class="input @error('kode_skema') invalid @enderror" value="{{ $v('kode_skema') }}">
                @error('kode_skema') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="jenis">Jenis</label>
                <select id="jenis" name="jenis" class="input @error('jenis') invalid @enderror">
                    <option value="">Pilih jenis</option>
                    @foreach (['KKNI', 'Okupasi', 'Klaster'] as $j)
                        <option value="{{ $j }}" @selected($v('jenis') === $j)>{{ $j }}</option>
                    @endforeach
                </select>
                @error('jenis') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field full">
                <label for="nama_skema">Nama Skema</label>
                <input id="nama_skema" name="nama_skema" type="text" class="input @error('nama_skema') invalid @enderror" value="{{ $v('nama_skema') }}">
                @error('nama_skema') <span class="err">{{ $message }}</span> @enderror
            </div>

            <div class="field full">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="input">{{ $v('deskripsi') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check', 'size' => 16]) Simpan</button>
            <a href="{{ route('skema.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>
</div>
@endsection
