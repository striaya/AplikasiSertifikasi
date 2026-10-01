<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\RedirectResponse;

trait HandlesApi
{
    /** Jika token ditolak API (401), bersihkan session dan arahkan ke login. */
    protected function guard(array $res): ?RedirectResponse
    {
        if ($res['status'] === 401) {
            session()->flush();

            return redirect()->route('login')
                ->withErrors(['login' => 'Sesi Anda berakhir, silakan login kembali.']);
        }

        return null;
    }

    /** Penanganan hasil simpan (tambah/ubah): sukses -> redirect, gagal -> kembali ke form + pesan validasi API. */
    protected function afterSave(array $res, string $successMessage, string $redirectTo): RedirectResponse
    {
        if ($redirect = $this->guard($res)) {
            return $redirect;
        }

        if ($res['ok']) {
            return redirect($redirectTo)->with('success', $successMessage);
        }

        return back()
            ->withErrors($res['errors'])
            ->withInput()
            ->with('error', $res['message']);
    }
}
