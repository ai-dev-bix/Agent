<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth provider
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            
            if ($existingUser) {
                // Update Google ID if not set
                if (!$existingUser->google_id) {
                    $existingUser->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                }
                
                Auth::login($existingUser);
                
                return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
            }
            
            // Create new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(24)), // Random password since they'll use Google
                'credits' => 0,
                'free_messages_used' => 0,
                'free_messages_limit' => (int) env('DEFAULT_FREE_MESSAGES', 3),
                'is_admin' => false,
                'preferences' => json_encode([
                    'theme' => 'system',
                    'language' => 'en',
                ]),
            ]);
            
            Auth::login($user);
            
            return redirect('/dashboard')->with('success', 'Account created successfully! Welcome to ' . config('app.name') . '!');
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Authentication failed. Please try again.');
        }
    }

    /**
     * Redirect to Facebook OAuth provider (if needed in future)
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook OAuth callback (if needed in future)
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            
            // Check if user already exists
            $existingUser = User::where('email', $facebookUser->getEmail())->first();
            
            if ($existingUser) {
                // Update Facebook ID if not set
                if (!$existingUser->facebook_id) {
                    $existingUser->update([
                        'facebook_id' => $facebookUser->getId(),
                    ]);
                }
                
                Auth::login($existingUser);
                
                return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
            }
            
            // Create new user
            $user = User::create([
                'name' => $facebookUser->getName(),
                'email' => $facebookUser->getEmail(),
                'facebook_id' => $facebookUser->getId(),
                'avatar' => $facebookUser->getAvatar(),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(24)), // Random password since they'll use Facebook
                'credits' => 0,
                'free_messages_used' => 0,
                'free_messages_limit' => (int) env('DEFAULT_FREE_MESSAGES', 3),
                'is_admin' => false,
                'preferences' => json_encode([
                    'theme' => 'system',
                    'language' => 'en',
                ]),
            ]);
            
            Auth::login($user);
            
            return redirect('/dashboard')->with('success', 'Account created successfully! Welcome to ' . config('app.name') . '!');
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Authentication failed. Please try again.');
        }
    }
}