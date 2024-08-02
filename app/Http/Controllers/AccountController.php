<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;

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
    // Validate the request
    $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|string|email|max:255|unique:users,email,' . auth()->id(),
    ]);

    // Update user information
    $user = auth()->user();
    $user->update($request->only('name', 'email'));

    // Redirect with a success message
    return redirect()->route('my-account')->with('status', 'Account information updated successfully.');
}

public function showChangePasswordForm()
{
    return view('password.change');
}
public function changePassword(Request $request)
{
    // Validate the password change request
    $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:8|confirmed',
    ]);

    // Check current password
    if (!Hash::check($request->current_password, auth()->user()->password)) {
        return back()->withErrors(['current_password' => 'Current password does not match.']);
    }

    // Update password
    auth()->user()->update(['password' => Hash::make($request->new_password)]);

    return redirect()->route('my-account')->with('status', 'Password changed successfully.');
}


}
