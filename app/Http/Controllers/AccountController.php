<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Mail\AccountUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordChanged;

class AccountController extends Controller
{
    /**
     * Display the user's account information.
     */
    public function index()
    {
        return view('account.index', [
            'user' => Auth::user()
        ]);
    }
    public function update(Request $request)
    {
        $user = Auth::user();
    
        // Update user information
        if ($request->has('name') || $request->has('email')) {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            ]);
    
            $user->name = $request->input('name', $user->name);
            $user->email = $request->input('email', $user->email);
        }
    
        // Update password if provided
        if ($request->filled('current_password') && $request->filled('new_password')) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);
    
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }
    
            $user->password = Hash::make($request->input('new_password'));
        }
    
        $user->save();
    
        // Send email notification
        Mail::to($user->email)->send(new AccountUpdated($user, $request->filled('new_password')));

    
        return redirect()->route('my-account')->with('status', 'Account updated successfully.');
    }
    


public function showChangePasswordForm()
{
    return view('password.change');
}
public function changePassword(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    if (!Hash::check($request->input('current_password'), $user->password)) {
        return back()->withErrors(['current_password' => 'The current password is incorrect.']);
    }

    $user->password = Hash::make($request->input('new_password'));
    $user->save();

    // Send email notification
    Mail::to($user->email)->send(new PasswordChanged($user));

    return redirect()->route('my-account')->with('status', 'Password changed successfully.');
}

}
