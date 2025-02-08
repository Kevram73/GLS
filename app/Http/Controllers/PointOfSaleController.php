<?php

namespace App\Http\Controllers;

use App\Models\PointOfSale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PointOfSaleController extends Controller
{
    /**
     * Lister tous les points de vente.
     */
    public function index()
    {
        $points = PointOfSale::latest()->paginate(10);
        return response()->json($points);
    }

    /**
     * Créer un nouveau point de vente.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:point_of_sales,name',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pointOfSale = PointOfSale::create($request->all());

        return response()->json(['message' => 'Point de vente créé avec succès', 'point_of_sale' => $pointOfSale], 201);
    }

    /**
     * Récupérer un point de vente spécifique.
     */
    public function show($id)
    {
        $pointOfSale = PointOfSale::findOrFail($id);
        return response()->json($pointOfSale);
    }

    /**
     * Mettre à jour un point de vente.
     */
    public function update(Request $request, $id)
    {
        $pointOfSale = PointOfSale::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:point_of_sales,name,' . $id,
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pointOfSale->update($request->all());

        return response()->json(['message' => 'Point de vente mis à jour avec succès', 'point_of_sale' => $pointOfSale]);
    }

    /**
     * Supprimer un point de vente (Soft Delete).
     */
    public function destroy($id)
    {
        $pointOfSale = PointOfSale::findOrFail($id);
        $pointOfSale->delete();

        return response()->json(['message' => 'Point de vente supprimé avec succès']);
    }

    /**
     * Lister les points de vente actifs.
     */
    public function activePoints()
    {
        $points = PointOfSale::where('is_active', true)->get();
        return response()->json($points);
    }

    /**
     * Récupérer les utilisateurs d’un point de vente.
     */
    public function getUsers($pointOfSaleId)
    {
        $users = User::where('point_of_sale_id', $pointOfSaleId)->get();
        return response()->json($users);
    }
}
