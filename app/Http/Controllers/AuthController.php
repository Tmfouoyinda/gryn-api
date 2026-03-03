<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request){
        //Request contient l'ensemble des données de notre formulaire
        //ici $request->validate nous permet de recup les données préciser en s'assurant que les condition de validtion qu'on a écrit sont bien valide snn il renvoit direct une erreur 
        $request -> validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        //Auth::attempt permet de verifier si dans note base de données un user avec cet email et password existe
        //si oui il renvoi true snn false
        // dans le cas ou c'est false le if il termine le programme et renvoi le message d'erreur 
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'message'=> 'Email ou mot de passe incorrect',
            ],401);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('auth_token')->plainTextToken ;

        return response()->json([
            'message'=> 'connecté avec succès',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function signUp(Request $request){
         $request -> validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        $user = User::create([
            'name' => $request -> name,
            'email' => $request -> email,
            'password' => $request -> password,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken ;
        
        return response() -> json([
            'message' => "compte crée avec succès",
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request ){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message'=> 'Déconnecté avec succès'
        ]);
    }

    public function me(Request $request){
        return response()->json($request->user());
    }
}
