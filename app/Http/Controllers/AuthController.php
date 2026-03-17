<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Connecte un utilisateur et retourne son token Sanctum.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Email ou mot de passe incorrect.'], 401);
        }

        $user  = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connecté avec succès.',
            'user'    => new UserResource($user),
            'token'   => $token,
        ]);
    }

    /**
     * Crée un nouveau compte utilisateur.
     */
    public function signUp(SignUpRequest $request): JsonResponse
    {
        $user  = User::create([
            'name'       => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => $request->password,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Compte créé avec succès.',
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 201);
    }

    /**
     * Révoque le token courant de l'utilisateur.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    /**
     * Retourne l'utilisateur authentifié.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Envoie un lien de réinitialisation de mot de passe par email.
     * On ne révèle pas si l'email existe ou non (anti-énumération).
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        Password::sendResetLink($request->only('email'));

        return response()->json(['message' => 'Si cet email existe, un lien de réinitialisation a été envoyé.']);
    }

    /**
     * Vérifie la validité d'un token de réinitialisation (≤ 60 min).
     */
    public function getResetPassword(string $token): JsonResponse
    {
        $record = DB::table('password_reset_tokens')
            ->where('created_at', '>=', now()->subMinutes(60))
            ->get()
            ->first(fn ($row) => Hash::check($token, $row->token));

        abort_if(! $record, 404, 'Lien invalide ou expiré.');

        return response()->json(['email' => $record->email]);
    }

    /**
     * Réinitialise le mot de passe avec le token reçu par email.
     */
    public function postResetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            fn ($user, $password) => $user->forceFill(['password' => Hash::make($password)])->save()
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Mot de passe réinitialisé avec succès.']);
        }

        return response()->json(['message' => 'Requête invalide.'], 422);
    }
}
