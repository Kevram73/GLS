<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Récupérer le profil de l'utilisateur authentifié.
     */
    public function profile()
    {
        return response()->json(Auth::user());
    }

    /**
     * Lister les utilisateurs par type de profil.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        if ($currentUser->type_user_id == 1) {
            $users = User::all();
        } else {
            $users = User::where('type_user_id', '<', $currentUser->type_user_id)->get();
        }

        return response()->json(UserResource::collection($users));
    }

    /**
     * Afficher un utilisateur spécifique.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json(UserResource::collection($user));
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email',
            'num_phone' => 'sometimes|string|max:15|unique:users,num_phone',
            'type_user_id' => 'required|exists:type_users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = new User();
        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->num_phone = $request->num_phone;
        $user->type_user_id = $request->type_user_id;
        $user->password = Hash::make('password');
        $user->actif = true;
        $user->save();

        return response()->json(['message' => 'Utilisateur créé avec succès', 'user' => $user]);
    }

    /**
     * Mettre à jour le profil de l'utilisateur authentifié.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'num_phone' => 'sometimes|string|max:15|unique:users,num_phone,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only(['nom', 'prenom', 'email', 'num_phone']));

        return response()->json(['message' => 'Profil mis à jour avec succès', 'user' => $user]);
    }

    /**
     * Modifier le mot de passe de l'utilisateur.
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();
        if($user == null)
        {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Mot de passe actuel incorrect'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Mot de passe mis à jour avec succès']);
    }

    /**
     * Supprimer un utilisateur (accessible uniquement pour l'utilisateur lui-même).
     */
    public function destroy()
    {
        $user = Auth::user();
        $user->delete();

        return response()->json(['message' => 'Compte supprimé avec succès']);
    }

    /**
     * Afficher les utilisateurs par type_user_id.
     */
    public function getUsersByType($type_user_id)
    {
        $users = User::where('type_user_id', $type_user_id)->get();
        return response()->json(UserResource::collection($users));
    }

    public function add_client(Request $request)
    {
        // Validation des données entrantes
        $validator = Validator::make($request->all(), [
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'num_phone' => 'required|string|max:15|unique:users,num_phone'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Génération aléatoire du mot de passe
        $pwd = rand(100000, 999999);

        // Création de l'utilisateur
        $user = User::create([
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'email'        => $request->email,
            'num_phone'    => $request->num_phone,
            'type_user_id' => 3,
            'password'     => Hash::make($pwd),
            'actif'        => true,
        ]);

        // Envoi automatique du mot de passe à l'utilisateur
        $this->sendPasswordToClient($user->email, $pwd);

        return response()->json(['message' => 'Utilisateur créé avec succès', 'user' => $user]);
    }

    // Méthode privée pour l'envoi de l'email
    private function sendPasswordToClient($email, $password)
    {
        Mail::raw("Votre mot de passe : $password", function ($message) use ($email) {
            $message->to($email)->subject('Votre mot de passe');
        });
    }

    public function check_email_availability($email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->json(['available' => false]);
        } else {
            return response()->json(['available' => true]);
        }
    }
    public function check_phone_availability($num_phone)
    {

        $user = User::where('num_phone', $num_phone)->first();

        if ($user) {
            return response()->json(['available' => false]);
        } else {
            return response()->json(['available' => true]);
        }
    }
}
