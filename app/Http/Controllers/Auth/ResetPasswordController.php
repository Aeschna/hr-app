<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChanged;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Get the response for a successful password reset.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    protected function sendResetResponse(Request $request, $response): RedirectResponse
    {
        $user = Auth::user();
        Mail::to($user->email)->send(new PasswordChanged($user));

        return Redirect::to($this->redirectTo)
            ->with('status', 'Your password has been reset successfully!');
    }
}
