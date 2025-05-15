<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class GoogleAuthController extends Controller
{
    public function loginWithGoogle(Request $request){
        $request->validate([
            'id_token' => 'required|string',
        ]);

        // 1. Verify the token with Google
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->id_token,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Invalid ID token'], 401);
        }

        $googleUser = $response->json();

        // Optional: Validate `aud` against your Google client ID
        // if ($googleUser['aud'] !== config('services.google.client_id')) {
        //     return response()->json(['error' => 'Invalid audience.'], 401);
        // }

        // 2. Find or create user
        $user = User::updateOrCreate(
            ['email' => $googleUser['email']],
            [
                'name' => $googleUser['name'],
                'google_id' => $googleUser['sub'],
                'avatar' => $googleUser['picture'],
            ]
        );

        // 3. Create token (Sanctum)
        $token = $user->createToken('google-login')->plainTextToken;

        // 4. Return response
        return response()->json([
            'user_id' => $user->id,
            'username' => $user->name,
            'access_token' => $token,
        ]);

    }
}
