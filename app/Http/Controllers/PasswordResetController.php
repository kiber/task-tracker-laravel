<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordResetEmailRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use SensitiveParameter;

class PasswordResetController extends Controller
{
    public function showPasswordResetRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function showPasswordResetForm(#[SensitiveParameter] string $token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->string('email'),
        ]);
    }

    public function sendPasswordResetEmail(SendPasswordResetEmailRequest $request)
    {
        $email = $request->string('email');

        Password::sendResetLink(['email' => $email]);

        return back()->with(
            'status',
            'If an account with this email exists, we will send a password reset link to you shortly.'
        );
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $validatedData = $request->validated();

        $status = Password::reset($validatedData, function (User $user, #[\SensitiveParameter] string $newPassword) {
            $user->password = Hash::make($newPassword);
            $user->remember_token = Str::random(60);
            $user->save();

            event(new PasswordReset($user));
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()->withInput($request->only('email'))->withErrors(['email' => 'Failed to reset password.']);
    }
}
