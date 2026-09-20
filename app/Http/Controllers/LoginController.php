<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], ['email' => 'メールアドレス', 'password' => 'パスワード']);

        $account = $this->testAccount();

        if ($account === null
            || $validated['email'] !== $account['email']
            || $validated['password'] !== $account['password']
        ) {
            return back()
                ->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。'])
                ->onlyInput('email');
        }

        $request->session()->put('authenticated', true);
        $request->session()->put('customer_name', $account['name']);
        $request->session()->put('customer_email', $account['email']);

        return redirect()->route('products.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['authenticated', 'customer_name', 'customer_email']);

        return redirect()->route('products.index');
    }

    /**
     * @return array{name: string, email: string, password: string}|null
     */
    private function testAccount(): ?array
    {
        $path = config_path('test-accounts.ini');

        if (! file_exists($path)) {
            return null;
        }

        $ini = parse_ini_file($path, true);

        return $ini['test_user'] ?? null;
    }
}
