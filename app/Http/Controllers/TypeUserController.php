<?php

namespace App\Http\Controllers;

use App\Models\TypeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TypeUserController extends Controller
{
    /**
     * Lister tous les types d’utilisateurs.
     */
    public function index()
    {
        $typeUsers = TypeUser::latest()->paginate(10);
        return response()->json($typeUsers);
    }

    public function get_other_types()
    {
        $type_user = Auth::user()->type_user_id;
        $typeUsers = TypeUser::where('id', '<', 1)->get();
        return response()->json($typeUsers);
    }

    /**
     * Créer un nouveau type d’utilisateur.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:type_users,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $typeUser = TypeUser::create($request->all());

        return response()->json(['message' => 'Type d’utilisateur créé avec succès', 'type_user' => $typeUser], 201);
    }

    /**
     * Récupérer un type d’utilisateur spécifique.
     */
    public function show($id)
    {
        $typeUser = TypeUser::findOrFail($id);
        return response()->json($typeUser);
    }

    /**
     * Mettre à jour un type d’utilisateur.
     */
    public function update(Request $request, $id)
    {
        $typeUser = TypeUser::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:type_users,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $typeUser->update($request->all());

        return response()->json(['message' => 'Type d’utilisateur mis à jour avec succès', 'type_user' => $typeUser]);
    }

    /**
     * Supprimer un type d’utilisateur (Soft Delete).
     */
    public function destroy($id)
    {
        $typeUser = TypeUser::findOrFail($id);
        $typeUser->delete();

        return response()->json(['message' => 'Type d’utilisateur supprimé avec succès']);
    }
}
