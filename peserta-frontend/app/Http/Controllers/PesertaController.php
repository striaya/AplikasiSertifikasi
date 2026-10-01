<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesApi;
use App\Services\ApiClient;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    use HandlesApi;

    public function __construct(private ApiClient $api)
    {
    }

    public function index(Request $request)
    {
        $query = array_filter(
            $request->only(['search', 'skema_id', 'status', 'page']),
            fn ($v) => $v !== null && $v !== ''
        );
        $query['limit'] = 10;

        $res   = $this->api->request('GET', '/peserta', [], $query);
        $skema = $this->api->request('GET', '/skema');

        if ($redirect = $this->guard($res)) {
            return $redirect;
        }

        return view('peserta.index', [
            'items'      => $res['data']['items'] ?? [],
            'pagination' => $res['data']['pagination'] ?? null,
            'skemaList'  => $skema['data'] ?? [],
            'apiError'   => $res['ok'] ? null : $res['message'],
        ]);
    }

    public function create()
    {
        $skema = $this->api->request('GET', '/skema');

        if ($redirect = $this->guard($skema)) {
            return $redirect;
        }

        return view('peserta.form', [
            'peserta'   => [],
            'skemaList' => $skema['data'] ?? [],
            'action'    => route('peserta.store'),
            'method'    => 'POST',
            'title'     => 'Tambah Peserta',
        ]);
    }

    public function store(Request $request)
    {
        $res = $this->api->request('POST', '/peserta', $this->payload($request));

        return $this->afterSave($res, 'Peserta berhasil ditambahkan.', route('peserta.index'));
    }

    public function show(string $id)
    {
        $res = $this->api->request('GET', "/peserta/{$id}");

        if ($redirect = $this->guard($res)) {
            return $redirect;
        }
        if (! $res['ok']) {
            return redirect()->route('peserta.index')->with('error', $res['message']);
        }

        return view('peserta.show', ['peserta' => $res['data']]);
    }

    public function edit(string $id)
    {
        $res   = $this->api->request('GET', "/peserta/{$id}");
        $skema = $this->api->request('GET', '/skema');

        if ($redirect = $this->guard($res)) {
            return $redirect;
        }
        if (! $res['ok']) {
            return redirect()->route('peserta.index')->with('error', $res['message']);
        }

        return view('peserta.form', [
            'peserta'   => $res['data'],
            'skemaList' => $skema['data'] ?? [],
            'action'    => route('peserta.update', $id),
            'method'    => 'PUT',
            'title'     => 'Ubah Peserta',
        ]);
    }

    public function update(Request $request, string $id)
    {
        $res = $this->api->request('PUT', "/peserta/{$id}", $this->payload($request));

        return $this->afterSave($res, 'Peserta berhasil diubah.', route('peserta.index'));
    }

    public function destroy(string $id)
    {
        // API Go mewajibkan ?confirm=true sebagai konfirmasi penghapusan.
        $res = $this->api->request('DELETE', "/peserta/{$id}", [], ['confirm' => 'true']);

        if ($redirect = $this->guard($res)) {
            return $redirect;
        }

        return redirect()->route('peserta.index')->with(
            $res['ok'] ? 'success' : 'error',
            $res['ok'] ? 'Peserta berhasil dihapus.' : $res['message']
        );
    }

    private function payload(Request $request): array
    {
        return [
            'nik'           => (string) $request->input('nik', ''),
            'nama'          => (string) $request->input('nama', ''),
            'email'         => (string) $request->input('email', ''),
            'no_telepon'    => (string) $request->input('no_telepon', ''),
            'jenis_kelamin' => (string) $request->input('jenis_kelamin', ''),
            'tanggal_lahir' => (string) $request->input('tanggal_lahir', ''),
            'alamat'        => (string) $request->input('alamat', ''),
            'skema_id'      => (int) $request->input('skema_id', 0),
            'status'        => (string) $request->input('status', ''),
        ];
    }
}
