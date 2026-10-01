<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private ApiClient $api)
    {
    }

    public function showLogin(Request $request)
    {
        if ($request->session()->has('api_token')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $res = $this->api->request('POST', '/auth/login', [
            'username' => (string) $request->input('username', ''),
            'password' => (string) $request->input('password', ''),
        ]);

        if (! $res['ok']) {
            $errors = $res['errors'] ?: ['login' => $res['message']];

            return back()->withErrors($errors)->onlyInput('username');
        }

        $request->session()->regenerate();
        session([
            'api_token' => $res['data']['token'],
            'api_user'  => $res['data']['user'],
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
