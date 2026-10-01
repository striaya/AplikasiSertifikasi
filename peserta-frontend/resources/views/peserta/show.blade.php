@extends('layouts.app')

@section('title', 'Detail Peserta')
@section('heading', $peserta['nama'])
@section('subheading', 'Detail data peserta')

@section('actions')
    <a href="{{ route('peserta.index') }}" class="btn btn-ghost">@include('partials.icon', ['name' => 'arrow-left', 'size' => 16]) Kembali</a>
    <a href="{{ route('peserta.edit', $peserta['id']) }}" class="btn btn-primary">@include('partials.icon', ['name' => 'edit', 'size' => 16]) Ubah Data</a>
@endsection

@section('content')
<div class="card card-pad">
    <dl class="detail">
        <div><dt>NIK</dt><dd class="num">{{ $peserta['nik'] }}</dd></div>
        <div><dt>Status</dt><dd>@include('partials.status', ['s' => $peserta['status']])</dd></div>
        <div><dt>Email</dt><dd>{{ $peserta['email'] }}</dd></div>
        <div><dt>No. Telepon</dt><dd class="num">{{ $peserta['no_telepon'] }}</dd></div>
        <div><dt>Jenis Kelamin</dt><dd>{{ $peserta['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
        <div><dt>Tanggal Lahir</dt><dd>{{ \Carbon\Carbon::parse($peserta['tanggal_lahir'])->translatedFormat('d F Y') }}</dd></div>
        <div class="full"><dt>Alamat</dt><dd>{{ $peserta['alamat'] }}</dd></div>
        <div>
            <dt>Skema Sertifikasi</dt>
            <dd>{{ $peserta['skema']['nama_skema'] ?? '-' }} <span class="badge badge-gray">{{ $peserta['skema']['kode_skema'] ?? '' }}</span></dd>
        </div>
        <div><dt>Jenis Skema</dt><dd>{{ $peserta['skema']['jenis'] ?? '-' }}</dd></div>
    </dl>
</div>
@endsection
