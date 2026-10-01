<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesApi;
use App\Services\ApiClient;
use Illuminate\Http\Request;

class SkemaController extends Controller
{
    use HandlesApi;

    public function __construct(private ApiClient $api)
    {
    }

    public function index(Request $request)
    {
        $query = array_filter($request->only('search'), fn ($v) => $v !== null && $v !== '');
        $res   = $this->api->request('GET', '/skema', [], $query);

        if ($redirect = $this->guard($res)) {
            return $redirect;
        }

        return view('skema.index', [
            'items'    => $res['data'] ?? [],
            'apiError' => $res['ok'] ? null : $res['message'],
        ]);
    }

    public function create()
    {
        return view('skema.form', [
            'skema'  => [],
            'action' => route('skema.store'),
            'method' => 'POST',
            'title'  => 'Tambah Skema',
        ]);
    }

    public function store(Request $request)
    {
        $res = $this->api->request('POST', '/skema', $this->payload($request));

        return $this->afterSave($res, 'Skema berhasil ditambahkan.', route('skema.index'));
    }

    public function edit(string $id)
    {
        $res = $this->api->request('GET', "/skema/{$id}");

        if ($redirect = $this->guard($res)) {
            return $redirect;
        }
        if (! $res['ok']) {
            return redirect()->route('skema.index')->with('error', $res['message']);
        }

        return view('skema.form', [
            'skema'  => $res['data'],
            'action' => route('skema.update', $id),
            'method' => 'PUT',
            'title'  => 'Ubah Skema',
        ]);
    }

    public function update(Request $request, string $id)
    {
        $res = $this->api->request('PUT', "/skema/{$id}", $this->payload($request));

        return $this->afterSave($res, 'Skema berhasil diubah.', route('skema.index'));
    }

    public function destroy(string $id)
    {
        $res = $this->api->request('DELETE', "/skema/{$id}");

        if ($redirect = $this->guard($res)) {
            return $redirect;
        }

        return redirect()->route('skema.index')->with(
            $res['ok'] ? 'success' : 'error',
            $res['ok'] ? 'Skema berhasil dihapus.' : $res['message']
        );
    }

    private function payload(Request $request): array
    {
        return [
            'kode_skema' => (string) $request->input('kode_skema', ''),
            'nama_skema' => (string) $request->input('nama_skema', ''),
            'jenis'      => (string) $request->input('jenis', ''),
            'deskripsi'  => (string) $request->input('deskripsi', ''),
        ];
    }
}
