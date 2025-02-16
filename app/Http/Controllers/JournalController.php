<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    /**
     * Lister tous les journaux.
     */
    public function index()
    {
        $journals = Journal::all();
        return response()->json($journals);
    }

    /**
     * Créer un nouveau journal.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:journals,title',
            'price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $journal = Journal::create($request->all());

        return response()->json(['message' => 'Journal créé avec succès', 'journal' => $journal], 201);
    }

    /**
     * Récupérer un journal spécifique.
     */
    public function show($id)
    {
        $journal = Journal::findOrFail($id);
        return response()->json($journal);
    }

    /**
     * Mettre à jour un journal.
     */
    public function update(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255|unique:journals,title,' . $id,
            'price' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $journal->update($request->all());

        return response()->json(['message' => 'Journal mis à jour avec succès', 'journal' => $journal]);
    }

    /**
     * Supprimer un journal (Soft Delete).
     */
    public function destroy($id)
    {
        $journal = Journal::findOrFail($id);
        $journal->delete();

        return response()->json(['message' => 'Journal supprimé avec succès']);
    }

    /**
     * Lister les journaux actifs.
     */
    public function activeJournals()
    {
        $journals = Journal::where('is_active', true)->get();
        return response()->json($journals);
    }
}
