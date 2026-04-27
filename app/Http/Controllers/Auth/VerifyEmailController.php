<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->route('id'));

        // Validate the hash matches the user's email
        if (! hash_equals(sha256($user->getEmailForVerification()), (string) $request->route('hash'))) {
            abort(403, 'Invalid verification link.');
        }

        // Log the user in if they aren't already (e.g. opened link from email client)
        if (! Auth::check() || Auth::id() !== $user->id) {
            Auth::login($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            session()->flash('success', 'Email verified successfully! Welcome to your dashboard.');
        }

        return redirect()->intended(route('user-dashboard', absolute: false).'?verified=1');
    }
}
