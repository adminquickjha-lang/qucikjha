<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Authentication failed.');
        }

        $user = User::updateOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'name' => $socialUser->getName(),
            'google_id' => $socialUser->getId(),
            'email_verified_at' => now(),
            // Avatar is optional, but many users like it
            // 'avatar' => $socialUser->getAvatar(),
        ]);

        // If the user doesn't have a role, default to user
        if (! $user->role) {
            $user->update(['role' => 'user']);
        }

        Auth::login($user);

        return redirect()->intended(route('user-dashboard', absolute: false));
    }
}
