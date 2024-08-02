<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display the user's account information.
     */
    public function index(Request $request)
    {
        // Kullanıcı bilgilerini al
        $user = $request->user();

        // Hesap bilgileri görünümüne veri gönder
        return view('account.index', compact('user'));
    }
    public function update(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
    ]);

    $user = $request->user();
    $user->update($request->only('name', 'email'));

    return redirect()->route('my-account')->with('success', 'Account updated successfully.');
}

}
