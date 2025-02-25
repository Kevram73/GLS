<?php

namespace App\Http\Controllers;

use App\Models\Plainte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlainteController extends Controller
{
    /**
     * Affiche la liste de toutes les plaintes.
     */
    public function index()
    {
        $plaintes = Plainte::all();
        return response()->json(['data' => $plaintes], 200);
    }

    /**
     * Affiche le formulaire de création d'une plainte.
     * (Non utilisé en API)
     */
    public function create()
    {
        return response()->json([
            'message' => 'Le formulaire de création n\'est pas disponible via l\'API.'
        ], 200);
    }

    /**
     * Stocke une nouvelle plainte dans la base de données.
     * Le champ register_id est défini par défaut avec l\'ID de l\'utilisateur connecté.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'client_id' => 'required|integer'
        ]);

        // Définir register_id avec l'ID de l'utilisateur connecté
        $validatedData['register_id'] = Auth::id();

        $plainte = Plainte::create($validatedData);

        return response()->json([
            'message' => 'Plainte créée avec succès',
            'data'    => $plainte
        ], 201);
    }

    /**
     * Affiche les détails d'une plainte spécifique.
     */
    public function show(string $id)
    {
        $plainte = Plainte::findOrFail($id);
        return response()->json(['data' => $plainte], 200);
    }

    /**
     * Affiche le formulaire d'édition d'une plainte.
     * (Non utilisé en API)
     */
    public function edit(string $id)
    {
        return response()->json([
            'message' => 'Le formulaire d\'édition n\'est pas disponible via l\'API.'
        ], 200);
    }

    /**
     * Met à jour une plainte existante.
     */
    public function update(Request $request, string $id)
    {
        $plainte = Plainte::findOrFail($id);

        $validatedData = $request->validate([
            'title'     => 'sometimes|required|string|max:255',
            'content'   => 'sometimes|required|string',
            'client_id' => 'sometimes|required|integer'
        ]);

        $plainte->update($validatedData);

        return response()->json([
            'message' => 'Plainte mise à jour avec succès',
            'data'    => $plainte
        ], 200);
    }

    /**
     * Supprime une plainte.
     */
    public function destroy(string $id)
    {
        $plainte = Plainte::findOrFail($id);
        $plainte->delete();

        return response()->json([
            'message' => 'Plainte supprimée avec succès'
        ], 200);
    }
}
