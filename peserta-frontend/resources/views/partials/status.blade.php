@php
    $map = [
        'terdaftar'      => ['Terdaftar', 'badge-blue'],
        'kompeten'       => ['Kompeten', 'badge-green'],
        'belum_kompeten' => ['Belum Kompeten', 'badge-amber'],
    ];
    [$label, $cls] = $map[$s ?? ''] ?? [ucfirst(str_replace('_', ' ', $s ?? '-')), 'badge-gray'];
@endphp
<span class="badge {{ $cls }}">{{ $label }}</span>
