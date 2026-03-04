<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'unauthorized'
            ], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type'  => 'bearer'
            ]
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Envoi du lien (on ignore le statut pour éviter l'énumération d'emails)
        Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => 'Cliquez sur le lien reçu par mail.'
        ]);
    }

    public function getResetPassword(string $token)
    {
        // Récupère l'entrée sans hasher manuellement le token
        $passwordReset = DB::table('password_reset_tokens')
            ->where('created_at', '>=', now()->subMinutes(60))
            ->get() // on récupère tout pour pouvoir utiliser Hash::check
            ->first(fn($row) => Hash::check($token, $row->token));

        abort_if(!$passwordReset, 404);

        return response()->json([
            'email' => $passwordReset->email,
            'token' => $token,
        ]);
    }

    public function postResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Mot de passe réinitialisé avec succès.']);
        }

        return response()->json(['message' => 'Requête invalide.'], 422);
    }
}