<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'type_user_id' => 'required|exists:type_users,id',
        ]);

        $users = User::where('type_user_id', $request->type_user_id)->paginate(10);

        return response()->json($users);
    }

    /**
     * Afficher un utilisateur spécifique.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
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
}
