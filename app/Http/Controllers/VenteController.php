<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class VenteController extends Controller
{
    /**
     * Lister toutes les ventes.
     */
    public function index()
    {
        if(Auth::user()->id == 1){
            $ventes = Vente::all();
        } else {
            $ventes = Vente::where('seller_id', Auth::user()->id)->get();
        }

        return response()->json($ventes);
    }

    /**
     * Créer une nouvelle vente.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'point_of_sale_id' => 'required|exists:point_of_sales,id',
            'client_id' => 'nullable|exists:users,id',
            'journal_id' => 'nullable|exists:journals,id',
            'nbre' => 'required|integer|min:1',
            'is_paid' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vente = Vente::create([
            'date' => $request->date,
            'montant' => $request->montant,
            'point_of_sale_id' => $request->point_of_sale_id,
            'client_id' => $request->client_id,
            'journal_id' => $request->journal_id,
            'nbre' => $request->nbre,
            'seller_id' => Auth::id(),
            'is_paid' => $request->is_paid,
        ]);

        return response()->json(['message' => 'Vente enregistrée avec succès', 'vente' => $vente], 201);
    }

    /**
     * Récupérer une vente spécifique.
     */
    public function show($id)
    {
        $vente = Vente::findOrFail($id);
        return response()->json($vente);
    }

    /**
     * Mettre à jour une vente.
     */
    public function update(Request $request, $id)
    {
        $vente = Vente::findOrFail($id);

        if ($vente->seller_id !== Auth::id()) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'sometimes|date',
            'montant' => 'sometimes|numeric|min:0',
            'point_of_sale_id' => 'sometimes|exists:point_of_sales,id',
            'client_id' => 'nullable|exists:users,id',
            'journal_id' => 'nullable|exists:journals,id',
            'nbre' => 'sometimes|integer|min:1',
            'is_paid' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vente->update($request->only(['date', 'montant', 'point_of_sale_id', 'client_id', 'journal_id', 'nbre', 'is_paid']));

        return response()->json(['message' => 'Vente mise à jour avec succès', 'vente' => $vente]);
    }

    /**
     * Supprimer une vente (Soft Delete).
     */
    public function destroy($id)
    {
        $vente = Vente::findOrFail($id);

        if ($vente->seller_id !== Auth::id()) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $vente->delete();
        return response()->json(['message' => 'Vente supprimée avec succès']);
    }

    /**
     * Récupérer les ventes d’un vendeur spécifique.
     */
    public function salesBySeller($sellerId)
    {
        $ventes = Vente::where('seller_id', $sellerId)->get();
        return response()->json($ventes);
    }

    /**
     * Récupérer les ventes d’un point de vente spécifique.
     */
    public function salesByPointOfSale($pointOfSaleId)
    {
        $ventes = Vente::where('point_of_sale_id', $pointOfSaleId)->get();
        return response()->json($ventes);
    }

    /**
     * Récupérer les ventes non payées.
     */
    public function unpaidSales()
    {
        $ventes = Vente::where('is_paid', false)->paginate(10);
        return response()->json($ventes);
    }
}
