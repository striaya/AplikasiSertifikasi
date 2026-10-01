<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesApi;
use App\Services\ApiClient;

class DashboardController extends Controller
{
    use HandlesApi;

    public function __construct(private ApiClient $api)
    {
    }

    public function index()
    {
        $res = $this->api->request('GET', '/dashboard');

        if ($redirect = $this->guard($res)) {
            return $redirect;
        }

        return view('dashboard', [
            'data'     => $res['data'],
            'apiError' => $res['ok'] ? null : $res['message'],
        ]);
    }
}
