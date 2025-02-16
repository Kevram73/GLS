<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */

    public function index(){
        $message = "Api works well";
        return response()->json(['message' => $message]);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'num_phone' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'type_user_id' => 'required|exists:type_users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'num_phone' => $request->num_phone,
            'password' => Hash::make($request->password),
            'type_user_id' => $request->type_user_id,
            'otp_code' => rand(100000, 999999),
            'otp_expires_at' => Carbon::now()->addMinutes(10),
            'actif' => false
        ]);

        // Send OTP to user
        $this->otpSend($user);

        return response()->json([
            'message' => 'User registered successfully. OTP sent to email.',
            'user' => $user
        ], 201);
    }


public function login(Request $request)
{
    // Validation des données
    $request->validate([
        'phone' => 'required|string',
        'password' => 'required|string',
    ]);

    // Vérification des identifiants (remplace `email` par `num_phone`)
    if (!Auth::attempt(['num_phone' => $request->phone, 'password' => $request->password])) {
        return response()->json(['message' => 'Numéro de téléphone ou mot de passe incorrect'], 401);
    }

    // Récupérer l'utilisateur authentifié
    $user = Auth::user();

    // Vérifier si l'utilisateur est actif (optionnel)
    if (isset($user->actif) && !$user->actif) {
        return response()->json(['message' => 'Compte désactivé. Contactez un administrateur.'], 403);
    }

    // Générer un token API avec Laravel Sanctum
    $token = $user->createToken('auth_token')->plainTextToken;

    // Retourner la réponse JSON
    return response()->json([
        'message' => 'Connexion réussie',
        'token' => $token,
        'user' => $user,
    ]);
}


    /**
     * Logout user and revoke token.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Send OTP to user's email.
     */
    public function otpSend(User $user)
    {
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // Simulating email sending
        Mail::raw("Your OTP code is: $otp", function ($message) use ($user) {
            $message->to($user->email)->subject('Your OTP Code');
        });

        return response()->json(['message' => 'OTP sent successfully']);
    }

    /**
     * Verify OTP.
     */
    public function otpVerify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp_code' => 'required|integer',
        ]);

        $user = User::where('num_phone', $request->phone)->first();

        if (!$user || $user->otp_code != $request->otp_code) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->actif = true;
        $user->save();

        return response()->json(['message' => 'OTP verified successfully']);
    }

    /**
     * Forgot password - send reset token.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $user = User::where('num_phone', $request->phone)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $token = Str::random(60);
        $user->password_reset_token = $token;
        $user->password_reset_expires_at = Carbon::now()->addMinutes(30);
        $user->save();

        Mail::raw("Your password reset token is: $token", function ($message) use ($user) {
            $message->to($user->email)->subject('Password Reset Token');
        });

        return response()->json(['message' => 'Password reset token sent']);
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->where('password_reset_token', $request->token)
            ->first();

        if (!$user || Carbon::now()->gt($user->password_reset_expires_at)) {
            return response()->json(['message' => 'Invalid or expired token'], 400);
        }

        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->password_reset_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
